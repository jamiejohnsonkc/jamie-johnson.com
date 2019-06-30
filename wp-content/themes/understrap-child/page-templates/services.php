<?php
/**
* Template Name: SERVICES 
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
		
			<?php get_template_part( 'panel-content/panel', 'services__splash' ); ?>
			<?php get_template_part( 'panel-content/panel', 'core-services' ); ?>
			<!-- ?php get_template_part( 'panel-content/panel', 'services__expertise' ); ?> -->
			<!-- ?php get_template_part( 'panel-content/panel', 'services__intro' ); ?> -->
			<!-- ?php get_template_part( 'panel-content/panel', 'services__summary' ); ?> -->
			<!-- ?php get_template_part( 'panel-content/panel', 'services__expertise' ); ?> -->
				<?php get_template_part( 'panel-content/panel', 'a-la-carte' ); ?>
		<!-- ?php get_template_part( 'panel-content/panel', 'services__webdev' ); ?>
		?php get_template_part( 'panel-content/panel', 'services__marcom' ); ?>
		?php get_template_part( 'panel-content/panel', 'services__smm' ); ?> -->
	<!-- 		php get_template_part( 'panel-content/panel', 'services__services' ); ?> -->
	
			<?php get_template_part( 'panel-content/panel', 'commitment' ); ?>
			<?php get_template_part( 'panel-content/panel', 'services__cta' ); ?>
	
		</main><!-- #main -->
		</div><!-- Wrapper end -->
		<?php get_footer(); ?>