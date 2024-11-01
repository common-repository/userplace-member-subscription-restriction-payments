import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
const fields = USERPLACE_ADMIN.fields;
export default class PayAsUGo extends Component {
	constructor(props) {
		super(props);
		let preValue = {};
		try {
			preValue = USERPLACE_ADMIN.PLAN_BUILDER
				? JSON.parse(USERPLACE_ADMIN.PLAN_BUILDER)
				: {};
		} catch (e) {
			console.log(e);
		}
		this.state = {
			preValue,
		};
	}
	render() {
		const { preValue } = this.state;
		const getUpdatedFields = data => {
			const newData = {};
			fields.forEach(field => {
				const id = field.id.replace('PayAsUGo__', '');
				if (data[id] === undefined) {
					newData[id] = field.value;
				} else {
					newData[id] = data[id];
				}
			});
			document.getElementById('_userplace_payasugo').value = JSON.stringify(
				newData
			);
		};
		const reuseFormOption = {
			reuseFormId: 'PayAsUGo',
			fields,
			getUpdatedFields,
			errorMessages: {},
			preValue,
		};
		return (
			<div>
				<ReuseForm {...reuseFormOption} />
			</div>
		);
	}
}

const documentRoot = document.getElementById('userplace_payasugo_id');
if (documentRoot) {
	render(<PayAsUGo />, documentRoot);
}
