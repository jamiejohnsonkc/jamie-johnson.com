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
	<article class="article__wrapper article__wrapper--fluid article__wrapper--black mo__article-wrapper">
		<div class="container container--mymo text--white mo__container">

			<h1 class="header pagination pagination--homepage  pagination--strengths-intro pagination--black mo__pagination">My M.O.</h1>
			<div class="mo__psst">&mdash;pssst!</div>
			<h2 class="headline headline__header headline--intro">Don't tell anyone, but I've always loathed khakis</h2>
			<h3 class="deck deck__header header__deck--white deck--intro mo__deck">I grew up on the business-side, but I've always had a festish for creativity.</h3>
			<p class="copy__header mo__header-copy">So I'm a fanatic when it comes to technology and the work, but business always comes first.</p>

			<h4 class="list-head list-head__block"></h4>
			<div class="mo__diagram-container">
				<div class="mo__diagram-box">
					<div class="mo__diagram-big-digit">1</div>
					<div class="mo__diagram-copy">The right solutions</div>
				</div>
				<div class="mo__diagram-box">
					<div class="mo__diagram-big-digit">2</div>
					<div class="mo__diagram-copy">The right execution</div>
				</div>
				<div class="mo__diagram-box">
					<div class="mo__diagram-big-digit">3</div>
					<div class="mo__diagram-copy">The right scale</div>
				</div>
			</div>


			<a href="#webdev-expertise" class="button ui-button ui-button__expand ui-button__expand--primary-white expand-button__expertise expand-button--webdev mo__expand-button" title="read more"><?php get_template_part('buttons/button', 'expand'); ?></a>
			<div class="container__accordion mo__accordian-container" id="webdev-expertise">
				<h4 class="headline headline__item">They'll make you stuff. I'll make sure your business gets what it needs. No more. No less.</h4>
				<p class="block copy copy__block">My goal is to help you understand, identify and engage the ideal marketing mix for your particular needs &mdash; whether its an interstellar marketing campaign or a coffee mug. It has to make sense for your business.</p>
			</div>


		</div>
		<a class="link link__text link__text--white link__text--center mo__text-link">> See where I've been</a>
	</article>
</section>