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
			<!-- <div class="pagination services-splash__pagination">i provide</div> -->
			<h1 class="title">
				<span class="title">grow</span>
				<br class="br__services-splash">
				<span class="title">engage</span>
				<br class="br__services-splash">
				<span class="title">convert</span>
				<br class="br__services-splash">
				<span class="title">deliver</span>
				</h1>
			<h2 class="services-splash__sub">marketing <span class="services-splash__separator">&#x2022;</span> marcom <span class="services-splash__separator">&#x2022;</span> operations <span class="services-splash__separator">&#x2022;</span> digital</h2>
			
			<a href="#services-intro" class="button ui-button__jump services-splash__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
		</div>
		<div class="background__image--cover services-splash__image-container">
			<img src="/wp-content/uploads/2019/06/hero-20.jpg" class="ring-for-service" alt="image of old-fasioned door bell surrounded by text that reads please ring for service">
		</div>
	</article>
</section>