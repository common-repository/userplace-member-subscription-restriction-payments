<?php

include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_header.php');

?>

<!-- HEADER -->
<?php include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_menu.php'); ?>
<!-- HEADER END -->

<!-- MAIN CONTENT -->
<div class="up-userplace-main-content console-page-main-content">
	<?php echo do_shortcode('[userplace_billing_details]'); ?>
</div>
<!-- MAIN CONTENT END -->
</div>
<!-- CONTENTS WRAPPER END -->

<?php
include_once(USERPLACE_TEMPLATE_PATH . '/console/userplace_console_footer.php');
