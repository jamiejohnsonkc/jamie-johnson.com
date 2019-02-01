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
						<?php get_template_part( 'panel-content/panel', 'mygoal' ); ?>
						<?php get_template_part( 'panel-content/panel', 'backstory' ); ?>
						<?php get_template_part( 'panel-content/panel', 'personal' ); ?>
						<?php get_template_part( 'panel-content/panel', 'aboutme-cta' ); ?>
						

<?php get_template_part( 'panel-content/panel', 'about-cta' ); ?>
	</article>
</main><!-- #main -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
