<?php
/**
 * Template Name: modusoperandi
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
						<?php get_template_part( 'panel-content/panel', 'smm' ); ?>
						<?php get_template_part( 'panel-content/panel', 'scv' ); ?>
						<?php get_template_part( 'panel-content/panel', 'customers' ); ?>
						<?php get_template_part( 'panel-content/panel', 'money' ); ?>
						<?php get_template_part( 'panel-content/panel', 'helpcta' ); ?>
</main><!-- #main -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
