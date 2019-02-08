<?php
/**
* Template Name: my value
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
			<?php get_template_part( 'panel-content/panel', 'my_value__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'my_value__advance_business_objectives' ); ?>
			<?php get_template_part( 'panel-content/panel', 'my_value__grow_competitive_advantage' ); ?>
			<?php get_template_part( 'panel-content/panel', 'my_value__modernize_marketing_operations' ); ?>
			<?php get_template_part( 'panel-content/panel', 'my_value__optimize_marketing_performance' ); ?>
			<?php get_template_part( 'panel-content/panel', 'my_value__help-cta' ); ?>
		</section>
		</main><!-- #main -->
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>