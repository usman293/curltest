<?php
/**
 * Template Name: Lost username
 *
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php 
				if(isset($_POST['recovering_uname_btn'])){
					$email = $_POST['email_for_getting_uname'];
					$user = get_user_by( 'email', $email );
					if(!empty($user)){
						$message = "Your username is ".$user->data->user_login;
						wp_mail( $email, 'Your Username', $message );
					}else{
						echo 'Sorry, your email is not existing.Please try another';
					}
					
				}
				echo '<div style="width:70%;margin-left:100px;">';
					echo '<h1>For Lost you Username</h1>';
					echo '<form  method="post" action="">';
						echo '<label>Enter Your Email Address</label>';
						echo '<input type="text" name="email_for_getting_uname" value="">';
						echo '<input type="submit" style="margin-top:10px;" name="recovering_uname_btn" value="Send" >';
					echo '</form>';
					echo '<br><br>';
					echo '<h1>For Lost Your Password';
					echo '<form method="post" action="http://localhost/curltest/wp-login.php?action=lostpassword" id="lostpasswordform" name="lostpasswordform">';
						echo '<p>
							<label for="user_login">Username or E-mail:<br>
							<input type="text" size="20" value="" class="input" id="user_login" name="user_login"></label>
							</p>';
						echo '<input type="hidden" value="" name="redirect_to">';	
						echo '<p class="submit"><input type="submit" value="Get New Password" class="button button-primary button-large" id="wp-submit" name="wp-submit"></p>';
					echo '</form>';
				echo '</div>';	
			?>
				
					
						
					
				
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php get_footer(); ?>
