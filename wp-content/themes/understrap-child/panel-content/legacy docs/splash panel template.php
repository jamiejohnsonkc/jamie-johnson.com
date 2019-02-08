<?php
/**
* Panel Splash template.
*
* @package understrap
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<article class="panel home-page splash">
	
		<h1>Hi <span class="br"><br></span>I'm Jamie</h1>
		
		<h2>strategist | creator | developer | leader | <strong>marketer</strong></h2>
		
		<h3>Salty Strategic Marketing Veteran <span class="br"><br></span>+ Front-End Design/Developer</h3>
		<p>Seeking contract, consulting or freelance opportunities <span class="br"><br></span>(or the right long-term gig)</p>
		<a class="link__advance--chevron" href="//localhost:3000/modus-operandi#i-advance-business-objectives" title="go to next">
		<img src="http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/chevdn.svg" alt="" class="chev--dwn style-svg"/>
	</a>
		<div class="panel-hero__img--mobile"><img src="/wp-content/uploads/2019/01/me-small.jpg" alt="profile pic"></div>

	</article><!-- cover end -->
	
	
	
<style>
//layout styles

.splash {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.splash {
    h2, h3{
margin-bottom: $rvr-2;
}
}

.panel-hero__img--mobile{
position: absolute;
bottom: 0;
left:25%;
z-index: -1;
}

//panel styles

.panel-hero__img--background{
	background: none;
	@include media-breakpoint-up(lg) {
	<!-- background-image: url("http://jamiejohnsonmev2.test/wp-content/uploads/2018/12/me.jpg"); -->
    background-position: right bottom;
    background-size: 80%;
    background-repeat: no-repeat;
}
}

.home-page.splash{
	.panel-hero__img--mobile{
	width: 50%;
	left: 50%;
	}
}