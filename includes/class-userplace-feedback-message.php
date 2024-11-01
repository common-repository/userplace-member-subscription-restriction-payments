<?php
/**
 * Handles the feedback message.
 */
namespace Userplace;
class Userplace_Feedback_message {
  public function __construct(){
    add_filter( 'userplace_settings_fields_array', array( $this, 'userplace_feedback_message_text') );
  }

  public function userplace_feedback_message_text($message = array()){
    $feedback_mesage = 			array(
      // general message
      array(
        'id' 		 		  => 'userplace_feedback_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'feedback',
        'label' 	 		=> esc_html__('General Messages Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'general_profile_update',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'feedback',
        'label' 	 	=> esc_html__('General Message : Successful Profile Update', 'userplace'),
        'param' 	  => 'general_profile_update',
        'value'     => esc_html__('Profile Updated Successfully .', 'userplace'),
      ),
      array(
        'id' 		 		=> 'general_settings_update',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'feedback',
        'label' 	 	=> esc_html__('General Message : Successful Settings Update', 'userplace'),
        'param' 	  => 'general_settings_update',
        'value'     => esc_html__('Settings Updated Successfully .', 'userplace'),
      ),
      array(
        'id' 		 		=> 'general_payment_update',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'feedback',
        'label' 	 	=> esc_html__('General Message : Successful Payment Gateway Settings Update', 'userplace'),
        'param' 	  => 'general_payment_update',
        'value'     => esc_html__('Settings Updated Successfully .', 'userplace'),
      ),
      array(
        'id' 		 		=> 'general_comment_restriction_message',
        'type' 		 	=> 'textarea',
        'menuId'	 	=> 'feedback',
        'label' 	 	=> esc_html__('General Message : Comment Restriction Message', 'userplace'),
        'param' 	  => 'general_payment_update',
        'value'     => esc_html__('Sorry, you are not eligible to view or post comment!', 'userplace'),
      ),
      // array(
      //   'id' 		 		=> 'general_registration_form_name',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'feedback',
      //   'label' 	 	=> esc_html__('Please select appropriate message', 'userplace'),
      //   'param' 	  => 'general_registration_form_name',
      // ),
      // array(
      //   'id' 		 		=> 'general_registration_form_name',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'feedback',
      //   'label' 	 	=> esc_html__('Please select appropriate message', 'userplace'),
      //   'param' 	  => 'general_registration_form_name',
      // ),
      // array(
      //   'id' 		 		=> 'general_change_pass_form_name',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'feedback',
      //   'label' 	 	=> esc_html__('Please select appropriate message', 'userplace'),
      //   'param' 	  => 'general_change_pass_form_name',
      // ),
      // array(
      //   'id' 		 		=> 'general_pass_lost_form_name',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'feedback',
      //   'label' 	 	=> esc_html__('Please select appropriate message', 'userplace'),
      //   'param' 	  => 'general_pass_lost_form_name',
      // ),
      // array(
      //   'id' 		 		=> 'general_pass_reset_form_name',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'feedback',
      //   'label' 	 	=> esc_html__('Please select appropriate message', 'userplace'),
      //   'param' 	  => 'general_pass_reset_form_name',
      // ),



      // general message end

      // login message
      array(
        'id' 		 		  => 'userplace_signin_general_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'login',
        'label' 	 		=> esc_html__('General Messages : Sign In Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_general_after_logged_out',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('General message - After logged out', 'userplace'),
        'param' 	  => 'signin_general_after_logged_out',
        'value'     => esc_html__('You have signed out. Would you like to sign in again?', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_general_lost_pass_sent',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('General message - lost password sent', 'userplace'),
        'param' 	  => 'signin_general_lost_pass_sent',
        'value'     => esc_html__('Check your email for a link to reset your password.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_general_pass_update',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('General message - password update', 'userplace'),
        'param' 	  => 'signin_general_pass_update',
        'value'     => esc_html__('Your password has been updated. You can sign in now.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_general_currently_registered',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('General message - currently registered', 'userplace'),
        'param' 	  => 'signin_general_currently_registered',
        'value'     => esc_html__('You have successfully registerd. Please check your email for setting up your password.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_general_forget_pass',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('General message - forget password', 'userplace'),
        'param' 	  => 'signin_general_forget_pass',
        'value'     => esc_html__('Forgot your password?', 'userplace'),
      ),


      array(
        'id' 		 		  => 'userplace_signin_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'login',
        'label' 	 		=> esc_html__('Feedback Messages : Sign In Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_feedback_empty_username',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('Error message - Empty Email/Username', 'userplace'),
        'param' 	  => 'signin_feedback_empty_username',
        'value'     => esc_html__('You do have an email/username, right?', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_empty_password',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('Error message - Empty Password', 'userplace'),
        'param' 	  => 'signin_feedback_empty_password',
        'value'     => esc_html__('You need to enter a password to login.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_invalid_username',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('Error message - Invalid Email/Username', 'userplace'),
        'param' 	  => 'signin_feedback_invalid_username',
        'value'     => esc_html__('Maybe you have used a different email/username during sign up!', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_incorrect_password',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'login',
        'label' 	 	=> esc_html__('Error message - Incorrect Password', 'userplace'),
        'param' 	  => 'signin_feedback_incorrect_password',
        'value'     => esc_html__('The password you have entered is incorrect', 'userplace'),
      ),
      // login message end

      // Registration Message
      array(
        'id' 		 		  => 'userplace_signup_general_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'signup',
        'label' 	 		=> esc_html__('General Messages : Sign Up Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_general_signup_note',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'signup',
        'label' 	 	=> esc_html__('General Message : SignUp Note', 'userplace'),
        'param' 	  => 'signin_general_signup_note',
        'value'     => esc_html__('Note: Your password generation link will be sent to your email address automatically.', 'userplace'),
      ),


      array(
        'id' 		 		  => 'userplace_signup_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'signup',
        'label' 	 		=> esc_html__('Feedback Messages : Sign Up Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_feedback_invalid_email',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'signup',
        'label' 	 	=> esc_html__('Error message - Invalid Email/Username', 'userplace'),
        'param' 	  => 'signin_feedback_invalid_email',
        'value'     => esc_html__('The email address you have entered is not valid.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_email_exists',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'signup',
        'label' 	 	=> esc_html__('Error message - Email Already Exists', 'userplace'),
        'param' 	  => 'signin_feedback_email_exists',
        'value'     => esc_html__('An account already exists with this email address.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_username_exists',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'signup',
        'label' 	 	=> esc_html__('Error message - Username Already Exists', 'userplace'),
        'param' 	  => 'signin_feedback_username_exists',
        'value'     => esc_html__('An account already exists with this username.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_closed',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'signup',
        'label' 	 	=> esc_html__('Error message - Registering user not allowed', 'userplace'),
        'param' 	  => 'signin_feedback_closed',
        'value'     => esc_html__('Registering new user is currently not allowed.', 'userplace'),
      ),
      // Registration message end

      // Lost password
      array(
        'id' 		 		  => 'userplace_general_lost_pass_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'lost_pass',
        'label' 	 		=> esc_html__('General Messages : Lost Password Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_general_lost_empty_username',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'lost_pass',
        'label' 	 	=> esc_html__('General Messages - Lost Password', 'userplace'),
        'param' 	  => 'signin_general_lost_empty_username',
        'value'     => esc_html__('Enter your email address & we will send you a link to pick a new password.', 'userplace'),
      ),
      array(
        'id' 		 		  => 'userplace_lost_pass_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'lost_pass',
        'label' 	 		=> esc_html__('Feedback Messages : Lost Password Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_feedback_lost_empty_username',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'lost_pass',
        'label' 	 	=> esc_html__('Error message - Empty Email/Username', 'userplace'),
        'param' 	  => 'signin_feedback_lost_empty_username',
        'value'     => esc_html__('You need to enter your email address to continue.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_invalidcombo',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'lost_pass',
        'label' 	 	=> esc_html__('Error message - Registration check', 'userplace'),
        'param' 	  => 'signin_feedback_invalidcombo',
        'value'     => esc_html__('There is no user registered with this email address.', 'userplace'),
      ),
      // Lost password end

      // Reset password
      array(
        'id' 		 		  => 'userplace_reset_pass_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'reset_pass',
        'label' 	 		=> esc_html__('Feedback Messages : Reset Password Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_feedback_invalidkey',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'reset_pass',
        'label' 	 	=> esc_html__('Error message - Password Reset Link', 'userplace'),
        'param' 	  => 'signin_feedback_invalidkey',
        'value'     => esc_html__('The password reset link you used is not valid anymore.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_reset_mismatch',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'reset_pass',
        'label' 	 	=> esc_html__('Error message - Password Reset Mismatch', 'userplace'),
        'param' 	  => 'signin_feedback_password_reset_mismatch',
        'value'     => esc_html__('The two passwords you entered did not match .', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_reset_empty',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'reset_pass',
        'label' 	 	=> esc_html__('Error message - Password Reset Empty', 'userplace'),
        'param' 	  => 'signin_feedback_password_reset_empty',
        'value'     => esc_html__('Sorry, we do not accept empty passwords.', 'userplace'),
      ),
      // Reset password end

      // Change password
      array(
        'id' 		 		  => 'userplace_general_change_pass_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'change_pass',
        'label' 	 		=> esc_html__('General Messages : Change Password Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_general_change_pass_message',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('General message - Change password message', 'userplace'),
        'param' 	  => 'signin_general_change_pass_message',
        'value'     => esc_html__('Your password has been changed. You can sign in now with your new password.', 'userplace'),
      ),
      array(
        'id' 		 		  => 'userplace_change_pass_label',
        'type' 		 		=> 'label', 
        'menuId'	 		=> 'change_pass',
        'label' 	 		=> esc_html__('Feedback Messages : Change Password Settings', 'userplace'),
        'label_type' 	=> 'h1'
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_old_mismatch',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('Error message - Password Old Mismatch', 'userplace'),
        'param' 	  => 'signin_feedback_password_old_mismatch',
        'value'     => esc_html__('You entered an incorrect old password.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_change_same',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('Error message - Password change name', 'userplace'),
        'param' 	  => 'signin_feedback_password_change_same',
        'value'     => esc_html__('Your current password and new passwrod must not be same.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_old_change_empty',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('Error message - Password Old Empty', 'userplace'),
        'param' 	  => 'signin_feedback_password_old_change_empty',
        'value'     => esc_html__('Old password field is empty. Enter your current password.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_change_empty',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('Error message - Password change empty', 'userplace'),
        'param' 	  => 'signin_feedback_password_change_empty',
        'value'     => esc_html__('Sorry, we do not accept empty passwords.', 'userplace'),
      ),
      array(
        'id' 		 		=> 'signin_feedback_password_change_mismatch',
        'type' 		 	=> 'text',
        'menuId'	 	=> 'change_pass',
        'label' 	 	=> esc_html__('Error message - Password change mismatch', 'userplace'),
        'param' 	  => 'signin_feedback_password_change_mismatch',
        'value'     => esc_html__('The two passwords you entered did not match.', 'userplace'),
      ),
      // Change password end



      // array(
      //   'id' 		 		=> 'signin_feedback_success',
      //   'type' 		 	=> 'text',
      //   'menuId'	 	=> 'login_registration',
      //   'label' 	 	=> esc_html__('Please select appropriate success message', 'userplace'),
      //   'param' 	  => 'signin_feedback_success',
      // ),


    );
    $message = array_merge($message, $feedback_mesage);
    return $message;
  }

}
