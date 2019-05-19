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
<!-- <article class="article__wrapper  basic homepage homepage__my-mo">
	<div class="container homepage__my-mo__container"> -->
<section>
	<article class="article__wrapper article__wrapper--fluid article__wrapper--black">
		<div class="container container--mymo text--white">

			<h1 class="header pagination pagination--homepage  pagination--strengths-intro pagination--black">My M.O.</h1>
			<h2 class="headline headline__header headline--intro">Pssst. Here's a secret: I've always secretly loathed khakis</h2>
			<h3 class="header deck header__deck header__deck--white deck--intro">I grew up on the business side of marketing. So while I'm a fan of technology and creativity, business comes first.</h3>

			<h4 class="list-head list-head__block"></h4>
			<ul class="list list--big-digits">
				<li class="list-item list-item__header list-item--intro"><span class="list-item list-item__header list-item--intro-digit">1.</span>The right solutions</li>
				<li class="list-item list-item__header list-item--intro"><span class="list-item list-item__header list-item--intro-digit">2.</span>The right execution</li>
				<li class="list-item list-item__header list-item--intro"><span class="list-item list-item__header list-item--intro-digit">3.</span>The right scale</li>
			</ul>


			<a href="#webdev-expertise" class="button ui-button ui-button__expand ui-button__expand--primary-white expand-button__expertise expand-button--webdev" title="read more"><?php get_template_part('buttons/button', 'expand'); ?></a>
			<div class="container__accordion container__accordion--mymo" id="webdev-expertise">
			<h4 class="headline headline__item">They'll make you stuff. I'll make sure your business gets what it needs. No more. No less.</h4>	
			<p class="block copy copy__block">My goal is to help you understand, identify and engage the ideal marketing mix for your particular needs &mdash; whether its an interstellar marketing campaign or a coffee mug. It has to make sense for your business.</p>
			</div>


		</div>
		<a class="link link__text link__text--white link__text--center link__text--martop">See where I've been</a>
	</article>
</section>