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
<section class="section section--splash section--background-splash">
	<!-- <article class="article__wrapper-fluid splash__article--wrapper background-splash__article-wrapper bgshoes"> -->
		<article>
		<div class="container container--splash container--splash-background background-splash__container">
			<div class="pagination pagination--splash background__splash-pagination">about me</div>
			<h1 class="title__header background__splash--title ">I'm a salty marketing veteran who knows a thing or two.</h1>
			<h2 class="title-sub background__splash--title-sub">Serious about marketing. But not too serious.</h2>
			<a href="#brands" class="button ui-button__jump background-splash__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
		</div>
		<div class="container-hero__img--mobile container-hero__img-mobile_shoes" id="my-shoes"><img src="/wp-content/uploads/2019/05/shoes-smallv2.png" alt="clown shoes image" id="shoes-img"/></div>
	</article>
</section>