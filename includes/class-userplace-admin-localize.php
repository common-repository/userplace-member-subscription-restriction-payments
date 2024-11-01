<?php

/**
 *
 */

namespace Userplace;

class Admin_Lacalize
{
	public static function admin_language()
	{

		/**
		 * Localize language files for js rendering
		 */
		$lang = array(
			'POST_TYPE' 														=> esc_html__('Post Type', 'userplace'),
			'TAXONOMY' 															=> esc_html__('Taxonomy', 'userplace'),
			'PLEASE_SELECT_ANY_POST_TYPE_YOU_WANT_TO_ADD_THIS_TAXONOMY'	 		=> esc_html__('Please select any post type you want to add this taxonomy', 'userplace'),
			'PLEASE_SELECT_ANY_POST_TYPE_YOU_WANT_TO_ADD_THIS_METABOX'	 		=> esc_html__('Please select any post type you want to add this metabox', 'userplace'),
			'PLEASE_SELECT_ANY_TAXONOMT_YOU_WANT_TO_ADD_THIS_TERM_META' 		=> esc_html__('Please select any taxonomy you want to add this term meta', 'userplace'),
			'ENABLE_HIERARCHY' 													=> esc_html__('Enable Hierarchy', 'userplace'),
			'IF_YOU_WANT_TO_ENABLE_THE_TAXONOMY_HIERARCHY_SET_TRUE' 			=> esc_html__('If you want to enable the taxonomy hierarchy set true', 'userplace'),
			'POST_FORMATS' 														=> esc_html__('Post Formats', 'userplace'),
			'ENABLE_POST_FORMATS_INTO_THIS_POST' 								=> esc_html__('Enable post formats into this post.', 'userplace'),
			'PAGE_ATTRIBUTES' 													=> esc_html__('Page Attributes', 'userplace'),
			'ENABLE_PAGE_ATTRIBUTES_INTO_THIS_POST' 							=> esc_html__('Enable page attributes into this post.', 'userplace'),
			'REVISIONS' 														=> esc_html__('Revisions', 'userplace'),
			'ENABLE_REVISIONS_INTO_THIS_POST' 									=> esc_html__('Enable revisions into this post.', 'userplace'),
			'COMMENTS' 															=> esc_html__('Comments', 'userplace'),
			'ENABLE_COMMENTS_INTO_THIS_POST' 									=> esc_html__('Enable comments into this post.', 'userplace'),
			'CUSTOM_FIELDS' 													=> esc_html__('Custom Fields', 'userplace'),
			'ENABLE_CUSTOM_FIELDS_INTO_THIS_POST' 								=> esc_html__('Enable custom fields into this post.', 'userplace'),
			'TRACKBACKS' 														=> esc_html__('Trackbacks', 'userplace'),
			'ENABLE_TRACKBACKS_INTO_THIS_POST' 									=> esc_html__('Enable trackbacks into this post.', 'userplace'),
			'EXCERPT' 															=> esc_html__('Excerpt', 'userplace'),
			'ENABLE_EXCERPT_INTO_THIS_POST' 									=> esc_html__('Enable excerpt into this post.', 'userplace'),
			'THUMBNAIL' 														=> esc_html__('Thumbnail', 'userplace'),
			'ENABLE_THUMBNAIL_INTO_THIS_POST' 									=> esc_html__('Enable thumbnail into this post.', 'userplace'),
			'AUTHOR'	 														=> esc_html__('Author', 'userplace'),
			'ENABLE_AUTHOR_INTO_THIS_POST' 										=> esc_html__('Enable author into this post.', 'userplace'),
			'EDITOR' 															=> esc_html__('Editor', 'userplace'),
			'ENABLE_EDITOR_INTO_THIS_POST' 										=> esc_html__('Enable editor into this post.', 'userplace'),
			'TITLE' 															=> esc_html__('Title', 'userplace'),
			'ENABLE_TITILE_INTO_THIS_POST' 										=> esc_html__('Enable title into this post.', 'userplace'),
			'ALL_ITEMS' 														=> esc_html__('All Items', 'userplace'),
			'SINGULAR_NAME' 													=> esc_html__('Singular Name', 'userplace'),
			'POST_SLUG' 														=> esc_html__('Post Slug', 'userplace'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_ALL_ITEMS_NAME_ADD_THE_NAME_HERE' 	=> esc_html__('If want to change the default all items name, add the name here', 'userplace'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_SINGULAR_NAME_ADD_THE_NAME_HERE' 	=> esc_html__('If want to change the default singular name, add the name here', 'userplace'),
			'IF_WANT_TO_CHANGE_THE_DEFAULT_POST_SLUG_ADD_THE_NAME_HERE' 		=> esc_html__('If want to change the default post slug, add the slug here', 'userplace'),
			'MENU_POSITION' 													=> esc_html__('Menu Position', 'userplace'),
			'SELECT_THE_POST_TYPE_MENU_POSITION' 								=> esc_html__('Select the post type menu position.', 'userplace'),
			'MENU_ICON' 														=> esc_html__('Menu Icon', 'userplace'),
			'SELECT_MENU_ICON' 													=> esc_html__('Select a menu icon.', 'userplace'),
			'BELOW_FIRST_SEPARATOR' 											=> esc_html__('Below First Separator', 'userplace'),
			'BELOW_POSTS' 														=> esc_html__('Below Posts', 'userplace'),
			'BELOW_MEDIA' 														=> esc_html__('Below Media', 'userplace'),
			'BELOW_LINKS' 														=> esc_html__('Below Links', 'userplace'),
			'BELOW_PAGES' 														=> esc_html__('Below Pages', 'userplace'),
			'BELOW_COMMENTS' 													=> esc_html__('Below Comments', 'userplace'),
			'BELOW_SECOND_SEPARATOR' 											=> esc_html__('Below Second Separator', 'userplace'),
			'BELOW_PLUGINS' 													=> esc_html__('Below Plugins', 'userplace'),
			'BELOW_USERS' 														=> esc_html__('Below Users', 'userplace'),
			'BELOW_TOOLS' 														=> esc_html__('Below Tools', 'userplace'),
			'BELOW_SETTINGS' 													=> esc_html__('Below Settings', 'userplace'),
			'DEFAULT_ICON' 														=> esc_html__('Default Icon', 'userplace'),
			'UPLOAD_ICON' 														=> esc_html__('Upload Icon', 'userplace'),
			'ICON_TYPE' 														=> esc_html__('Icon Type', 'userplace'),
			'SELECT_THE_DEFAULT_ICON_TYPE_OR_UPLOAD_A_NEW' 						=> esc_html__('Select the default icon type or upload a new.', 'userplace'),
			'UPLOAD_CUSTOM_ICON' 												=> esc_html__('Upload Custom Icon', 'userplace'),
			'YOU_CAN_UPLOAD_ANY_CUSTOM_IMAGE_ICON' 								=> esc_html__('You can upload any custom image icon.', 'userplace'),
			'BUNDLE_COMPONENT' 													=> esc_html__('Bundle Component', 'userplace'),
			'PICK_COLOR' 														=> esc_html__('Pick Color', 'userplace'),
			'NO_RESULT_FOUND'	 												=> esc_html__('No result found', 'userplace'),
			'SEARCH' 															=> esc_html__('search', 'userplace'),
			'OPEN_ON_SELECTED_HOURS' 											=> esc_html__('Open on selected hours', 'userplace'),
			'ALWAYS_OPEN' 														=> esc_html__('Always open', 'userplace'),
			'NO_HOURS_AVAILABLE' 												=> esc_html__('No hours available', 'userplace'),
			'PERMANENTLY_CLOSE' 												=> esc_html__('Permanently closed', 'userplace'),
			'MONDAY' 															=> esc_html__('Monday', 'userplace'),
			'TUESDAY' 															=> esc_html__('Tuesday', 'userplace'),
			'WEDNESDAY' 														=> esc_html__('Wednesday', 'userplace'),
			'THURSDAY' 															=> esc_html__('Thursday', 'userplace'),
			'FRIDAY' 															=> esc_html__('Friday', 'userplace'),
			'SATURDAY' 															=> esc_html__('Saturday', 'userplace'),
			'SUNDAY' 															=> esc_html__('Sunday', 'userplace'),
			'WRONG_PASS' 														=> esc_html__('Wrong Password', 'userplace'),
			'PASS_MATCH' 														=> esc_html__('Password Matched', 'userplace'),
			'CONFIRM_PASS' 														=> esc_html__('Confirm Password', 'userplace'),
			'CURRENTLY_WORK' 													=> esc_html__('I currently work here', 'userplace'),
			'TEMPLATE_DESIGN_TYPE' 												=> esc_html__('Template Design Type', 'userplace'),
			'PLEASE_SELECT_THE_TEMPLATE_DESIGN_TYPE' 							=> esc_html__('Please select the template design type', 'userplace'),
			'DEFAULT' 															=> esc_html__('Default', 'userplace'),
			'ALTERNATIVE' 														=> esc_html__('Alternative', 'userplace'),
		);

		return $lang;
	}

	public static function admin_error()
	{
		/**
		 * Localize Error Message files for js rendering
		 */
		$error_message_list = array(
			'notNull'   => esc_html__('The field should not be empty', 'userplace'),
			'email'     => esc_html__('The field should be email', 'userplace'),
			'isNumeric' => esc_html__('The field should be numeric', 'userplace'),
			'isURL'     => esc_html__('The field should be Url', 'userplace'),
		);

		return $error_message_list;
	}

	public static function dynamic_page_builder_tab_list()
	{
		$tabs_in_dynamic_page = array();
		$tabs_in_dynamic_page['general'] 	= esc_html__('General Options', 'userplace');
		$tabs_in_dynamic_page['header'] 	= esc_html__('Header Options', 'userplace');
		$tabs_in_dynamic_page['background'] = esc_html__('Background Options', 'userplace');
		$tabs_in_dynamic_page['banner'] 	= esc_html__('Banner Options', 'userplace');
		$tabs_in_dynamic_page['sidebar'] 	= esc_html__('Sidebar Options', 'userplace');
		$tabs_in_dynamic_page['footer'] 	= esc_html__('Footer Options', 'userplace');
		$tabs_in_dynamic_page['copyright'] 	= esc_html__('Copyright Options', 'userplace');


		return $tabs_in_dynamic_page;
	}

	public static function get_all_taxonomies()
	{
		$restricted_taxonomies = array(
			'nav_menu',
			'link_category',
			'post_format',
		);

		$args 		= array();
		$output 	= 'objects'; // or objects
		$operator 	= 'or'; // 'and' or 'or'
		$taxonomies = get_taxonomies($args, $output, $operator);

		$formatted_taxonomies = array();

		if ($taxonomies) {
			foreach ($taxonomies  as $key => $taxonomy) {
				if (!in_array($key, $restricted_taxonomies)) {
					$formatted_taxonomies[$taxonomy->name] = $taxonomy->labels->singular_name;
				}
			}
		}

		return $formatted_taxonomies;
	}

	public static function get_all_posts()
	{
		$restricted_post_types = array(
			'attachment',
			'userplace_faq',
			'userplace_template',
			'userplace_component',
			'userplace_taxonomy',
			'userplace_term_metabox',
			'userplace_metabox',
			'userplace_form_builder',
			'userplace_plan',
			'userplace_rb_post',
			'userplace_post_type',
			'userplace_console',
			'reactive_builder',
			'reactive_grid',
			'userplace_role',
			'userplace_coupon',
			'reuseb_metabox',
			'reuseb_taxonomy',
			'reuseb_post_type',
			'reuseb_term_metabox',
			'reuseb_template',
			'page'
		);

		$args = array(
			'public'   => true,
		);

		$output 				= 'objects'; // 'names' or 'objects' (default: 'names')
		$operator 				= 'and'; // 'and' or 'or' (default: 'and')
		$post_types 			= get_post_types($args, $output, $operator);
		$formatted_post_types 	= array();

		foreach ($post_types as $key => $post_type) {
			if (!in_array($key, $restricted_post_types)) {
				$formatted_post_types[$post_type->name] = $post_type->labels->singular_name;
			}
		}

		return $formatted_post_types;
	}
}
