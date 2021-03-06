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
<section class="section personal__section" id="personal">
	<article class="article__wrapper content__wrapper about-me personal">
		<div class="image__container--fam" id="fam-container"><img src="/wp-content/uploads/2019/05/famv3.jpg" alt="profile pic" id="fam-img"></div>

		<div class="container background about-me__container_personal">
			<div class="personal__about-me_content personal__content-container-wrapper">
				<div class="personal__content-container">
					<div class="pagination pagination--personal personal__pagination">A Bit About Me</div>
					<h2 class="headline headline__header personal__headline">Proud Dad, <br class="br"><br>Lucky Husband</h2>
					<div class="copy__container personal__copy-container">
						<p class="copy copy__header copy--personal personal__copy"></p>
						<p class="copy copy__header copy--personal personal__copy">Music junky. Recovering gamer. Tech enthusiast. Involuntary landlord. Overzealous DIY'er. Aspiring photographer. Struggling greens keeper. Lifetime learner. But mostly (and thankfully) Dad and husband.</p>
					</div>
				</div>
			</div>



		</div>
		<a href="#background__cta" class="button ui-button__jump categories__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
	</article>
</section>