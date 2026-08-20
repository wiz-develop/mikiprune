
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
$posts_per_page = 6;
$blog_keyword = isset($_GET['blog_keyword']) && is_string($_GET['blog_keyword'])
    ? sanitize_text_field(wp_unslash($_GET['blog_keyword']))
    : '';
$blog_category = isset($_GET['blog_category']) && is_string($_GET['blog_category'])
    ? sanitize_title(wp_unslash($_GET['blog_category']))
    : '';
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
$selected_blog_category = null;

foreach ($blog_child_categories as $child_category) {
    if ($blog_category === $child_category->slug) {
        $selected_blog_category = $child_category;
        break;
    }
}

if (!$selected_blog_category) {
    $blog_category = '';
}

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

$is_blog_search = '' !== $blog_keyword || '' !== $blog_category || '' !== $blog_month;
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
                                if ($latest_posts) : setup_postdata($latest_posts[0]);
                                    $latest_id = $latest_posts[0]->ID;
                                    $title = strip_tags(get_the_title($latest_id));
                                    $limit = 30;
                                    if(mb_strlen($title) > $limit) { 
                                        $title = mb_substr($title, 0, $limit).'…';
                                    }

                                    $contributor = strip_tags(get_post_meta( $latest_id, 'blog-contributor', true ));
                                    $blog_summary = get_post_meta( $latest_id, 'blog-summary', true );
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
                                'posts_per_page' => $is_blog_search ? -1 : $posts_per_page,
                                'post_status'    => 'publish',
                                'category__and'  => array($blog_root_category->term_id),
                                'orderby' => 'post_date',
                                'order' => 'desc',
                            );
                            if ($is_blog_search) {
                                if ('' !== $blog_keyword) {
                                    $post_args['s'] = $blog_keyword;
                                }
                                if ($selected_blog_category) {
                                    $post_args['category__and'][] = $selected_blog_category->term_id;
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
                            $posts = get_posts($post_args);
                        ?>
                        <div class="blog-list-heading">
                            <h2>記事一覧</h2>
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
                                        <form id="blog-search-form" class="blog-search__form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
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
                                                    <span class="blog-search__selected"><?php echo $selected_blog_category ? esc_html($selected_blog_category->name) : 'すべて'; ?></span>
                                                    <span class="blog-search__filter-arrow" aria-hidden="true"></span>
                                                </div>
                                                <div class="blog-search__choices">
                                                    <label class="blog-search__choice<?php if ('' === $blog_category) echo ' is-active'; ?>">
                                                        <input type="radio" name="blog_category" value="" <?php checked('', $blog_category); ?>>
                                                        <span>すべて</span>
                                                    </label>
                                                    <?php foreach ($blog_child_categories as $index => $child_category) : ?>
                                                        <label class="blog-search__choice<?php if ($blog_category === $child_category->slug) echo ' is-active'; ?><?php if ($index >= 5) echo ' blog-search__choice--extra'; ?>">
                                                            <input type="radio" name="blog_category" value="<?php echo esc_attr($child_category->slug); ?>" <?php checked($blog_category, $child_category->slug); ?>>
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

                                            <a class="blog-search__reset" href="<?php echo esc_url(get_permalink()); ?>#blog-search-results">
                                                <svg class="blog-search__reset-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                                    <path d="M4.3 6.1A6.5 6.5 0 1 1 3.2 12" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.6"/>
                                                    <path d="M4.3 2.7v3.4H.9" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"/>
                                                </svg>
                                                <span class="blog-search__reset-text">検索条件をリセット</span>
                                            </a>
                                        </form>
                                    </div>
                                    <?php if ($is_blog_search) : ?>
                                        <p class="blog-search__status" aria-live="polite">検索結果：<?php echo count($posts); ?>件</p>
                                    <?php endif; ?>
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
    search.querySelectorAll('input[name="blog_category"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            search.querySelectorAll('.blog-search__choice').forEach(function (choice) {
                choice.classList.remove('is-active');
            });
            radio.closest('.blog-search__choice').classList.add('is-active');
            selectedCategory.textContent = radio.nextElementSibling.textContent;
        });
    });
})();
</script>

<?php
get_footer();
