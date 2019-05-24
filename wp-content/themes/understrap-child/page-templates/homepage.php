<?php
/**
* Template Name: HOMEPAGE

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
	
			<?php get_template_part( 'panel-content/panel', 'home_page__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'home_page__placeholder' ); ?>
			<?php get_template_part( 'panel-content/panel', 'home_page__claim' ); ?>
			<?php get_template_part( 'panel-content/panel', 'home_page__hybrid' ); ?>
			<?php get_template_part( 'panel-content/panel', 'home_page__stats' ); ?>
			<?php get_template_part( 'panel-content/panel', 'home_page__mymo' ); ?>
			<!-- php get_template_part( 'panel-content/panel', 'home_page__strengths' );  -->
			<?php get_template_part( 'panel-content/panel', 'home_page__proof' ); ?>
<?php get_template_part( 'panel-content/panel', 'home_page__cta' ); ?>

		</main><!-- #main -->
		<div class="scroll-top" id="scroll-up" onclick="topFunction()"><a href="" title=""><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/chevup.svg" alt="" class="style-svg" id="scroll-top"/></a></div>
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>									