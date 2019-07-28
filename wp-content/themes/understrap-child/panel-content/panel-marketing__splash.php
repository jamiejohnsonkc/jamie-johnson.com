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

<section>
<article class="article__wrapper--fluid article--marketing-splash">
<div class="marketing-splash__container">
<div class="pagination marketing-splash__pagination">my purpose</div>
<h1 class="title">i make marketing better</h1>
<div class="copy__container">
<h2>Today's marketing challenges mandate new approaches.</h2> 
<p>Customers don't want to be bothered, they don't want to be sold. And they aren't moved by empty or self-serving claims.</p>
<p>It's time to expect more from your marketing.</p>
</div>
<a href="#marketing-summary" class="button ui-button__jump marketing-splash__link--jump" title="read more"><?php get_template_part('buttons/button', 'jump'); ?></a>
</div>
<div class="background__image--cover marketing-splash__image-container">
<img src="/wp-content/uploads/2019/05/growth-2.jpg" class="image-window" alt="image of person looking out of window at large metropolitan city">
</div>
</article>
</section>
 