<?php
/**
* Template Name: marketing
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
		
			<?php get_template_part( 'panel-content/panel', 'marketing__splash' ); ?>
		<?php get_template_part( 'panel-content/panel', 'marketing__intro' ); ?>
			<?php get_template_part( 'panel-content/panel', 'marketing__better' ); ?>
				<?php get_template_part( 'panel-content/panel', 'marketing__pov' ); ?>

	
			<?php get_template_part( 'panel-content/panel', 'marketing__approach' ); ?>
			<?php get_template_part( 'panel-content/panel', 'marketing__cta' ); ?>
	
		</main><!-- #main -->
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>