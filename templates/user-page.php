<?php

get_header('userplace');
global $userplace_payment_user_id;

$userplace_payment_username 	= get_query_var('user');
$userplace_payment_user 		  = get_user_by('login', $userplace_payment_username);
if ($userplace_payment_user) {
	$userplace_payment_user_id  = $userplace_payment_user->ID;
	$query_args = array(
		'post_type' 	=> 'userplace_template',
		'post_per_page' => 1,
		'meta_key'      => 'userplace_payment_template_select_type',
		'meta_value'    => 'user',
	);

	$the_query = get_posts($query_args);

	// if template found load it
	if ($the_query) {
		$template = get_post($the_query[0]->ID);
		if ($template) {
			$design     = get_post_meta($the_query[0]->ID, 'userplace_payment_template_design_select', true);
			if ($design == 'alt') { ?>
				<div class="rq-page-content-two">
					<div class="blog-post-single-wrapper listing-detials-content-two rq-user-profile-wrapper">
					<?php
				}
				echo do_shortcode($template->post_content);

				if ($design == 'alt') { ?>
					</div>
				</div>
<?php
				}
			} else {
				$search_page_url = site_url() . '/opps-not-found';
				$string = '<script type="text/javascript">';
				$string .= 'window.location = "' . $search_page_url . '"';
				$string .= '</script>';
				echo apply_filters('userplace_redirect_not_found', $string);
			}
		} else {
			$search_page_url = site_url() . '/opps-not-found';
			$string = '<script type="text/javascript">';
			$string .= 'window.location = "' . $search_page_url . '"';
			$string .= '</script>';
			echo apply_filters('userplace_redirect_not_found', $string);
		}
	} else {
		$search_page_url = site_url() . '/opps-not-found';
		$string = '<script type="text/javascript">';
		$string .= 'window.location = "' . $search_page_url . '"';
		$string .= '</script>';
		echo apply_filters('userplace_redirect_not_found', $string);
	}

	get_footer('userplace');
