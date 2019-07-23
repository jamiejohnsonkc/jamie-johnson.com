<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content afterP`
 *
 * @package understrap
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod('understrap_container_type');
?>

<?php get_template_part('sidebar-templates/sidebar', 'footerfull'); ?>

<div class="footer footer__wrapper">


	<footer class="footer-container site-footer footer-content-wrapper">
		<div class="row">
			<!-- <div class="col-12"></div> -->

			<div class="footer-content col-lg-3 col-md-6 col-sm-12">
				<div class="footer__content-container--contact">
					<div class="footer__contact--bug">
						<a href="/index" class="footer__contact--logo-container"><img src="/wp-content/uploads/2019/05/jj-logo.svg" class="style-svg logo-site-footer" alt="jjohnson logo"></a>
						<div class="footer__contact--moniker">
							<a href="/contact" class="footer__contact--name footer__text-link">jamie johnson</a>
							<!-- <div class="footer__contact--tag">lorem ipsum dolemite</div> -->
						</div>
					</div>
					<div class="footer__contact--tag">Kansas City (Lenexa, KS)</div>
					<a class="footer__contact--phone footer__text-link" href="tel:913.586.8042">913.586.8042</a>
					<a href="mailto:jamie@jamie-johnson.com?Subject=Marketing%20Extraordinire" target="_top" class="button footer__contact--button footer__text-link">Drop Me A Note</a>
				</div>
			</div>

			
		
			<div class="footer-content col-lg-2 col-md-6 col-sm-12">
				<div class="headline headline__item footer__head">End-to-end Marketing Expertise</div>
				<ul class="list footer__list">
					<li class="list-item list-item__item footer__list-item"><a href="/services#modern-marketing-management" class="footer__text-link">Strategic Marketing Management</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#communications" class="footer__text-link">Digital & Analog Communications</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#operations" class="footer__text-link">Marketing Operations</a></li>
				</ul>
			</div>
			<div class="footer-content col-lg-2 col-md-6 col-sm-12">
				<div class="headline headline__item footer__head">Comprehensive Capabilitities</div>
				<ul class="list footer__list">
					<li class="list-item list-item__item footer__list-item"><a href="/services#alacarte" class="footer__text-link">Mobile + Web Development</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#alacarte" class="footer__text-link">Strategic Marketing Management</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#alacarte" class="footer__text-link">Integrated Communications</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#alacarte" class="footer__text-link">Management & Control</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#alacarte" class="footer__text-link">Strategy & Implementation</a></li>
				</ul>
			</div>
			<div class="footer-content col-lg-2 col-md-6 col-sm-12">
				<div class="headline headline__item footer__head">Flexible & Agile Services</div>
				<ul class="list footer__list">
					<li class="list-item list-item__item footer__list-item"><a href="/services#flexible" class="footer__text-link">Execution</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#flexible" class="footer__text-link">Planning</a></li>
					<li class="list-item list-item__item footer__list-item"><a href="/services#flexible" class="footer__text-link">Consulting</a></li>
				</ul>
			</div>
			<div class="footer-content col-lg-3 col-md-6 col-sm-12 footer-content__icons">
				<div class="headline headline__item footer__head">Contact</div>
				<div class="contact-icons">


					<div class="footer-contact-icon-container">
						<div class="footer__subhead">write</div>
						<!-- //! email -->
						<div class="footer-contact-icon">
							<a href="mailto:jamie@jamie-johnson.com?Subject=Marketing%20Extraordinire" target="_top" class="button footer__text-link">
								<img src="/wp-content/uploads/2019/05/contact-07.svg" class="style-svg icon-email" alt="...">
							</a>
						</div>
						<!-- //! what's app -->

						<!-- <div class="footer-contact-icon">
						<a><img src="/wp-content/uploads/2019/05/contact-01.svg" class="style-svg icon-uncertain" alt="..."></a>
					</div> -->
					<!-- //! Messenger -->
					<div class="footer-contact-icon">
						<a href="https://www.facebook.com/jamie.johnson.37051579" class="button footer__text-link">
							<img src="/wp-content/uploads/2019/05/contact-02.svg" class="style-svg icon-messenger" alt="...">
						</a>
					</div>

				</div>



				<div class="footer-contact-icon-container">
					<div class="footer__subhead">follow</div>
					<div class="footer-contact-icon">
						<a href="/" class="button footer__text-link"><img src="/wp-content/uploads/2019/05/contact-04.svg" class="style-svg icon-reddit" alt="..."></a>
						</div>
					<div class="footer-contact-icon">
						<a href="https://www.linkedin.com/in/jamiejohnsonkc/" class="button footer__text-link"><img src="/wp-content/uploads/2019/07/linkedin.svg" class="style-svg icon-linkedin" alt="..."></a>
						</div>

						<div class="footer-contact-icon">
						<a href="https://www.quora.com/profile/Jamie-Johnson-16" class="button footer__text-link"><img src="/wp-content/uploads/2019/05/contact-03.svg" class="style-svg icon-quora" alt="...">
						</a>
						</div>
						<div class="footer-contact-icon">
						<a href="https://github.com/jamiejohnsonkc" class="button footer__text-link"><img src="/wp-content/uploads/2019/07/github_glyph.svg" class="style-svg icon-github--glyph" alt="..."></a>
						</div>
					</div>
				<div class="footer-contact-icon-container">
					<div class="footer__subhead">speak</div>
					<div class="footer-contact-icon">
						<a href="tel:913.586.8042" class="button footer__text-link"><img src="/wp-content/uploads/2019/07/mobile.svg" class="style-svg icon-mobile" alt="..."></a>
					</div>
					<!-- <div class="footer-contact-icon">
						<a href="/" class="button footer__text-link"><img src="/wp-content/uploads/2019/07/skype.svg" class="style-svg icon-quora" alt="...">
						</a>
						</div> -->
				</div>
				<!-- <ul class="list footer__list">
				<li class="list-item list-item__item footer__list-item footer__nav">services</li>
				<li class="list-item list-item__item footer__list-item footer__nav">background</li>
				<li class="list-item list-item__item footer__list-item footer__nav">why me</li>
			</ul> -->
			</div>
		</div>
		<div class="col-12 footer__site-info">
			<div class="site-info">&copy; 2018. All Rights Reserved. Privacy Policy</div>
		</div>
</div>






</footer><!-- #colophon -->

</div>


</div> <!-- #page we need this extra closing tag here -->


<?php wp_footer(); ?>

</body>

</html>