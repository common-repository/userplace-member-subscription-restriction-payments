<?php

/**
 * Nav Menu API: Userplace_Nav_Walker class
 *
 * @package Userplace
 * @subpackage Nav_Menus
 * @since 1.0.0
 */


/**
 * Core class used to implement an HTML list of nav menu items.
 *
 * @since 1.0.0
 *
 * @see Walker
 */

class Userplace_Mobile_Nav_Walker extends Walker_Nav_Menu
{
	use Userplace\Payment_Info;
	/**
	 * Starts the list before the elements are added.
	 *
	 * @since 1.0.0
	 *
	 * @see Walker::start_lvl()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 */
	function start_lvl(&$output, $depth = 0, $args = array())
	{
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"dropdown-menu\">\n";
	}


	/**
	 * Starts the element output.
	 *
	 * @since 1.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param stdClass $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
	 */
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
	{
		global $wp_query, $wpdb;
		if (is_array($args)) {
			$item_output = $args['before'];
		} else {
			$item_output = $args->before;
		}
		$user = wp_get_current_user();
		$role = (array) $user->roles;
		$user_id  = get_current_user_id();
		$user_subscribed_plan = $this->getUserSubscriptionPlan($user_id);
		if (current_user_can('administrator')) {
			$item_output = self::render_menu($output, $item, $depth, $args, $id);
		} elseif (is_user_logged_in()) {
			switch ($item->userplace_nav_public) {
				case 'logged_in':
					if (empty($item->restricted_plans) || !in_array($user_subscribed_plan, $item->restricted_plans) || $role[0] === 'administrator') {
						$item_output = self::render_menu($output, $item, $depth, $args, $id);
					}
					break;
				case 'everyone':
					if (empty($item->restricted_plans) || !in_array($role[0], $item->restricted_plans) || $role[0] === 'administrator') {
						$item_output = self::render_menu($output, $item, $depth, $args, $id);
					}
					break;
			}
		} else {
			switch ($item->userplace_nav_public) {
				case 'logged_out':
					$item_output = self::render_menu($output, $item, $depth, $args, $id);
					break;

				case 'everyone':
					$item_output = self::render_menu($output, $item, $depth, $args, $id);
					break;
			}
		}

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}


	/**
	 * Fallback Function
	 *
	 * @since 1.0.0
	 *
	 * @see Walker
	 */
	public static function fallback($args)
	{
		if (current_user_can('manage_options')) {
			extract($args);

			$fb_output = null;

			if ($container) {
				$fb_output = '<' . $container;

				if ($container_id)
					$fb_output .= ' id="' . $container_id . '"';

				if ($container_class)
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul class="userplace-initialize-menu ';

			if ($menu_id)
				$fb_output .= ' id="' . $menu_id . '"';

			if ($menu_class)
				$fb_output .= $menu_class;

			$fb_output .= '">';
			$fb_output .= '<li><a href="' . admin_url('nav-menus.php') . '">'.esc_html__('Add A Menu', 'userplace').'</a></li>';
			$fb_output .= '</ul>';

			if ($container) {
				$fb_output .= '</' . $container . '>';
			}
			
			$allowed_html = [
				'a' 	=> [
					'href'     => true,
					'rel'      => true,
					'rev'      => true,
					'name'     => true,
					'target'   => true,
				],
				'ul' 	=> [],
				'li' 	=> [],
				'span' 	=> [],
				'p'		=> []
			];

			echo wp_kses($fb_output, $allowed_html);
		}
	}

	private static function render_menu(&$output, $item, $depth = 0, $args = array(), $id = 0)
	{
		if (is_array($args)) {
			$item_output = $args['before'];
		} else {
			$item_output = $args->before;
		}
		$indent = ($depth) ? str_repeat("\t", $depth) : '';
		$class_names = $value = '';
		$classes = empty($item->classes) ? array() : (array) $item->classes;

		$key = '_menu_item_menu_item_parent';
		$has_children = 0;
		if (in_array('menu-item-has-children', $classes)) {
			$has_children = 1;
			array_push($classes, "dropdown rq-dropdown");
		}

		if (in_array('current-menu-item', $classes, true) || in_array('current_page_item', $classes, true) || in_array('current-menu-ancestor', $classes, true)) {
			$classes = array_diff($classes, array('current-menu-item', 'current_page_item', 'active'));
			array_push($classes, "active");
		}

		if ($item->enable_popup == 'enable') {
			array_push($classes, "userplace-menu-popup");
		}

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
		$class_names = ' class="' . esc_attr($class_names) . '"';

		$output .= $indent . '<li id="mobile-menu-item-' . $item->ID . '"' . $value . $class_names . '>';
		$attributes  = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn) . '"' : '';

		if ($item->hash == 1) {
			$attributes .= ' href="' . get_site_url() . '/#' . esc_attr($item->subtitle) . '"';
		} else {
			if ($has_children > 0) {
				$attributes .= !empty($item->url)        ? ' href="' . esc_attr($item->url) . '" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" ' : '';
			} else {
				$attributes .= !empty($item->url)        ? ' href="' . esc_attr($item->url) . '"' : '';
			}
		}

		$link_class_names = ' class="' . esc_attr($item->userplace_nav_class) . '"';

		$item_output .= '<a' . $attributes . ' ' . $link_class_names . '>';
		if (isset($item->icon) && !empty($item->icon)) {
			$item_output .= '<span><i class="' . $item->icon . '"></i></span>';
		}
		if (is_array($args)) {
			$item_output .= $args['link_before'] . apply_filters('the_title', $item->title, $item->ID) . $args['link_after'];
		} else {
			$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		}

		if ($has_children > 0 && isset($args->hide_dp_icon) && $args->hide_dp_icon !== 'yes') {
			$item_output .= '<span class="ion-chevron-down"></span>';
		}
		//$item_output .= $args->link_after;
		// $item_output .= ' '.$item->popup_shortcode.'</a>';
		$item_output .= '</a>';
		if ($has_children > 0) {
			$item_output .= '<span class="ion ion-ios-arrow-forward menu-drop-down-selector"></span>';
		}
		if (is_array($args)) {
			$item_output .= $args['after'];
		} else {
			$item_output .= $args->after;
		}
		$item_output .= '<div class="white-popup mfp-hide">';
		$item_output .= do_shortcode($item->popup_content);
		$item_output .= '</div>';

		return $item_output;
	}
}
