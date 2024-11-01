<?php
global $wpdb, $wp_version;
?>

<style>
	.adminContainer {
		display: flex;
		justify-content: center;
		align-items: center;
		align-content: center;
		margin-top: 100px;
	}
</style>

<div class="adminContainer">
	<h1><?php esc_html_e('Welcome to Userplace', 'userplace') ?></h1>
</div>

<div class="reactive-trick reactive-wrap">
	<table class="widefat userplace-system-status" cellspacing="0">
		<thead>
			<tr>
				<th colspan="3" data-export-label="System Status">
					<h4 style="margin-top: 0"><?php esc_html_e('System Status', 'userplace') ?></h4>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php esc_html_e('WP Version: ', 'userplace') ?></td>
				<td class="<?php echo esc_attr($wp_version) >= USERPLACE_REQUIRED_WP_VERSION ? 'userplace-status-success' : 'userplace-status-error' ?>"><?php echo esc_html($wp_version); ?>
					<?php if ($wp_version < USERPLACE_REQUIRED_WP_VERSION) { ?>
						<code><?php esc_html_e('The Recommended Minimum WP version is ' . USERPLACE_REQUIRED_WP_VERSION, 'userplace'); ?></code>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('WP Memory Limit: ', 'userplace') ?></td>
				<td class="<?php echo esc_attr(WP_MEMORY_LIMIT) >= 256 ? 'userplace-status-success' : 'userplace-status-error' ?>"><?php echo esc_html(WP_MEMORY_LIMIT); ?>
					<code><?php esc_html_e('The Recommended Minimum WP Memory Limit is 256M', 'userplace'); ?></code>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('WP Multisite: ', 'userplace') ?></td>
				<td><?php if (is_multisite()) echo esc_html__('Yes', 'userplace');
						else echo esc_html__('No', 'userplace') ?> </td>
			</tr>
			<tr class="real-memory">
				<td><?php esc_html_e('Server Memory Limit:', 'userplace') ?></td>
				<td class="<?php echo ini_get('memory_limit') >= 256 ? 'userplace-status-success' : 'userplace-status-error' ?>"> <?php echo esc_html(ini_get('memory_limit')) ?>
					<?php if (ini_get('memory_limit') < 256) { ?>
						<code><?php esc_html_e('The Recommended Minimum Server Memory Limit is 256M', 'userplace'); ?></code>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP Version: ', 'userplace') ?></td>
				<td class="<?php echo phpversion() >= USERPLACE_REQUIRED_PHP_VERSION ? 'userplace-status-success' : 'userplace-status-error' ?>"><?php echo esc_html(phpversion()); ?>
					<?php if (phpversion() < USERPLACE_REQUIRED_PHP_VERSION) { ?>
						<code><?php esc_html_e('The Recommended Minimum PHP version is ' . USERPLACE_REQUIRED_PHP_VERSION, 'userplace'); ?></code>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP Post Maximum Size:', 'userplace') ?> </td>
				<td><?php echo esc_html(ini_get('post_max_size')); ?> </td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP Maximum Execution Time: ', 'userplace') ?></td>
				<td><?php echo esc_html(ini_get('max_execution_time')); ?> </td>
			</tr>
			<tr>
				<td><?php esc_html_e('PHP Maximum Input Vars: ', 'userplace') ?></td>
				<td class="<?php echo ini_get('max_input_vars') >= 3000 ? 'userplace-status-success' : 'userplace-status-error' ?>"><?php echo esc_html(ini_get('max_input_vars')); ?>
					<?php if (ini_get('max_input_vars') < 3000) { ?>
						<code><?php esc_html_e('The Recommended Minimum Input Vars is 3000', 'userplace'); ?></code>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e('Maximum Upload Size: ', 'userplace') ?></td>
				<td><?php echo esc_html(size_format(wp_max_upload_size())); ?> </td>
			</tr>
			<tr>
				<td><?php esc_html_e('Mysql Version: ', 'userplace') ?></td>
				<td class="<?php echo esc_attr($wpdb->db_version()) >= 5.6 ? 'userplace-status-success' : 'userplace-status-error' ?>"><?php echo esc_html($wpdb->db_version()); ?>
					<?php if ($wpdb->db_version() < 5.6) { ?>
						<code><?php esc_html_e('The Recommended Minimum Mysql version is 5.6.0 or greater', 'userplace'); ?></code>
					<?php } ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php
$query = $wpdb->prepare("SELECT log_details, created_at FROM {$wpdb->prefix}userplace_logs WHERE id <> %s ORDER BY id DESC LIMIT 20", '');
$results = $wpdb->get_results($query, 'ARRAY_A');
$new_result = [];
foreach ($results as $key => $error) {
	$new_result[$error['created_at']] = $error['log_details'];
}
?>

<div class="adminContainer">
	<h1> <?php esc_html_e('Recent Error Logs ', 'userplace') ?></h1>
</div>
<div class="reactive-trick reactive-wrap">
	<pre class="scwp-snippet">
		<div class="scwp-clippy-icon" data-clipboard-snippet="">
			<img class="clippy" width="13" src="<?php print USERPLACE_IMG ?>clippy.svg" alt="<?php esc_attr_e('Copy to clipboard', 'userplace') ?>">
		</div>
		<code class="js hljs php"><?php return esc_html(print_r($new_result)) ?></code>
	</pre>
</div>