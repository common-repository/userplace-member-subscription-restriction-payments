import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
import Menu from '../planBuilderMenu';
const fields = [
  {
    id: 'plan_name',
    type: 'text',
    menuId: 'plan',
    label: 'Plan Name',
    param: 'plan_name',
    multiple: false,
  },
  {
    id: 'plan_description',
    type: 'text',
    menuId: 'plan',
    label: 'Plan Description',
    param: 'plan_description',
    multiple: false,
  },
  {
    id: 'plan_type',
    type: 'select', 
    menuId: 'plan',
    label: 'Set Plan Type',
    param: 'plan_type',
    options: {
      FIXED: 'Fixed',
      INFINITE: 'Infinite',
    },
  },
  {
    id: 'payment_type',
    type: 'select', 
    menuId: 'payment',
    label: 'Set Payment Type',
    param: 'payment_type',
    options: {
      REGULAR: 'Regular',
      TRIAL: 'Trial',
    },
  },
  {
    id: 'payment_frequency',
    type: 'select', 
    menuId: 'payment',
    label: 'Set Payment Frequency',
    param: 'payment_frequency',
    options: {
      DAY: 'Day',
      WEEK: 'Week',
      MONTH: 'Month',
      YEAR: 'Year',
    },
  },
  {
    id: 'payment_cycle',
    type: 'text',
    menuId: 'payment',
    label: 'Payment Cycle',
    param: 'payment_cycle',
    multiple: false,
  },
  {
    id: 'plan_price',
    type: 'text',
    menuId: 'payment',
    label: 'Plan Price',
    param: 'plan_price',
    multiple: false,
  },
  {
    id: 'currency',
    type: 'select', 
    menuId: 'payment',
    label: 'Set Payment Frequency',
    param: 'currency',
    options: {
      USD: 'USD',
      CAD: 'CAD',
    },
  },
  {
    id: 'plan_recurrence',
    type: 'switch', 
    menuId: 'payment',
    label: 'Enable Recurring Payment',
    param: 'plan_recurrence',
    value: true,
  },

];
export default class PlanBuilder extends Component {
  constructor(props) {
    super(props);
    let preValue = {};
    try {
      preValue = USERPLACE_ADMIN.PLAN_BUILDER ? JSON.parse(USERPLACE_ADMIN.PLAN_BUILDER) : {};
    } catch (e) {
      console.log(e);
    }
    this.state = {
      preValue,
      menuId: 'plan',
    };
  }
  render() {
    const { preValue, menuId } = this.state;
    const getUpdatedFields = (data) => {
      const newData = {};
      fields.forEach(field => {
        const id = field.id.replace('PlanBuilder__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      document.getElementById('_userplace_plan_builder').value = JSON.stringify(newData);
    }
    const reuseFormOption = {
      reuseFormId: 'PlanBuilder',
      fields,
      getUpdatedFields,
      errorMessages: {},
      preValue,
      menuId,
    };
    const changeMenu = (newMenuId) => {
      this.setState({
        menuId: newMenuId,
      });
    }
    return (<div>
      <Menu fields={fields} changeMenu={changeMenu} />
      <ReuseForm {...reuseFormOption} />
    </div>);
  }
}
