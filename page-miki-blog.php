
<?php
/**
 * The template for displaying all single posts
 * Template Name: 新ブログ ブログトップ
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();

$blog_about = CFS()->get('blog_about');
$blog_page_permalink = get_permalink(get_queried_object_id());
$posts_per_page = 6;
$search_posts_per_page = 9;
$blog_page = isset($_GET['blog_page']) && is_scalar($_GET['blog_page'])
    ? max(1, absint($_GET['blog_page']))
    : 1;
$blog_keyword = isset($_GET['blog_keyword']) && is_string($_GET['blog_keyword'])
    ? sanitize_text_field(wp_unslash($_GET['blog_keyword']))
    : '';
$blog_search_keyword = preg_replace('/[\s　,，、]+/u', ' ', trim($blog_keyword));
$blog_search_keyword = is_string($blog_search_keyword) ? trim($blog_search_keyword) : '';
$blog_keyword_terms = '' !== $blog_search_keyword ? preg_split('/\s+/u', $blog_search_keyword, -1, PREG_SPLIT_NO_EMPTY) : array();
$blog_keyword_terms = is_array($blog_keyword_terms) ? array_values(array_unique($blog_keyword_terms)) : array();
$blog_keyword_label = implode('、', $blog_keyword_terms);
$requested_blog_categories = isset($_GET['blog_category']) ? wp_unslash($_GET['blog_category']) : array();
if (!is_array($requested_blog_categories)) {
    $requested_blog_categories = array($requested_blog_categories);
}
$requested_blog_categories = array_values(array_unique(array_filter(array_map(function ($category_slug) {
    return is_string($category_slug) ? sanitize_title($category_slug) : '';
}, $requested_blog_categories))));
$blog_month = isset($_GET['blog_month']) && is_string($_GET['blog_month'])
    ? sanitize_text_field(wp_unslash($_GET['blog_month']))
    : '';

if (!preg_match('/^\d{6}$/', $blog_month)) {
    $blog_month = '';
}

$blog_root_category = get_category_by_slug('blog');
$blog_child_categories = $blog_root_category ? get_categories(array(
    'parent' => $blog_root_category->cat_ID,
    'meta_key' => 'katakana',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'hide_empty' => true,
)) : array();
$selected_blog_categories = array();

foreach ($blog_child_categories as $child_category) {
    if (in_array($child_category->slug, $requested_blog_categories, true)) {
        $selected_blog_categories[] = $child_category;
    }
}

$blog_categories = wp_list_pluck($selected_blog_categories, 'slug');
$selected_blog_category_names = wp_list_pluck($selected_blog_categories, 'name');
$selected_blog_category_label = count($selected_blog_category_names) > 1
    ? count($selected_blog_category_names).'件選択'
    : ($selected_blog_category_names ? $selected_blog_category_names[0] : 'すべて');

$blog_archive_months = array();
if ($blog_root_category) {
    $archive_post_ids = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'category__and' => array($blog_root_category->term_id),
        'orderby' => 'post_date',
        'order' => 'DESC',
        'fields' => 'ids',
        'no_found_rows' => true,
    ));

    foreach ($archive_post_ids as $archive_post_id) {
        $month_value = get_post_time('Ym', false, $archive_post_id);
        if (!isset($blog_archive_months[$month_value])) {
            $blog_archive_months[$month_value] = get_post_time('Y年n月', false, $archive_post_id);
        }
    }
}

$is_blog_search = '' !== $blog_search_keyword || !empty($blog_categories) || '' !== $blog_month;
// if (wp_is_mobile()) {
//     $posts_per_page = 6;
// } else {
//     $posts_per_page = 8;
// }
?>
<div id="app_func" class="app_func app_func_page func_key_page">
	<main>
		<div id="app_func_screen" class="app_func_screen app_func_screen_blog screen_key_blog">
			<div class="widget_type_page_header screen_widget_key_header_blog">
				<?php get_template_part( 'template-parts/header/blog', 'header' ); ?>
			</div>
            <section class="pb-5">
                <div class="widget_type_blog-list screen_widget_key_blog-list pt-5">
                    <div class="widget_content container">
                        <div class="d-lg-flex justify-content-between align-items-start">
                            <?php
                                $latest_id = 0;
                                $latest_post_args = array(
                                    'post_type' => 'post',
                                    'posts_per_page' => 1,
                                    'post_status'    => 'publish',
                                    'category__and'  => array(get_category_by_slug('blog')->term_id),
                                    'orderby' => 'post_date',
                                    'order' => 'desc',
                                    'date_query' => array(
                                        array(
                                            'before' => 'now',
                                            'after' => '1 month ago',
                                            'inclusive' => true,
                                        ),
                                    ),
                                );
                                $latest_posts = get_posts($latest_post_args);
                                $latest_id = $latest_posts ? $latest_posts[0]->ID : 0;
                                if (!$is_blog_search && $latest_posts) : setup_postdata($latest_posts[0]);
                                    $title = strip_tags(get_the_title($latest_id));
                                    $limit = 30;
                                    if(mb_strlen($title) > $limit) { 
                                        $title = mb_substr($title, 0, $limit).'…';
                                    }

                                    $contributor = strip_tags(get_post_meta( $latest_id, 'blog-contributor', true ));
                                    $blog_summary = get_post_meta( $latest_id, 'blog-summary', true );
                                    $latest_category_name = '';
                                    $latest_category_fallback = '';
                                    $latest_categories = get_the_category($latest_id);

                                    if ($blog_root_category && $latest_categories) {
                                        foreach ($latest_categories as $latest_category) {
                                            if ($latest_category->term_id !== $blog_root_category->term_id && cat_is_ancestor_of($blog_root_category, $latest_category)) {
                                                if ('blog-mikiprune' === $latest_category->slug) {
                                                    $latest_category_fallback = $latest_category->name;
                                                    continue;
                                                }

                                                $latest_category_name = $latest_category->name;
                                                break;
                                            }
                                        }

                                        if ('' === $latest_category_name) {
                                            $latest_category_name = $latest_category_fallback;
                                        }
                                    }
                            ?>
                            <div class="latest_column px-3 px-sm-4 py-3">
                                <a href="<?php the_permalink($latest_id); ?>" onclick="gtag('event', 'link_click', {'event_category':'link_click', 'event_label':'ブログ 最新記事', 'value': '1'})">
                                    <article class="slide_item">
                                        <div class="d-lg-flex">
                                            <div class="slide_item__img bg-white">
                                                <?php if (has_post_thumbnail($latest_id)) : ?>
                                                    <img src="<?php echo get_the_post_thumbnail_url($latest_id, 'full'); ?>" alt="<?php echo $title; ?>">
                                                <?php else: ?>
                                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/common/thumbnail-image.jpg" alt="<?php echo $title; ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="slide_item__about position-relative">
                                                <div class="good position-absolute d-flex align-items-center mb-0">
                                                    <img src="<?php echo get_stylesheet_directory_uri();?>/assets/image/common/icon/good_icon.png">
                                                    <span><?php echo get_post_meta( $latest_id, 'ratings_score', true ); ?></span>
                                                </div>
                                                <div class="post-date">
                                                    <span class="new py-1 me-2">NEW</span><time datetime="<?php echo get_the_time('Y-m-d', $latest_id); ?>"><?php echo get_the_time('Y.m.d', $latest_id); ?></time>
                                                </div>
                                                <div class="slide_item__about__name">
                                                    <h3><?php echo $title; ?></h3>
                                                    <?php if ($blog_summary && mb_strlen(strip_tags($blog_summary)) > 60) {
                                                        echo '<p class="mb-0">'.mb_substr(strip_tags($blog_summary), 0, 60).'…</p>';
                                                    } elseif ($blog_summary) {
                                                        echo '<p class="mb-0">'.$blog_summary.'</p>';
                                                    } elseif (mb_strlen(strip_tags(get_the_content())) > 60) {
                                                        echo '<p class="mb-0">'.mb_substr(strip_tags(get_the_content()), 0, 60).'…</p>';
                                                    } else {
                                                        echo '<p class="mb-0">'.mb_substr(strip_tags(get_the_content())).'</p>';
                                                    }
                                                    ?>
                                                </div>
                                                <?php if ($latest_category_name) : ?>
                                                <div class="post-category">
                                                    <span><?php echo esc_html($latest_category_name); ?></span>
                                                </div>
                                                <?php endif; ?>
                                                <?php if ($contributor) : ?>
                                                <div class="post-note">
                                                    <p class="post-contributor mb-0"><small>投稿者：<?php echo $contributor; ?></small></p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </article>
                                </a>
                            </div>
                            <?php endif; wp_reset_postdata(); ?>
                        </div>
                        <?php
                            $post_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => $is_blog_search ? $search_posts_per_page : $posts_per_page,
                                'paged' => $is_blog_search ? $blog_page : 1,
                                'post_status'    => 'publish',
                                'tax_query'      => array(
                                    array(
                                        'taxonomy'         => 'category',
                                        'field'            => 'term_id',
                                        'terms'            => array($blog_root_category->term_id),
                                        'operator'         => 'IN',
                                        'include_children' => false,
                                    ),
                                ),
                                'orderby' => 'post_date',
                                'order' => 'desc',
                            );
                            if ($is_blog_search) {
                                if ($latest_id) {
                                    $post_args['post__not_in'] = array($latest_id);
                                }
                                if ('' !== $blog_search_keyword) {
                                    $post_args['s'] = $blog_search_keyword;
                                }
                                if ($selected_blog_categories) {
                                    $post_args['tax_query']['relation'] = 'AND';
                                    $post_args['tax_query'][] = array(
                                        'taxonomy'         => 'category',
                                        'field'            => 'term_id',
                                        'terms'            => wp_list_pluck($selected_blog_categories, 'term_id'),
                                        'operator'         => 'IN',
                                        'include_children' => false,
                                    );
                                }
                                if ('' !== $blog_month) {
                                    $post_args['date_query'] = array(
                                        array(
                                            'year' => (int) substr($blog_month, 0, 4),
                                            'month' => (int) substr($blog_month, 4, 2),
                                        ),
                                    );
                                }
                            } else {
                                $post_args['offset'] = 1;
                                $post_args['date_query'] = array(
                                    array(
                                        'before' => 'now',
                                        'after' => '1 year ago',
                                        'inclusive' => true,
                                    ),
                                );
                            }
                            $blog_posts_query = new WP_Query($post_args);
                            $posts = $blog_posts_query->posts;
                            $result_total_count = $is_blog_search ? (int) $blog_posts_query->found_posts : count($posts);
                            $result_display_count = count($posts);
                            $search_condition_labels = array();

                            if ('' !== $blog_keyword_label) {
                                $search_condition_labels[] = 'キーワード：'.$blog_keyword_label;
                            }
                            if ($selected_blog_category_names) {
                                $search_condition_labels[] = 'カテゴリー：'.implode('、', $selected_blog_category_names);
                            }
                            if ('' !== $blog_month) {
                                $search_condition_labels[] = '年月：'.($blog_archive_months[$blog_month] ?? substr($blog_month, 0, 4).'年'.(int) substr($blog_month, 4, 2).'月');
                            }
                        ?>
                        <div class="blog-list-heading<?php if ($is_blog_search) echo ' is-search-result'; ?>">
                            <h2><?php echo $is_blog_search ? '検索結果' : '記事一覧'; ?></h2>
                            <?php if ($is_blog_search) : ?>
                                <div class="blog-result-summary" aria-live="polite">
                                    <p class="blog-result-summary__count mb-0">
                                        <span><?php echo esc_html($result_total_count); ?>件中</span>
                                        <span aria-hidden="true">/</span>
                                        <strong><?php echo esc_html($result_display_count); ?>件</strong>
                                    </p>
                                    <div class="blog-result-summary__conditions">
                                        <span class="blog-result-summary__label">検索条件</span>
                                        <ul class="blog-result-summary__list mb-0">
                                            <?php foreach ($search_condition_labels as $condition_label) : ?>
                                                <li><?php echo esc_html($condition_label); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row pt-5">
                            <aside class="col-12 col-lg-4 order-1 order-lg-2 blog-search-column">
                                <section class="blog-search<?php if ($is_blog_search) echo ' is-open'; ?>" aria-label="ブログ記事検索">
                                    <div class="blog-search__panel">
                                        <h3 class="blog-search__heading blog-search__heading--desktop">
                                            <span class="blog-search__dot" aria-hidden="true"></span>
                                            <span>ブログ記事を探す</span>
                                        </h3>
                                        <button class="blog-search__toggle" type="button" aria-expanded="<?php echo $is_blog_search ? 'true' : 'false'; ?>" aria-controls="blog-search-form">
                                            <span class="blog-search__dot" aria-hidden="true"></span>
                                            <span>ブログ記事を探す</span>
                                            <svg class="blog-search__chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" aria-hidden="true" focusable="false">
                                                <path d="M1 1.25 6 6.25l5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                                            </svg>
                                        </button>
                                        <form id="blog-search-form" class="blog-search__form" method="get" action="<?php echo esc_url($blog_page_permalink); ?>">
                                            <label class="screen-reader-text" for="blog-keyword">ブログ記事のキーワード</label>
                                            <div class="blog-search__input-wrap">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                                    <circle cx="8.25" cy="8.25" r="5.75" fill="none" stroke="currentColor" stroke-width="1.6"/>
                                                    <path d="m12.5 12.5 4.5 4.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.6"/>
                                                </svg>
                                                <input id="blog-keyword" name="blog_keyword" type="search" value="<?php echo esc_attr($blog_keyword); ?>" placeholder="例：プルーン、レシピ、食育、宇宙">
                                            </div>
                                            <button class="blog-search__submit" type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                                    <circle cx="8.25" cy="8.25" r="5.75" fill="none" stroke="currentColor" stroke-width="1.8"/>
                                                    <path d="m12.5 12.5 4.5 4.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"/>
                                                </svg>
                                                <span>検索する</span>
                                            </button>

                                            <fieldset class="blog-search__filter blog-search__categories">
                                                <legend class="screen-reader-text">カテゴリー</legend>
                                                <div class="blog-search__filter-heading">
                                                    <span>カテゴリー</span>
                                                    <span class="blog-search__selected"><?php echo esc_html($selected_blog_category_label); ?></span>
                                                    <span class="blog-search__filter-arrow" aria-hidden="true"></span>
                                                </div>
                                                <div class="blog-search__choices">
                                                    <label class="blog-search__choice blog-search__choice--all<?php if (!$blog_categories) echo ' is-active'; ?>">
                                                        <input type="checkbox" value="" data-all-categories <?php checked(empty($blog_categories)); ?>>
                                                        <span>すべて</span>
                                                    </label>
                                                    <?php foreach ($blog_child_categories as $index => $child_category) : ?>
                                                        <label class="blog-search__choice<?php if (in_array($child_category->slug, $blog_categories, true)) echo ' is-active'; ?><?php if ($index >= 5) echo ' blog-search__choice--extra'; ?>">
                                                            <input type="checkbox" name="blog_category[]" value="<?php echo esc_attr($child_category->slug); ?>" <?php checked(in_array($child_category->slug, $blog_categories, true)); ?>>
                                                            <span><?php echo esc_html($child_category->name); ?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php if (count($blog_child_categories) > 5) : ?>
                                                    <button class="blog-search__show-categories" type="button" aria-expanded="false">
                                                        <span>カテゴリーを全て見る</span><b aria-hidden="true">＋</b>
                                                    </button>
                                                <?php endif; ?>
                                            </fieldset>

                                            <label class="blog-search__filter blog-search__month" for="blog-month">
                                                <span class="blog-search__filter-label">年月</span>
                                                <select id="blog-month" name="blog_month">
                                                    <option value="">すべての年月</option>
                                                    <?php foreach ($blog_archive_months as $month_value => $month_label) : ?>
                                                        <option value="<?php echo esc_attr($month_value); ?>" <?php selected($blog_month, $month_value); ?>><?php echo esc_html($month_label); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </label>

                                            <a class="blog-search__reset" href="<?php echo esc_url($blog_page_permalink); ?>#blog-search-results">
                                                <svg class="blog-search__reset-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                                    <path d="M4.3 6.1A6.5 6.5 0 1 1 3.2 12" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.6"/>
                                                    <path d="M4.3 2.7v3.4H.9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
                                                </svg>
                                                <span class="blog-search__reset-text">検索条件をリセット</span>
                                            </a>
                                        </form>
                                    </div>
                                </section>
                            </aside>
                            <div class="col-12 col-lg-8 order-2 order-lg-1 blog-results-column">
                                <?php if ($posts) : ?>
                                    <span id="blog-search-results" class="blog-search-results__anchor" aria-hidden="true"></span>
                                    <div id="js_late_posts" class="pickup_columns blog-search-results">
                                        <?php
                                            foreach($posts as $index => $post): setup_postdata($post);
                                                get_template_part('template-parts/content/post/post', 'column'); //商品一覧ページ下のブログと共通
                                            endforeach;
                                        ?>
                                        <?php if (!$is_blog_search && count($posts) == $posts_per_page) : ?>
                                        <div class="more_disp btn__blog anime text-center w-100">
                                            <button data-post="blog" class="anime-scroll red-flame_btn rounded d-flex justify-content-between align-items-center mx-auto">
                                                <span class="pe-4">もっと見る</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
                                                    <defs>
                                                        <style>.a{fill:#c1454a;}.b{fill:none;stroke:#fff;stroke-width:3px;}</style>
                                                    </defs>
                                                    <g transform="translate(-0.443 -0.04)">
                                                        <circle class="a" cx="10.5" cy="10.5" r="10.5" transform="translate(0.443 0.039)"/>
                                                        <line class="b" x2="12.12" transform="translate(4.557 10.764)"/>
                                                        <line class="b" x2="12.12" transform="translate(10.617 4.704) rotate(90)"/>
                                                    </g>
                                                </svg>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($is_blog_search && $blog_posts_query->max_num_pages > 1) : ?>
                                        <?php
                                            $pagination_add_args = array();
                                            if ('' !== $blog_keyword) {
                                                $pagination_add_args['blog_keyword'] = $blog_keyword;
                                            }
                                            if ($blog_categories) {
                                                $pagination_add_args['blog_category'] = $blog_categories;
                                            }
                                            if ('' !== $blog_month) {
                                                $pagination_add_args['blog_month'] = $blog_month;
                                            }
                                        ?>
                                        <nav class="blog-pagination" aria-label="検索結果ページ">
                                            <?php
                                                echo paginate_links(array(
                                                    'base' => trailingslashit($blog_page_permalink).'%_%',
                                                    'format' => '?blog_page=%#%',
                                                    'current' => $blog_page,
                                                    'total' => $blog_posts_query->max_num_pages,
                                                    'type' => 'list',
                                                    'prev_text' => '前へ',
                                                    'next_text' => '次へ',
                                                    'add_args' => $pagination_add_args,
                                                    'add_fragment' => '#blog-search-results',
                                                ));
                                            ?>
                                        </nav>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <div id="blog-search-results" class="blog-search-empty">
                                        <p>該当するブログ記事はありません。検索条件を変えてお試しください。</p>
                                    </div>
                                <?php endif; wp_reset_postdata(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?php if (!$is_blog_search) : ?>
            <section class="pt-5">
                <div class="widget_type_page_title screen_widget_key_page_title mb-3">
                    <div class="widget_content container">
                        <div class="widget_header anime">
                            <h2 class="anime-scroll">PICK UP<span class="d-block">おすすめ記事</span></h2>
                        </div>
                    </div>
                </div>
                <?php
                    $post_args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 8,
                        'post_status'    => 'publish',
                        'category_name'  => 'recommended',
                        'orderby' => 'post_date',
                        'order' => 'desc',
                    );
                    $posts = get_posts($post_args);
                ?>
                <div class="widget_type_blog-list screen_widget_key_blog-list py-5">
                    <?php if ($posts) : ?>
                        <div class="widget_content container">
                            <div id="js_recommended_posts" class="pickup_columns pt-0">
                                <?php
                                    foreach($posts as $post): setup_postdata($post);
                                        get_template_part('template-parts/content/post/post', 'column');
                                    endforeach;
                                ?>
                                <?php if (count($posts) == 8) : ?>
                                    <div class="more_disp btn__recommended anime text-center w-100">
                                        <button data-post="recommended" class="anime-scroll red-flame_btn rounded d-flex justify-content-between align-items-center mx-auto">
                                            <span class="pe-4">もっと見る</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
                                                <defs>
                                                    <style>.a{fill:#c1454a;}.b{fill:none;stroke:#fff;stroke-width:3px;}</style>
                                                </defs>
                                                <g transform="translate(-0.443 -0.04)">
                                                    <circle class="a" cx="10.5" cy="10.5" r="10.5" transform="translate(0.443 0.039)"/>
                                                    <line class="b" x2="12.12" transform="translate(4.557 10.764)"/>
                                                    <line class="b" x2="12.12" transform="translate(10.617 4.704) rotate(90)"/>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="widget_content container">
                            <p class="mb-0">おすすめ記事はございません。</p>
                        </div>
                    <?php endif; wp_reset_postdata(); ?>
                </div>
            </section>
            <!-- <section class="blog-cat-list pb-5">
                <?php get_template_part('template-parts/content/content', 'blog-cats'); // カテゴリー一覧 ?>
            </section> -->
            <section>
                <div class="widget_type_page_title screen_widget_key_page_title py-5">
                    <div class="widget_content container">
                        <div class="widget_header anime">
                            <h2 class="anime-scroll">ABOUT<span class="d-block">隣のミキさん</span></h2>
                        </div>
                        <div class="profile-about mt-4">
                            <?php echo $blog_about; ?>
                        </div>
                    </div>
                </div>
                <section class="widget_type_blog_profile screen_widget_key_blog_profile pb-5">
                    <div class="widget_content container">
                        <h3 class="profile-title mb-4">投稿者のご紹介</h3>
                        <div class="container-fluid">
                            <ul class="row list-unstyled">
                                <?php
                                    $fields = CFS()->get('post_user_list');
                                    foreach ($fields as $field) :
                                        $user_img = $field['user_img'];
                                        $user_name = $field['user_name'];
                                        $user_about = $field['user_about'];
                                ?>
                                    <li class="col-6 col-lg-3 mb-4">
                                        <?php if ($user_img) : ?>
                                            <div class="profile-img text-center mb-3">
                                                <img src="<?php echo $field['user_img']; ?>" alt="<?php echo $user_name; ?>"/>
                                            </div>
                                        <?php endif; ?>
                                        <p class="fw-bold mb-0 text-md-center text-sm-start"><?php echo $user_name ?></p>
                                        <?php if ($user_about) : ?>
                                            <p class="profile-note mb-0"><?php echo $user_about; ?></p>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </section>
            </section>
            <?php endif; ?>
        </div>
	</main><!-- #main -->
</div><!-- #primary -->

<script>
(function () {
    var search = document.querySelector('.screen_key_blog .blog-search');
    if (!search) return;

    var toggle = search.querySelector('.blog-search__toggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            var isOpen = search.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    var categoryToggle = search.querySelector('.blog-search__show-categories');
    if (categoryToggle) {
        categoryToggle.addEventListener('click', function () {
            var isExpanded = search.classList.toggle('show-all-categories');
            categoryToggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
            categoryToggle.querySelector('span').textContent = isExpanded ? 'カテゴリーを閉じる' : 'カテゴリーを全て見る';
            categoryToggle.querySelector('b').textContent = isExpanded ? '−' : '＋';
        });
    }

    var selectedCategory = search.querySelector('.blog-search__selected');
    var allCategory = search.querySelector('[data-all-categories]');
    var categoryCheckboxes = Array.prototype.slice.call(search.querySelectorAll('input[name="blog_category[]"]'));

    function syncCategoryState() {
        var checkedCategories = categoryCheckboxes.filter(function (checkbox) {
            return checkbox.checked;
        });

        allCategory.checked = checkedCategories.length === 0;
        allCategory.closest('.blog-search__choice').classList.toggle('is-active', allCategory.checked);
        categoryCheckboxes.forEach(function (checkbox) {
            checkbox.closest('.blog-search__choice').classList.toggle('is-active', checkbox.checked);
        });

        if (checkedCategories.length === 0) {
            selectedCategory.textContent = 'すべて';
        } else if (checkedCategories.length === 1) {
            selectedCategory.textContent = checkedCategories[0].nextElementSibling.textContent;
        } else {
            selectedCategory.textContent = checkedCategories.length + '件選択';
        }
    }

    allCategory.addEventListener('change', function () {
        if (allCategory.checked) {
            categoryCheckboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
        }
        syncCategoryState();
    });

    categoryCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (checkbox.checked) {
                allCategory.checked = false;
            }
            syncCategoryState();
        });
    });
})();
</script>

<?php
get_footer();
