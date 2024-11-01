import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
const fields = USERPLACE_GLOBAL.fields;
const conditions = USERPLACE_GLOBAL.conditions;

export default class SingleRestriction extends Component {
	constructor(props) {
		super(props);
		let preValue = {};
		try {
			preValue = USERPLACE_GLOBAL.RESTRICTIONS_SETTINGS
				? JSON.parse(USERPLACE_GLOBAL.RESTRICTIONS_SETTINGS)
				: {};
		} catch (e) {}
		this.state = {
			preValue,
		};
	}
	render() {
		const { preValue } = this.state;
		const getUpdatedFields = data => {
			const newData = {};
			fields.forEach(field => {
				const id = field.id.replace('SingleRestriction__', '');
				if (data[id] === undefined) {
					newData[id] = field.value;
				} else {
					newData[id] = data[id];
				}
			});
			if( !newData.restricted_plans ) {
				newData.restricted_plans = '';
			}
			document.getElementById(
				'__userplace_restrictions_settings'
			).value = JSON.stringify(newData);
		};
		const reuseFormOption = {
			reuseFormId: 'SingleRestriction',
			fields,
			getUpdatedFields,
			errorMessages: {},
			preValue,
			conditions,
		};
		return (
			<div>
				<ReuseForm {...reuseFormOption} />
			</div>
		);
	}
}

const documentRoot = document.getElementById(
	'_userplace_restrictions_settings'
);
if (documentRoot) {
	render(<SingleRestriction />, documentRoot);
}
