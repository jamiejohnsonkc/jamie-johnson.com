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
<section class="splash__container">
	<article class="homepage-article__container bgme">

		<div class="container container--splash-homepage" id="home__splash">
			<div class="home__splash-header_container">
			<div class="home__splash_greeting">Hi!</div>
				<div class="home__splash_headline">I'm Jamie</div>
			</div>
			<!-- <div class="home__splash-label_container">
				<div class="splash__label splash__label--head">i am a professional <br class="home-splash__label-head_br--tablet-wide"></div>
				<div class="splash__label splash__label--item">designer</div>
				<div class="splash__label splash__label_divider">&#x000B7 </div>
				<div class="splash__label splash__label--item">developer</div>
				<div class="splash__label splash__label_divider">&#x000B7;</div>
				<div class="splash__label splash__label--item splash__label_item--desktop">producer</div>
				<div class="splash__label splash__label_divider splash__label_divider--desktop">&#x000B7;</div>
				<div class="splash__label splash__label--item">manager</div>
				<div class="splash__label splash__label_divider">&#x000B7;</div>
				<div class="splash__label splash__label--item splash__label_item--tablet-wide">analyst</div>
				<div class="splash__label splash__label_divider splash__label_divider--tablet-wide">&#x000B7;</div>
				<div class="splash__label splash__label--item">strategist</div>
				<div class="splash__label splash__label_divider">&#x000B7;</div>
				<div class="splash__label splash__label--emph">marketer</div>
			</div> -->

			<h1 class="home__splash-label_container">
				<span class="splash__label splash__label--head">i am a professional <br class="home-splash__label-head_br--tablet-wide"></span>
				<span class="splash__label splash__label--item">designer</span>
				<span class="splash__label splash__label_divider">&#x000B7 </span>
				<span class="splash__label splash__label--item">developer</span>
				<span class="splash__label splash__label_divider">&#x000B7;</span>
				<span class="splash__label splash__label--item splash__label_item--desktop">producer</span>
				<span class="splash__label splash__label_divider splash__label_divider--desktop">&#x000B7;</span>
				<span class="splash__label splash__label--item">manager</span>
				<span class="splash__label splash__label_divider">&#x000B7;</span>
				<span class="splash__label splash__label--item splash__label_item--tablet-wide">analyst</span>
				<span class="splash__label splash__label_divider splash__label_divider--tablet-wide">&#x000B7;</span>
				<span class="splash__label splash__label--item">strategist</span>
				<span class="splash__label splash__label_divider">&#x000B7;</span>
				<span class="splash__label splash__label--emph">marketer</span>
</h1>


			<a class="ui-button__jump home-splash__link--jump" href="#placeholder" title="go to next"><?php get_template_part( 'buttons/button', 'jump' ); ?></a>


		</div>

		<div class="container-hero__img--mobile" id="profile-small"><img src="/wp-content/uploads/2019/01/me-small.jpg" alt="profile pic"></div>

	</article>
</section>