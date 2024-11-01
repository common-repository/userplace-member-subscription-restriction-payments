<?php
/*
 * Plugin Name: Userplace - Member Subscription, Restriction & Payments
 * Plugin URI: https://redq.io/userplace
 * Description: It’ll help you to monetize your site, you will able to create plans, subscriptions and restrictions that applies into view level and submission level.
 * Version: 1.2.0
 * Author: RedQ,Inc
 * Author URI: https://redq.io
 * Requires at least: 4.7
 * Tested up to: 5.8.2
 *
 * Text Domain: userplace
 * Domain Path: /languages/
 *
 * Copyright: © 2018 redqteam.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

class Userplace
{
	/**
	 * @var null
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	public $post_types = null;


	/**
	 * @create instance on self
	 *
	 * @since 1.0.0
	 */
	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		if (!defined('USERPLACE_REQUIRED_PHP_VERSION')) {
			define('USERPLACE_REQUIRED_PHP_VERSION', 5.4);
		}

		if (!defined('USERPLACE_REQUIRED_WP_VERSION')) {
			define('USERPLACE_REQUIRED_WP_VERSION', 4.5);
		}

		add_action('admin_init', array($this, 'check_version'));
		if (!self::compatible_version()) {
			return;
		}
		add_action('admin_init', array($this, 'userplace_plugin_redirect'));
		add_action('admin_bar_menu', array($this, 'userplace_top_bar_console_menu'), 999);
		$this->Bootstrap();
		$this->LoadClasses();
		add_filter('pre_get_document_title', array($this, 'pre_get_document_title'), 999, 1);
	}
	function pre_get_document_title($title)
	{
		if (get_query_var('user')) {
			$title = get_bloginfo() . ' – ' . get_query_var('user') . 
			esc_html__(' Profile', 'userplace');
		}
		return $title;
	}


	// The backup sanity check, in case the plugin is activated in a weird way,
	// or the versions change after activation.
	public function check_version()
	{
		if (!is_userplace_configured_properly() && current_user_can('administrator')) {
			add_action('admin_notices', array($this, 'plugin_not_configured'));
		}
		if (!self::compatible_version()) {
			if (is_plugin_active(plugin_basename(__FILE__))) {
				deactivate_plugins(plugin_basename(__FILE__));
				add_action('admin_notices', array($this, 'disabled_notice'));
				if (isset($_GET['activate'])) {
					unset($_GET['activate']);
				}
			}
		}
	}

	public function disabled_notice()
	{
		if (phpversion() < USERPLACE_REQUIRED_PHP_VERSION) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e('Can\'t Activate! Userplace requires PHP ' . USERPLACE_REQUIRED_PHP_VERSION . ' or higher!', 'Userplace'); ?></p>
			</div>
		<?php
		}
		if ($GLOBALS['wp_version'] < USERPLACE_REQUIRED_WP_VERSION) { ?>
			<div class="notice notice-error is-dismissible">
				<p><?php esc_html_e('Can\'t Activate! Userplace requires Wordpress ' . USERPLACE_REQUIRED_WP_VERSION . ' or higher!', 'Userplace'); ?></p>
			</div>
		<?php
		}
	}
	public function plugin_not_configured()
	{ ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e('Userplace not configured properly! Please go to ', 'userplace') ?><a href="' . admin_url('admin.php?page=userplace_configuration_status') . '" target="_blank"><?php esc_html_e('this', 'userplace') ?></a> <?php esc_html_e('url to know how to configure', 'Userplace'); ?></p>
		</div>
<?php
	}

	static function compatible_version()
	{
		if (phpversion() < USERPLACE_REQUIRED_PHP_VERSION || $GLOBALS['wp_version'] < USERPLACE_REQUIRED_WP_VERSION) return false;
		return true;
	}

	public function userplace_plugin_redirect()
	{
		if (get_option('userplace_do_activation_redirect', false)) {
			delete_option('userplace_do_activation_redirect');
			if (!isset($_GET['activate-multi'])) {
				wp_redirect(admin_url('admin.php?page=userplace'));
			}
		}
	}

	public function Bootstrap()
	{
		if (!defined('USERPLACE_DIR')) {
			define('USERPLACE_DIR', untrailingslashit(plugin_dir_path(__FILE__)));
		}

		if (!defined('USERPLACE_URL')) {
			define('USERPLACE_URL', untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)),  basename(__FILE__))));
		}

		if (!defined('USERPLACE_FILE')) {
			define('USERPLACE_FILE', dirname(__FILE__));
		}

		if (!defined('USERPLACE_CSS')) {
			define('USERPLACE_CSS', USERPLACE_URL . '/assets/dist/css/');
		}

		if (!defined('USERPLACE_JS')) {
			define('USERPLACE_JS',  USERPLACE_URL . '/assets/dist/js/');
		}

		if (!defined('USERPLACE_JS_VENDOR')) {
			define('USERPLACE_JS_VENDOR',  USERPLACE_URL . '/assets/dist/ven/');
		}

		if (!defined('USERPLACE_REUSE_VENDOR')) {
			define('USERPLACE_REUSE_VENDOR',  USERPLACE_URL . '/assets/dist/ven/reuse-form/');
		}

		if (!defined('USERPLACE_IMG')) {
			define('USERPLACE_IMG',  USERPLACE_URL . '/assets/dist/img/');
		}

		if (!defined('USERPLACE_INCLUDE')) {
			define('USERPLACE_INCLUDE', USERPLACE_DIR . '/includes/');
		}

		if (!defined('USERPLACE_TEMPLATE_PATH')) {
			define('USERPLACE_TEMPLATE_PATH', plugin_dir_path(__FILE__) . 'templates/');
		}

		if (!defined('USERPLACE_SHORTCODE_PATH')) {
			define('USERPLACE_SHORTCODE_PATH', plugin_dir_path(__FILE__) . 'shortcodes/');
		}
	}

	public function LoadClasses()
	{
		require_once('vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'class-userplace-install.php');
		register_activation_hook(__FILE__, array('Userplace\\Install', 'install'));
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'userplace-payment-utility.php');
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'class-userplace-nav-menu-metabox.php');
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'userplace-template-hooks.php');
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'userplace-template-functions.php');
		require_once(USERPLACE_INCLUDE . DIRECTORY_SEPARATOR . 'userplace-timezones.php');
		$classNames = array(
			'ICONS_Provider',
			'Listing',
			'SaveMeta',
			'Template_Loader',
			'Provider',
			'Payment_Init',
			'Ajax_Handler',
			'Router',
			'Payment_Frontend_Scripts',
			'Payment_Shortcode',
			'Post_Restrictions',
			'CommentSinglePostRestriction',
			'LoginRegister',
			'PDFInvoice',
			'Userplace_Feedback_message',
			'Integrations',
		);
		foreach ($classNames as $className) {
			$dynamicName = "Userplace\\" . $className;
			if (class_exists($dynamicName)) {
				new $dynamicName();
			}
		}

		// Load in admin panel
		if (is_admin()) {
			$adminClassNames = array(
				'Admin_Scripts',
				'Admin_Menu',
				// 'Taxonomy_Meta',
				// 'Generate_Term_Meta',
				// 'Listing_Add_Metabox',
				'HandleRestrictions',
				'AdminColumn',
				'ExtendTinyMC',
			);
			if (!class_exists('Load_Google_Map')) {
				$adminClassNames[] = 'GoogleMapLoading';
			}
			foreach ($adminClassNames as $className) {
				$dynamicName = "Userplace\\" . $className;
				if (class_exists($dynamicName)) {
					new $dynamicName();
				}
			}
		}

		add_action('plugins_loaded', array($this, 'load_textdomain'));
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function userplace_top_bar_console_menu($wp_admin_bar)
	{
		if (is_user_logged_in()) {
			$console_page_url = esc_url(site_url('/console'));
			$args = array(
				'id' => 'userplace-custom-console-button',
				'title' => esc_html__('Console', 'userplace'),
				'href' => $console_page_url,
				'meta' => array(
					'class' => 'userplace-custom-console-button-class'
				)
			);
			$wp_admin_bar->add_menu($args);
		} else {
			exit;
		}
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain()
	{
		load_plugin_textdomain('userplace', false, basename(dirname(__FILE__)) . '/languages');
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function template_path()
	{
		return apply_filters('userplace_template_path', 'userplace/');
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function template_path_name()
	{
		return apply_filters('userplace_template_path_name', 'userplace');
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_path()
	{
		return untrailingslashit(plugin_dir_path(__FILE__));
	}
}

function Userplace()
{
	return Userplace::instance();
}

$GLOBALS['userplace'] = Userplace();
