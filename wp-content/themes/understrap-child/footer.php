<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
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
						<div class="footer__contact--logo-container"><img src="/wp-content/uploads/2019/05/jj-logo.svg" class="style-svg logo-site-footer" alt="jjohnson logo"></div>
						<div class="footer__contact--moniker">
							<div class="footer__contact--name">jamie johnson</div>
							<!-- <div class="footer__contact--tag">lorem ipsum dolemite</div> -->
						</div>
					</div>
					<div class="footer__contact--tag">Kansas City (Lenexa, KS)</div>
					<div class="footer__contact--phone">913-207-6966</div>
					<div class="button footer__contact--button">Drop Me A Note</div>
				</div>
			</div>

			<div class="footer-content col-lg-3 col-md-6 col-sm-12">
				<div class="headline headline__item footer__head">Areas of Expertise</div>
				<ul class="list footer__list">
					<li class="list-item list-item__item footer__list-item">Web Design & Development</li>
					<li class="list-item list-item__item footer__list-item">Integrated Communications</li>
					<li class="list-item list-item__item footer__list-item">Marketing Operations</li>
					<li class="list-item list-item__item footer__list-item">Strategic Marketing Management</li>

				</ul>
			</div>
			<div class="footer-content col-lg-3 col-md-6 col-sm-12">
				<div class="headline headline__item footer__head">Core Services</div>
				<ul class="list footer__list">
					<li class="list-item list-item__item footer__list-item">Design & Development</li>
					<li class="list-item list-item__item footer__list-item">Research & Planning</li>
					<li class="list-item list-item__item footer__list-item">Management Consulting</li>
				</ul>
			</div>
			<div class="footer-content col-lg-3 col-md-6 col-sm-12 footer-content__icons">
			<div class="headline headline__item footer__head">Contact</div>	
			<div class="contact-icons">
					
						
					<div class="footer-contact-icon-container">
					<div class="footer__subhead">write</div>
					<img src="/wp-content/uploads/2019/05/contact-07.svg" class="style-svg footer-contact-icon" alt="...">
							<img src="/wp-content/uploads/2019/05/contact-01.svg" class="style-svg footer-contact-icon" alt="...">
							<img src="/wp-content/uploads/2019/05/contact-02.svg" class="style-svg footer-contact-icon" alt="...">		

					</div>		
				
				
				
						<div class="footer-contact-icon-container">
						<div class="footer__subhead">follow</div>	
							<img src="/wp-content/uploads/2019/05/contact-04.svg" class="style-svg footer-contact-icon" alt="...">
							<img src="/wp-content/uploads/2019/05/contact-03.svg" class="style-svg footer-contact-icon" alt="...">
						</div>
									
						<div class="footer-contact-icon-container">
						<div class="footer__subhead">speak</div>
						<div class="footer-contact-icon footer-contact-icon--phone">913-207-6966</div>
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