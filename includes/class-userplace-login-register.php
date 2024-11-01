<?php

/**
 * Handles the restriction checking.
 */

namespace Userplace;
// use Userplace_Feedback_message;
class LoginRegister
{
	public $sign_in_page = null;
	public $register_page = null;
	public $pick_a_new_password_page = null;
	public $forgot_your_password_page = null;
	public $change_password_page = null;

	public function __construct()
	{
		add_shortcode('userplace_login_form', array($this, 'render_login_form'));
		add_shortcode('userplace_register_form', array($this, 'render_register_form'));
		add_shortcode('userplace_password_reset_form', array($this, 'render_password_reset_form'));
		add_shortcode('userplace_password_lost_form', array($this, 'render_password_lost_form'));
		add_shortcode('userplace_change_password_form', array($this, 'render_change_password_form'));
		add_action('login_form_register', array($this, 'redirect_to_custom_register'));
		add_action('login_form_register', array($this, 'do_register_user'));
		add_filter('authenticate', array($this, 'maybe_redirect_at_authenticate'), 101, 3);
		add_action('wp_logout', array($this, 'redirect_after_logout'));
		add_filter('login_redirect', array($this, 'redirect_after_login'), 10, 3);
		add_action('login_form_login', array($this, 'redirect_to_custom_login'));
		add_action('login_form_lostpassword', array($this, 'redirect_to_custom_lostpassword'));
		add_action('login_form_lostpassword', array($this, 'do_password_lost'));
		add_filter('retrieve_password_message', array($this, 'replace_retrieve_password_message'), 10, 4);
		add_action('login_form_rp', array($this, 'redirect_to_custom_password_reset'));
		add_action('login_form_resetpass', array($this, 'redirect_to_custom_password_reset'));
		add_action('login_form_rp', array($this, 'do_password_reset'));
		add_action('login_form_resetpass', array($this, 'do_password_reset'));
		add_action('login_form_changepass', array($this, 'do_password_change'));
		// add_filter( 'lostpassword_url', array( $this, 'lostpassword_url' ), 101 );

		$userplace_settings = json_decode(get_option('userplace_settings'), true);

		$this->sign_in_page         = (isset($userplace_settings['sign_in'])) ? get_post_field('post_name', $userplace_settings['sign_in']) : '';
		$this->register_page        = (isset($userplace_settings['register'])) ? get_post_field('post_name', $userplace_settings['register']) : '';
		$this->pick_a_new_password  = (isset($userplace_settings['pick_a_new_password'])) ? get_post_field('post_name', $userplace_settings['pick_a_new_password']) : '';
		$this->forgot_your_password = (isset($userplace_settings['forgot_your_password'])) ? get_post_field('post_name', $userplace_settings['forgot_your_password']) : '';
		$this->change_password_page = 'console/change-password';
	}

	public function lostpassword_url()
	{
		$redirect_url = home_url($this->forgot_your_password);
		return $redirect_url;
	}

	/**
	 * Change the user's password if the password change form was submitted.
	 */
	public function do_password_change()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$user = wp_get_current_user();

			if (!$user || is_wp_error($user)) {
				wp_redirect(home_url($this->change_password_page . '?login=not_loggedin'));
				exit;
			}

			if (isset($_POST['pass0']) && $_POST['pass1']) {

				if ($user && !wp_check_password($_POST['pass0'], $user->data->user_pass, $user->ID)) {
					// Passwords don't match
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_old_mismatch', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if (($_POST['pass0'] == $_POST['pass1']) && ($_POST['pass0'] == $_POST['pass2'])) {
					// Passwords don't match
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_change_same', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if ($_POST['pass1'] != $_POST['pass2']) {
					// Passwords don't match
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_change_mismatch', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if (empty($_POST['pass0'])) {
					// Password is empty
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_old_change_empty', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if (empty($_POST['pass1'])) {
					// Password is empty
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_change_empty', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				// Parameter checks OK, reset password
				reset_password($user, sanitize_text_field($_POST['pass1']));

				// Log-in again.
				wp_set_auth_cookie($user->ID);
				wp_set_current_user($user->ID);
				do_action('wp_login', $user->user_login, $user);

				wp_redirect(home_url($this->change_password_page . '?password=changed'));
			} else {
				if (empty($_POST['pass0'])) {
					// Password is empty
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_old_change_empty', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if (empty($_POST['pass1'])) {
					// Password is empty
					$redirect_url = home_url($this->change_password_page);

					$redirect_url = add_query_arg('error', 'password_change_empty', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				echo esc_html__("Invalid request.", 'userplace');
			}

			exit;
		}
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 */
	public function do_password_reset()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$rp_key = sanitize_text_field($_REQUEST['rp_key']);
			$rp_login = sanitize_text_field($_REQUEST['rp_login']);

			$user = check_password_reset_key($rp_key, $rp_login);

			if (!$user || is_wp_error($user)) {
				if ($user && $user->get_error_code() === 'expired_key') {
					wp_redirect(home_url($this->sign_in_page . '?login=expiredkey'));
				} else {
					wp_redirect(home_url($this->sign_in_page . '?login=invalidkey'));
				}
				exit;
			}

			if (isset($_POST['pass1'])) {
				if ($_POST['pass1'] != $_POST['pass2']) {
					// Passwords don't match
					$redirect_url = home_url($this->pick_a_new_password);

					$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
					$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
					$redirect_url = add_query_arg('error', 'password_reset_mismatch', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				if (empty($_POST['pass1'])) {
					// Password is empty
					$redirect_url = home_url($this->pick_a_new_password);

					$redirect_url = add_query_arg('key', $rp_key, $redirect_url);
					$redirect_url = add_query_arg('login', $rp_login, $redirect_url);
					$redirect_url = add_query_arg('error', 'password_reset_empty', $redirect_url);

					wp_redirect($redirect_url);
					exit;
				}

				// Parameter checks OK, reset password
				reset_password($user, sanitize_text_field($_POST['pass1']));
				wp_redirect(home_url($this->sign_in_page . '?password=changed'));
			} else {
				echo esc_html__("Invalid request.", 'userplace');
			}

			exit;
		}
	}

	/**
	 * A shortcode for rendering the form used to reset a user's password.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_reset_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false);
		$attributes = shortcode_atts($default_attributes, $attributes);

		if (is_user_logged_in()) {
			return '<div class="userplace-password-reset-message">' . esc_html__('You are already signed in.', 'userplace') . '</div>';
		} else {
			if (isset($_REQUEST['login']) && isset($_REQUEST['key'])) {
				$attributes['login'] = sanitize_text_field($_REQUEST['login']);
				$attributes['key'] = sanitize_text_field($_REQUEST['key']);

				// Error messages
				$errors = array();
				if (isset($_REQUEST['error'])) {
					$error_codes = explode(',', sanitize_text_field($_REQUEST['error']));

					foreach ($error_codes as $code) {
						$errors[] = $this->get_error_message($code);
					}
				}
				$attributes['errors'] = $errors;

				return $this->get_template_html('password_reset_form', $attributes);
			} else {
				return '<div class="userplace-password-reset-message"><p class="login-error">' . esc_html__('Invalid password reset link.', 'userplace') . '</p></div>';
			}
		}
	}

	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 */
	public function redirect_to_custom_password_reset()
	{
		if ('GET' == $_SERVER['REQUEST_METHOD']) {
			// Verify key / login combo
			$user = check_password_reset_key(sanitize_text_field($_REQUEST['key']), sanitize_text_field($_REQUEST['login']));
			if (!$user || is_wp_error($user)) {
				if ($user && $user->get_error_code() === 'expired_key') {
					wp_redirect(home_url($this->sign_in_page . '?login=expiredkey'));
				} else {
					wp_redirect(home_url($this->sign_in_page . '?login=invalidkey'));
				}
				exit;
			}

			$redirect_url = home_url($this->pick_a_new_password);
			$redirect_url = add_query_arg('login', sanitize_text_field($_REQUEST['login']), $redirect_url);
			$redirect_url = add_query_arg('key', sanitize_text_field($_REQUEST['key']), $redirect_url);

			wp_redirect($redirect_url);
			exit;
		}
	}

	/**
	 * Returns the message body for the password reset mail.
	 * Called through the retrieve_password_message filter.
	 *
	 * @param string  $message    Default mail message.
	 * @param string  $key        The activation key.
	 * @param string  $user_login The username for the user.
	 * @param WP_User $user_data  WP_User object.
	 *
	 * @return string   The mail message to send.
	 */
	public function replace_retrieve_password_message($message, $key, $user_login, $user_data)
	{
		// Create new message
		$msg  = esc_html__('Hello!', 'userplace') . "\r\n\r\n";
		/* translators: %s is replaced with "string" */
		$msg .= sprintf(__('You asked us to reset your password for your account using the email address %s.', 'userplace'), $user_login) . "\r\n\r\n";
		$msg .= esc_html__("If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'userplace') . "\r\n\r\n";
		$msg .= esc_html__('To reset your password, visit the following address:', 'userplace') . "\r\n\r\n";
		$msg .= site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n\r\n";
		$msg .= esc_html__('Thanks!', 'userplace') . "\r\n";

		return $msg;
	}

	/**
	 * Initiates password reset.
	 */
	public function do_password_lost()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$errors = retrieve_password();
			if (is_wp_error($errors)) {
				// Errors found
				$redirect_url = home_url($this->forgot_your_password);
				$redirect_url = add_query_arg('errors', join(',', $errors->get_error_codes()), $redirect_url);
			} else {
				// Email sent
				$redirect_url = home_url($this->sign_in_page);
				$redirect_url = add_query_arg('checkemail', 'confirm', $redirect_url);
			}

			wp_redirect($redirect_url);
			exit;
		}
	}
	/**
	 * A shortcode for rendering the form used to initiate the password reset.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_password_lost_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false);
		$attributes = shortcode_atts($default_attributes, $attributes);

		// Retrieve possible errors from request parameters
		$attributes['errors'] = array();
		if (isset($_REQUEST['errors'])) {
			$error_codes = explode(',', $_REQUEST['errors']);

			foreach ($error_codes as $error_code) {
				$attributes['errors'][] = $this->get_error_message($error_code);
			}
		}

		if (is_user_logged_in()) {
			return '<div class="userplace-password-lost-message">' . esc_html__('You are already signed in.', 'userplace') . '</div>';
		} else {
			return $this->get_template_html('password_lost_form', $attributes);
		}
	}

	/**
	 * A shortcode for rendering the login form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_login_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false, 'show_in_popup' => false);
		$attributes = shortcode_atts($default_attributes, $attributes);
		$show_title = $attributes['show_title'];
		$show_in_popup = $attributes['show_in_popup'];

		if (is_user_logged_in()) {
			ob_start();
			$current_user = wp_get_current_user();
?>
			<div class="member-login-msg">
				<p><?php esc_html_e('Hello ', 'userpalce') ?><strong><?php echo esc_html($current_user->user_login) ?></strong> (<?php esc_html_e('not ', 'userpalce') ?><strong><?php echo esc_html($current_user->user_login) ?></strong>? <a href="<?php echo wp_logout_url(home_url()); ?>"><?php esc_html_e('Log out', 'userplace') ?></a>).</p>
			</div>
		<?php return ob_get_clean();
		}
		// Pass the redirect parameter to the WordPress login functionality: by default,
		// don't specify a redirect, but if a valid redirect URL has been passed as
		// request parameter, use it.
		$attributes['redirect'] = '';
		if (isset($_REQUEST['redirect_to'])) {
			$attributes['redirect'] = wp_validate_redirect($_REQUEST['redirect_to'], $attributes['redirect']);
		}

		// Error messages
		$errors = array();
		if (isset($_REQUEST['login'])) {
			$error_codes = explode(',', sanitize_text_field($_REQUEST['login']));

			foreach ($error_codes as $code) {
				$errors[] = $this->get_error_message($code);
			}
		}
		$attributes['errors'] = $errors;

		// Check if user just logged out
		$attributes['logged_out'] = isset($_REQUEST['logged_out']) && sanitize_text_field($_REQUEST['logged_out']) == true;

		// check if user registerd with email address
		$attributes['registered'] = (isset($_REQUEST['registered']) && !empty($_REQUEST['registered'])) ? sanitize_text_field($_REQUEST['registered']) : '';

		// check if user forced to login
		$attributes['redirect_to'] = (isset($_REQUEST['redirect_to']) && !empty($_REQUEST['redirect_to'])) ? sanitize_text_field($_REQUEST['redirect_to']) : '';

		// Check if the user just requested a new password
		$attributes['lost_password_sent'] = isset($_REQUEST['checkemail']) && $_REQUEST['checkemail'] == 'confirm';

		// Check if user just updated password
		$attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';

		$attributes['register_page'] = home_url($this->register_page);

		// Render the login form using an external template
		return $this->get_template_html('login_form', $attributes);
	}

	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	public function do_register_user()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD']) {
			$redirect_url = home_url($this->register_page);

			if (!get_option('users_can_register')) {
				// Registration closed, display error
				$redirect_url = add_query_arg('register-errors', 'closed', $redirect_url);
			} else {

				do_action('userplace_before_register_form_post', $_POST, $redirect_url);
				$username   = sanitize_text_field($_POST['username']);
				$email      = sanitize_email($_POST['email']);
				$first_name = sanitize_text_field($_POST['first_name']);
				$last_name  = sanitize_text_field($_POST['last_name']);

				$result = $this->register_user($username, $email, $first_name, $last_name);

				if (is_wp_error($result)) {
					// Parse errors into a string and append as parameter to redirect
					$errors = join(',', $result->get_error_codes());
					$redirect_url = add_query_arg('register-errors', $errors, $redirect_url);
				} else {

					do_action('userplace_register_before_suceessfull_redirection', $_POST);

					// Success, redirect to login page.
					$redirect_url = home_url($this->sign_in_page);
					$redirect_url = add_query_arg('registered', $email, $redirect_url);
				}
			}

			wp_redirect($redirect_url);
			exit;
		}
	}

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user($username, $email, $first_name, $last_name)
	{
		$errors = new \WP_Error();

		// Email address is used as both username and email. It is also the only
		// parameter we need to validate
		if (!is_email($email)) {
			$errors->add('email', $this->get_error_message('email'));
			return $errors;
		}

		if (username_exists($username)) {
			$errors->add('username_exists', $this->get_error_message('username_exists'));
			return $errors;
		}

		if (preg_match('/^\S.*\s.*\S$/', $username)) {
			$errors->add('username_invalid', esc_html__('The given username is not valid', 'userplace'));
			return $errors;
		}

		if (email_exists($email)) {
			$errors->add('email_exists', $this->get_error_message('email_exists'));
			return $errors;
		}

		// Generate the password so that the subscriber will have to check email...
		$password = wp_generate_password(12, false);

		$user_data = array(
			'user_login'    => $username,
			'user_email'    => $email,
			'user_pass'     => $password,
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'nickname'      => $username,
		);

		$user_id = wp_insert_user($user_data);
		wp_new_user_notification($user_id, null, 'both');

		$user_pre_value = array(
			'first_name'    => $first_name,
			'last_name'     => $last_name,
			'user_gender'   => '',
			'user_timezone' => '',
		);
		update_user_meta($user_id, 'userplace_user_settings_prevalue', json_encode($user_pre_value, true));

		$default_plan = userplace_get_default_plan();
		if (!empty($default_plan->plan_id)) {
			update_user_meta($user_id, 'userplace_customer_plan_id', $default_plan->plan_id);
		}

		return $user_id;
	}

	/**
	 * A shortcode for rendering the new user registration form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_register_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false,  'show_in_popup' => false);
		$attributes = shortcode_atts($default_attributes, $attributes);
		$show_in_popup = $attributes['show_in_popup'];

		if (is_user_logged_in()) {
			ob_start();
			$current_user = wp_get_current_user();
		?>
			<div class="member-register-msg">
				<p><?php esc_html_e('Hello ', 'userpalce') ?><strong><?php echo esc_html($current_user->user_login) ?></strong> (<?php esc_html_e('not ', 'userpalce') ?><strong><?php echo esc_html($current_user->user_login) ?></strong>? <a href="<?php echo wp_logout_url(home_url()); ?>"><?php esc_html_e('Log out', 'userplace') ?></a>).</p>
			</div>
		<?php return ob_get_clean();
		} elseif (!get_option('users_can_register')) {
			ob_start();
		?>
			<p class="login-warning">
				<?php echo esc_html__('Registering new users is currently not allowed.', 'userplace'); ?>
			</p>
<?php
			return ob_get_clean();
		} else {
			// Retrieve possible errors from request parameters
			$attributes['errors'] = array();
			if (isset($_REQUEST['register-errors'])) {
				$error_codes = explode(',', sanitize_text_field($_REQUEST['register-errors']));

				foreach ($error_codes as $error_code) {
					$attributes['errors'][] = $this->get_error_message($error_code);
				}
			}

			$attributes['login_page'] = home_url($this->sign_in_page);
			$attributes = apply_filters('userplace_register_form_attributes', $attributes);

			return $this->get_template_html('register_form', $attributes);
		}
	}

	/**
	 * A shortcode for rendering the existing user change password form.
	 *
	 * @param  array   $attributes  Shortcode attributes.
	 * @param  string  $content     The text content for shortcode. Not used.
	 *
	 * @return string  The shortcode output
	 */
	public function render_change_password_form($attributes, $content = null)
	{
		// Parse shortcode attributes
		$default_attributes = array('show_title' => false);
		$attributes = shortcode_atts($default_attributes, $attributes);

		if (!is_user_logged_in()) {
			return esc_html__('You are not signed in.', 'userplace');
		} else {
			// Retrieve possible errors from request parameters
			$attributes['errors'] = array();
			if (isset($_REQUEST['error'])) {
				$error_codes = explode(',', sanitize_text_field($_REQUEST['error']));

				foreach ($error_codes as $error_code) {
					$attributes['errors'][] = $this->get_error_message($error_code);
				}
			}

			// Check if user just updated password
			$attributes['password_updated'] = isset($_REQUEST['password']) && $_REQUEST['password'] == 'changed';

			return $this->get_template_html('change_password_form', $attributes);
		}
	}

	/**
	 * Renders the contents of the given template to a string and returns it.
	 *
	 * @param string $template_name The name of the template to render (without .php)
	 * @param array  $attributes    The PHP variables for the template
	 *
	 * @return string               The contents of the template.
	 */
	private function get_template_html($template_name, $attributes = null)
	{
		if (!$attributes) {
			$attributes = array();
		}

		ob_start();

		do_action('userplace_before_' . $template_name, $attributes);

		$template = 'shortcode/' . $template_name . '.php';

		userplace_get_template($template, array('attributes' => $attributes));

		do_action('userplace_after_' . $template_name, $attributes);

		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message($error_code)
	{
		$message = array();
		$data_message = array();
		$data = new Userplace_Feedback_message();
		$data_message = $data->userplace_feedback_message_text($message);
		$userplace_settings_feedback = json_decode(get_option('userplace_settings'), true);

		$empty_username = '';
		$empty_password = '';
		$invalid_username = '';
		$incorrect_password = '';
		$invalid_email = '';
		$username_exists = '';
		$email_exists = '';
		$closed = '';
		$lost_empty_username = '';
		$invalidcombo = '';
		$invalidkey = '';
		$password_reset_mismatch = '';
		$password_reset_empty = '';
		$password_old_mismatch = '';
		$password_change_same = '';
		$password_old_change_empty = '';
		$password_change_empty = '';
		$password_change_mismatch = '';

		// login
		$empty_username = !empty($userplace_settings_feedback['signin_feedback_empty_username']) ? $userplace_settings_feedback['signin_feedback_empty_username'] : esc_html__('You do have an email address, right?', 'userplace');
		$empty_password = !empty($userplace_settings_feedback['signin_feedback_empty_password']) ? $userplace_settings_feedback['signin_feedback_empty_password'] : esc_html__('You need to enter a password to login.', 'userplace');
		$invalid_username = !empty($userplace_settings_feedback['signin_feedback_invalid_username']) ? $userplace_settings_feedback['signin_feedback_invalid_username'] : esc_html__('Maybe you have used a different email/username during sign up!', 'userplace');
		$incorrect_password = !empty($userplace_settings_feedback['signin_feedback_incorrect_password']) ? $userplace_settings_feedback['signin_feedback_incorrect_password'] : esc_html__('The password you have entered is incorrect', 'userplace');

		// register
		$invalid_email = !empty($userplace_settings_feedback['signin_feedback_invalid_email']) ? $userplace_settings_feedback['signin_feedback_invalid_email'] : esc_html__('The email address you entered is not valid.', 'userplace');
		$email_exists = !empty($userplace_settings_feedback['signin_feedback_email_exists']) ? $userplace_settings_feedback['signin_feedback_email_exists'] : esc_html__('An account exists with this email address.', 'userplace');
		$username_exists = !empty($userplace_settings_feedback['signin_feedback_username_exists']) ? $userplace_settings_feedback['signin_feedback_username_exists'] : esc_html__('An account exists with this username.', 'userplace');
		$closed = !empty($userplace_settings_feedback['signin_feedback_closed']) ? $userplace_settings_feedback['signin_feedback_closed'] : esc_html__('Registering new users is currently not allowed.', 'userplace');

		// lost
		$lost_empty_username = !empty($userplace_settings_feedback['signin_feedback_lost_empty_username']) ? $userplace_settings_feedback['signin_feedback_lost_empty_username'] : esc_html__('You need to enter your email address to continue.', 'userplace');
		$invalidcombo = !empty($userplace_settings_feedback['signin_feedback_invalidcombo']) ? $userplace_settings_feedback['signin_feedback_invalidcombo'] : esc_html__('There is no user registered with this email address.', 'userplace');

		// reset
		$invalidkey = !empty($userplace_settings_feedback['signin_feedback_invalidkey']) ? $userplace_settings_feedback['signin_feedback_invalidkey'] : esc_html__('The password reset link you used is not valid anymore.', 'userplace');
		$password_reset_mismatch = !empty($userplace_settings_feedback['signin_feedback_password_reset_mismatch']) ? $userplace_settings_feedback['signin_feedback_password_reset_mismatch'] : esc_html__('The two passwords you entered did not match.', 'userplace');
		$password_reset_empty = !empty($userplace_settings_feedback['signin_feedback_password_reset_empty']) ? $userplace_settings_feedback['signin_feedback_password_reset_empty'] : esc_html__('Sorry, we do not accept empty passwords.', 'userplace');

		// change
		$password_old_mismatch = !empty($userplace_settings_feedback['signin_feedback_password_old_mismatch']) ? $userplace_settings_feedback['signin_feedback_password_old_mismatch'] : esc_html__('You entered an incorrect old password.', 'userplace');
		$password_change_same = !empty($userplace_settings_feedback['signin_feedback_password_change_same']) ? $userplace_settings_feedback['signin_feedback_password_change_same'] : esc_html__('Your current password and new passwrod must not be same.', 'userplace');
		$password_old_change_empty = !empty($userplace_settings_feedback['signin_feedback_password_old_change_empty']) ? $userplace_settings_feedback['signin_feedback_password_old_change_empty'] : esc_html__('Old password field is empty. Enter your current password.', 'userplace');
		$password_change_empty = !empty($userplace_settings_feedback['signin_feedback_password_change_empty']) ? $userplace_settings_feedback['signin_feedback_password_change_empty'] : esc_html__('Sorry, we do not accept empty passwords.', 'userplace');
		$password_change_mismatch = !empty($userplace_settings_feedback['signin_feedback_password_change_mismatch']) ? $userplace_settings_feedback['signin_feedback_password_change_mismatch'] : esc_html__('The two passwords you entered did not match.', 'userplace');


		switch ($error_code) {
			case 'empty_username':
				if (!empty($empty_username)) {
					return esc_attr($empty_username);
				} else {
					return esc_html__('You do have an email address, right?', 'userplace');
				}

			case 'empty_password':
				if (!empty($empty_password)) {
					return $empty_password;
				} else {
					return esc_html__('You need to enter a password to login.', 'userplace');
				}

			case 'invalid_username':
				if (!empty($invalid_username)) {
					return $invalid_username;
				} else {
					return esc_html__(
						"We don't have any users with that email address. Maybe you have used a different email/username during sign up!",
						'userplace'
					);
				}


			case 'incorrect_password':
				if (!empty($incorrect_password)) {
					/* translators: %s is replaced with "ask if you forget password link" */
					$err = $incorrect_password . esc_html__(" <a href='%s'>Did you forget your password</a>?", 'userplace');
				} else {
					/* translators: %s is replaced with "forget password link" */
					$err = esc_html__(
						"The password you have entered is incorrect. <a href='%s'>Did you forget your password</a>?",
						'userplace'
					);
				}
				return sprintf($err, wp_lostpassword_url());

				// Registration errors
			case 'email':
				if (!empty($invalid_email)) {
					return $invalid_email;
				} else {
					return esc_html__('The email address you have entered is not valid.', 'userplace');
				}


			case 'email_exists':
				if (!empty($email_exists)) {
					return $email_exists;
				} else {
					return esc_html__('An account already exists with this email address.', 'userplace');
				}

			case 'username_exists':
				if (!empty($username_exists)) {
					return $username_exists;
				} else {
					return esc_html__('An account exists with this username.', 'userplace');
				}

			case 'username_invalid':

				return esc_html__('The given username is not valid. No white space character is allowed', 'userplace');


			case 'closed':
				if (!empty($closed)) {
					return $closed;
				} else {
					return esc_html__('Registering new user is currently not allowed.', 'userplace');
				}


				// Lost password

			case 'empty_username':
				if (!empty($lost_empty_username)) {
					return $lost_empty_username;
				} else {
					return esc_html__('You need to enter your email address to continue.', 'userplace');
				}

			case 'invalid_email':
			case 'invalidcombo':
				if (!empty($invalidcombo)) {
					return $invalidcombo;
				} else {
					return esc_html__('There is no user registered with this email address.', 'userplace');
				}


				// Reset password

			case 'expiredkey':
			case 'invalidkey':
				if (!empty($invalidkey)) {
					return $invalidkey;
				} else {
					return esc_html__('The password reset link you used is not valid anymore.', 'userplace');
				}


			case 'password_reset_mismatch':
				if (!empty($password_reset_mismatch)) {
					return $password_reset_mismatch;
				} else {
					return esc_html__("The two passwords you entered did not match.", 'userplace');
				}


			case 'password_reset_empty':
				if (!empty($password_reset_empty)) {
					return $password_reset_empty;
				} else {
					return esc_html__("Sorry, we don't accept empty passwords.", 'userplace');
				}


				// Change password
			case 'password_old_mismatch':
				if (!empty($password_old_mismatch)) {
					return $password_old_mismatch;
				} else {
					return esc_html__('You entered an incorrect old password.');
				}

			case 'password_change_same':
				if (!empty($password_change_same)) {
					return $password_change_same;
				} else {
					return esc_html__('Your current password and new passwrod must not be same.');
				}

			case 'password_old_change_empty':
				if (!empty($password_old_change_empty)) {
					return $password_old_change_empty;
				} else {
					return esc_html__('Old password field is empty. Enter your current password.');
				}

			case 'password_change_empty':
				if (!empty($password_change_empty)) {
					return $password_change_empty;
				} else {
					return esc_html__("Sorry, we don't accept empty passwords.");
				}

			case 'password_change_mismatch':
				if (!empty($password_change_mismatch)) {
					return $password_change_mismatch;
				} else {
					return esc_html__("The two passwords you entered did not match.", 'userplace');
				}


			default:
				break;
		}

		$error_message = apply_filters('userplace_login_register_error_message', $error_code);

		if (empty($error_message)) {
			return esc_html__(
				'An unknown error occurred. Please try again later.',
				'userplace'
			);
		} else {
			return $error_message;
		}
	}

	/**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register()
	{
		if ('GET' == $_SERVER['REQUEST_METHOD']) {
			if (is_user_logged_in()) {
				$this->redirect_logged_in_user();
			} else {
				wp_redirect(home_url($this->register_page));
			}
			exit;
		}
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user($redirect_to = null)
	{
		$user = wp_get_current_user();
		if (user_can($user, 'manage_options')) {
			if ($redirect_to) {
				wp_safe_redirect($redirect_to);
			} else {
				wp_redirect(admin_url());
			}
		} else {
			wp_redirect(home_url('console'));
		}
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate($user, $username, $password)
	{
		// Check if the earlier authenticate filter (most likely,
		// the default WordPress authentication) functions have found errors
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (is_wp_error($user)) {
				$error_codes = join(',', $user->get_error_codes());

				$login_url = home_url($this->sign_in_page);
				$login_url = add_query_arg('login', $error_codes, $login_url);

				wp_redirect($login_url);
				exit;
			}
		}

		return $user;
	}

	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login($redirect_to, $requested_redirect_to, $user)
	{
		$redirect_url = home_url();

		if (!isset($user->ID)) {
			return $redirect_url;
		}

		if (user_can($user, 'manage_options')) {
			// Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
			if ($requested_redirect_to == '') {
				$redirect_url = admin_url();
			} else {
				$redirect_url = $requested_redirect_to;
			}
		} else {
			// Non-admin users always go to their account page after login
			// Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
			if ($requested_redirect_to == '') {
				$redirect_url = home_url('console');
			} else {
				$redirect_url = $requested_redirect_to;
			}
		}

		return wp_validate_redirect($redirect_url, home_url());
	}

	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$redirect_to = isset($_REQUEST['redirect_to']) ? sanitize_text_field($_REQUEST['redirect_to']) : null;

			if (is_user_logged_in()) {
				$this->redirect_logged_in_user($redirect_to);
				exit;
			}

			// The rest are redirected to the login page
			$login_url = home_url($this->sign_in_page);
			if (!empty($redirect_to)) {
				$login_url = add_query_arg('redirect_to', $redirect_to, $login_url);
			}

			wp_redirect($login_url);
			exit;
		}
	}

	/**
	 * Redirect to custom login page after the user has been logged out.
	 */
	public function redirect_after_logout()
	{
		$redirect_url = home_url($this->sign_in_page . '?logged_out=true');
		wp_safe_redirect($redirect_url);
		exit;
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 */
	public function redirect_to_custom_lostpassword()
	{
		if ('GET' == $_SERVER['REQUEST_METHOD']) {
			if (is_user_logged_in()) {
				$this->redirect_logged_in_user();
				exit;
			}

			wp_redirect(home_url($this->forgot_your_password));
			exit;
		}
	}
}
