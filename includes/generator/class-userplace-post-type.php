<?php

/**
 * Generate custom post type on request
 */

namespace Userplace;

use Doctrine\Common\Inflector\Inflector;

class Generate_Post_Type
{

	/**
	 *  Sample Array
	 *  $post_types = array(
	 *  	array(
	 *  		'name' => 'custom_name',
	 *  		'supports' => array(),
	 *  		'menuPosition' => '',
	 *  		'postPublic' => '',
	 *  		'publiclyQueryable' => '',
	 *  		'showUi' => '',
	 *  		'showInMenu' => '',
	 *  		'hasArchive' => '',
	 *  		'hierarchical' => '',
	 *  	)
	 *  );
	 */

	protected $config = array(
		"name" 							=> 'custom',
		"postPublic" 				=> true,
		"publiclyQueryable" => true,
		"showUi" 						=> true,
		"showInMenu" 				=> true,
		"rewrite" 					=> true,
		"hasArchive" 				=> true,
		"hierarchical" 			=> true,
		"menuPosition" 			=> 25,
		"supports" 					=> array(),
		"menuIcon" 					=> 'dashicons-plus',
	);

	protected $supports = array(
		'title' 					=> false,
		'editor' 					=> false,
		'author' 					=> false,
		'thumbnail' 			=> false,
		'excerpt' 				=> false,
		'trackbacks' 			=> false,
		'customFields' 		=> false,
		'comments' 				=> false,
		'revisions' 			=> false,
		'pageAttributes' 	=> false,
		'postFormats' 		=> false,
	);

	public function __construct($post_types)
	{
		$this->generate_custom_post($post_types);
		add_filter('enter_title_here', array($this, 'change_default_title'));
	}

	public function change_default_title($title)
	{
		$screen = get_current_screen();
		if ('userplace_post_type' == $screen->post_type ||  'userplace_taxonomy' == $screen->post_type ||  'userplace_term_metabox' == $screen->post_type ||  'userplace_metabox' == $screen->post_type) { // Replace 'post' with the name of the post type you want to use with the new title
			$title .= ' (Required)';
		}

		return $title;
	}


	/**
	 * Generate Custom post type
	 *
	 * @param array $post_types
	 *
	 * @return void
	 *
	 */

	public function generate_custom_post($post_types)
	{
		$post_type_supports = array();
		global $wp_roles;
		if (!empty($post_types)) {
			foreach ($post_types as $post_type) {
				$post_type 	= array_merge($this->config, $post_type);
				$support 		= array_merge($this->supports, $post_type['supports']);

				$post_type_supports[] = ($support['title'] ? 'title' : '');
				$post_type_supports[] = ($support['editor'] ? 'editor' : '');
				$post_type_supports[] = ($support['author'] ? 'author' : '');
				$post_type_supports[] = ($support['thumbnail'] ? 'thumbnail' : '');
				$post_type_supports[] = ($support['excerpt'] ? 'excerpt' : '');
				$post_type_supports[] = ($support['trackbacks'] ? 'trackbacks' : '');
				$post_type_supports[] = ($support['customFields'] ? 'custom-fields' : '');
				$post_type_supports[] = ($support['comments'] ? 'comments' : '');
				$post_type_supports[] = ($support['revisions'] ? 'revisions' : '');
				$post_type_supports[] = ($support['pageAttributes'] ? 'page-attributes' : '');
				$post_type_supports[] = ($support['postFormats']  ? 'post-formats' : '');
				$plural_name = Inflector::pluralize($post_type['showName']);
				$singular_name = Inflector::singularize($post_type['showName']);
				// Post type labels
				$labels = array_merge(array(
					'name'               => _x($plural_name, 'post type general name'),
					'singular_name'      => _x($singular_name, 'post type singular name'),
					'add_new'            => _x('Add New ' . $singular_name, $singular_name),
					'add_new_item'       => esc_html__('Add New ' . $singular_name, 'userplace'),
					'edit_item'          => esc_html__('Edit ' . $singular_name, 'userplace'),
					'new_item'           => esc_html__('New ' . $singular_name), 'userplace',
					'all_items'          => esc_html__('All ' . $singular_name, 'userplace'),
					'view_item'          => esc_html__('View ' . $singular_name, 'userplace'),
					'search_items'       => esc_html__('Search ' . $singular_name, 'userplace'),
					'not_found'          => esc_html__('No ' . $singular_name . ' found', 'userplace'),
					'not_found_in_trash' => esc_html__('No ' . $singular_name . ' found in Trash', 'userplace'),
					'parent_item_colon'  => '',
					'menu_name'          => esc_html($plural_name)
				), isset($post_type['label']) ? $post_type['label'] : array());

				$post_type_name = str_replace(' ', '_', strtolower($post_type['name']));
				$post_type_name_plural = str_replace(' ', '_', strtolower(Inflector::pluralize($post_type['name'])));

				$args = array(
					'labels'             => $labels,
					'public'             => (bool) $post_type['postPublic'],
					'publicly_queryable' => (bool) $post_type['publiclyQueryable'],
					'show_ui'            => (bool) $post_type['showUi'],
					'show_in_menu'       => $post_type['showInMenu'],
					'show_in_rest'       => isset($post_type['showInRest']) ? $post_type['showInRest'] : false,
					'query_var'          => true,
					'capability_type'	 => array($post_type_name, $post_type_name_plural),
					'map_meta_cap'       => true,
					'rewrite'            => (bool) $post_type['rewrite'],
					'has_archive'        => (bool) $post_type['hasArchive'],
					'hierarchical'       => (bool) $post_type['hierarchical'],
					'menu_position'      => $post_type['menuPosition'],
					'supports'           => $post_type_supports,
					'menu_icon'			 => $post_type['menuIcon'],
					'exclude_from_search' => isset($post_type['excludeFromSearch']) ? $post_type['excludeFromSearch'] : false,
				);

				// Register post type
				register_post_type($post_type_name, $args);

				// Post type capabilities for administrator
				$wp_roles->add_cap('administrator', "edit_{$post_type_name}");
				$wp_roles->add_cap('administrator', "read_{$post_type_name}");
				$wp_roles->add_cap('administrator', "delete_{$post_type_name}");
				$wp_roles->add_cap('administrator', "edit_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "edit_others_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "publish_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "read_private_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "delete_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "delete_private_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "delete_published_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "delete_others_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "edit_private_{$post_type_name_plural}");
				$wp_roles->add_cap('administrator', "edit_published_{$post_type_name_plural}");

				// clean the post type supports array
				$post_type_supports = array();
			}
		}

		return $wp_roles;
	}
}
