<?php
/**
 * container Splash template.
 *
 * @package understrap
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<section class="section section--splash section--justification-splash">
	<article class="article__wrapper--fluid splash__article--wrapper article--services-splash">
		<div class="services-splash__container">
			<h1 class="pagination services-splash__pagination">i provide</h1>
			<h2 class="title">Comprehensive Marketing Solutions</h2>
			<h3 class="title-sub services-splash__title-sub">Modern approaches delivered with old-fashioned values.</h3>
			
			<a href="#services-summary" class="button ui-button__jump services-splash__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
		</div>
		<div class="background__image--cover services-splash__image-container">
			<img src="/wp-content/uploads/2019/06/hero-20.jpg" class="ring-for-service" alt="...">
		</div>
	</article>
</section>