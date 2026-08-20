
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
$cat_num = 4;
$posts_per_page = 6;
$blog_keyword = isset($_GET['blog_keyword']) && is_string($_GET['blog_keyword'])
    ? sanitize_text_field(wp_unslash($_GET['blog_keyword']))
    : '';
$is_blog_search = '' !== $blog_keyword;
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
                            <section class="blog_top_cat mt-0 mt-4">
                                <div class="widget_type_archive_cat screen_widget_key_archive_cat_list">
                                    <div class="cat-list">
                                        <ul class="d-flex align-items-center flex-wrap mb-0">
                                            <?php
                                                $cat_blog = get_category_by_slug("blog");
                                                $child_cat_args = array(
                                                    'parent' => $cat_blog->cat_ID,
                                                    'meta_key' => 'katakana',
                                                    'orderby' => 'meta_value',
                                                    'order' => 'ASC',
                                                );
                                                $child_cats = get_categories($child_cat_args);
                                                foreach ($child_cats as $index => $child_cat) :
                                            ?>
                                                <li class="cat-item mb-2 <?php if($index > $cat_num) echo 'toggle-cat d-none'; ?>">
                                                    <a href="/<?php echo $child_cat->slug; ?>/">
                                                        <div class="link_item">
                                                            <div class="link_name w-100">
                                                                <div class="link_name_detail">
                                                                    <p class="mb-0"><?php echo $child_cat->name; ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <button id="toggle-cat-btn" class="anime-scroll d-flex justify-content-between align-items-center py-2 ps-2 pe-0">
                                            <span class="toggle-cat-btn-text pe-2">カテゴリーを全て見る</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
                                                <defs>
                                                    <style>.a{fill:#c1454a;}.b{fill:none;stroke:#fff;stroke-width:3px;}</style>
                                                </defs>
                                                <g transform="translate(-0.443 -0.04)">
                                                    <circle class="a" cx="10.5" cy="10.5" r="10.5" transform="translate(0.443 0.039)"></circle>
                                                    <line class="b" x2="12.12" transform="translate(4.557 10.764)"></line>
                                                    <line class="b" x2="12.12" transform="translate(10.617 4.704) rotate(90)"></line>
                                                </g>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <?php
                            $post_args = array(
                                'post_type' => 'post',
                                'posts_per_page' => $is_blog_search ? -1 : $posts_per_page,
                                'post_status'    => 'publish',
                                'category__and'  => array(get_category_by_slug('blog')->term_id),
                                'orderby' => 'post_date',
                                'order' => 'desc',
                            );
                            if ($is_blog_search) {
                                $post_args['s'] = $blog_keyword;
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
                            <div class="col-12 col-lg-8">
                                <section class="blog-search" aria-label="ブログ記事検索">
                                    <details class="blog-search__panel" <?php if ($is_blog_search) echo 'open'; ?>>
                                        <summary class="blog-search__summary">
                                            <span class="blog-search__dot" aria-hidden="true"></span>
                                            <span>ブログ記事を探す</span>
                                            <svg class="blog-search__chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" aria-hidden="true" focusable="false">
                                                <path d="M1 1.25 6 6.25l5-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"/>
                                            </svg>
                                        </summary>
                                        <form class="blog-search__form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                                            <label class="screen-reader-text" for="blog-keyword">ブログ記事のキーワード</label>
                                            <div class="blog-search__controls">
                                                <div class="blog-search__input-wrap">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false">
                                                        <circle cx="8.25" cy="8.25" r="5.75" fill="none" stroke="currentColor" stroke-width="1.6"/>
                                                        <path d="m12.5 12.5 4.5 4.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.6"/>
                                                    </svg>
                                                    <input id="blog-keyword" name="blog_keyword" type="search" value="<?php echo esc_attr($blog_keyword); ?>" placeholder="例：プルーン、レシピ、食育、宇宙">
                                                </div>
                                                <button type="submit">検索</button>
                                            </div>
                                            <?php if ($is_blog_search) : ?>
                                                <div class="blog-search__status" aria-live="polite">
                                                    <p class="mb-0">「<?php echo esc_html($blog_keyword); ?>」の検索結果：<?php echo count($posts); ?>件</p>
                                                    <a href="<?php echo esc_url(get_permalink()); ?>#blog-search-results">検索条件をクリア</a>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    </details>
                                </section>
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
                                        <p>該当するブログ記事はありません。キーワードを変えてお試しください。</p>
                                    </div>
                                <?php endif; wp_reset_postdata(); ?>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="widget_side">
                                    <div class="widget_content">
                                        <div class="widget_body">
                                            <?php get_template_part('template-parts/side/sidebar-blog') ?>
                                        </div>
                                    </div>
                                </div>
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

<?php
get_footer();
