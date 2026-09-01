<?php
/**
 * The template for displaying all single posts
 * Template Name: 会社概要・沿革
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>
<div id="app_func" class="app_func app_func_page func_key_page pt-4 pb-5">
	<main>
		<div id="app_func_screen" class="app_func_screen app_func_screen_company screen_key_company py-5">
			<?php get_template_part('template-parts/parts/about-link-page_top'); ?>
			<div class="widget_type_page_title screen_widget_key_page_title">
				<div class="widget_content container">
					<h1 class="mb-0">
						<?php if(get_locale() == 'ja') : ?>
							<?php the_title(); ?>
							<?php if(is_page('company')): ?>
							<span class="d-block">Company</span>
							<?php elseif(is_page('history')): ?>
							<span class="d-block">History</span>
							<?php endif; ?>
						<?php elseif(get_locale() == 'en_US') : ?>
						<?php the_title(); ?>
						<?php endif; ?>
					</h1>
				</div>
			</div>
			<div class="widget_type_company_about screen_widget_key_company_about py-5 bg-white">
				<div class="widget_content container">
					<div class="widget_body">
						<section class="anime">
							<?php if(is_page('history')): ?>
								<?php if(get_locale() == 'ja') : ?>
								<h2 class="company-catch anime-scroll">企業としても<span>健康でいられるように</span></h2>
								<?php endif; ?>
							<?php endif; ?>
							<div class="item-list anime">
								<?php
									$fields = CFS()->get('item_list');
									foreach ($fields as $field) :
								?>
								<div class="item-list__content anime-scroll d-lg-flex d-sm-block flex-wrap">
									<div class="item_tit"><p class="mb-0"><?php echo $field['item_tit']; ?></p></div>
									<div class="item_about"><p class="mb-0"><?php echo $field['item_about']; ?></p></div>
								</div>
								<?php endforeach; ?>
							</div>
							<?php //get_template_part('template-parts/parts/about-link_btn');?>
						</section>
					</div>
				</div>
			</div>
			<?php get_template_part('template-parts/parts/about-link-page_bottom'); ?>
		</div>
	</main><!-- #main -->
</div><!-- #primary -->
<?php
get_footer();
