<div class="notice notice-error is-dismissible">
	<h3><?php esc_html_e('Sorry this post has been Drafted due to lack of facility in your Subscription', 'userplace'); ?></h3>
	<?php
	if (isset($restriction_details[$post->post_type])) {
		if ($restriction_details[$post->post_type]['max_posts'] ==  $restriction_details[$post->post_type]['used_quota']) { ?>
			<p><?php printf(esc_html_x('Post limit exceeded! You can only publish <strong>%d</strong> posts with this Subscription', 'userplace'), $restriction_details[$post->post_type]['max_posts']); ?></p>
		<?php }
		if ($restriction_details[$post->post_type]['max_terms_per_post'] < $total_number_of_terms) { ?>
			<p><?php printf(esc_html_x('Terms limit exceeded! You can only add <strong>%d</strong> post terms in a post.', 'userplace'), $restriction_details[$post->post_type]['max_terms_per_post']); ?></p>
		<?php }
	}
	if (!empty($not_allowed_term)) { ?>
		<p><?php printf(esc_html_x('Sorry these terms are not allowed in your subscription plan: <strong>%s</strong>', 'userplace'), implode(', ', $not_allowed_term)); ?></p>
	<?php }
	if (!empty($not_allowed_metas)) { ?>
		<p><?php printf(esc_html_x('Sorry these metakeys are not allowed in your subscription plan: <strong>%s</strong>', 'userplace'), implode(', ', $not_allowed_metas)); ?></p>
	<?php } ?>
	<?php if (isset($submission_allowed) && !$submission_allowed) { ?>
		<p><?php echo esc_html__('Sorry you can not add listing in your subscription', 'userplace') ?></p>
	<?php } ?>
</div>