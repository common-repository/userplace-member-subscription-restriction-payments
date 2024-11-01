import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
const fields = [
	{
		id: 'plan_id',
		type: 'text',
		label: 'Plan ID',
		param: 'plan_id',
		multiple: false,
	},
];
export default class GetGatewayPlanId extends Component {
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
				const id = field.id.replace('PlanBuilder__', '');
				if (data[id] === undefined) {
					newData[id] = field.value;
				} else {
					newData[id] = data[id];
				}
			});
			document.getElementById('_userplace_plan_builder').value = JSON.stringify(
				newData
			);
		};
		const reuseFormOption = {
			reuseFormId: 'PlanBuilder',
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

const documentRoot = document.getElementById('userplace_gateway_plan_id');
if (documentRoot) {
	render(<GetGatewayPlanId />, documentRoot);
}
