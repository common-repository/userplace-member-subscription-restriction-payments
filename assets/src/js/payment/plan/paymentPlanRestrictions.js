import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
import Menu from '../formHolder';
const fields = USERPLACE_ADMIN_RESTRICTIONS.fields;

export default class PaymentPlanRestrictions extends Component {
	constructor(props) {
		super(props);
		let preValue = {};
		try {
			preValue = USERPLACE_ADMIN_RESTRICTIONS.PLAN_RESTRICTIONS
				? JSON.parse(USERPLACE_ADMIN_RESTRICTIONS.PLAN_RESTRICTIONS)
				: {};
		} catch (e) {
			console.log(e);
		}
		this.state = {
			preValue,
			menuId: 'general',
		};
	}
	render() {
		const { preValue, menuId } = this.state;
		const getUpdatedFields = data => {
			const newData = {};
			fields.forEach(field => {
				const id = field.id.replace('PlanRestrictions__', '');
				if (data[id] === undefined) {
					newData[id] = field.value;
				} else {
					newData[id] = data[id];
				}
			});
			document.getElementById(
				'_userplace_plan_restrictions'
			).value = JSON.stringify(newData);
		};
		const reuseFormOption = {
			reuseFormId: 'PlanRestrictions',
			fields,
			getUpdatedFields,
			errorMessages: {},
			preValue,
			menuId,
		};
		const changeMenu = newMenuId => {
			this.setState({
				menuId: newMenuId,
			});
		};
		return (
			<div className={'scwp-pageSettings-wrapper'}>
				<Menu
					fields={fields}
					changeMenu={changeMenu}
					menus={USERPLACE_ADMIN_RESTRICTIONS.menus}
					menuId={this.state.menuId}
				/>
				<ReuseForm {...reuseFormOption} />
			</div>
		);
	}
}

const documentRoot = document.getElementById(
	'userplace_plan_restrictions_for_post'
);
if (documentRoot) {
	render(<PaymentPlanRestrictions />, documentRoot);
}
