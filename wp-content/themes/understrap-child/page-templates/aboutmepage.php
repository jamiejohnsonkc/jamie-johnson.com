<?php
/**
* Template Name: aboutmepage
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
		<section>
			<?php get_template_part( 'panel-content/panel', 'about_me__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'about_me__backstory' ); ?>
			<?php get_template_part( 'panel-content/panel', 'about_me__personal' ); ?>
			<?php get_template_part( 'panel-content/panel', 'about_me__cta' ); ?>
		</section>
		</main><!-- #main -->
		</div><!-- Wrapper end -->
<?php get_footer(); ?>