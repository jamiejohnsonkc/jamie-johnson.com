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
			<div class="pagination services-splash__pagination">i provide</div>
			<h1 class="title">Comprehensive Marketing Solutions</h1>
			<h2 class="title-sub services-splash__title-sub">Modern approaches delivered with old-fashioned values.</h2>
			
			<a href="#services-intro" class="button ui-button__jump services-splash__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
		</div>
		<div class="background__image--cover services-splash__image-container">
			<img src="/wp-content/uploads/2019/06/hero-20.jpg" class="ring-for-service" alt="image of old-fasioned door bell surrounded by text that reads please ring for service">
		</div>
	</article>
</section>