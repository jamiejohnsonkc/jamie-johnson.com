<?php

/**
 * Template Name: SERVICES 
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package understrap
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
get_header();
?>
<div class="wrapper" id="full-width-page-wrapper">
	<main class="site-main" id="main" role="main">
		<?php get_template_part('panel-content/panel', 'services__splash'); ?>
		<?php get_template_part('panel-content/panel', 'services__intro'); ?>
		<?php get_template_part('panel-content/panel', 'core-services'); ?>
		<?php get_template_part('panel-content/panel', 'a-la-carte'); ?>
		<?php get_template_part('panel-content/panel', 'flexible'); ?>
		<?php get_template_part('panel-content/panel', 'commitment'); ?>
		<?php get_template_part('panel-content/panel', 'focus'); ?>
		<?php get_template_part('panel-content/panel', 'services__cta'); ?>
	</main>
</div>
<?php get_footer(); ?>