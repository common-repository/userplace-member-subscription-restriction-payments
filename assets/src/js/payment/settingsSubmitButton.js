import React from 'react';
import { http } from '../utility/helper.js';
const $ = jQuery;

export default function Button() {
	const onClick = event => {
		event.preventDefault();
		const settings = $('#_userplace_settings').val();
		let data = {};
		try {
			data = JSON.parse(settings);
		} catch (e) {}
		data.userplace_settings = settings;
		data.action = USERPLACE_PAYMENT_AJAX_DATA.action;
		data.nonce = USERPLACE_PAYMENT_AJAX_DATA.nonce;
		data.action_type = 'update_option';
		http.post(data).end(function(err, res) {
			if (res) {
				if (jQuery('.notification-container-settings').hasClass('dismiss')) {
					jQuery('.notification-container-settings')
						.removeClass('dismiss')
						.addClass('userplace-selected')
						.show()
						.delay(2000)
						.fadeOut(3000)
						.addClass('dismiss')
						.removeClass('userplace-selected');
				}
			}
		});
	};
	return (
		<button className="up-userplace-btn" type="button" onClick={onClick}>
			{' '}
			Save{' '}
		</button>
	);
}
