<?php
/**
 * Template Name: canvas
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

<div id="svgContainer">
	<div class="test1" id="test1">
		<img src="http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/app-icon-design_-4.svg" alt="" class="wp-image-23 style-svg"/></div>

	<div class="test2" id="test2">
		<img src="http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/app-icon-design_-2.svg" alt="" class="style-svg"/></div>

	<div class="test3" id="test3"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/single-latop-art.svg" alt="" class="style-svg"/></div>

</div>

<div class="wrapper" id="full-width-page-wrapper">

<h1>confirmation</h1>
</div><!-- Wrapper end -->

<?php get_footer(); ?>
