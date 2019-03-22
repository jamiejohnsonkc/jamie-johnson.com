<?php
/**
* Template Name: WHY ME
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
					
			<?php get_template_part( 'panel-content/panel', 'evidence__splash' ); ?>
				<?php get_template_part( 'panel-content/panel', 'evidence__summary' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__outcome-driven' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__value' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__modern' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__optimize' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__endorsements' ); ?>
			<?php get_template_part( 'panel-content/panel', 'evidence__atypical' ); ?>
			
			<?php get_template_part( 'panel-content/panel', 'why_me__cta' ); ?>
		</section>
		</main><!-- #main -->
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>