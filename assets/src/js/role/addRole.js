import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;
const fields = USERPLACE_ADMIN.fields;
export default class RoleSettings extends Component {
  constructor(props) {
    super(props);
    let preValue = {};
    try {
      preValue = USERPLACE_ADMIN.ROLE_SETTINGS
        ? JSON.parse(USERPLACE_ADMIN.ROLE_SETTINGS)
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
        const id = field.id.replace('RoleSettings__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      document.getElementById(
        '_userplace_add_new_role_settings'
      ).value = JSON.stringify(newData);
    };
    const reuseFormOption = {
      reuseFormId: 'RoleSettings',
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

const documentRoot = document.getElementById('userplace_add_new_role_settings');
if (documentRoot) {
  render(<RoleSettings />, documentRoot);
}