<?php

namespace Userplace;
// reference the Dompdf namespace
use Dompdf\Dompdf;

/**
 * 
 */
class PDFInvoice
{

	function __construct()
	{
		add_action("wp_ajax_userplace_download_pdf_invoice", array($this, 'download_pdf_invoice'));
	}

	public function download_pdf_invoice()
	{
		global $wpdb;
		$id = intval($_GET['id']);
		$query = $wpdb->prepare("SELECT * from {$wpdb->prefix}userplace_invoices WHERE id=%d", $id);
		$result = $wpdb->get_results($query);
		ob_start(); ?>
		<!DOCTYPE html>
		<html>

		<head>
			<meta charset='utf-8'>
			<title><?php esc_html_e('Invoice ', 'userplace') ?> #135</title>
			<style>
				body {
					font-size: 16px;
					color: #49535F;
					font-family: sans-serif;
				}

				.invoice-site-url {
					font-weight: bold;
				}

				.invoice-id {
					font-size: 26px;
					color: #f64e54;
					margin: 30px 0;
				}

				.invoice-client {
					margin-bottom: 5px;
				}

				table {
					margin-top: 30px;
					width: 100%;
					border: 0;
					table-layout: auto;
					border-spacing: 0px;
				}

				th,
				td {
					padding: 13px 15px;
					border: none;
				}

				th {
					text-align: left;
					font-weight: bold;
					background-color: #F5F9FA;
				}

				td {
					color: #8D93A6;
				}

				.invoice-client,
				.invoice-date {
					font-size: 14px;
					color: #4A5560;
				}

				.invoice-client span,
				.invoice-date span {
					margin-right: 5px;
				}
			</style>
		</head>

		<body>
			<div class='invoice-site-url'><a href="<?php echo esc_url(site_url()) ?>"><?php bloginfo('name'); ?></a></div>
			<h2 class='invoice-id'><?php esc_html_e('Invoice #', 'userplace') ?><?php echo esc_html($result[0]->id) ?></h2>
			<div class="invoice-client"><span><?php esc_html_e('Client:', 'userplace') ?></span> <?php echo esc_html($result[0]->customer); ?></div>
			<div class="invoice-date"><span><?php esc_html_e('Date:', 'userplace') ?></span> <?php echo esc_html($result[0]->created_at); ?></div>
			<table>
				<thead>
					<tr>
						<th><?php esc_html_e('Transaction ID', 'userplace') ?></th>
						<th><?php esc_html_e('Plan', 'userplace') ?></th>
						<th><?php esc_html_e('Card', 'userplace') ?></th>
						<th><?php esc_html_e('Total', 'userplace') ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo esc_html($result[0]->transaction_id); ?></td>
						<td><?php echo esc_html($result[0]->plan); ?></td>
						<td><?php echo esc_html($result[0]->brand . ' ' . '**** **** **** ' . $result[0]->last4); ?></td>
						<td><?php echo esc_html($result[0]->amount); ?> <?php echo esc_html($result[0]->currency); ?>*</td>
					</tr>
				</tbody>
			</table>
		</body>

		</html>
<?php
		$html = ob_get_clean();
		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		// $dompdf->loadHtml('hello world');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();
		$file_name = "Receipt-" . $result[0]->created_at;
		// Output the generated PDF to Browser
		$dompdf->stream($file_name);

		wp_die();
	}
}
