import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;

let fields = [
  {
    id: 'userplace_payment_template_select_type',
    type: 'select',
    label: 'Select Template Type',
    param: 'userplace_payment_template_select_type',
    multiple: false,
    options: {
      user: 'User',
      console: 'Console',
    },
    value: null, // eikhane changes
  },
];
export default class TeplateGenerator extends Component {
  constructor(props) {
    super(props);
    let preValue = {};
    console.log(USERPLACE_ADMIN.UPDATED_TEMPLATE);
    try {
      preValue = USERPLACE_ADMIN.UPDATED_TEMPLATE
        ? JSON.parse(USERPLACE_ADMIN.UPDATED_TEMPLATE)
        : {};
    } catch (e) {}
    this.state = {
      preValue,
    };
  }
  render() {
    const { preValue } = this.state;
    let changedData = null;
    const getUpdatedFields = data => {
      const newData = {};
      fields.forEach(field => {
        const id = field.id.replace('TemplateSettings__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      changedData = data;
      document.getElementById(
        '_userplace_payment_template_data'
      ).value = JSON.stringify(newData);
    };

    const reuseFormOption = {
      reuseFormId: 'TeplateSettings',
      fields,
      preValue,
      getUpdatedFields,
      errorMessages: {},
    };
    return (
      <div>
        <ReuseForm {...reuseFormOption} />
      </div>
    );
  }
}

const documentRoot = document.getElementById(
  'userplace_payment_template_metabox'
);
if (documentRoot) {
  render(<TeplateGenerator />, documentRoot);
}
