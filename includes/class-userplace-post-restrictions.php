<?php

/**
 * Handle Post Restrictions
 */

namespace Userplace;

class Post_Restrictions
{

	public function __construct()
	{
		// add_action('pre_get_posts', array($this, 'pre_get_posts'), 0, 1);
	}

	public function pre_get_posts($query)
	{
		$current_user_id    = \get_current_user_id();
		$current_user       = new \WP_User($current_user_id);
		$current_user_roles = $current_user->roles;
		$restricted_posts   = self::post_restrictions();

		// For default post type restriction
		if (($query->is_front_page() || $query->is_home()) && $query->is_main_query()) {
			foreach ($restricted_posts as $restricted_post) {
				if (is_user_logged_in()) {
					switch ($restricted_post['public_view']) {
						case 'logged_in':
							if ($restricted_post['post_type'] === 'post') {
								if (empty($current_user) || in_array($current_user_roles[0], $restricted_post['restricted_roles'])) {
									$query->init();    //If processing the main query on the main page, reset it.
									//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
									$query->set('post_type', 'dummyPostType');
								}
							}
							break;

						case 'everyone':
							if ($restricted_post['post_type'] === 'post') {
								if (empty($current_user) || in_array($current_user_roles[0], $restricted_post['restricted_roles'])) {
									$query->init();    //If processing the main query on the main page, reset it.
									//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
									$query->set('post_type', 'dummyPostType');
								}
							}
							break;

						default:
							# code...
							break;
					}
				} else {

					switch ($restricted_post['public_view']) {
						case 'logged_in':
							if ($restricted_post['post_type'] === 'post') {
								$query->init();    //If processing the main query on the main page, reset it.
								//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
								$query->set('post_type', 'dummyPostType');
							}
							break;

						case 'everyone':
							// there is no need for every one case
							break;

						default:
							# code...
							break;
					}
				}
			}
		}

		// post restrction for all custom post types 
		foreach ($restricted_posts as $restricted_post) {
			if ($current_user_id) {
				// logged in users
				switch ($restricted_post['public_view']) {
					case 'logged_in':
						if ($query->is_archive() && $query->is_post_type_archive($restricted_post['post_type'])) {
							$restricted_post_roles = (isset($restricted_post['restricted_roles'])) ? $restricted_post['restricted_roles'] : array();
							if (empty($current_user) || in_array($current_user_roles[0], $restricted_post_roles)) {
								$query->init();    //If processing the main query on the main page, reset it.
								//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
								$query->set('post_type', 'dummyPostType');
							}
						}
						break;

					case 'everyone':
						if ($query->is_archive() && $query->is_post_type_archive($restricted_post['post_type'])) {
							$restricted_post_roles = (isset($restricted_post['restricted_roles'])) ? $restricted_post['restricted_roles'] : array();
							if (empty($current_user) || in_array($current_user_roles[0], $restricted_post_roles)) {
								$query->init();    //If processing the main query on the main page, reset it.
								//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
								$query->set('post_type', 'dummyPostType');
							}
						}
						break;

					default:
						# code...
						break;
				}
			} else {
				// logged out user
				switch ($restricted_post['public_view']) {
					case 'logged_in':
						if ($query->is_archive() && $query->is_post_type_archive($restricted_post['post_type'])) {
							$query->init();    //If processing the main query on the main page, reset it.
							//HACK: Modify the query to search for something bogus to ensure that the default values don't provide a result set.
							$query->set('post_type', 'dummyPostType');
						}
						break;

					case 'everyone':
						// there is no need for every one case
						break;

					default:
						# code...
						break;
				}
			}
		}

		return $query;
	}

	public static function post_restrictions()
	{
		$post_restrictions = userplace_get_settings('post_restriction');

		$restricted_posts = array();
		if ($post_restrictions) {
			foreach ($post_restrictions as $restriction) {
				$post_type;
				foreach ($restriction['data'] as $data) {
					if ($data['id'] === 'post_type') {
						$post_type = $data['value'];
						$restricted_posts[$post_type]['post_type'] = $data['value'];
					} else if ($data['id'] === 'public_view') {
						$restricted_posts[$post_type]['public_view'] = $data['value'];
					} else if ($data['id'] === 'restricted_roles') {
						$restricted_posts[$post_type]['restricted_roles'] = ($data['value']) ? explode(',', $data['value']) : array();
					}
				}
			}
		}

		return $restricted_posts;
	}
}
