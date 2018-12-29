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
	<article>
						<?php get_template_part( 'panel-content/panel', 'aboutsplash' ); ?>
						<?php get_template_part( 'panel-content/panel', 'hybrid' ); ?>
						<?php get_template_part( 'panel-content/panel', 'brands' ); ?>
						<?php get_template_part( 'panel-content/panel', 'categories' ); ?>
						<?php get_template_part( 'panel-content/panel', 'expertise' ); ?>
						<?php get_template_part( 'panel-content/panel', 'webdev' ); ?>
						<?php get_template_part( 'panel-content/panel', 'personal' ); ?>
						<?php get_template_part( 'panel-content/panel', 'aboutcta' ); ?>
</article>
</main><!-- #main -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
