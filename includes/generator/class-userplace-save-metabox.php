<?php

/**
 * Generate MetaBox Saver
 */

namespace Userplace;

class Generate_Metabox_Saver
{
	public function __construct($args)
	{
		$this->generate_metabox_saver($args);
	}
	public function generate_metabox_saver($args)
	{
		global $wpdb;
		if (isset($args) && is_array($args)) {
			foreach ($args as $meta_args) {
				$post_type = get_post_type($meta_args['post_id']);

				if ($post_type === $meta_args['post_type']) {
					if (isset($meta_args['meta_fields']) && is_array($meta_args['meta_fields'])) {
						foreach ($meta_args['meta_fields'] as $key => $meta_field) {
							if (isset($_POST[$meta_field])) {
								$meta_value = $_POST[$meta_field];
								$json_meta_value = json_decode(stripslashes_deep($meta_value), true);

								if (get_post_type() == 'userplace_plan') {
									$default_plan = userplace_get_default_plan();
									if (!empty($default_plan->plan_id)) {
										if (isset($json_meta_value['default_plan']) && $json_meta_value['default_plan'] == 'true') {
											update_post_meta($default_plan->ID, 'default_plan', 'false');
											$plan_restrictions = json_decode(get_post_meta($default_plan->ID, '_userplace_plan_restrictions', true), true);
											$plan_restrictions['default_plan'] = 'false';
											update_post_meta($default_plan->ID, '_userplace_plan_restrictions', json_encode($plan_restrictions, true));
										}
									}
								}


								$updated_pre_value = array();
								if (isset($_POST['_thumbnail_id'])) {
									$featured_image_id 	= intval($_POST['_thumbnail_id']);
									$featured_image_url = wp_get_attachment_url($featured_image_id);
									$featured_image[] 	= array(
										'id' 	=> $featured_image_id,
										'rsid' 	=> $featured_image_id,
										'value' => $featured_image_id,
										'url' 	=> $featured_image_url,
									);
								}
								// Find Out all the registered form for this post type
								$meta_query = $wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta}
									WHERE meta_key = %s and
								meta_value = %s", 'formBuilderPostTypeSelect', $meta_args['post_type']);
								$form_list = $wpdb->get_results($meta_query, 'ARRAY_A');
								if (isset($form_list) && !empty($form_list)) {
									$form_post_id = $form_list[0]['post_id'];
									//get form settings
									$form_builder_string_meta = get_post_meta($form_post_id, '_reuse_form_builder_data', true);
									$form_builder_meta_object = json_decode($form_builder_string_meta);
									$fields = $form_builder_meta_object->formBuilder->fields;
									if (isset($fields) && is_array($fields)) {
										foreach ($fields as $key => $field) {
											if ($field->type == 'geobox') {
												$map_field_id = $field->id;
											}
										}
									}
									$global_settings = $form_builder_meta_object->formBuilder->globalSettings->settings->fieldSettings;
									if (isset($global_settings) && is_array($global_settings)) {
										foreach ($global_settings as $value) {
											if ($value->postDestination == 'taxonomies') {
												$fields_by_destinations['taxonomies'][] = $value;
											}
											if ($value->postDestination == 'post_keys') {
												$fields_by_destinations['post_keys'][] = $value;
											}
											if ($value->postDestination == 'meta_keys') {
												$fields_by_destinations['meta_keys'][] = $value;
											}
										}
									}
									$content_post = get_post($meta_args['post_id']);
									if (isset($fields_by_destinations) && is_array($fields_by_destinations) && array_key_exists('post_keys', $fields_by_destinations)) {
										if (isset($fields_by_destinations['post_keys']) && is_array($fields_by_destinations['post_keys'])) {
											foreach ($fields_by_destinations['post_keys'] as $key => $post_key) {
												$updated_pre_value[$post_key->fieldKey] = preg_replace('/\r\n|\r|\n/', '<br/>', $content_post->{$post_key->saveKey});
											}
										}
									}
									if (isset($fields_by_destinations) && is_array($fields_by_destinations) && array_key_exists('meta_keys', $fields_by_destinations)) {
										foreach ($fields_by_destinations['meta_keys'] as $key => $meta_key) {
											if (isset($featured_image) && $meta_key->saveKey == '_thumbnail_id') {
												$updated_pre_value[$meta_key->fieldKey] = $featured_image;
											}
											if (isset($json_meta_value) && is_array($json_meta_value) && array_key_exists($meta_key->saveKey, $json_meta_value)) {
												$updated_pre_value[$meta_key->fieldKey] = $json_meta_value[$meta_key->saveKey];
											}
										}
									}
									if (isset($fields_by_destinations) && is_array($fields_by_destinations) && array_key_exists('taxonomies', $fields_by_destinations)) {
										if (isset($fields_by_destinations['taxonomies']) && is_array($fields_by_destinations['taxonomies'])) {
											foreach ($fields_by_destinations['taxonomies'] as $taxonomies) {
												$all_terms_for_a_taxonomy = [];
												$terms = wp_get_post_terms($meta_args['post_id'], $taxonomies->saveKey);
												if (isset($terms) && is_array($terms)) {
													foreach ($terms as $key => $single_term) {
														$all_terms_for_a_taxonomy[] = $single_term->slug;
													}
												}
												$updated_pre_value[$taxonomies->fieldKey] = implode(',', $all_terms_for_a_taxonomy);
											}
										}
									}
									update_post_meta($meta_args['post_id'], 'pre_value', $updated_pre_value);
								}
								update_post_meta($meta_args['post_id'], $meta_field, $meta_value);
								if (isset($meta_args['has_individual']) && $meta_args['has_individual']) {
									if (isset($meta_value) && $meta_value) {
										$meta_object_data = json_decode(stripslashes_deep($meta_value), true);
										if (isset($meta_object_data) && is_array($meta_object_data)) {
											foreach ($meta_object_data as $key => $value) {
												if (isset($map_field_id) && $key == $map_field_id) {
													if (isset($value) && is_array($value)) {
														foreach ($value as $mapDataKey => $singleMapData) {
															update_post_meta($meta_args['post_id'], $mapDataKey, $singleMapData);
														}
													}
													userplace_reactive_lat_long($meta_args['post_id'], $value);
												}
												update_post_meta($meta_args['post_id'], $key, $value);
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
