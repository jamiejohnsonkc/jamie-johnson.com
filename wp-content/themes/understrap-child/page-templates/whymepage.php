<?php
/**
* Template Name: why me
*
* Template for displaying a page without sidebar even if a sidebar widget is published.
*
* @package understrap
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>
<div class="wrapper" id="full-width-page-wrapper">
	<main class="site-main" id="main" role="main">
		<section id="section__why-me">
					
			<?php get_template_part( 'panel-content/panel', 'why_me__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'why_me__atypical' ); ?>
			<?php get_template_part( 'panel-content/panel', 'why_me__brands' ); ?>
			<?php get_template_part( 'panel-content/panel', 'why_me__categories' ); ?>
			
			<?php get_template_part( 'panel-content/panel', 'why_me__cta' ); ?>
		</section>
		</main><!-- #main -->
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>