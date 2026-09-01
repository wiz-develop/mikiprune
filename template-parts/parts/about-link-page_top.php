<?php
    $about_page = get_page_by_path('about');
    $about_page_id = $about_page->ID;
    $content_list = CFS()->get('content_list', $about_page_id);
    if ($content_list) :
?>
<div class="widget_type_page_top_link screen_widget_key_page_top_link mb-5">
    <div class="widget_content container">
        <nav class="d-flex justify-content-start justify-content-lg-center align-items-center flex-wrap">
            <?php
                foreach ($content_list as $content) :
                    $content_url = $content['content_url'];
                    $content_tit = $content['content_tit'];
            ?>
            <a class="top_link d-inline-block mb-2" href="<?php echo $content_url; ?>"><?php echo $content_tit; ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</div>
<?php endif; ?>
