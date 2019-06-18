<?php
/**
 *
 * @package understrap
 */
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>

<!-- <div class="better-carousel-item__image-container    better__image-container--table">    <img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-2-1.jpg" class="image--conference-table" alt="..."></div>
<div class="better-carousel-item__image-container    better__image-container--smoke">    <img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-14.jpg" class="image--smoke" alt="..."></div>
<div class="better-carousel-item__image-container    better__image-container--signature"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-4-1.jpg" class="image--signature" alt="..."></div>
<div class="better-carousel-item__image-container    better__image-container--hellya">   <img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-2-1.jpg" class="image--awesome" alt="..."></div> -->


<section>
	<article>
		<div class="body__container expertise__body-container">
			<div class="body-item__container--wrapper">
				<div class="body-item__container expertise-body-item--container">
					<div class="header__wrapper expertise__header-wrapper">
						<div class="header__container better-header__container">
							<h2 class="headline__header">Lorem Ipsum Healine</h2>
							<h3 class="deck deck__header">I help young and emerging businesses achieve outcomes critical for long-term success:</h3>

							<!-- <h3 class="deck deck__header">Operative marketing solutions at the right scale, scope and strategy.</h3> -->
							<!-- <p id="demo"></p> -->
						</div>

					</div>

					<div id="advantage" class="slide carousel-fade expertise__carousel" data-interval="false">
						<div class="carousel-controls expertise__carousel--controls">
							<ol class="carousel-indicators advantage-carousel-indicators expertise__carousel--indicators">
								<li data-target="#advantage" data-slide-to="0" class="expertise__carousel-indicator-list-item list-item active">Maximize Advantage</li>
								<!-- <li class="expertise__carousel--list-divider">&#x000B7</li> -->
								<li data-target="#advantage" data-slide-to="1" class="expertise__carousel-indicator-list-item list-item">Get Found</li>
								<!-- <li class="expertise__carousel--list-divider">&#x000B7</li> -->
								<li data-target="#advantage" data-slide-to="2" class="expertise__carousel-indicator-list-item list-item">Earn Trust</li>
								<li data-target="#advantage" data-slide-to="3" class="expertise__carousel-indicator-list-item list-item">Optimize Performance</li>
							</ol>
						</div>


						<div class="carousel-inner expertise__carousel--inner advantage-carousel__inner">
							<div class="expertise__carousel-item carousel-item active">
							<div class="carousel-item-wrap carousel-item-wrap-conference-table">
								<div class="carousel-item__header expertise-carousel-item__header header__maximize-advantage">
									<h3 class="headline__item expertise-carousel-item__headline">Maximize Advantage</h3>	
									<div class="better-carousel-item-overlay better-carousel-item-overlay__one"></div>
									<div class="better-carousel-item__image-container better__image-container--table"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/03/conftable.jpg" class="image--conference-table" alt="..."></div>

									<h4 class="subhead__item--vmar-1-1 expertise-carousel-item__subhead maximize-advantage-subhead">Designing, communicating, selling and delivering superior competitive value.</h4>
								</div>
								<ul class="list list--inset expertise-carousel-item__list">
									<li class="list-item list-item__item expertise-carousel-item__list-item">Value design</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Competitive auditing</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Business planning / marketing alignment</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Organizational alignment</li>
								</ul>
								</div>
								<a href="/" class="button ui-button__jump better-large-format__link--jump jump__link--black-white" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
		

							</div>
							<div class="expertise__carousel-item carousel-item carousel-item--start-conversations">
						<div class="carousel-item-wrap carousel-item-wrap--start-conversations">
								<div class="carousel-item__header expertise-carousel-item__header header__start-conversations">
									<h3 class="headline__item expertise-carousel-item__headline">Get Found</h3>
									<div class="better-carousel-item-overlay better-carousel-item-overlay__two"></div>
									<div class="better-carousel-item__image-container better__image-container--smoke"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-14.jpg" class="image--smoke" alt="..."></div>
									<h4 class="subhead__item--vmar-1-1 expertise-carousel-item__subhead">Get found then build rapport.</h4>
								</div>
								<ul class="list list--inset expertise-carousel-item__list">
									<li class="list-item list-item__item expertise-carousel-item__list-item">Mobile/Web responsive functionality</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Custom builds, Wordpress and custom theme development</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Site Design, protyping and build out</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">UX, site architecture and supporting graphics</li>
								</ul>
								</div>
							
								<a href="/" class="button ui-button__jump better-large-format__link--jump jump__link--white-outline" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
							
							</div>
							<div class="expertise__carousel-item carousel-item carousel-item--signature">
							<div class="carousel-item-wrap carousel-item-wrap-signature">
							<div class="better-carousel-item-overlay better-carousel-item-overlay__three"></div>
								<div class="carousel-item__header expertise-carousel-item__header header__earn-trust">
									<h3 class="headline__item expertise-carousel-item__headline">Earn Trust</h3>
									<div class="better-carousel-item__image-container better__image-container--signature"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-4-1.jpg" class="image--signature" alt="..."></div>
									<h4 class="subhead__item--vmar-1-1 expertise-carousel-item__subhead">Don't just position yourself. Demonstrate your value.</h4>
								</div>
								<ul class="list list--inset expertise-carousel-item__list">
									<li class="list-item list-item__item expertise-carousel-item__list-item">Mobile/Web responsive functionality</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Custom builds, Wordpress and custom theme development</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Site Design, protyping and build out</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">UX, site architecture and supporting graphics</li>
								</ul>
								</div>
								<a href="/" class="button ui-button__jump better-large-format__link--jump jump__link--black-white" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
							</div>

							<div class="expertise__carousel-item carousel-item carousel-item--awesome">
							<div class="carousel-item-wrap carousel-item-wrap-awesome">
								<div class="better-carousel-item-overlay better-carousel-item-overlay__four"></div>
								<div class="carousel-item__header expertise-carousel-item__header header__optimize-performance">
									<h3 class="headline__item expertise-carousel-item__headline">Optimize Performance</h3>
									<div class="better-carousel-item__image-container better__image-container--awesome"><img src="http://jamiejohnsonmev2.test/wp-content/uploads/2019/06/hero-2-1.jpg" class="image--awesome" alt="..."></div>
									<h4 class="subhead__item--vmar-1-1 expertise-carousel-item__subhead">Implementing best practices of marketing management, strategy and engagement into your operations.</h4>
								</div>
								<ul class="list list--inset expertise-carousel-item__list">
									<li class="list-item list-item__item expertise-carousel-item__list-item">Mobile/Web responsive functionality</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Custom builds, Wordpress and custom theme development</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">Site Design, protyping and build out</li>
									<li class="list-item list-item__item expertise-carousel-item__list-item">UX, site architecture and supporting graphics</li>
								</ul>
								</div>
								<a href="/" class="button ui-button__jump better-large-format__link--jump jump__link--white-outline" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
							</div>

						

							<a class="carousel-control-prev advantage__carousel-control-prev" href="#advantage" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon advantage-carousel-control-prev-icon" aria-hidden="false"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next advantage__carousel-control-next" href="#advantage" role="button" data-slide="next">
				<span class="carousel-control-next-icon advantage-carousel-control-next-icon" aria-hidden="false"></span>
				<span class="sr-only">Next</span>
			</a>
						</div>

					</div>
					<hr class="expertise-carousel-hr">
					<a href="/" class="button ui-button__jump better__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
				</div>
			
			</div>
		
	</article>
</section>