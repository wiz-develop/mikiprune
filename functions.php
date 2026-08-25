<?php
/**
 * Twenty Nineteen functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

/**
 * Twenty Nineteen only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

if ( ! function_exists( 'twentynineteen_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function twentynineteen_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Twenty Nineteen, use a find and replace
		 * to change 'twentynineteen' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'twentynineteen', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1568, 9999 );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'menu-1' => __( 'Primary', 'twentynineteen' ),
				'footer' => __( 'Footer Menu', 'twentynineteen' ),
				'social' => __( 'Social Links Menu', 'twentynineteen' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 190,
				'width'       => 190,
				'flex-width'  => false,
				'flex-height' => false,
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style-editor.css' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => __( 'Small', 'twentynineteen' ),
					'shortName' => __( 'S', 'twentynineteen' ),
					'size'      => 19.5,
					'slug'      => 'small',
				),
				array(
					'name'      => __( 'Normal', 'twentynineteen' ),
					'shortName' => __( 'M', 'twentynineteen' ),
					'size'      => 22,
					'slug'      => 'normal',
				),
				array(
					'name'      => __( 'Large', 'twentynineteen' ),
					'shortName' => __( 'L', 'twentynineteen' ),
					'size'      => 36.5,
					'slug'      => 'large',
				),
				array(
					'name'      => __( 'Huge', 'twentynineteen' ),
					'shortName' => __( 'XL', 'twentynineteen' ),
					'size'      => 49.5,
					'slug'      => 'huge',
				),
			)
		);

		// Editor color palette.
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => 'default' === get_theme_mod( 'primary_color' ) ? __( 'Blue', 'twentynineteen' ) : null,
					'slug'  => 'primary',
					'color' => twentynineteen_hsl_hex( 'default' === get_theme_mod( 'primary_color' ) ? 199 : get_theme_mod( 'primary_color_hue', 199 ), 100, 33 ),
				),
				array(
					'name'  => 'default' === get_theme_mod( 'primary_color' ) ? __( 'Dark Blue', 'twentynineteen' ) : null,
					'slug'  => 'secondary',
					'color' => twentynineteen_hsl_hex( 'default' === get_theme_mod( 'primary_color' ) ? 199 : get_theme_mod( 'primary_color_hue', 199 ), 100, 23 ),
				),
				array(
					'name'  => __( 'Dark Gray', 'twentynineteen' ),
					'slug'  => 'dark-gray',
					'color' => '#111',
				),
				array(
					'name'  => __( 'Light Gray', 'twentynineteen' ),
					'slug'  => 'light-gray',
					'color' => '#767676',
				),
				array(
					'name'  => __( 'White', 'twentynineteen' ),
					'slug'  => 'white',
					'color' => '#FFF',
				),
			)
		);

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}
endif;
add_action( 'after_setup_theme', 'twentynineteen_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function twentynineteen_widgets_init() {

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'twentynineteen' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Add widgets here to appear in your footer.', 'twentynineteen' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

}
add_action( 'widgets_init', 'twentynineteen_widgets_init' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width Content width.
 */
function twentynineteen_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'twentynineteen_content_width', 640 );
}
add_action( 'after_setup_theme', 'twentynineteen_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function twentynineteen_scripts() {
	wp_enqueue_style( 'twentynineteen-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );

	wp_style_add_data( 'twentynineteen-style', 'rtl', 'replace' );

	if ( has_nav_menu( 'menu-1' ) ) {
		wp_enqueue_script( 'twentynineteen-priority-menu', get_theme_file_uri( '/js/priority-menu.js' ), array(), '20181214', true );
		// wp_enqueue_script( 'twentynineteen-touch-navigation', get_theme_file_uri( '/js/touch-keyboard-navigation.js' ), array(), '20181231', true );
	}

	wp_enqueue_style( 'twentynineteen-print-style', get_template_directory_uri() . '/print.css', array(), wp_get_theme()->get( 'Version' ), 'print' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'twentynineteen_scripts' );

/**
 * Fix skip link focus in IE11.
 *
 * This does not enqueue the script because it is tiny and because it is only for IE11,
 * thus it does not warrant having an entire dedicated blocking script being loaded.
 *
 * @link https://git.io/vWdr2
 */
function twentynineteen_skip_link_focus_fix() {
	// The following is minified via `terser --compress --mangle -- js/skip-link-focus-fix.js`.
	?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'twentynineteen_skip_link_focus_fix' );

// function my_load_widget_scripts() {
//     wp_enqueue_script('jquery-script', get_template_directory_uri() . '/assets/js/jquery-3.6.0.min.js', array());
// 	// wp_enqueue_script('jquery-script', '//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js', array());
//     wp_enqueue_script('bootstrap-script', get_template_directory_uri() . '/assets/js/bootstrap.js', array());
// 	wp_enqueue_script('slick-script', get_template_directory_uri() . '/assets/js/slick.min.js', array());
// 	wp_enqueue_script('common-script', get_template_directory_uri() . '/assets/js/common.js', array());
// }

// add_action('wp_footer', 'my_load_widget_scripts');

// function my_addasync_jquery_script_enqueue_script( $tag, $jquery_script ) {
//     if ( 'jquery-script' !== $jquery_script ) { return $tag; }
//     return str_replace( ' src', ' async="async" src', $tag );
// }
// add_filter( 'script_loader_tag', 'my_addasync_jquery_script_enqueue_script', 10, 2 );

// function my_addasync_bootstrap_script_enqueue_script( $tag, $bootstrap_script ) {
//     if ( 'bootstrap-script' !== $bootstrap_script ) { return $tag; }
//     return str_replace( ' src', ' async="async" src', $tag );
// }
// add_filter( 'script_loader_tag', 'my_addasync_bootstrap_script_enqueue_script', 10, 2 );

// function my_is_mobile(){
//     $pattern = '/iPhone|iPod|Android.*Mobile/i';
//     return preg_match( $pattern, $_SERVER['HTTP_USER_AGENT'] );
// }

/**
 * Enqueue supplemental block editor styles.
 */
function twentynineteen_editor_customizer_styles() {

	wp_enqueue_style( 'twentynineteen-editor-customizer-styles', get_theme_file_uri( '/style-editor-customizer.css' ), false, '1.1', 'all' );

	if ( 'custom' === get_theme_mod( 'primary_color' ) ) {
		// Include color patterns.
		require_once get_parent_theme_file_path( '/inc/color-patterns.php' );
		wp_add_inline_style( 'twentynineteen-editor-customizer-styles', twentynineteen_custom_colors_css() );
	}
}
add_action( 'enqueue_block_editor_assets', 'twentynineteen_editor_customizer_styles' );

/**
 * Display custom color CSS in customizer and on frontend.
 */
function twentynineteen_colors_css_wrap() {

	// Only include custom colors in customizer or frontend.
	if ( ( ! is_customize_preview() && 'default' === get_theme_mod( 'primary_color', 'default' ) ) || is_admin() ) {
		return;
	}

	require_once get_parent_theme_file_path( '/inc/color-patterns.php' );

	$primary_color = 199;
	if ( 'default' !== get_theme_mod( 'primary_color', 'default' ) ) {
		$primary_color = get_theme_mod( 'primary_color_hue', 199 );
	}
	?>

	<style type="text/css" id="custom-theme-colors" <?php echo is_customize_preview() ? 'data-hue="' . absint( $primary_color ) . '"' : ''; ?>>
		<?php echo twentynineteen_custom_colors_css(); ?>
	</style>
	<?php
}
add_action( 'wp_head', 'twentynineteen_colors_css_wrap' );

/**
 * SVG Icons class.
 */
require get_template_directory() . '/classes/class-twentynineteen-svg-icons.php';

/**
 * Custom Comment Walker template.
 */
require get_template_directory() . '/classes/class-twentynineteen-walker-comment.php';

/**
 * Common theme functions.
 */
require get_template_directory() . '/inc/helper-functions.php';

/**
 * SVG Icons related functions.
 */
require get_template_directory() . '/inc/icon-functions.php';

/**
 * Enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom template tags for the theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/*-------------------------------------------*/
/*  ファイルの更新日時を取得
/*-------------------------------------------*/
function update_date($path) {
	return date("ymdHis", filemtime($path));
}

/*-------------------------------------------*/
/*  ヘッダー、フッターでの読み込み
/*-------------------------------------------*/
function add_wp_head_custom(){ ?>
	<!-- head内に書きたいコード -->
	<?php date_default_timezone_set('Asia/Tokyo'); ?>
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/bootstrap.min.css?ver=5.0.2"/>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/slick.css"/>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style.css?ver=<?php echo update_date( get_stylesheet_directory()."/assets/css/style.css"); ?>" media="all" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style_yoshi.css?ver=<?php echo update_date(get_stylesheet_directory()."/assets/css/style_yoshi"); ?>" media="all" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/style_kawa.css?ver=<?php echo update_date(get_stylesheet_directory()."/assets/css/style_kawa"); ?>" media="all" />
	<!-- <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/top/reset.css?ver=<?php //echo update_date(get_stylesheet_directory()."/assets/css/top/reset.css"); ?>" media="all" /> -->
	<!-- <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/top/vegas.min.css?ver=<?php echo update_date(get_stylesheet_directory()."/assets/css/top/vegas.min.css"); ?>" media="all" /> -->
	<!-- <link rel="stylesheet" href="<?php // echo get_stylesheet_directory_uri(); ?>/assets/css/top/6-1-4.css?ver=<?php // echo update_date(get_stylesheet_directory()."/assets/css/top/6-1-4.css"); ?>" media="all" /> -->
	<link href="https://fonts.googleapis.com/css2?family=BIZ+UDPGothic:wght@400;700&family=BIZ+UDPMincho&family=Kosugi&family=Noto+Serif+JP:wght@200;300;400;500;600;700;900&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&display=swap" rel="stylesheet">
	<!-- <link rel="shortcut icon" href="/cms/wp-content/themes/zenkosai/assets/images/common/favicon.png">
	<link rel="apple-touch-icon" href="apple-touch-icon.png" sizes="180×180">
	<link rel="icon" href="apple-touch-icon.png"> -->
<?php }
add_action( 'wp_head', 'add_wp_head_custom',99);

function add_wp_footer_custom(){ ?>
	<!-- footer内に書きたいコード -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<!-- <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/jquery-3.6.0.min.js"></script> -->
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/bootstrap.bundle.js?ver=5.0.2"></script>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/slick.min.js"></script>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/jquery.arctext.js"></script>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/script.js"></script>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/common.js?ver=<?php echo update_date( get_stylesheet_directory()."/assets/js/common.js"); ?>"></script>
	<!-- <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/top/vegas.min.js"></script> -->
<?php }
add_action( 'wp_footer', 'add_wp_footer_custom', 99);

// 管理画面での読み込み
function enqueue_post_styles() {
  global $pagenow;
  if ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'edit.php' ) {
	$postType = get_post_type();
	if ( $postType == "p_navi" ) {
		// 商品ナビ カスタムフィールド(CFS) ループ行表示をカスタマイズ
		wp_enqueue_style(
			'my_admin_style',
			get_stylesheet_directory_uri() . '/assets/css/admin_p_navi_style.css'
		);
		wp_enqueue_script(
			'my_admin_script',
			get_stylesheet_directory_uri() . '/assets/js/admin_p_navi_script.js', array(), '1.0.0', true
    	);
	} else {
		// その他管理画面
		wp_enqueue_script(
			'my_admin_script_other',
			get_stylesheet_directory_uri() . '/assets/js/admin_script.js', array(), '1.0.0', true
    	);
	}
  }
}
add_action( 'admin_enqueue_scripts', 'enqueue_post_styles' );

/*-------------------------------------------*/
/*  common.jsでサイトのURL・テーマURLを使えるようにする
/*-------------------------------------------*/
$tmp_path_arr = array(
	'temp_uri' => get_template_directory_uri(),
	'home_url' => home_url()
);
wp_enqueue_script( 'common', get_template_directory_uri() . '/assets/js/common.js', '', update_date((get_stylesheet_directory_uri()."/assets/js/common.js")), true );
wp_localize_script( 'common', 'tmp_path', $tmp_path_arr );

// 記事の自動整形を無効化
remove_filter('the_content', 'wpautop');

/*-------------------------------------------*/
/*  パンくずリスト
/*-------------------------------------------*/
function breadcrumb() {
	$home = '<li><a href="/">HOME</a></li>';
	if(get_locale() == 'en_US') {
		$products_slug = '/en/products_en';
	} elseif(get_locale() == 'zh_CN') {
		$products_slug = '/zh';
	} else {
		$products_slug = '/products';
	}
	$product_cats = array('food', 'food_en' , 'food_zh' , 'beauty_care', 'household', 'personal_care');
    
    echo '<ul id="breadcrumb" class="d-flex list-unstyled mb-0">';
	if ( is_category() ) {
        // カテゴリページの場合
        $cat = get_queried_object();
        $cat_id = $cat->parent;
        $cat_list = array();
        while ($cat_id != 0){
            $cat = get_category( $cat_id );
            $cat_link = get_category_link( $cat_id );
			if ($cat->slug != 'product') {
				if (in_array($cat->slug, $product_cats)) {
					$page_data = cat_editiong_page($cat->slug);
					array_unshift( $cat_list, '<li><a href="'.$products_slug.'/'.$page_data->post_name.'">'.$page_data->post_title.'</a></li>' );
				} elseif ($cat->slug = 'blog') {
					array_unshift( $cat_list, '<li><a href="/miki-blog/">隣のミキさん</a></li>' );
				} elseif ( $cat->category_count > 0) {
					array_unshift( $cat_list, '<li><a href="'.$cat_link.'">'.$cat->name.'</a></li>' );
				}
			}
			$cat_id = $cat->parent;
        }
        echo $home;
        foreach($cat_list as $value){
            echo $value;
        }
        the_archive_title('<li>', '</li>');
    }
    else if ( is_archive() ) {
    // 月別アーカイブ・タグページの場合
		echo $home;
		the_archive_title('<li>', '</li>');
    }
    else if ( is_single() ) {
		// 投稿ページの場合
		echo $home;
		$cat = get_the_category();

		if (in_category($product_cats)) {
			foreach ($cat as $cat_item) {
				if (in_array($cat_item->slug, $product_cats)) {
					$page_data = cat_editiong_page($cat_item->slug);
					echo '<li><a href="'.$products_slug.'/'.$page_data->post_name.'">'.$page_data->post_title.'</a></li>';
				}
			}
			if (in_category('beauty_care')) {
				foreach ($cat as $cat_item) {
					$parent = get_category($cat_item->category_parent);
					if ($parent->slug === 'beauty_care') {
						$list_page_link = 'beauty_care_list/brand_'.$cat_item->slug;
						echo '<li><a href="/products/'.$list_page_link.'">'.$cat_item->name.'</a></li>';
					}
				}
			}
		} elseif (in_category('blog')) {
			echo '<li><a href="/miki-blog/">隣のミキさん</a></li>';
		} elseif (is_singular('news')) {
			echo '<li><a href="/farm/">ミキプルーン農園だより</a></li>';
		} else {
			if( isset($cat[0]->cat_ID) ) $cat_id = $cat[0]->cat_ID;
			$cat_list = array();
			while ($cat_id != 0){
				$cat = get_category( $cat_id );

				if ($cat->taxonomy == 'category') {
					array_unshift( $cat_list, '<li><a href="/'.$cat->slug.'/">'.$cat->name.'</a></li>' );
				} else {
					$cat_link = get_category_link( $cat_id );
					array_unshift( $cat_list, '<li><a href="'.$cat_link.'">'.$cat->name.'</a></li>' );
				}
				$cat_id = $cat->parent;
			}
			foreach($cat_list as $value){
				echo $value;
			}
		}

		$title = strip_tags(get_the_title());
		$limit = 25;
		if(mb_strlen($title) > $limit) { 
			$title = mb_substr($title, 0, $limit - 1).'…';
		}
		echo '<li>'.$title.'</li>';
    }
    else if( is_page() ) {
		// 固定ページの場合
		echo $home;
		$ancestors_ids = array_reverse(get_post_ancestors( $post ));
		$products_page = get_page_by_path('products');
		$products_page_id = $products_page->ID;
		foreach($ancestors_ids as $ancestors_id){
			if ($products_page_id != $ancestors_id) {
				echo '<li><a href="'.get_page_link( $ancestors_id ).'" >'.get_page($ancestors_id)->post_title.'</a></li>';
			}
		}
		$title = strip_tags(get_the_title());
		$limit = 25;
		if(mb_strlen($title) > $limit) { 
			$title = mb_substr($title, 0, $limit - 1).'…';
		}
		echo '<li>'.$title.'</li>';
    }
    else if( is_search() ) {
		// 検索ページの場合
		echo $home;
		echo '<li>「'.get_search_query().'」の検索結果</li>';
    }
    else if( is_404() ) {
		// 404ページの場合
		echo $home;
		echo '<li>ページが見つかりません</li>';
    }
    echo "</ul>";
}
 
// アーカイブの余計なタイトルを削除
add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_month() ) {
        $title = single_month_title( '', false );
    }
    return $title;
});

/*-------------------------------------------*/
/*  ページがなければトップページへリダイレクト
/*-------------------------------------------*/
add_action( 'template_redirect', 'is404_redirect_home' );
function is404_redirect_home() {
  if ( is_404() ) {
    wp_safe_redirect( home_url( '/' ) );
    exit();
  }
  if ( is_404() ) {
    wp_safe_redirect( home_url( '/' ), 301 );
    exit();
  }
}

/*-------------------------------------------*/
/*  関数
/*-------------------------------------------*/
/*
* カテゴリーに紐づく固定ページの情報を返す
* @param  string $slug カテゴリーのスラッグ
* @return array 固定ページの情報
*/
function cat_editiong_page( $cat_slug ){
	if(get_locale() == 'en_US') {
		$page_slug = 'products_en/';
	} elseif(get_locale() == 'zh_CN') {
		$page_slug = '/';
	} else {
		$page_slug = 'products/';
	}

	if ($cat_slug === 'food') {
		$page_slug .= 'food_list';
	} elseif ($cat_slug === 'beauty_care') {
		$page_slug .= 'beauty_care_list';
	} elseif ($cat_slug === 'household') {
		$page_slug .= 'household_list';
	} elseif ($cat_slug === 'personal_care') {
		$page_slug .= 'personal_care_list';
	} elseif ($cat_slug === 'food_en') {
		$page_slug .= 'food_list_en';
	} elseif ($cat_slug === 'food_zh') {
		$page_slug .= 'food_list_zh';
	}

	$page_data = get_page_by_path($page_slug);
	return $page_data;
}

/*
* 言語ごとに商品カテゴリーの情報を返す
* @return array 商品カテゴリーの情報
*/
function product_cat_data(){
	if(get_locale() == 'en_US') {
		$product_slug = 'product_en';
	} elseif(get_locale() == 'zh_CN') {
		$product_slug = 'product_zh';
	} else {
		$product_slug = 'product';
	}
	return get_category_by_slug($product_slug);
}

/*-------------------------------------------*/
/* ACF カスタムフィールドもプレビューできるようにする
/*-------------------------------------------*/
function get_preview_id($postId) {
    global $post;
    $previewId = 0;
    if ( isset($_GET['preview'])
            && ($post->ID == $postId)
                && $_GET['preview'] == true
                    &&  ($postId == url_to_postid($_SERVER['REQUEST_URI']))
        ) {
        $preview = wp_get_post_autosave($postId);
        if ($preview != false) { $previewId = $preview->ID; }
    }
    return $previewId;
}
 
add_filter('get_post_metadata', function($meta_value, $post_id, $meta_key, $single) {
    if ($preview_id = get_preview_id($post_id)) {
        if ($post_id != $preview_id) {
            $meta_value = get_post_meta($preview_id, $meta_key, $single);
        }
    }
    return $meta_value;
}, 10, 4);
 
add_action('wp_insert_post', function ($postId) {
    global $wpdb;
    if (wp_is_post_revision($postId)) {
        if (count($_POST['fields']) != 0) {
            foreach ($_POST['fields'] as $key => $value) {
                $field = get_field($key);
                if ( !isset($field['name']) || !isset($field['key']) ) continue;
                if (count(get_metadata('post', $postId, $field['name'], $value)) != 0) {
                    update_metadata('post', $postId, $field['name'], $value);
                    update_metadata('post', $postId, "_" . $field['name'], $field['key']);
                } else {
                    add_metadata('post', $postId, $field['name'], $value);
                    add_metadata('post', $postId, "_" . $field['name'], $field['key']);
                }
            }
        }
        do_action('save_preview_postmeta', $postId);
    }
});

/*-------------------------------------------*/
/*	商品ナビ カスタムフィールド(CFS) ループ行表示をカスタマイズ
/*-------------------------------------------*/
function init_session_start(){
  session_start();
}
add_action('init', 'init_session_start');

/*-------------------------------------------*/
/*	販売者ご紹介お申し込みページ
/*-------------------------------------------*/
function send_date_time( $value, $key, $insert_contact_data_id ) {
	if ( $key === 'send_datetime' ) {
			return date_i18n( 'Y年m月d日 H時i分' );
	}
	return $value;
}
add_filter( 'mwform_custom_mail_tag_mw-wp-form-12035', 'send_date_time', 10, 3 );

function my_mwform_inquiry_data_columns( $columns ) {
	$columns = array(
		'contact_reason' => '販売者ご紹介のお申し込みのきっかけ',
		'contact_reason_other_txt' => '※その他のきっかけ',
		'interest_things' => '興味ある商品',
		'interest_things_txt' => '※特に興味のある商品',
		'select_known' => 'ミキの商品の認知',
		'last_name_c' => '姓',
		'first_name_c' => '名',
		'last_name' => 'セイ',
		'first_name' => 'メイ',
		'select_sex' => '性別',
		'select_age' => '年代',
		'when_contact' => '連絡希望時間帯',
		'select_job' => '職業',
		'telephone_num' => '電話番号',
		'zipcode' => '郵便番号',
		'address_area' => '都道府県',
		'address_city' => '市区町村',
		'address_code' => '番地',
		'address_apert' => 'アパート・マンション名',
		'mailaddress' => 'メールアドレス',
	);
	return $columns;
}
add_filter( 'mwform_inquiry_data_columns-mwf_12035', 'my_mwform_inquiry_data_columns' );

function my_mwform_response_statuses( $response_statuses ) {
	$response_statuses = array(
		'not-supported' => esc_html__( 'Not supported', 'mw-wp-form' ),
		'r_s_supported' => esc_html__( 'Supported', 'mw-wp-form' ),
		'r_s_reservation' => esc_html__( 'Reservation', 'mw-wp-form' ),
		'r_s_now_supporting' => '対応中'
	);
	return $response_statuses;
}
add_filter( 'mwform_response_statuses_mwf_12035', 'my_mwform_response_statuses' );

// URLクエリ変数を追加する
function add_subtitle( $vars ) {
	$vars[] = 'response_status';
	$vars[] = 'address_area';
	$vars[] = 'start_date_year';
	$vars[] = 'start_date_month';
	$vars[] = 'start_date_day';
	$vars[] = 'end_date_year';
	$vars[] = 'end_date_month';
	$vars[] = 'end_date_day';	
	return $vars;
}
add_filter('query_vars', 'add_subtitle');

// 問い合わせ一覧画面に対応状況・都道府県の絞り込み検索プルダウンメニューを表示
function add_subtitle_filter(){
	global $post_type;
	// MW WP Form以外では実行しないようにする。***の部分は、フォームIDに合わせて変更する
	if ( $post_type == 'mwf_12035' ) {
		echo '<div id="mwf_miki_searchbox">';
			// 日付範囲で絞り込み検索するフォーム
			echo '<div class="search_date"><h3>1.検索期間を選択してください。</h3><div class="search_date_s">検索開始日時';
			echo '<select name="start_date_year" id="year">';
			for($dateYear=2019; $dateYear<=2030; $dateYear=$dateYear+1){
				$selected = "";
				if(get_query_var('start_date_year') == $dateYear) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				} elseif (!get_query_var('start_date_year') && $dateYear==2025) {
					$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateYear . '" '. $selected .'>'. $dateYear . '</option>';
			}
			echo '</select>年 ';

			echo '<select name="start_date_month" id="month">';
			for($dateMonth=1; $dateMonth<=12; $dateMonth=$dateMonth+1){
				$selected = "";
				if(get_query_var('start_date_month') == $dateMonth) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateMonth . '" '. $selected .'>'. $dateMonth . '</option>';
			}
			echo '</select>月 ';

			echo '<select name="start_date_day" id="day">';
			for($dateDay=1; $dateDay<=31; $dateDay=$dateDay+1){
				$selected = "";
				if(get_query_var('start_date_day') == $dateDay) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateDay . '" '. $selected .'>'. $dateDay . '</option>';
			}
			echo '</select>日 </div>';

			echo '<div class="search_date_e">検索終了日時<select name="end_date_year" id="Eyear">';
			for($dateYear=2019; $dateYear<=2030; $dateYear=$dateYear+1){
				$selected = "";
				if(get_query_var('end_date_year') == $dateYear) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				} elseif (!get_query_var('end_date_year') && $dateYear==2030) {
					$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateYear . '" '. $selected .'>'. $dateYear . '</option>';
			}
			echo '</select>年 ';

			echo '<select name="end_date_month" id="Emonth">';
			for($dateMonth=1; $dateMonth<=12; $dateMonth=$dateMonth+1){
				$selected = "";
				if(get_query_var('end_date_month') == $dateMonth) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateMonth . '" '. $selected .'>'. $dateMonth . '</option>';
			}
			echo '</select>月 ';

			echo '<select name="end_date_day" id="Eday">';
			for($dateDay=1; $dateDay<=31; $dateDay=$dateDay+1){
				$selected = "";
				if(get_query_var('end_date_day') == $dateDay) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				}
				echo '<option value="'. $dateDay . '" '. $selected .'>'. $dateDay . '</option>';
			}
			echo '</select>日 </div></div>';
echo <<< HTML
<script>
/**
 * イベントを登録する（IE8以下が addEventListener() に対応していないためのラッパー関数）
 *
 * @param t 対象ノード
 * @param p イベントタイプ
 * @param l 実行される関数
 */
var _addEvent=function(t,p,l){try{t.addEventListener(p,l,false);}catch(e){t.attachEvent("on"+p,function(e){l.call(t,e);});}};

(function(){
  _addEvent(window, "load", function(e) {
    var yearId = "year"; // 年コントロールのID
    var monthId = "month"; // 月コントロールのID
    var dayId = "day"; // 日コントロールのID

    var targetYear = document.getElementById(yearId);
    var targetMonth = document.getElementById(monthId);
    var targetDay = document.getElementById(dayId);

    _addEvent(targetYear, "change", function(e) {
      // 年コントロールを変更したとき
      nonExistDayIsNonDisplayed(this, targetMonth, targetDay);
    });
    _addEvent(targetMonth, "change", function(e) {
      // 月コントロールを変更したとき
      nonExistDayIsNonDisplayed(targetYear, this, targetDay);
    });
  });

  /**
   * 存在しない日（2月30日など）の選択肢を非表示にする
   *
   * @param targetYear 年コントロール
   * @param targetMonth 月コントロール
   * @param targetDay 日コントロール
   */
  var nonExistDayIsNonDisplayed = function(targetYear, targetMonth, targetDay) {
    var selectedMonthValue = parseInt(targetMonth.getElementsByTagName("option")[targetMonth.selectedIndex].value, 10);
    var targetDayOptions = targetDay.getElementsByTagName("option");

    if (selectedMonthValue === 2) {
      // 2月の場合
      var selectedYearValue = parseInt(targetYear.getElementsByTagName("option")[targetYear.selectedIndex].value, 10)
      var leapYear = isLeapYear(selectedYearValue); // 閏年か

      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        var dayValue = parseInt(targetDayOption.value, 10);
        if (dayValue >= 30 || (dayValue === 29 && !leapYear)) {
          targetDayOption.disabled = true; // 選択不能指定
          if (targetDayOption.selected) {
            // 29日(閏年でない場合のみ)、30日、31日のいずれかが選択されていた場合は、2月の最終日に変更
            if (leapYear) {
              targetDay.value = "29";
            } else {
              targetDay.value = "28";
            }
          }
        } else if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    } else if (selectedMonthValue === 4 || selectedMonthValue === 6 || selectedMonthValue === 9 || selectedMonthValue === 11) {
      // 月の日数が30日の場合
      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        var dayValue = parseInt(targetDayOption.value, 10);
        if (dayValue >= 31) {
          targetDayOption.disabled = true; // 選択不能指定
          if (targetDayOption.selected) {
            // 31日が選択されていた場合は、各月の最終日に変更
            targetDay.value = "30";
          }
        } else if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    } else {
      // 月の日数が31日の場合
      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    }
  };

  /**
   * 閏年か
   *
   * @param year 年
   *
   * @return 閏年ならtrue、それ以外の場合はfalse
   */
  var isLeapYear = function(year) {
    return new Date(year, 1, 29).getMonth() === 1;
  };
})();

var _addEvent=function(t,p,l){try{t.addEventListener(p,l,false);}catch(e){t.attachEvent("on"+p,function(e){l.call(t,e);});}};

(function(){
  _addEvent(window, "load", function(e) {
    var yearId = "Eyear"; // 年コントロールのID
    var monthId = "Emonth"; // 月コントロールのID
    var dayId = "Eday"; // 日コントロールのID

    var targetYear = document.getElementById(yearId);
    var targetMonth = document.getElementById(monthId);
    var targetDay = document.getElementById(dayId);

    _addEvent(targetYear, "change", function(e) {
      // 年コントロールを変更したとき
      nonExistDayIsNonDisplayed(this, targetMonth, targetDay);
    });
    _addEvent(targetMonth, "change", function(e) {
      // 月コントロールを変更したとき
      nonExistDayIsNonDisplayed(targetYear, this, targetDay);
    });
  });

  /**
   * 存在しない日（2月30日など）の選択肢を非表示にする
   *
   * @param targetYear 年コントロール
   * @param targetMonth 月コントロール
   * @param targetDay 日コントロール
   */
  var nonExistDayIsNonDisplayed = function(targetYear, targetMonth, targetDay) {
    var selectedMonthValue = parseInt(targetMonth.getElementsByTagName("option")[targetMonth.selectedIndex].value, 10);
    var targetDayOptions = targetDay.getElementsByTagName("option");

    if (selectedMonthValue === 2) {
      // 2月の場合
      var selectedYearValue = parseInt(targetYear.getElementsByTagName("option")[targetYear.selectedIndex].value, 10)
      var leapYear = isLeapYear(selectedYearValue); // 閏年か

      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        var dayValue = parseInt(targetDayOption.value, 10);
        if (dayValue >= 30 || (dayValue === 29 && !leapYear)) {
          targetDayOption.disabled = true; // 選択不能指定
          if (targetDayOption.selected) {
            // 29日(閏年でない場合のみ)、30日、31日のいずれかが選択されていた場合は、2月の最終日に変更
            if (leapYear) {
              targetDay.value = "29";
            } else {
              targetDay.value = "28";
            }
          }
        } else if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    } else if (selectedMonthValue === 4 || selectedMonthValue === 6 || selectedMonthValue === 9 || selectedMonthValue === 11) {
      // 月の日数が30日の場合
      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        var dayValue = parseInt(targetDayOption.value, 10);
        if (dayValue >= 31) {
          targetDayOption.disabled = true; // 選択不能指定
          if (targetDayOption.selected) {
            // 31日が選択されていた場合は、各月の最終日に変更
            targetDay.value = "30";
          }
        } else if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    } else {
      // 月の日数が31日の場合
      for (var i = targetDayOptions.length - 1; i >= 0; i--) {
        var targetDayOption = targetDayOptions[i];
        if (targetDayOption.disabled) {
          // 選択不能指定が成されていたら解除
          targetDayOption.disabled = false;
        } else {
          break;
        }
      }
    }
  };

  /**
   * 閏年か
   *
   * @param year 年
   *
   * @return 閏年ならtrue、それ以外の場合はfalse
   */
  var isLeapYear = function(year) {
    return new Date(year, 1, 29).getMonth() === 1;
  };
})();
</script>
HTML;
			
			// 都道府県の絞り込み検索
			echo '<div class="search_area"><h3>2.エリアを選択してください。</h3><div class="search_area">';

 			$areaJapan = array(
				'すべてのエリア' => 'all',
				'東日本支店' => array(
					'北海道' => '北海道',
					'青森県' => '青森県',
					'岩手県' => '岩手県',
					'秋田県' => '秋田県',
					'山形県' => '山形県',
					'宮城県' => '宮城県',
					'福島県' => '福島県',
					'新潟県' => '新潟県',
					'栃木県' => '栃木県',
					'長野県' => '長野県',
					'山梨県' => '山梨県',
					'群馬県' => '群馬県',
					'茨城県' => '茨城県'
				),
				'東京支店' => array(
					'埼玉県' => '埼玉県',
					'千葉県' => '千葉県',
					'東京都' => '東京都'
				),
				'横浜支店' => array(
					'神奈川県' => '神奈川県'
				),
				'静岡支店' => array(
					'静岡県' => '静岡県'
				),
				'名古屋支店' => array(
					'岐阜県' => '岐阜県',
					'愛知県' => '愛知県',
					'三重県' => '三重県'
				),
				'大阪支店' => array(
					'滋賀県' => '滋賀県',
					'京都府' => '京都府',
					'大阪府' => '大阪府',
					'兵庫県' => '兵庫県',
					'奈良県' => '奈良県',
					'和歌山県' => '和歌山県'
				),
				'西日本支店' => array(
					'福井県' => '福井県',
					'富山県' => '富山県',
					'石川県' => '石川県',
					'岡山県' => '岡山県',
					'広島県' => '広島県',
					'鳥取県' => '鳥取県',
					'島根県' => '島根県',
					'山口県' => '山口県',
					'香川県' => '香川県',
					'徳島県' => '徳島県',
					'愛媛県' => '愛媛県',
					'高知県' => '高知県'
				),
				'九州支店' => array(
					'福岡県' => '福岡県',
					'佐賀県' => '佐賀県',
					'長崎県' => '長崎県',
					'熊本県' => '熊本県',
					'大分県' => '大分県',
					'宮崎県' => '宮崎県',
					'鹿児島県' => '鹿児島県',
					'沖縄県' => '沖縄県'
				),
			);

			echo '<table><tbody><tr>';
			
			$loopCounter = 0;
			foreach ($areaJapan as $key => $value) {
				$selected = "";
				$addressAreaArray = get_query_var('address_area');
				if($addressAreaArray[array_search($key, $addressAreaArray)] == $key) {
						// URLクエリと一致する場合はselectedに
						$selected = 'checked';
				}
				echo '<td><input id="checkAll'.$loopCounter.'" type="checkbox" name="address_area[]" value="'. $key . '"'. $selected . '>'. $key . '</input></td>';
				$loopCounter++;
			}

echo <<< HTML
			</tr>
			<tr>
			<td></td>
			<td>北海道<br>
				青森、岩手<br>
				秋田、山形<br>
				宮城、福島<br>
				新潟、栃木<br>
				長野、山梨<br>
				群馬、茨城<br>
			</td>
			<td>千葉、埼玉<br>
				東京<br>
			</td>
			<td>神奈川<br>
			</td>
			<td>静岡<br>
			</td>
			<td>愛知、岐阜<br>
				三重<br>
			</td>
			<td>大阪、奈良<br>
				滋賀、兵庫<br>
				和歌山<br>
				京都<br>
			</td>
			<td>福井、富山<br>
				石川、岡山<br>
				広島<br>
				鳥取、島根<br>
				山口<br>
				香川、徳島<br>
				愛媛、高知<br>
			</td>
			<td>福岡、大分<br>
				長崎、佐賀<br>
				熊本、宮崎<br>
				鹿児島<br>
				沖縄<br>
			</td>
		</tr>
	</tbody>
</table>
HTML;
			echo '<div class="list-hidden">';

			foreach ($areaJapan as $key => $value1) {
				foreach($value1 as $key2 => $value2){
					$selected = "";
					$addressAreaArray = get_query_var('address_area');
					if($addressAreaArray[array_search($value2, $addressAreaArray)] == $value2) {
						// URLクエリと一致する場合はselectedに
						$selected = 'checked';
					}
					echo '<input class="list '.$key.'" type="checkbox" name="address_area[]" value="'. $value2 . '"'. $selected . '>'. $value2 . '</input>';
				}
			}
echo <<< HTML
			</div></div></div>

			<script>
			jQuery(function() {
				jQuery('#checkAll1').on("click",function(){
					jQuery('.東日本支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll2').on("click",function(){
					jQuery('.東京支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll3').on("click",function(){
					jQuery('.横浜支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll4').on("click",function(){
					jQuery('.静岡支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll5').on("click",function(){
					jQuery('.名古屋支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll6').on("click",function(){
					jQuery('.大阪支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll7').on("click",function(){
					jQuery('.西日本支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll8').on("click",function(){
					jQuery('.九州支店').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll0').on("click",function(){
					jQuery('#checkAll1').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll2').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll3').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll4').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll5').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll6').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll7').prop("checked", jQuery(this).prop("checked"));
					jQuery('#checkAll8').prop("checked", jQuery(this).prop("checked"));
					
					jQuery('.list').prop("checked", jQuery(this).prop("checked"));
				});
			});
			jQuery(function() {
				jQuery('#checkAll1,#checkAll2,#checkAll3,#checkAll4,#checkAll5,#checkAll6,#checkAll7,#checkAll8').on('click', function() {
					if (jQuery('.list-hidden :checked').length == jQuery('.list-hidden :input').length){
						jQuery('#checkAll0').prop('checked', 'checked');
					}else{
						jQuery('#checkAll0').prop('checked', false);
					}
				});
			});
			</script>
HTML;
		// 対応状況の絞り込み検索
		echo '<div class="search_status"><h3>3.対応状況を選択してください。</h3><div class="search_status"><select name="response_status">';
		echo '<option value="">すべての対応状況</option>';
		$status = array(
			'not-supported' => '未対応',
			'r_s_supported' => '対応済み',
			'r_s_reservation' => '保留',
			'r_s_now_supporting' => '対応中'
		);
		foreach ($status as $key => $value) {
				$selected = "";
				if(get_query_var('response_status') == $key) {
						// URLクエリと一致する場合はselectedに
						$selected = ' selected="selected"';
				}
				echo '<option value="'. $key . '"'. $selected . '>'. $value . '</option>';
		}
echo <<< HTML
</select></div></div></div>
<style>
    .alignleft.actions.bulkactions {
        display: none;
    }
    .list-hidden {
            display: none;
    }
    div#mwf_miki_searchbox div {
        margin: 10px 0;
    }
    div#mwf_miki_searchbox h3 {
        padding: 3px 0 3px 5px;
        background-color: cornsilk;
        font-size: 14px;
    }
    div#mwf_miki_searchbox {
            padding: 10px;
            background: white;
    }
    .search_area td {
            padding: 0px 10px;
            vertical-align: text-top;
    }
    .search_area tr:nth-child(2) {
            font-size: 12px;
    }
    .tablenav.top {
        height: 100%;
    }
    select#filter-by-date {
        display: none!important;
    }
    .tablenav .actions select{
        float: inherit!important
    }
</style>
HTML;
	}
};
add_action('restrict_manage_posts', 'add_subtitle_filter');

// 絞り込み検索実行時の処理
function get_inquiry_data_subtitle($args) {
	$value1 = get_query_var('response_status');
	$value2 = get_query_var('address_area');
	$dateSyear = get_query_var('start_date_year');
	$dateSmonth = get_query_var('start_date_month');
	$dateSday = get_query_var('start_date_day');
	$dateEyear = get_query_var('end_date_year');
	$dateEmonth = get_query_var('end_date_month');
	$dateEday = get_query_var('end_date_day');

	$querys = array();

	// 以下は、CSVダウンロードボタンを押した時用の処理（get_query_varで何も返ってこないため、URLパラメータを直接参照する）
	if(empty($value1)){
			if(isset($_GET['response_status'])){
					$value1 = $_GET['response_status'];
			}
	}
	if(empty($value2)){
			if(isset($_GET['address_area'])){
					$value2 = $_GET['address_area'];
			}
	}
	if(empty($dateSyear)){
		if(isset($_GET['start_date_year'])){
				$dateSyear = $_GET['start_date_year'];
		}else{
			$dateSyear = 2018;
		}
	}
	if(empty($dateSmonth)){
		if(isset($_GET['start_date_month'])){
				$dateSmonth = $_GET['start_date_month'];
		}else{
			$dateSmonth = 1;
		}
	}
	if(empty($dateSday)){
		if(isset($_GET['start_date_day'])){
				$dateSday = $_GET['start_date_day'];
		}else{
			$dateSday = 1;
		}
	}
	if(empty($dateEyear)){
		if(isset($_GET['end_date_year'])){
				$dateEyear = $_GET['end_date_year'];
		}else{
			$dateEyear = 2030;
		}
	}
	if(empty($dateEmonth)){
		if(isset($_GET['end_date_month'])){
				$dateEmonth = $_GET['end_date_month'];
		}else{
			$dateEmonth = 12;
		}
	}
	if(empty($dateEday)){
		if(isset($_GET['end_date_day'])){
				$dateEday = $_GET['end_date_day'];
		}else{
			$dateEday = 31;
		}
	}

	if(!empty($value1)) {
			$querys = array_merge( $querys, array( array( 
					'key'   => '_mw-wp-form_data',
					'value' => $value1,
					'compare' => 'LIKE'         // 対応状況のみレコード内にmemoのデータも一緒に入っているため、部分一致で検索する
			) ) );
	}
	if(!empty($value2)) {
			$querys = array_merge( $querys, array( array(
					'key'   => 'address_area',
					'value' => $value2
			) ) );
	}

	$args = array_merge( $args, array(
			'meta_query' => $querys,
			'date_query' => array(
				array(
					'after' => array(
						'year' => $dateSyear,
						'month' => $dateSmonth,
						'day' => $dateSday,
					),
					'before' => array(
						'year' => $dateEyear,
						'month' => $dateEmonth,
						'day' => $dateEday,
					),
					'inclusive' => true //境界値を含む
				),
			),
	) );
	return $args;
};
add_filter( 'mwform_get_inquiry_data_args-mwf_12035', 'get_inquiry_data_subtitle' );

function my_admin_mail( $Mail, $values, $Data ) {
	// 本文を変更
	$Datetime = date_i18n( 'Y年m月d日 H時i分' );
	$Mail->body =
	'販売者ご紹介お申し込みがありました。'
	."\n\n".
	'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	."\n".
	'【お問い合わせ時間】'
	."\n".
	$Datetime
	."\n\n".
	'【販売者ご紹介のお申し込みのきっかけ】'
	."\n".
	$Data->get('contact_reason')
	."  ".
	$Data->get('contact_reason_other_txt')
	."\n\n".
	'【興味ある商品】'
	."\n".
	$Data->get('interest_things')
	."  ".
	$Data->get('interest_things_txt')
	."\n\n".
	'【ミキの商品の認知】'
	."\n".
	$Data->get('select_known')
	."\n".
	'━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	."\n\n".
	'※1 「個人情報の保護に関する法律」等に基づき、'
	."\n".
	'　 お客様からお預かりしている個人情報の明記については'
	."\n".
	'　 記しておりません。'
	."\n".
	'※2 本メールはお問合せがあった際の自動配信メールです。'
	."\n".
	'ご対応よろしくお願いいたします。';
	return $Mail;
}
add_filter( 'mwform_admin_mail_mw-wp-form-12035', 'my_admin_mail',10, 3  );
/*	販売者ご紹介お申し込みページ END */

/*-------------------------------------------*/
/*	フリーワード検索
/*-------------------------------------------*/
// $search .= "{$wpdb->posts}.post_type IN ('post','page','prune_ency','news','q_and_a')";
//サイト内検索のカスタマイズ
//サイト内検索のカスタマイズ
function custom_search($search, $wp_query) {
	global $wpdb;

	//検索ページ以外だったら終了
	if (!$wp_query->is_search)
	return $search;

	if (!isset($wp_query->query_vars))
	return $search;

	// タグ名・カテゴリ名・カスタムフィールド も検索対象にする
	$search_words = explode(' ', isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '');
	if ( count($search_words) > 0 ) {
		$search = '';
		foreach ( $search_words as $word ) {
			if ( !empty($word) ) {
				$search_word = $wpdb->escape("%{$word}%");
				$search .= " AND (
						{$wpdb->posts}.post_title LIKE '{$search_word}'
						OR {$wpdb->posts}.post_content LIKE '{$search_word}'
						OR {$wpdb->posts}.ID IN (
							SELECT distinct r.object_id
							FROM {$wpdb->term_relationships} AS r
							INNER JOIN {$wpdb->term_taxonomy} AS tt ON r.term_taxonomy_id = tt.term_taxonomy_id
							INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
							WHERE t.name LIKE '{$search_word}'
						OR t.slug LIKE '{$search_word}'
						OR tt.description LIKE '{$search_word}'
						)
						OR {$wpdb->posts}.ID IN (
							SELECT distinct p.post_id
							FROM {$wpdb->postmeta} AS p
							WHERE p.meta_value LIKE '{$search_word}'
						)
				) ";
			}
		}
	}

	return $search;
}
add_filter('posts_search','custom_search', 10, 2);

// 検索キーワードに全角空白が入っていた場合に、and検索になるように
function empty_search( $query ) {
    if ( $query->is_main_query() && $query->is_search && ! $query->is_admin ) {
    $s = $query->get( 's' );
    $s = str_replace('　',' ', $s );
    $query->set( 's', $s );
    }
}
add_action( 'pre_get_posts', 'empty_search' );

/*-------------------------------------------*/
/*  アーカイブ
/*-------------------------------------------*/
/* the_archive_title() :より前の不要な文字を削除 */
add_filter( 'get_the_archive_title', function ($title) {
    if (is_category()) {
        $title = single_cat_title('',false);
    } elseif (is_tag()) {
        $title = single_tag_title('',false);
	} elseif (is_tax()) {
	    $title = single_term_title('',false);
	} elseif (is_post_type_archive() ){
		$title = post_type_archive_title('',false);
	} elseif (is_date()) {
	    $title = get_the_time('Y年');
	} elseif (is_search()) {
	    $title = '検索結果：'.esc_html( get_search_query(false) );
	} elseif (is_404()) {
	    $title = '「404」ページが見つかりません';
	} else {

	}
    return $title;
});

// メインクエリの表示件数を変更
function set_pre_get_posts($query) {
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	$blog_id = get_category_by_slug('blog')->cat_ID;
	$blog_children = get_term_children( $blog_id, 'category' );
	// if (($query->is_category('blog')) || $query->is_category('recommended') || $query->is_category($blog_children)) {
	if (($query->is_category('blog')) || $query->is_category('recommended') ) {
		$query->set('posts_per_page', '12');
		return;
	}
}
add_action('pre_get_posts', 'set_pre_get_posts');

/*-------------------------------------------*/
/*  確認用
/*-------------------------------------------*/
function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}

/*-------------------------------------------*/
/*  旧ブログページの関数
/*-------------------------------------------*/
function getPostBlogList($category_name, $year, $month, $day, $row_count,$paged ) 
{
	session_start();
	$_SESSION['permalink'] = get_the_permalink();
	$_SESSION['uri'] = $_SERVER["REQUEST_URI"];
//	echo 'XXXXX' . $_SESSION['uri'];

	if ($row_count == null) {
		$row_count= 999;
	}
	
	if ($paged == null) {
		$paged= 1;
	}
	
	$posts = array();
	$args = array(
		'post_status'    => 'publish',
//		'posts_per_page' => $row_count,
//		'paged' => $paged,
		'category__and'  => array(get_category_by_slug('blog')->term_id),
		'orderby' => 'post_date',
		'order' => 'desc'
	);
	$args['date_query'] = array(
		array(
			'year'  => $year,
			'month' => $month,
			'day'   => $day,
			),
		);
	if ($category_name != null) {
		$args['category_name'] = $category_name;
	}

	
	$ids = array();
	$args['posts_per_page'] = 999;
	$the_query = new WP_Query($args);
    while($the_query->have_posts()) : $the_query->the_post();
		$ids[] = array('id' => get_the_ID(),'permalink' => get_permalink());
	endwhile;

	$args['posts_per_page'] = $row_count;
	$args['paged'] = $paged;
	$the_query = new WP_Query($args);
	while($the_query->have_posts()) : $the_query->the_post();
		$post['post_id'] = get_the_ID();
		$post['post_date'] = get_the_date();
		$post['post_date_yyyy'] = get_the_date('Y');
		$post['post_date_yyyymmdd'] = get_the_date('Ymd');
		$post['title'] = get_the_title();
		$post['permalink'] = get_permalink();
//		$post['post_thumbnail_url'] = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
		$post['post_thumbnail_url'] = wp_get_attachment_image_src(get_post_thumbnail_id(),'medium')[0];
		$post['content'] = get_the_content();
		$posts[] = $post;
		
	endwhile;

	$big = 999999999; // need an unlikely integer

	$paginate = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '&paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $the_query->max_num_pages,
		'type' => 'array',
		'prev_text' => 'prev',
		'next_text' => 'next',
	) );

	// 記事ID保持
	$_SESSION['ids'] = $ids;
	return array('posts'=> $posts,'paginate'=> $paginate);

}


function getPostBlogYYYYMMList($category_name) 
{
	
	$posts = array();
	$args = array(
		'post_status'    => 'publish',
		'posts_per_page' => 9999,
		'category__and'  => array(get_category_by_slug('blog')->term_id),
		'orderby' => 'post_date',
		'order' => 'desc'
	);
	if ($category_name != null) {
		$args['category_name'] = $category_name;
	}

	$the_query = new WP_Query($args);
	
	$yyyymmList = array();
	$ids = array();
    while($the_query->have_posts()) : $the_query->the_post();
		$ids[] = array('id' => get_the_ID(),'permalink' => get_permalink());
		$yyyymm = get_the_date('Ym');
		if ($yyyymmList[$yyyymm]) {
			$yyyymmList[$yyyymm] = $yyyymmList[$yyyymm] + 1;
		} else {
			$yyyymmList[$yyyymm] = 1;
		}
	endwhile;
	
	// 記事ID保持
	session_start();
	$_SESSION['ids'] = $ids;
	$_SESSION['permalink'] = get_the_permalink();

	return array('yyyymmList'=> $yyyymmList);

}

// RSSフィードのカスタマイズ アプリの記事一覧表示用
function modify_rss2_query($query_string)  
{
	$cat_id = get_query_var('category_name');
	$args = array(
		'category_name' => $cat_id,
	);
    return $args;
}
add_action( 'rss2_head', 'modify_rss2_query' );

add_shortcode('tsuji', function(){
  return '<span class="tsuji-ivs">辻&#xE0100;</span>';
});

add_filter( 'wp_kses_allowed_html', 'my_allow_img_attributes', 10, 2 );
function my_allow_img_attributes( $allowed, $context ) {
    if ( $context === 'post' ) {
        if ( isset( $allowed['img'] ) ) {
            // src, alt, class, width, height などを明示的に許可
            $allowed['img']['src']   = true;
            $allowed['img']['alt']   = true;
            $allowed['img']['class'] = true;
            $allowed['img']['width'] = true;
            $allowed['img']['height'] = true;
            $allowed['img']['style']  = true;
        }
    }
    return $allowed;
}

function is_mw_wp_form_admin_editor() {
    if ( ! is_admin() ) {
        return false;
    }

    if ( function_exists( 'get_current_screen' ) ) {
        $screen = get_current_screen();
        if ( $screen && $screen->post_type === 'mw-wp-form' ) {
            return true;
        }
    }

    global $typenow;
    if ( $typenow === 'mw-wp-form' ) {
        return true;
    }

    $post_id = 0;
    if ( isset( $_GET['post'] ) ) {
        $post_id = absint( $_GET['post'] );
    } elseif ( isset( $_POST['post_ID'] ) ) {
        $post_id = absint( $_POST['post_ID'] );
    }

    return $post_id && get_post_type( $post_id ) === 'mw-wp-form';
}

add_filter( 'user_can_richedit', 'disable_visual_editor_for_mw_wp_form' );
function disable_visual_editor_for_mw_wp_form( $default ) {
    if ( is_mw_wp_form_admin_editor() ) {
        return false;
    }

    return $default;
}

add_filter('tiny_mce_before_init', 'my_tinymce_prevent_relative_urls');
function my_tinymce_prevent_relative_urls($initArray) {
    // URLの自動変換を無効にする
    $initArray['convert_urls'] = false;
    // 相対パスへの変換を無効にし、絶対パスを維持する
    $initArray['relative_urls'] = false;
    // ホスト名（ドメイン）を削除しない
    $initArray['remove_script_host'] = false;
    if ( is_mw_wp_form_admin_editor() ) {
        $initArray['wpautop'] = false;
        $initArray['forced_root_block'] = false;
        $initArray['force_br_newlines'] = false;
        $initArray['force_p_newlines'] = false;
    }
    return $initArray;
}

require_once get_template_directory() . '/inc/blog-search-analytics.php';
