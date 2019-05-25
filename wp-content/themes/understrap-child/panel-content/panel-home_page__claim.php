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
<article class="article__wrapper article__wrapper--overlap homepage-claim__wrapper claim__article-wrapper">
	<div class="homepage-claim__background-image--container claim__background-image-container">
		<img src="//jamiejohnsonmev2.test/wp-content/uploads/2019/04/arrow-up.svg" class="style-svg arrow-up-svg" alt="...">
	</div>
	<div class="homepage-claim__container homepage-claim__container--content-wrapper claim__container">
		<h1 class="pagination pagination--homepage pagination--claim pagination--overlay claim__pagination ">What I Do</h1>
		<div class="homepage-claim__container homepage-claim__container--content claim__content-container">
			<div class="headline headline__header title title--overlay claim__title">I <span class="headline headline__header title title--overlay claim__title--emph">help</span> young and emerging businesses <span class="headline headline__header title title--overlay claim__title--emph">optimize marketing</span> strategy, operations and activities <span class="headline headline__header title title--overlay claim__title--emph">for performance</span> and <span class="headline headline__header title title--overlay claim__title--emph">growth</span></div>
		</div>
	</div>
	<a href="#services-summary" class="button ui-button ui-button__jump ui-button__jump--primary-white claim__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
</article>