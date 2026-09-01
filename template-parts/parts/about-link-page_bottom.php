<?php
    $about_page = get_page_by_path('about');
    $about_page_id = $about_page->ID;
    $content_list = CFS()->get('content_list', $about_page_id);
    if ($content_list) :
?>
    <div class="widget_type_page_bottom_link screen_widget_key_page_bottom_link mt-5">
        <div class="widget_content container">
            <p class="mb-0 text-center fw-bold fs-6"><span class="bottom_link__title px-3 pb-1">企業情報</span></p>
            <nav class="d-flex justify-content-start align-items-center flex-wrap mt-4">
                <?php
                    foreach ($content_list as $content) :
                        $content_url = $content['content_url'];
                        $content_tit = $content['content_tit'];
                ?>
                <div class="bottom_link rounded anime_hover-up border">
                    <a href="<?php echo $content_url; ?>">
                        <div class="bottom_link__item d-flex align-items-center">
                            <div class="bottom_link__item__icon">
                                <img src="<?php echo $content['content_img']; ?>" alt="<?php echo $content_tit; ?>">
                            </div>
                            <div class="bottom_link__item__name">
                                <p class="mb-0"><?php echo $content_tit; ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>
<?php endif; ?>
