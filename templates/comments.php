<?php
$comment_restriction_feedback = userplace_get_settings('general_comment_restriction_message');
if(isset($comment_restriction_feedback) && $comment_restriction_feedback != ''){
	$message = $comment_restriction_feedback;
}else{
	$message =  esc_html__('Sorry You are not eligible to view or post comment!', 'userplace');
}
$message = apply_filters('userplace_comment_restriction_message',  $message);
echo do_shortcode($message);
