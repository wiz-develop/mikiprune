<?php
/**
 * Blog search analytics for the Miki blog.
 *
 * Stores search conditions without IP addresses, user IDs, or user agents and
 * provides a date-filtered report and CSV export in WordPress admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('MIKI_BLOG_SEARCH_ANALYTICS_DB_VERSION', '1.0.0');

function miki_blog_search_analytics_table_name() {
    global $wpdb;

    return $wpdb->prefix.'miki_blog_search_log';
}

function miki_blog_search_analytics_install() {
    global $wpdb;

    $table_name = miki_blog_search_analytics_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH.'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$table_name} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        searched_at datetime NOT NULL,
        keyword text NOT NULL,
        category_slugs text NOT NULL,
        category_names text NOT NULL,
        archive_month char(6) NOT NULL DEFAULT '',
        result_count bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        KEY searched_at (searched_at),
        KEY archive_month (archive_month)
    ) {$charset_collate};";

    dbDelta($sql);
    update_option('miki_blog_search_analytics_db_version', MIKI_BLOG_SEARCH_ANALYTICS_DB_VERSION, false);
}

function miki_blog_search_analytics_maybe_install() {
    if (MIKI_BLOG_SEARCH_ANALYTICS_DB_VERSION !== get_option('miki_blog_search_analytics_db_version')) {
        miki_blog_search_analytics_install();
    }
}
add_action('init', 'miki_blog_search_analytics_maybe_install', 1);
add_action('after_switch_theme', 'miki_blog_search_analytics_install');

function miki_blog_search_analytics_clean_list($values) {
    if (!is_array($values)) {
        return array();
    }

    $values = array_map(function ($value) {
        return is_scalar($value) ? sanitize_text_field((string) $value) : '';
    }, $values);

    return array_values(array_unique(array_filter($values, 'strlen')));
}

function miki_blog_search_analytics_log($search_data) {
    global $wpdb;

    if (is_admin() || !is_array($search_data)) {
        return false;
    }

    miki_blog_search_analytics_maybe_install();

    $keyword = isset($search_data['keyword']) && is_scalar($search_data['keyword'])
        ? sanitize_text_field((string) $search_data['keyword'])
        : '';
    $category_slugs = miki_blog_search_analytics_clean_list($search_data['category_slugs'] ?? array());
    $category_names = miki_blog_search_analytics_clean_list($search_data['category_names'] ?? array());
    $archive_month = isset($search_data['archive_month']) && is_scalar($search_data['archive_month'])
        ? sanitize_text_field((string) $search_data['archive_month'])
        : '';
    $archive_month_number = (int) substr($archive_month, 4, 2);
    $archive_month = preg_match('/^\d{6}$/', $archive_month)
        && $archive_month_number >= 1
        && $archive_month_number <= 12
        ? $archive_month
        : '';
    $result_count = isset($search_data['result_count']) ? max(0, (int) $search_data['result_count']) : 0;

    return false !== $wpdb->insert(
        miki_blog_search_analytics_table_name(),
        array(
            'searched_at' => current_time('mysql'),
            'keyword' => $keyword,
            'category_slugs' => wp_json_encode($category_slugs, JSON_UNESCAPED_UNICODE),
            'category_names' => wp_json_encode($category_names, JSON_UNESCAPED_UNICODE),
            'archive_month' => $archive_month,
            'result_count' => $result_count,
        ),
        array('%s', '%s', '%s', '%s', '%s', '%d')
    );
}

function miki_blog_search_analytics_add_admin_menu() {
    add_menu_page(
        'ブログ検索履歴',
        'ブログ検索履歴',
        'manage_options',
        'miki-blog-search-analytics',
        'miki_blog_search_analytics_render_admin_page',
        'dashicons-search',
        58
    );
}
add_action('admin_menu', 'miki_blog_search_analytics_add_admin_menu');

function miki_blog_search_analytics_valid_date($date_value) {
    if (!is_string($date_value) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date_value, $matches)) {
        return false;
    }

    return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
}

function miki_blog_search_analytics_get_date_range($source) {
    $today = current_time('Y-m-d');
    $default_start = date_i18n('Y-m-d', current_time('timestamp') - (29 * DAY_IN_SECONDS));
    $start_date = isset($source['start_date']) && is_scalar($source['start_date'])
        ? sanitize_text_field(wp_unslash((string) $source['start_date']))
        : $default_start;
    $end_date = isset($source['end_date']) && is_scalar($source['end_date'])
        ? sanitize_text_field(wp_unslash((string) $source['end_date']))
        : $today;
    $is_valid = miki_blog_search_analytics_valid_date($start_date)
        && miki_blog_search_analytics_valid_date($end_date)
        && $start_date <= $end_date;

    if (!$is_valid) {
        $start_date = $default_start;
        $end_date = $today;
    }

    return array(
        'start_date' => $start_date,
        'end_date' => $end_date,
        'start_datetime' => $start_date.' 00:00:00',
        'end_datetime' => $end_date.' 23:59:59',
        'is_valid' => $is_valid,
    );
}

function miki_blog_search_analytics_decode_list($json_value) {
    $values = json_decode((string) $json_value, true);

    return is_array($values) ? miki_blog_search_analytics_clean_list($values) : array();
}

function miki_blog_search_analytics_month_label($month_value) {
    if (!preg_match('/^(\d{4})(\d{2})$/', (string) $month_value, $matches)
        || (int) $matches[2] < 1
        || (int) $matches[2] > 12) {
        return 'すべて';
    }

    return $matches[1].'年'.(int) $matches[2].'月';
}

function miki_blog_search_analytics_get_summary($range) {
    global $wpdb;

    $table_name = miki_blog_search_analytics_table_name();
    $sql = $wpdb->prepare(
        "SELECT COUNT(*) AS search_count,
            COALESCE(SUM(CASE WHEN result_count = 0 THEN 1 ELSE 0 END), 0) AS zero_result_count,
            COALESCE(AVG(result_count), 0) AS average_result_count
        FROM {$table_name}
        WHERE searched_at >= %s AND searched_at <= %s",
        $range['start_datetime'],
        $range['end_datetime']
    );

    return $wpdb->get_row($sql);
}

function miki_blog_search_analytics_get_rows($range, $limit, $offset) {
    global $wpdb;

    $table_name = miki_blog_search_analytics_table_name();
    $sql = $wpdb->prepare(
        "SELECT id, searched_at, keyword, category_slugs, category_names, archive_month, result_count
        FROM {$table_name}
        WHERE searched_at >= %s AND searched_at <= %s
        ORDER BY searched_at DESC, id DESC
        LIMIT %d OFFSET %d",
        $range['start_datetime'],
        $range['end_datetime'],
        $limit,
        $offset
    );

    return $wpdb->get_results($sql);
}

function miki_blog_search_analytics_export_url($range) {
    $url = add_query_arg(
        array(
            'action' => 'miki_blog_search_analytics_csv',
            'start_date' => $range['start_date'],
            'end_date' => $range['end_date'],
        ),
        admin_url('admin-post.php')
    );

    return wp_nonce_url($url, 'miki_blog_search_analytics_csv');
}

function miki_blog_search_analytics_render_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('この画面を表示する権限がありません。');
    }

    global $wpdb;

    miki_blog_search_analytics_maybe_install();
    $range = miki_blog_search_analytics_get_date_range($_GET);
    $per_page = 50;
    $current_page = isset($_GET['paged']) && is_scalar($_GET['paged'])
        ? max(1, absint($_GET['paged']))
        : 1;
    $summary = miki_blog_search_analytics_get_summary($range);
    $total_rows = $summary ? (int) $summary->search_count : 0;
    $total_pages = max(1, (int) ceil($total_rows / $per_page));
    $current_page = min($current_page, $total_pages);
    $rows = miki_blog_search_analytics_get_rows($range, $per_page, ($current_page - 1) * $per_page);
    $average_result_count = $summary ? round((float) $summary->average_result_count, 1) : 0;
    $zero_result_count = $summary ? (int) $summary->zero_result_count : 0;
    ?>
    <div class="wrap miki-search-report">
        <div class="miki-search-report__heading">
            <div>
                <h1>ブログ検索履歴</h1>
                <p>訪問者が指定した検索条件と検索結果件数を確認できます。個人情報は記録していません。</p>
            </div>
            <?php if ($range['is_valid'] && $total_rows > 0) : ?>
                <a class="button button-secondary miki-search-report__csv" href="<?php echo esc_url(miki_blog_search_analytics_export_url($range)); ?>">
                    <span class="dashicons dashicons-download" aria-hidden="true"></span>
                    CSVをダウンロード
                </a>
            <?php endif; ?>
        </div>

        <?php if (!$range['is_valid']) : ?>
            <div class="notice notice-error inline"><p>期間が正しくありません。開始日は終了日以前の日付を指定してください。</p></div>
        <?php endif; ?>

        <form class="miki-search-report__filter" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="miki-blog-search-analytics">
            <div class="miki-search-report__date-field">
                <label for="miki-search-start-date">検索期間の開始</label>
                <input id="miki-search-start-date" type="date" name="start_date" value="<?php echo esc_attr($range['start_date']); ?>" required>
            </div>
            <span class="miki-search-report__date-separator" aria-hidden="true">〜</span>
            <div class="miki-search-report__date-field">
                <label for="miki-search-end-date">検索期間の終了</label>
                <input id="miki-search-end-date" type="date" name="end_date" value="<?php echo esc_attr($range['end_date']); ?>" required>
            </div>
            <button class="button button-primary" type="submit">この期間を表示</button>
        </form>

        <div class="miki-search-report__summary" aria-label="検索履歴の集計">
            <section class="miki-search-report__metric">
                <span>検索回数</span>
                <strong><?php echo esc_html(number_format_i18n($total_rows)); ?></strong>
                <small>回</small>
            </section>
            <section class="miki-search-report__metric">
                <span>結果が0件の検索</span>
                <strong><?php echo esc_html(number_format_i18n($zero_result_count)); ?></strong>
                <small>回</small>
            </section>
            <section class="miki-search-report__metric">
                <span>平均ヒット件数</span>
                <strong><?php echo esc_html(number_format_i18n($average_result_count, 1)); ?></strong>
                <small>件</small>
            </section>
        </div>

        <div class="miki-search-report__table-card">
            <div class="miki-search-report__table-heading">
                <h2>検索明細</h2>
                <p><?php echo esc_html($range['start_date']); ?> 〜 <?php echo esc_html($range['end_date']); ?>／<?php echo esc_html(number_format_i18n($total_rows)); ?>件</p>
            </div>
            <div class="miki-search-report__table-scroll">
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th class="column-date">検索日時</th>
                            <th>フリーワード</th>
                            <th>カテゴリー</th>
                            <th class="column-month">年月</th>
                            <th class="column-results">結果件数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                            <?php foreach ($rows as $row) : ?>
                                <?php $category_names = miki_blog_search_analytics_decode_list($row->category_names); ?>
                                <tr>
                                    <td><?php echo esc_html(mysql2date('Y.m.d H:i', $row->searched_at)); ?></td>
                                    <td>
                                        <?php if ('' !== $row->keyword) : ?>
                                            <span class="miki-search-report__condition"><?php echo esc_html($row->keyword); ?></span>
                                        <?php else : ?>
                                            <span class="miki-search-report__empty">指定なし</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($category_names) : ?>
                                            <div class="miki-search-report__categories">
                                                <?php foreach ($category_names as $category_name) : ?>
                                                    <span><?php echo esc_html($category_name); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else : ?>
                                            <span class="miki-search-report__empty">すべて</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html(miki_blog_search_analytics_month_label($row->archive_month)); ?></td>
                                    <td>
                                        <strong class="miki-search-report__result-count<?php echo 0 === (int) $row->result_count ? ' is-zero' : ''; ?>">
                                            <?php echo esc_html(number_format_i18n((int) $row->result_count)); ?>件
                                        </strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td class="miki-search-report__no-data" colspan="5">この期間の検索履歴はありません。</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1) : ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $pagination_base = add_query_arg(
                            array(
                                'page' => 'miki-blog-search-analytics',
                                'start_date' => $range['start_date'],
                                'end_date' => $range['end_date'],
                            ),
                            admin_url('admin.php')
                        );
                        $pagination_base .= '&paged=%#%';
                        echo wp_kses_post(paginate_links(array(
                            'base' => $pagination_base,
                            'current' => $current_page,
                            'total' => $total_pages,
                            'prev_text' => '‹',
                            'next_text' => '›',
                        )));
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <style>
        .miki-search-report { max-width: 1280px; color: #2c3338; }
        .miki-search-report__heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; margin: 22px 0 20px; }
        .miki-search-report__heading h1 { margin: 0 0 6px; font-size: 26px; font-weight: 700; }
        .miki-search-report__heading p { margin: 0; color: #646970; }
        .miki-search-report__csv { display: inline-flex !important; align-items: center; gap: 5px; flex: 0 0 auto; }
        .miki-search-report__csv .dashicons { width: 18px; height: 18px; font-size: 18px; }
        .miki-search-report__filter { display: flex; align-items: flex-end; gap: 12px; border: 1px solid #dcdcde; border-left: 4px solid #c1454a; border-radius: 4px; padding: 18px 20px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
        .miki-search-report__date-field { display: grid; gap: 6px; }
        .miki-search-report__date-field label { color: #50575e; font-size: 12px; font-weight: 600; }
        .miki-search-report__date-field input { min-width: 170px; min-height: 34px; }
        .miki-search-report__date-separator { padding-bottom: 8px; color: #8c8f94; }
        .miki-search-report__filter .button { min-height: 34px; margin-left: 4px; }
        .miki-search-report__summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin: 18px 0; }
        .miki-search-report__metric { border: 1px solid #dcdcde; border-radius: 4px; padding: 18px 20px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
        .miki-search-report__metric span { display: block; margin-bottom: 9px; color: #646970; font-size: 12px; font-weight: 600; }
        .miki-search-report__metric strong { color: #c1454a; font-size: 28px; line-height: 1; }
        .miki-search-report__metric small { margin-left: 4px; color: #646970; }
        .miki-search-report__table-card { border: 1px solid #c3c4c7; background: #fff; }
        .miki-search-report__table-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 20px; padding: 16px 18px; border-bottom: 1px solid #dcdcde; }
        .miki-search-report__table-heading h2 { margin: 0; font-size: 16px; }
        .miki-search-report__table-heading p { margin: 0; color: #646970; font-size: 12px; }
        .miki-search-report__table-scroll { overflow-x: auto; }
        .miki-search-report table { min-width: 850px; border: 0; box-shadow: none; }
        .miki-search-report th { font-weight: 600; }
        .miki-search-report th, .miki-search-report td { padding: 12px 14px; vertical-align: middle; }
        .miki-search-report .column-date { width: 145px; }
        .miki-search-report .column-month { width: 105px; }
        .miki-search-report .column-results { width: 90px; text-align: right; }
        .miki-search-report td:last-child { text-align: right; }
        .miki-search-report__condition { font-weight: 600; }
        .miki-search-report__categories { display: flex; flex-wrap: wrap; gap: 5px; }
        .miki-search-report__categories span { border-radius: 999px; padding: 3px 8px; background: #f7e8e8; color: #8b3034; font-size: 11px; line-height: 1.4; }
        .miki-search-report__empty { color: #8c8f94; }
        .miki-search-report__result-count { font-variant-numeric: tabular-nums; }
        .miki-search-report__result-count.is-zero { color: #b32d2e; }
        .miki-search-report__no-data { height: 110px; color: #646970; text-align: center; }
        .miki-search-report .tablenav { margin: 0; padding: 12px 16px; border-top: 1px solid #dcdcde; }
        @media (max-width: 782px) {
            .miki-search-report__heading { display: block; }
            .miki-search-report__csv { margin-top: 14px !important; }
            .miki-search-report__filter { align-items: stretch; flex-direction: column; }
            .miki-search-report__date-field input { width: 100%; max-width: none; }
            .miki-search-report__date-separator { display: none; }
            .miki-search-report__filter .button { margin: 4px 0 0; }
            .miki-search-report__summary { grid-template-columns: 1fr; }
            .miki-search-report__table-heading { display: block; }
            .miki-search-report__table-heading p { margin-top: 6px; }
        }
    </style>
    <?php
}

function miki_blog_search_analytics_csv_value($value) {
    $value = (string) $value;

    if (preg_match('/^[=+\-@]/u', $value)) {
        return "'".$value;
    }

    return $value;
}

function miki_blog_search_analytics_export_csv() {
    if (!current_user_can('manage_options')) {
        wp_die('CSVをダウンロードする権限がありません。');
    }

    check_admin_referer('miki_blog_search_analytics_csv');
    $range = miki_blog_search_analytics_get_date_range($_GET);

    if (!$range['is_valid']) {
        wp_die('期間が正しくありません。開始日は終了日以前の日付を指定してください。');
    }

    nocache_headers();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="blog-search-'.$range['start_date'].'-'.$range['end_date'].'.csv"');

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");
    fputcsv($output, array('ID', '検索日時', 'フリーワード', 'カテゴリー名', 'カテゴリースラッグ', '年月', '検索結果件数'), ',', '"', '');

    $offset = 0;
    $batch_size = 1000;

    do {
        $rows = miki_blog_search_analytics_get_rows($range, $batch_size, $offset);

        foreach ($rows as $row) {
            $category_names = implode(' / ', miki_blog_search_analytics_decode_list($row->category_names));
            $category_slugs = implode(' / ', miki_blog_search_analytics_decode_list($row->category_slugs));
            fputcsv($output, array(
                $row->id,
                $row->searched_at,
                miki_blog_search_analytics_csv_value($row->keyword),
                miki_blog_search_analytics_csv_value($category_names),
                miki_blog_search_analytics_csv_value($category_slugs),
                '' !== $row->archive_month ? $row->archive_month : 'すべて',
                (int) $row->result_count,
            ), ',', '"', '');
        }

        $offset += $batch_size;
    } while (count($rows) === $batch_size);

    fclose($output);
    exit;
}
add_action('admin_post_miki_blog_search_analytics_csv', 'miki_blog_search_analytics_export_csv');
