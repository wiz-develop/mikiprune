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

function miki_blog_search_analytics_normalize_keyword($keyword) {
    $keyword = sanitize_text_field((string) $keyword);

    if (function_exists('mb_convert_kana')) {
        $keyword = mb_convert_kana($keyword, 'asKV', 'UTF-8');
    }

    if (function_exists('mb_strtolower')) {
        $keyword = mb_strtolower($keyword, 'UTF-8');
    } else {
        $keyword = strtolower($keyword);
    }

    $keyword = preg_replace('/[\s\x{3000},，、]+/u', ' ', trim($keyword));

    return is_string($keyword) ? trim($keyword) : '';
}

function miki_blog_search_analytics_keyword_terms($keyword) {
    $keyword = miki_blog_search_analytics_normalize_keyword($keyword);

    if ('' === $keyword) {
        return array();
    }

    $terms = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY);

    return is_array($terms) ? array_values(array_unique($terms)) : array();
}

function miki_blog_search_analytics_category_pairs($slugs_json, $names_json) {
    $slugs = miki_blog_search_analytics_decode_list($slugs_json);
    $names = miki_blog_search_analytics_decode_list($names_json);
    $pairs = array();

    foreach ($slugs as $index => $slug) {
        $pairs[] = array(
            'slug' => $slug,
            'name' => isset($names[$index]) && '' !== $names[$index] ? $names[$index] : $slug,
        );
    }

    usort($pairs, function ($left, $right) {
        return strcmp($left['slug'], $right['slug']);
    });

    return $pairs;
}

function miki_blog_search_analytics_sort_aggregates(&$rows, $label_key) {
    usort($rows, function ($left, $right) use ($label_key) {
        if ($left['search_count'] !== $right['search_count']) {
            return $right['search_count'] <=> $left['search_count'];
        }

        if ($left['zero_result_count'] !== $right['zero_result_count']) {
            return $right['zero_result_count'] <=> $left['zero_result_count'];
        }

        return strnatcasecmp($left[$label_key], $right[$label_key]);
    });
}

function miki_blog_search_analytics_aggregate_rows($rows) {
    $keyword_map = array();
    $condition_map = array();
    $keyword_search_count = 0;

    if (!is_array($rows) && !($rows instanceof Traversable)) {
        $rows = array();
    }

    foreach ($rows as $row) {
        $result_count = max(0, (int) $row->result_count);
        $keyword_terms = miki_blog_search_analytics_keyword_terms($row->keyword);

        if ($keyword_terms) {
            $keyword_search_count++;
        }

        foreach ($keyword_terms as $term) {
            if (!isset($keyword_map[$term])) {
                $keyword_map[$term] = array(
                    'keyword' => $term,
                    'search_count' => 0,
                    'zero_result_count' => 0,
                    'result_count_sum' => 0,
                    'last_searched_at' => '',
                );
            }

            $keyword_map[$term]['search_count']++;
            $keyword_map[$term]['zero_result_count'] += 0 === $result_count ? 1 : 0;
            $keyword_map[$term]['result_count_sum'] += $result_count;
            $keyword_map[$term]['last_searched_at'] = max($keyword_map[$term]['last_searched_at'], $row->searched_at);
        }

        $canonical_terms = $keyword_terms;
        sort($canonical_terms, SORT_STRING);
        $category_pairs = miki_blog_search_analytics_category_pairs($row->category_slugs, $row->category_names);
        $category_slugs = wp_list_pluck($category_pairs, 'slug');
        $category_names = wp_list_pluck($category_pairs, 'name');
        $normalized_keyword = implode(' ', $canonical_terms);
        $archive_month = preg_match('/^\d{6}$/', (string) $row->archive_month) ? (string) $row->archive_month : '';
        $condition_key = hash('sha256', wp_json_encode(array($normalized_keyword, $category_slugs, $archive_month), JSON_UNESCAPED_UNICODE));

        if (!isset($condition_map[$condition_key])) {
            $condition_map[$condition_key] = array(
                'condition_label' => $normalized_keyword."\n".implode(' / ', $category_names)."\n".$archive_month,
                'keyword' => $normalized_keyword,
                'category_names' => $category_names,
                'archive_month' => $archive_month,
                'search_count' => 0,
                'zero_result_count' => 0,
                'result_count_sum' => 0,
                'last_searched_at' => '',
            );
        }

        $condition_map[$condition_key]['search_count']++;
        $condition_map[$condition_key]['zero_result_count'] += 0 === $result_count ? 1 : 0;
        $condition_map[$condition_key]['result_count_sum'] += $result_count;
        $condition_map[$condition_key]['last_searched_at'] = max($condition_map[$condition_key]['last_searched_at'], $row->searched_at);
    }

    $keyword_rows = array_values($keyword_map);
    $condition_rows = array_values($condition_map);
    miki_blog_search_analytics_sort_aggregates($keyword_rows, 'keyword');
    miki_blog_search_analytics_sort_aggregates($condition_rows, 'condition_label');

    return array(
        'keyword_rows' => $keyword_rows,
        'condition_rows' => $condition_rows,
        'keyword_search_count' => $keyword_search_count,
    );
}

function miki_blog_search_analytics_log($search_data) {
    global $wpdb;

    if (is_admin() || !is_array($search_data)) {
        return false;
    }

    miki_blog_search_analytics_maybe_install();

    $keyword = isset($search_data['keyword']) && is_scalar($search_data['keyword'])
        ? miki_blog_search_analytics_normalize_keyword((string) $search_data['keyword'])
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
        'ブログ検索レポート',
        'ブログ検索レポート',
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

    return $wpdb->get_results($sql) ?: array();
}

function miki_blog_search_analytics_get_all_rows($range) {
    global $wpdb;

    $table_name = miki_blog_search_analytics_table_name();
    $sql = $wpdb->prepare(
        "SELECT id, searched_at, keyword, category_slugs, category_names, archive_month, result_count
        FROM {$table_name}
        WHERE searched_at >= %s AND searched_at <= %s
        ORDER BY searched_at DESC, id DESC",
        $range['start_datetime'],
        $range['end_datetime']
    );

    return $wpdb->get_results($sql) ?: array();
}

function miki_blog_search_analytics_average($row) {
    return $row['search_count'] > 0
        ? round($row['result_count_sum'] / $row['search_count'], 1)
        : 0;
}

function miki_blog_search_analytics_rate($count, $total) {
    return $total > 0 ? round(((int) $count / (int) $total) * 100, 1) : 0;
}

function miki_blog_search_analytics_admin_url($range, $view, $paged = 1) {
    $args = array(
        'page' => 'miki-blog-search-analytics',
        'view' => $view,
        'start_date' => $range['start_date'],
        'end_date' => $range['end_date'],
    );

    if ($paged > 1) {
        $args['paged'] = $paged;
    }

    return add_query_arg($args, admin_url('admin.php'));
}

function miki_blog_search_analytics_export_url($range, $report) {
    $report = in_array($report, array('keywords', 'conditions', 'details'), true) ? $report : 'keywords';
    $url = add_query_arg(
        array(
            'action' => 'miki_blog_search_analytics_csv',
            'report' => $report,
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

    miki_blog_search_analytics_maybe_install();
    $range = miki_blog_search_analytics_get_date_range($_GET);
    $views = array(
        'keywords' => 'キーワード別集計',
        'conditions' => '検索条件別集計',
        'details' => '検索明細',
    );
    $current_view = isset($_GET['view']) && is_scalar($_GET['view'])
        ? sanitize_key(wp_unslash((string) $_GET['view']))
        : 'keywords';
    $current_view = isset($views[$current_view]) ? $current_view : 'keywords';
    $per_page = 50;
    $current_page = isset($_GET['paged']) && is_scalar($_GET['paged'])
        ? max(1, absint($_GET['paged']))
        : 1;
    $summary = miki_blog_search_analytics_get_summary($range);
    $all_rows = miki_blog_search_analytics_get_all_rows($range);
    $aggregates = miki_blog_search_analytics_aggregate_rows($all_rows);
    $total_search_count = $summary ? (int) $summary->search_count : 0;
    $zero_result_count = $summary ? (int) $summary->zero_result_count : 0;
    $zero_result_rate = miki_blog_search_analytics_rate($zero_result_count, $total_search_count);
    $average_result_count = $summary ? round((float) $summary->average_result_count, 1) : 0;
    $unique_keyword_count = count($aggregates['keyword_rows']);

    if ('conditions' === $current_view) {
        $view_rows = $aggregates['condition_rows'];
    } elseif ('details' === $current_view) {
        $view_rows = $all_rows;
    } else {
        $view_rows = $aggregates['keyword_rows'];
    }

    $total_items = count($view_rows);
    $total_pages = max(1, (int) ceil($total_items / $per_page));
    $current_page = min($current_page, $total_pages);
    $row_offset = ($current_page - 1) * $per_page;
    $rows = array_slice($view_rows, $row_offset, $per_page);
    $max_keyword_count = $aggregates['keyword_rows'] ? (int) $aggregates['keyword_rows'][0]['search_count'] : 0;
    ?>
    <div class="wrap miki-search-report">
        <div class="miki-search-report__heading">
            <div>
                <h1>ブログ検索レポート</h1>
                <p>検索ニーズと見つからなかった情報を確認できます。個人情報は記録していません。</p>
            </div>
            <?php if ($range['is_valid'] && $total_search_count > 0) : ?>
                <div class="miki-search-report__csv-actions" aria-label="CSVダウンロード">
                    <a class="button button-secondary" href="<?php echo esc_url(miki_blog_search_analytics_export_url($range, 'keywords')); ?>">キーワード集計CSV</a>
                    <a class="button button-secondary" href="<?php echo esc_url(miki_blog_search_analytics_export_url($range, 'conditions')); ?>">検索条件集計CSV</a>
                    <a class="button button-secondary" href="<?php echo esc_url(miki_blog_search_analytics_export_url($range, 'details')); ?>">検索明細CSV</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!$range['is_valid']) : ?>
            <div class="notice notice-error inline"><p>期間が正しくありません。開始日は終了日以前の日付を指定してください。</p></div>
        <?php endif; ?>

        <form class="miki-search-report__filter" method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>">
            <input type="hidden" name="page" value="miki-blog-search-analytics">
            <input type="hidden" name="view" value="<?php echo esc_attr($current_view); ?>">
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
                <strong><?php echo esc_html(number_format_i18n($total_search_count)); ?></strong>
                <small>回</small>
            </section>
            <section class="miki-search-report__metric">
                <span>検索されたキーワード</span>
                <strong><?php echo esc_html(number_format_i18n($unique_keyword_count)); ?></strong>
                <small>種類</small>
            </section>
            <section class="miki-search-report__metric">
                <span>結果が0件の検索</span>
                <strong><?php echo esc_html(number_format_i18n($zero_result_count)); ?></strong>
                <small>回・<?php echo esc_html(number_format_i18n($zero_result_rate, 1)); ?>%</small>
            </section>
            <section class="miki-search-report__metric">
                <span>平均ヒット件数</span>
                <strong><?php echo esc_html(number_format_i18n($average_result_count, 1)); ?></strong>
                <small>件</small>
            </section>
        </div>

        <nav class="miki-search-report__tabs" aria-label="レポート表示切り替え">
            <?php foreach ($views as $view_key => $view_label) : ?>
                <a class="miki-search-report__tab<?php echo $current_view === $view_key ? ' is-active' : ''; ?>"
                    href="<?php echo esc_url(miki_blog_search_analytics_admin_url($range, $view_key)); ?>"
                    <?php if ($current_view === $view_key) echo 'aria-current="page"'; ?>>
                    <?php echo esc_html($view_label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="miki-search-report__table-card">
            <div class="miki-search-report__table-heading">
                <div>
                    <h2><?php echo esc_html($views[$current_view]); ?></h2>
                    <?php if ('keywords' === $current_view) : ?>
                        <p>複数キーワードは各語に1回ずつ加算します。使用率の合計は100%にならない場合があります。</p>
                    <?php elseif ('conditions' === $current_view) : ?>
                        <p>キーワード・カテゴリー・年月が同じ検索を1つにまとめています。</p>
                    <?php else : ?>
                        <p>検索1回ごとの元データです。</p>
                    <?php endif; ?>
                </div>
                <p><?php echo esc_html($range['start_date']); ?> 〜 <?php echo esc_html($range['end_date']); ?>／<?php echo esc_html(number_format_i18n($total_items)); ?>件</p>
            </div>
            <div class="miki-search-report__table-scroll">
                <?php if ('keywords' === $current_view) : ?>
                <table class="widefat fixed striped miki-search-report__table--keywords">
                    <thead>
                        <tr>
                            <th class="column-rank">順位</th>
                            <th>キーワード</th>
                            <th class="column-count">検索回数</th>
                            <th class="column-rate">使用率</th>
                            <th class="column-zero">0件検索</th>
                            <th class="column-average">平均ヒット</th>
                            <th class="column-date">最終検索日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                            <?php foreach ($rows as $index => $row) : ?>
                                <?php
                                    $usage_rate = miki_blog_search_analytics_rate($row['search_count'], $aggregates['keyword_search_count']);
                                    $zero_rate = miki_blog_search_analytics_rate($row['zero_result_count'], $row['search_count']);
                                    $demand_width = miki_blog_search_analytics_rate($row['search_count'], $max_keyword_count);
                                ?>
                                <tr>
                                    <td class="miki-search-report__rank"><?php echo esc_html(number_format_i18n($row_offset + $index + 1)); ?></td>
                                    <td class="miki-search-report__keyword-cell">
                                        <span class="miki-search-report__demand" style="--demand-width: <?php echo esc_attr($demand_width); ?>%;">
                                            <strong><?php echo esc_html($row['keyword']); ?></strong>
                                        </span>
                                    </td>
                                    <td><strong><?php echo esc_html(number_format_i18n($row['search_count'])); ?>回</strong></td>
                                    <td><?php echo esc_html(number_format_i18n($usage_rate, 1)); ?>%</td>
                                    <td class="<?php echo $row['zero_result_count'] > 0 ? 'is-zero' : ''; ?>">
                                        <?php echo esc_html(number_format_i18n($row['zero_result_count'])); ?>回
                                        <small>（<?php echo esc_html(number_format_i18n($zero_rate, 1)); ?>%）</small>
                                    </td>
                                    <td><?php echo esc_html(number_format_i18n(miki_blog_search_analytics_average($row), 1)); ?>件</td>
                                    <td><?php echo esc_html(mysql2date('Y.m.d H:i', $row['last_searched_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td class="miki-search-report__no-data" colspan="7">この期間にフリーワード検索はありません。</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php elseif ('conditions' === $current_view) : ?>
                <table class="widefat fixed striped miki-search-report__table--conditions">
                    <thead>
                        <tr>
                            <th>フリーワード</th>
                            <th>カテゴリー</th>
                            <th class="column-month">年月</th>
                            <th class="column-count">検索回数</th>
                            <th class="column-zero">0件検索</th>
                            <th class="column-average">平均ヒット</th>
                            <th class="column-date">最終検索日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows) : ?>
                            <?php foreach ($rows as $row) : ?>
                                <?php $zero_rate = miki_blog_search_analytics_rate($row['zero_result_count'], $row['search_count']); ?>
                                <tr>
                                    <td>
                                        <?php if ('' !== $row['keyword']) : ?>
                                            <strong><?php echo esc_html($row['keyword']); ?></strong>
                                        <?php else : ?>
                                            <span class="miki-search-report__empty">指定なし</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['category_names']) : ?>
                                            <div class="miki-search-report__categories">
                                                <?php foreach ($row['category_names'] as $category_name) : ?>
                                                    <span><?php echo esc_html($category_name); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else : ?>
                                            <span class="miki-search-report__empty">すべて</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html(miki_blog_search_analytics_month_label($row['archive_month'])); ?></td>
                                    <td><strong><?php echo esc_html(number_format_i18n($row['search_count'])); ?>回</strong></td>
                                    <td class="<?php echo $row['zero_result_count'] > 0 ? 'is-zero' : ''; ?>">
                                        <?php echo esc_html(number_format_i18n($row['zero_result_count'])); ?>回
                                        <small>（<?php echo esc_html(number_format_i18n($zero_rate, 1)); ?>%）</small>
                                    </td>
                                    <td><?php echo esc_html(number_format_i18n(miki_blog_search_analytics_average($row), 1)); ?>件</td>
                                    <td><?php echo esc_html(mysql2date('Y.m.d H:i', $row['last_searched_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td class="miki-search-report__no-data" colspan="7">この期間の検索条件はありません。</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <table class="widefat fixed striped miki-search-report__table--details">
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
                                            <strong><?php echo esc_html($row->keyword); ?></strong>
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
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1) : ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <?php
                        $pagination_base = miki_blog_search_analytics_admin_url($range, $current_view).'&paged=%#%';
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
        .miki-search-report__csv-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 6px; max-width: 550px; }
        .miki-search-report__filter { display: flex; align-items: flex-end; gap: 12px; border: 1px solid #dcdcde; border-left: 4px solid #c1454a; border-radius: 4px; padding: 18px 20px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
        .miki-search-report__date-field { display: grid; gap: 6px; }
        .miki-search-report__date-field label { color: #50575e; font-size: 12px; font-weight: 600; }
        .miki-search-report__date-field input { min-width: 170px; min-height: 34px; }
        .miki-search-report__date-separator { padding-bottom: 8px; color: #8c8f94; }
        .miki-search-report__filter .button { min-height: 34px; margin-left: 4px; }
        .miki-search-report__summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 18px 0; }
        .miki-search-report__metric { border: 1px solid #dcdcde; border-radius: 4px; padding: 18px 20px; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
        .miki-search-report__metric span { display: block; margin-bottom: 9px; color: #646970; font-size: 12px; font-weight: 600; }
        .miki-search-report__metric strong { color: #c1454a; font-size: 28px; line-height: 1; }
        .miki-search-report__metric small { margin-left: 4px; color: #646970; }
        .miki-search-report__tabs { display: flex; gap: 4px; margin: 22px 0 0; border-bottom: 1px solid #c3c4c7; }
        .miki-search-report__tab { position: relative; margin-bottom: -1px; padding: 11px 18px; border: 1px solid transparent; color: #50575e; font-weight: 600; text-decoration: none; }
        .miki-search-report__tab:hover, .miki-search-report__tab:focus-visible { color: #8b3034; }
        .miki-search-report__tab:focus-visible { outline: 2px solid #c1454a; outline-offset: 2px; }
        .miki-search-report__tab.is-active { border-color: #c3c4c7 #c3c4c7 #fff; background: #fff; color: #8b3034; }
        .miki-search-report__tab.is-active::before { position: absolute; top: -1px; right: -1px; left: -1px; height: 3px; background: #c1454a; content: ''; }
        .miki-search-report__table-card { border: 1px solid #c3c4c7; border-top: 0; background: #fff; }
        .miki-search-report__table-heading { display: flex; align-items: baseline; justify-content: space-between; gap: 20px; padding: 16px 18px; border-bottom: 1px solid #dcdcde; }
        .miki-search-report__table-heading h2 { margin: 0; font-size: 16px; }
        .miki-search-report__table-heading p { margin: 0; color: #646970; font-size: 12px; }
        .miki-search-report__table-heading > div p { margin-top: 5px; }
        .miki-search-report__table-scroll { overflow-x: auto; }
        .miki-search-report table { min-width: 940px; border: 0; box-shadow: none; }
        .miki-search-report th { font-weight: 600; }
        .miki-search-report th, .miki-search-report td { padding: 12px 14px; vertical-align: middle; }
        .miki-search-report .column-rank { width: 52px; text-align: center; }
        .miki-search-report .column-date { width: 140px; }
        .miki-search-report .column-month { width: 105px; }
        .miki-search-report .column-count { width: 90px; text-align: right; }
        .miki-search-report .column-rate { width: 75px; text-align: right; }
        .miki-search-report .column-zero { width: 115px; text-align: right; }
        .miki-search-report .column-average { width: 105px; text-align: right; }
        .miki-search-report .column-results { width: 90px; text-align: right; }
        .miki-search-report__table--keywords td:nth-child(n+3):not(:last-child), .miki-search-report__table--conditions td:nth-child(n+4):not(:last-child), .miki-search-report__table--details td:last-child { text-align: right; }
        .miki-search-report__rank { color: #8c8f94; text-align: center; font-variant-numeric: tabular-nums; }
        .miki-search-report__keyword-cell { padding-top: 8px !important; padding-bottom: 8px !important; }
        .miki-search-report__demand { position: relative; display: block; min-height: 30px; padding: 6px 9px; overflow: hidden; border-radius: 3px; }
        .miki-search-report__demand::before { position: absolute; inset: 0 auto 0 0; width: var(--demand-width); background: #f7e8e8; content: ''; }
        .miki-search-report__demand strong { position: relative; }
        .miki-search-report__categories { display: flex; flex-wrap: wrap; gap: 5px; }
        .miki-search-report__categories span { border-radius: 999px; padding: 3px 8px; background: #f7e8e8; color: #8b3034; font-size: 11px; line-height: 1.4; }
        .miki-search-report__empty { color: #8c8f94; }
        .miki-search-report__result-count { font-variant-numeric: tabular-nums; }
        .miki-search-report .is-zero, .miki-search-report__result-count.is-zero { color: #b32d2e; }
        .miki-search-report td small { color: inherit; white-space: nowrap; }
        .miki-search-report__no-data { height: 110px; color: #646970; text-align: center; }
        .miki-search-report .tablenav { margin: 0; padding: 12px 16px; border-top: 1px solid #dcdcde; }
        @media (max-width: 782px) {
            .miki-search-report__heading { display: block; }
            .miki-search-report__csv-actions { justify-content: flex-start; margin-top: 14px; }
            .miki-search-report__filter { align-items: stretch; flex-direction: column; }
            .miki-search-report__date-field input { width: 100%; max-width: none; }
            .miki-search-report__date-separator { display: none; }
            .miki-search-report__filter .button { margin: 4px 0 0; }
            .miki-search-report__summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .miki-search-report__tabs { overflow-x: auto; }
            .miki-search-report__tab { flex: 0 0 auto; }
            .miki-search-report__table-heading { display: block; }
            .miki-search-report__table-heading p { margin-top: 6px; }
        }
        @media (max-width: 480px) {
            .miki-search-report__summary { grid-template-columns: 1fr; }
            .miki-search-report__csv-actions .button { width: 100%; text-align: center; }
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
    $report = isset($_GET['report']) && is_scalar($_GET['report'])
        ? sanitize_key(wp_unslash((string) $_GET['report']))
        : 'keywords';
    $report = in_array($report, array('keywords', 'conditions', 'details'), true) ? $report : 'keywords';

    if (!$range['is_valid']) {
        wp_die('期間が正しくありません。開始日は終了日以前の日付を指定してください。');
    }

    $all_rows = miki_blog_search_analytics_get_all_rows($range);
    $aggregates = miki_blog_search_analytics_aggregate_rows($all_rows);

    nocache_headers();
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="blog-search-'.$report.'-'.$range['start_date'].'-'.$range['end_date'].'.csv"');

    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");

    if ('keywords' === $report) {
        fputcsv($output, array('順位', 'キーワード', '検索回数', '使用率（%）', '0件検索回数', '0件率（%）', '平均ヒット件数', '最終検索日時'), ',', '"', '');

        foreach ($aggregates['keyword_rows'] as $index => $row) {
            fputcsv($output, array(
                $index + 1,
                miki_blog_search_analytics_csv_value($row['keyword']),
                $row['search_count'],
                miki_blog_search_analytics_rate($row['search_count'], $aggregates['keyword_search_count']),
                $row['zero_result_count'],
                miki_blog_search_analytics_rate($row['zero_result_count'], $row['search_count']),
                miki_blog_search_analytics_average($row),
                $row['last_searched_at'],
            ), ',', '"', '');
        }
    } elseif ('conditions' === $report) {
        fputcsv($output, array('フリーワード', 'カテゴリー名', '年月', '検索回数', '0件検索回数', '0件率（%）', '平均ヒット件数', '最終検索日時'), ',', '"', '');

        foreach ($aggregates['condition_rows'] as $row) {
            fputcsv($output, array(
                miki_blog_search_analytics_csv_value('' !== $row['keyword'] ? $row['keyword'] : '指定なし'),
                miki_blog_search_analytics_csv_value($row['category_names'] ? implode(' / ', $row['category_names']) : 'すべて'),
                '' !== $row['archive_month'] ? $row['archive_month'] : 'すべて',
                $row['search_count'],
                $row['zero_result_count'],
                miki_blog_search_analytics_rate($row['zero_result_count'], $row['search_count']),
                miki_blog_search_analytics_average($row),
                $row['last_searched_at'],
            ), ',', '"', '');
        }
    } else {
        fputcsv($output, array('ID', '検索日時', 'フリーワード', 'カテゴリー名', 'カテゴリースラッグ', '年月', '検索結果件数'), ',', '"', '');

        foreach ($all_rows as $row) {
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
    }

    fclose($output);
    exit;
}
add_action('admin_post_miki_blog_search_analytics_csv', 'miki_blog_search_analytics_export_csv');
