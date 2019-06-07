<?php
/**
* Template Name: contact-page
*
* Template for displaying a page without sidebar even if a sidebar widget is published.
*
* @package understrap
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php

  //response generation function

  $response = "";

  //function to generate response
  function my_contact_form_generate_response($type, $message){

    global $response;

    if($type == "success") $response = "<div class='contact-form-message' id='success'>{$message}</div>";
    else $response = "<div class='contact-form-message' id='error'>{$message}</div>";

  }

  //response messages
  $not_human       = "time to brush up on that basic algebra, huh?";
  $missing_content = "Please supply all information";
  $email_invalid   = "Email Address Invalid";
  $message_unsent  = "Double check your entries. Something's amiss.";
  $message_sent    = "Message Sent!";

  //user posted variables
  $name = $_POST['message_name'];
  $email = $_POST['message_email'];
  $message = $_POST['message_text'];
  $human = $_POST['message_human'];

  //php mailer variables
  $to = get_option('admin_email');
  $subject = "Someone sent a message from ".get_bloginfo('name');
  $headers = 'From: '. $email . "\r\n" .
    'Reply-To: ' . $email . "\r\n";

  if(!$human == 0){
    if($human != 2) my_contact_form_generate_response("error", $not_human); //not human!
    else {

      //validate email
      if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        my_contact_form_generate_response("error", $email_invalid);
      else //email is valid
      {
        //validate presence of name and message
        if(empty($name) || empty($message)){
          my_contact_form_generate_response("error", $missing_content);
        }
        else //ready to go!
        {
          $sent = wp_mail($to, $subject, strip_tags($message), $headers);
          if($sent) my_contact_form_generate_response("success", $message_sent); //message sent!
          else my_contact_form_generate_response("error", $message_unsent); //message wasn't sent
        }
      }
    }
  }
  else if ($_POST['submitted']) my_contact_form_generate_response("error", $missing_content);

?>

<?php get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>
<div class="contact__wrapper" id="full-width-page-wrapper">
	<div class="<?php echo esc_attr( $container ); ?>" id="content">
		<div class="row">
			<div class="col-md-12 contact__content-area" id="primary">
				<main class="site-main" id="main" role="main">
					<section class="container-fluid contact__container" id="contact">
          <div class="row">
          <div class="col-lg-6 container__contact--content">
            <img src="/wp-content/uploads/2019/06/hero-10.jpg" class="relax-img" alt="...">
          </div>  
          <div class="col-lg-6 container__contact--form">
					<div class="contact__form--wrapper">
              <h1 class="headline__header contact__header">Whatcha Need?</h1>
							<p class="deck deck__block contact__deck">Curious? Wanna kick the tires a little? Go ahead ask me something. You know you want to.</p>
				
						<div class="contact__form"> 
						<div id="respond">
                <?php echo $response; ?>
                <form role="form" action="<?php the_permalink(); ?>" method="post">
                		<br style="clear:both">
                  <label for="name">Name: <span>*</span> <br><input class="form-control" id="name" type="text" name="message_name" value="<?php echo esc_attr($_POST['message_name']); ?>"></label>
                  <label for="message_email">Email: <span>*</span> <br><input class="form-control" id="email" type="text" name="message_email" value="<?php echo esc_attr($_POST['message_email']); ?>"></label>
                  <label for="message_text">Message: <span>*</span> <br><textarea class="form-control" id="message" type="text" name="message_text"><?php echo esc_textarea($_POST['message_text']); ?></textarea></label>
                  <label for="message_human">what + 3 = 5 ?</label>
                    
                  <input class="form-control" id="verification" type="text" name="message_human"></label>
                  <input type="hidden" name="submitted" value="1">
                  <input type="submit">
                </form>
              </div>
                  </div>
                  </div>
          </div>
          </div>
							<!-- <form role="form">
									<br style="clear:both">
									<h3 style="margin-bottom: 25px; text-align: center;">Contact Form</h3>
									<div class="form-group">
											<input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
									</div>
									<div class="form-group">
											<input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
									</div>
									<div class="form-group">
											<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile Number" required>
									</div>
									<div class="form-group">
											<input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
									</div>
									<div class="form-group">
											<textarea class="form-control" type="textarea" id="message" placeholder="Message" maxlength="140" rows="7"></textarea>
											<span class="help-block"><p id="characterLeft" class="help-block ">You have reached the limit</p></span>
									</div>
									
									<button type="button" id="submit" name="submit" class="btn btn-primary pull-right">Submit Form</button>
							</form> -->
					</section>
					</main><!-- #main -->
					</div><!-- #primary -->
					</div><!-- .row end -->
					<!-- Container end -->
				</div>
				</div><!-- Wrapper end -->
				<?php get_footer(); ?>