<div class="rqInvoiceTable up-userplace-widgets">
	<h3 class="rqPageTitle"><?php echo esc_html__('Payment history ', 'userplace'); ?></h3>
	<div class="rqUserplaceTable">
		<div class="rqUserplaceTableHead">
			<h4 class="rqUserplaceInvoiceId"><?php echo esc_html__('ID ', 'userplace'); ?></h4>
			<h4 class="rqUserplaceInvoiceDate"><?php echo esc_html__('Date', 'userplace'); ?></h4>
			<h4 class="rqUserplaceInvoiceAmount"><?php echo esc_html__('Amount ', 'userplace'); ?></h4>
			<h4 class="rqUserplaceInvoiceStatus"><?php echo esc_html__('Currency ', 'userplace'); ?></h4>
			<h4 class="rqUserplaceInvoiceDownload">Download</h4>
		</div>
		<?php $admin_url = admin_url('admin-ajax.php'); ?>
		<div class="rqUserplaceTableBody">
			<?php
			foreach ($invoices as $invoice) : ?>
				<div class="rqUserplaceTabelListItem">
					<span class="rqUserplaceInvoiceId"><?php echo esc_html($invoice->id); ?></span>
					<span class="rqUserplaceInvoiceDate"><?php echo esc_html(date_format(\Carbon\Carbon::parse($invoice->created_at, 'UTC'), 'M j, Y h:m:s A')); ?></span>
					<span class="rqUserplaceInvoiceAmount"><?php echo esc_html($invoice->amount); ?></span>
					<span class="rqUserplaceInvoiceAmount"><?php echo esc_html(strtoupper($invoice->currency)); ?></span>
					<?php $ajax_url = $admin_url . '?action=userplace_download_pdf_invoice&id=' . esc_attr($invoice->id); ?>
					<span class="rqUserplaceInvoiceDownload"><a href="<?php echo esc_url($ajax_url) ?>"><i class="ion-ios-cloud-download"></i></a></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php if (empty($invoices)) { ?>
		<div class="userplace-no-invoices"><?php echo esc_html__('No Transactions available!', 'userplace'); ?></div>
	<?php } ?>
</div>