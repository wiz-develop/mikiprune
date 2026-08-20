<?php
    $title = strip_tags(get_the_title());
    $limit = 30;
    if(mb_strlen($title) > $limit) {
        $title = mb_substr($title, 0, $limit).'…';
    }

    $contributor = strip_tags(get_post_meta(get_the_ID(), 'blog-contributor', true));
    $card_category_name = '';
    $blog_root_category = get_category_by_slug('blog');
    $post_categories = get_the_category();

    if ($blog_root_category && $post_categories) {
        foreach ($post_categories as $post_category) {
            if ($post_category->term_id !== $blog_root_category->term_id && cat_is_ancestor_of($blog_root_category, $post_category)) {
                $card_category_name = $post_category->name;
                break;
            }
        }
    }
?>
<a href="<?php the_permalink(); ?>">
    <article class="slide_item position-relative">
        <div class="good bg-white position-absolute d-flex align-items-center pr-2 mb-0">
            <img src="<?php echo get_stylesheet_directory_uri();?>/assets/image/common/icon/good_icon.png" alt="">
            <span class="good-label">いいね！</span>
            <span class="good-count"><?php echo get_post_meta(get_the_ID(), 'ratings_score', true); ?></span>
        </div>
        <div class="slide_item__img">
            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>" alt="<?php echo $title; ?>">
            <?php else: ?>
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/common/thumbnail-image.jpg" alt="<?php echo $title; ?>">
            <?php endif; ?>
        </div>
        <div class="slide_item__about">
            <div class="post-date">
                <time datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y.m.d'); ?></time>
            </div>
            <div class="slide_item__about__name">
                <h3 class="mb-0"><?php echo $title; ?></h3>
            </div>
            <?php if ($card_category_name) : ?>
                <div class="post-category">
                    <span><?php echo esc_html($card_category_name); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($contributor) : ?>
                <div class="post-note">
                    <p class="post-contributor mb-0"><small>投稿者：<?php echo $contributor; ?></small></p>
                </div>
            <?php endif; ?>
        </div>
    </article>
</a>
