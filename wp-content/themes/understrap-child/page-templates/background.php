<?php
/**
* Template Name: BACKGROUND
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
		<section id="section__about-me">
			<?php get_template_part( 'panel-content/panel', 'background__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__summary' ); ?>
			<?php get_template_part( 'panel-content/panel', 'background__brands' ); ?>
			<?php get_template_part( 'panel-content/panel', 'background__categories' ); ?>
			<?php get_template_part( 'panel-content/panel', 'background__personal' ); ?>
			<?php get_template_part( 'panel-content/panel', 'background__cta' ); ?>
		</section>
		</main><!-- #main -->
		</div><!-- Wrapper end -->
<?php get_footer(); ?>