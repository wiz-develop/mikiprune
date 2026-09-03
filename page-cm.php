
<?php
/**
 * The template for displaying all single posts
 * Template Name: TVCM紹介
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();

$page_header_img = CFS()->get('page_header_img');
$page_header_img_sp = CFS()->get('page_header_img_sp');
$page_img = CFS()->get('page_img');
$page_about= CFS()->get('page_about');
$new_cm_catch = CFS()->get('new_cm_catch');
$new_cm_movie = CFS()->get('new_cm_movie');
$new_cm_about = CFS()->get('new_cm_about');
$new_cm_making_catch = CFS()->get('new_cm_making_catch');
$new_cm_making_movie = CFS()->get('new_cm_making_movie');
$blog_link_list = CFS()->get('blog_link_list');
$cm_archive_list = CFS()->get('cm_archive_list');
?>
<div id="app_func" class="app_func app_func_page func_key_page">
    <main>
        <div id="app_func_screen" class="app_func_screen app_func_screen_cm screen_key_cm pb-4">
            <div class="widget_type_page_header screen_widget_key_page_header">
                <div class="page_header_img">
                    <?php if(wp_is_mobile()) : ?>
                        <img src="<?php echo $page_header_img_sp; ?>" alt="<?php the_title(); ?>">
                    <?php else : ?>
                        <img src="<?php echo $page_header_img; ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </div>
            </div>
            <div class="widget_type_archive_cat screen_widget_key_archive_cat_list pt-5 pb-4">
                <div class="widget_content container">
                    <?php get_template_part('template-parts/side/category-ad');?>
                </div>
            </div>
            <div class="widget_type_page_about_twocolumn screen_widget_key_page_about_twocolumn pt-3 pb-5">
                <div class="widget_content anime container">
                    <div class="anime-scroll row">
                        <div class="page-about_img col-12 col-lg-6">
                            <img src="<?php echo $page_img; ?>">
                        </div>
                        <div class="page-about_detail col-12 col-lg-6 mt-md-4">
                            <p class="mb-0"><?php echo $page_about; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="widget_type_cm-list screen_widget_key_cm-list">
                <div class="cm-introduction">
                    <div class="cm-section anime">
                        <div class="main-cm-caption anime-scroll">
                            <h2 class="main-cm-year">
                                <?php $new_cm_year = CFS()->get('new_cm_year'); ?>
                                <img src="<?php echo CFS()->get('new_cm_year_img'); ?>" alt="<?php echo $new_cm_year; ?>年">
                            </h2>
                            <div class="main-cmtitle">
                                <img src="<?php echo CFS()->get('new_cm_title_img'); ?>" alt="<?php echo CFS()->get('new_cm_title'); ?>">
                            </div>
                        </div>
                        <div class="main-cm-movie">
                            <div id="cm-<?php echo $new_cm_year; ?>" class="main-cm-load modal_trigger">
                                <img src="<?php echo $new_cm_catch; ?>">
                            </div>
                            <div class="movie-modal_box modal_box">
                                <div class="modal_bg"></div>
                                <div class="movie-modal_inner modal_inner">
                                    <div class="movie-modal_block modal_block">
                                        <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo $new_cm_catch; ?>" oncontextmenu="return false;">
                                            <source type="video/mp4" src="<?php echo $new_cm_movie; ?>">
                                            <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                        </video>
                                    </div>
                                    <div class="movie-modal_close modal_close">
                                        <div class="rounded text-center">
                                            閉じる<span class="pl-3">×</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="main-cm-description">
                                <div class="main-cm-tx">
                                    <p class="mb-0"><?php echo $new_cm_about; ?></p>
                                </div>
                                <?php if ($new_cm_making_movie) : ?>
                                <div class="main-making-cm">
                                    <div id="making-<?php echo $new_cm_year; ?>" class="main-cm-load modal_trigger">
                                        <button class="bg-white_btn active anime_hover-up rounded">メイキング映像を見る</button>
                                    </div>
                                    <div class="movie-modal_box modal_box">
                                        <div class="modal_bg"></div>
                                        <div class="movie-modal_inner modal_inner">
                                            <div class="movie-modal_block modal_block">
                                                <video class="position-relative w-100" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo $new_cm_making_catch; ?>" oncontextmenu="return false;">
                                                    <source type="video/mp4" src="<?php echo $new_cm_making_movie; ?>">
                                                    <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                </video>
                                            </div>
                                            <div class="movie-modal_close modal_close">
                                                <div class="rounded text-center">
                                                    閉じる<span class="pl-3">×</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($blog_link_list) : ?>
                                <div class="official-blog anime">
                                    <?php foreach ($blog_link_list as $blog_link) : ?>
                                    <div class="kiki-link anime-scroll">
                                        <div class="kiki-link-title"><?php echo $blog_link['blog_link_tit']; ?></div>
                                        <div class="kiki-link-next">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/bloglink-head.png">
                                        </div>
                                        <a href="<?php echo $blog_link['blog_link_url']; ?>" target="_blank" rel="noopener noreferrer">
                                            <div class="kiki-link-img">
                                                <img src="<?php echo $blog_link['blog_link_img']; ?>">
                                            </div>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-5 mb-3">
                            <div class="nakai-30th d-flex justify-content-center align-items-end">
                                <div class="nakai-30th_th">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/nakai-30th_fukidashi.png" alt="中井さんCM出演30周年">
                                </div>
                                <div class="main-cm-movie mx-0">
                                    <button type="button" class="bg-white_btn active rounded-pill px-3 py-2 d-block mx-0 mb-3 main-cm-load modal_trigger w-auto">中井貴一さんインタビュー映像</button>
                                    <div class="movie-modal_box modal_box">
                                        <div class="modal_bg"></div>
                                        <div class="movie-modal_inner modal_inner">
                                            <div class="movie-modal_block modal_block">
                                                <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo $new_cm_catch; ?>" oncontextmenu="return false;">
                                                    <source type="video/mp4" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/movie/cm/cm2026_intervew.mp4">
                                                    <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                </video>
                                            </div>
                                            <div class="movie-modal_close modal_close">
                                                <div class="rounded text-center">
                                                    閉じる<span class="pl-3">×</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="mb-0 text-center">1996年よりミキプルーンのＣＭにご出演いただいている中井貴一さんに、<br>これまでの作品に対してや出演30周年を迎えての想いを語っていただきました。</p>
                        </div>
                    </div>
                    <?php
                        $fields = CFS()->get('cm_archive_list');
                        if ($fields) :
                            foreach ($fields as $field) :
                    ?>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll d-md-flex align-items-center">
                            <img src="<?php echo $field['cm_year_img']; ?>" alt="<?php echo $field['cm_year']; ?>年">
                        </h2>
                        <div class="ac-child anime">
                            <?php
                                $cm_list = $field['cm_list'];
                                foreach ($cm_list as $cm):
                            ?>
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end">
                                    <div class="cm-load row align-items-center">
                                        <div id="cm-<?php echo $field['cm_year']; ?>" class="<?php if ($cm['cm_movie']) echo 'modal_trigger'; ?> cm-img col-12 col-md-6">
                                            <?php if ($cm['cm_catch']) : ?>
                                            <img src="<?php echo $cm['cm_catch']; ?>" alt="<?php echo $field['cm_year']; ?>年CM">
                                            <?php endif; ?>
                                        </div>
                                        <div class="movie-modal_box modal_box">
                                            <div class="modal_bg"></div>
                                            <div class="movie-modal_inner modal_inner">
                                                <div class="movie-modal_block modal_block">
                                                    <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo $cm['cm_catch']; ?>" oncontextmenu="return false;">
                                                        <source type="video/mp4" src="<?php echo $cm['cm_movie']; ?>">
                                                        <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                    </video>
                                                </div>
                                                <div class="movie-modal_close modal_close">
                                                    <div class="rounded text-center">
                                                        閉じる<span class="pl-3">×</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cm_title col-12 col-md-6">
                                            <h3 class="cm_title_tx"><?php echo $cm['cm_title']; ?></h3>
                                            <?php
                                                    if ($cm['cm_sub_movie']) :
                                            ?>
                                                <div id="making-<?php echo $field['cm_year']; ?>" class="making-cm modal_trigger">
                                                    <button class="bg-white_btn active anime_hover-up rounded"><?php echo $cm['cm_sub_button']; ?></button>
                                                </div>
                                                <div class="movie-modal_box modal_box">
                                                    <div class="modal_bg"></div>
                                                    <div class="movie-modal_inner modal_inner">
                                                        <div class="movie-modal_block modal_block">
                                                            <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo $cm['cm_sub_catch']; ?>" oncontextmenu="return false;">
                                                                <source type="video/mp4" src="<?php echo $cm['cm_sub_movie']; ?>">
                                                                <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                            </video>
                                                        </div>
                                                        <div class="movie-modal_close modal_close">
                                                            <div class="rounded text-center">
                                                                閉じる<span class="pl-3">×</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($cm['cm_brand_movie']) : ?>
                                                <?php if (!empty($cm['cm_brand_detail'])) : ?>
                                                    <h3 class="cm_title_tx mt-3" style="white-space: pre-line;">
                                                        <?php echo str_replace(array('<br>', '<br/>', '<br />'), '', $cm['cm_brand_detail']); ?>
                                                    </h3>
                                                <?php endif; ?>
                                                                                                <?php if ( $brand_detail ) : ?>
                                                        <p class="cm_brand_detail mt-3">
                                                            <?php echo nl2br( esc_html( $brand_detail ) ); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                        <div id="brand-<?php echo $field['cm_year']; ?>" class="making-cm modal_trigger mt-3">
                                                            <button class="bg-white_btn active anime_hover-up rounded"><?php echo $cm['cm_brand_button']; ?></button>
                                                        </div>
                                                        <div class="movie-modal_box modal_box">
                                                            <div class="modal_bg"></div>
                                                            <div class="movie-modal_inner modal_inner">
                                                                <div class="movie-modal_block modal_block">
                                                                    <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" oncontextmenu="return false;">
                                                                        <source type="video/mp4" src="<?php echo $cm['cm_brand_movie']; ?>">
                                                                        <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                                    </video>
                                                                </div>
                                                                <div class="movie-modal_close modal_close">
                                                                    <div class="rounded text-center">
                                                                        閉じる<span class="pl-3">×</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/2006year.png" alt="2006年">
                        </h2>
                        <div class="ac-child anime-scroll">
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_11_ph.jpg" alt="キーチーとミキプルーン篇（2006年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_11_scene.jpg">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">キーチーとミキプルーン篇</h3>
                                        <div class="cap_des_tx">
                                            <p classs="mb-0">中井貴一さん紛するキーチーと子供たちが繰り広げる、まるで絵本から飛び出したようなファンタジータッチの楽しい作品です。<br>
                    1人4役をこなす中井貴一さんもさすがですが、子どもたちのかわいい演技にも注目！<p class="mb-0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/2004year.png" alt="2004年">
                        </h2>
                        <div class="ac-child anime">
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_09_ph.jpg" alt="食育篇（2004年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_09_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">食育篇</h3>
                                        <div class="cap_des_tx cap_des_txfirst">
                                            <p classs="mb-0">食生活の大切さを知り、毎日の食卓に「食育」を取り入れ、より豊かな食文化・食生活・健康を育てようというメッセージを込めた作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_10_ph.jpg" alt="プルーンクッキング篇（2004年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_10_scene.jpg">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">プルーンクッキング篇</h3>
                                        <div class="cap_des_tx">
                                            <p classs="mb-0">ミキプルーンを料理の材料として使えば、アイディア次第で毎日の食卓が健康メニューに。<br>
                    “プルーンクッキングはじめましょう！”と中井貴一さんが呼びかける作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/2002year.png" alt="2002年">
                        </h2>
                        <div class="ac-child anime">
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_01_ph.jpg" alt="注目のまと篇（2002年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_01_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">注目のまと篇</h3>
                                        <div class="cap_des_tx cap_des_txfirst">
                                            <p classs="mb-0">ガーデンパーティーでミキプルーンを取り出す中井貴一さん。 ふと気付くといつの間にか人だかり。「これは何？」と英語・スペイン語、そしてイタリア語・中国語までも飛び交い、注目のまとになってしまった中井さんとミキプルーン。逃げ惑いながら「This is myミキプルーン」と必死に説明するもどこまでも追いかけられてしまうという、コミカルでユーモラスな作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_02_ph.jpg" alt="農園主現る篇（2002年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_02_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">農園主現る篇</h3>
                                        <div class="cap_des_tx">
                                            <p classs="mb-0">「ミキプルーン」のふるさとカリフォルニアのミキプルーン農園で撮影した作品です。 2002年夏、たわわに実る季節に「ミスターミキプルーン」中井貴一さんが農園を訪れ、プルーンの実を一粒一粒大切に育てる農園主との出会いをコミカルなタッチで描きました。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/2000year.png" alt="2000年">
                        </h2>
                        <div class="ac-child anime">
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_03_ph.jpg" alt="プルーンの夢篇（2002年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_03_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">プルーンの夢篇</h3>
                                        <div class="cap_des_tx cap_des_txfirst">
                                            <p classs="mb-0">ミキプルーン農園でいつの間にか気持ちが良くなって、ウトウトとしてしまった中井貴一さん。 夢の中にはプルーンの花、ダイナミックなプルーンの収穫の様子が 浮かんできます。そしていよいよミキプルーンを食べようとするところで、どこからか「キイチ～」という声が聞こえてくるという、 2000年の作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <div id="cm-2000" class="modal_trigger">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm-04eye.jpg" alt="プルーンの苗木篇（2000年）">
                                            </div>
                                            <div class="movie-modal_box modal_box">
                                                <div class="modal_bg"></div>
                                                <div class="movie-modal_inner modal_inner">
                                                    <div class="movie-modal_block modal_block">
                                                        <video class="position-relative w-100" preload="none" controls="controls" webkit-playsinline="" playsinline="" controlslist="nodownload" poster="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm-04eye.jpg" oncontextmenu="return false;">
                                                            <source type="video/mp4" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/movie/04.mp4">
                                                            <p class="mb-0">ご利用のブラウザは動画の再生に対応していません。</p>
                                                        </video>
                                                    </div>
                                                    <div class="movie-modal_close modal_close">
                                                        <div class="rounded text-center">
                                                            閉じる<span class="pl-3">×</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_04_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">プルーンの苗木篇</h3>
                                        <div class="cap_des_tx">
                                            <p classs="mb-0">ミキは、自社農園であるカリフォルニアのミキプルーン農園で、 多くの人々の愛情に育まれたプルーンを、1本1本大切に育てています。春には農園一面が 真っ白に埋め尽くされるほどに、咲き乱れるプルーンの花。 そのプルーンの花に焦点をあてた作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cm-section anime">
                        <h2 class="cm-year ac-parent anime-scroll">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/1997year.png" alt="1997年">
                        </h2>
                        <div class="ac-child anime">
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_05_ph.jpg" alt="カフェ篇（1997年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_05_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">カフェ篇</h3>
                                        <div class="cap_des_tx cap_des_txfirst">
                                            <p classs="mb-0">コミカルなおまわりさんと中井貴一さんのやりとりがユーモラスなこの作品は、 フランスで撮影されました。ミキプルーンの魅力を、フランスでもみんなに説明 しようとする中井さんですが、騒ぎは広まってしまい、そこにおまわりさんが・・・ 1997年の作品です。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cm-cont">
                                <div class="cm-movie cm-movie_end cm-movie-cap">
                                    <div class="cm-cap">
                                        <div class="cm-cap_image">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_06_ph.jpg" alt="市場篇（1997年）">
                                        </div>
                                        <div class="cm-cap_scene">
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/image/cm/cm_06_scene.png">
                                        </div>
                                    </div>
                                    <div class="cm_title cm-cap_title">
                                        <h3 class="cm_title_tx cap_title_tx">市場篇</h3>
                                        <div class="cap_des_tx">
                                            <p classs="mb-0">フランスのとある市場でプルーンを見つけた中井貴一さん。 西アジアのコーカサス地方が原産地といわれているプルーンは、南フランスを経由して、現在の一大産地 カリフォルニアへと伝播されていきます。その期間はおよそ2000年、プルーンは時を越え、 愛されているフルーツなのです。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main><!-- #main -->
</div><!-- #primary -->
<style>
@media screen and ( max-width:768px){
	.other-banner {
        margin-top: 3%;
    }
}
@media screen and ( max-width:420px){
	.other-banner {
        margin-top: 6%;
    }
}
</style>
<?php
get_footer();