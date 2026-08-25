<?php
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME'])[0];
require_once $parse_uri.'wp-load.php';

$requested_now_post_num = isset($_POST['now_post_num']) && is_scalar($_POST['now_post_num'])
    ? (string) wp_unslash($_POST['now_post_num'])
    : '';
$now_post_num = preg_match('/^\d+$/', $requested_now_post_num)
    ? (int) $requested_now_post_num
    : 0;
$requested_get_post_num = isset($_POST['get_post_num']) && is_scalar($_POST['get_post_num'])
    ? (string) wp_unslash($_POST['get_post_num'])
    : '';
$get_post_num = preg_match('/^[1-9]\d*$/', $requested_get_post_num)
    ? (int) $requested_get_post_num
    : 6;
$get_post_num = max(1, min(12, $get_post_num));
$post_cat_data = isset($_POST['post_cat']) && is_string($_POST['post_cat'])
    ? sanitize_key(wp_unslash($_POST['post_cat']))
    : '';

if (!in_array($post_cat_data, array('blog', 'recommended', 'good'), true)) {
    status_header(400);
    exit;
}

$post_cat = $post_cat_data;
$post_class = $post_cat_data;
$args_add = array();

if ('good' === $post_cat_data) {
    $post_cat = 'blog';
    $args_add = array(
        'meta_key' => 'ratings_score',
        'orderby' => 'meta_value_num',
    );
}

$post_args = array(
    'post_type' => 'post',
    'posts_per_page' => $get_post_num,
    'offset' => $now_post_num,
    'post_status' => 'publish',
    'category_name' => $post_cat,
    'orderby' => 'date',
    'order' => 'DESC',
);
$posts = new WP_Query(array_merge($post_args, $args_add));
$html = '';

if ($posts->have_posts()) {
    while ($posts->have_posts()) {
        $posts->the_post();
        ob_start();
        get_template_part('template-parts/content/post/post', 'column');
        $html .= ob_get_clean();
    }
}

$has_more = $now_post_num + (int) $posts->post_count < (int) $posts->found_posts;
wp_reset_postdata();

if (!$has_more) {
    $html .= '<style>.more_disp.btn__'.esc_attr($post_class).'{display:none}</style>';
}

echo $html;
