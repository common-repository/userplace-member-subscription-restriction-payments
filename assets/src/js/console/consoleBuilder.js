import React, { Component } from 'react';
import { render } from 'react-dom';
const ReuseForm = __REUSEFORM__;

let fields = USERPLACE_ADMIN.FIELDS;
// let fields = [
//   {
//     type: 'iconpicker',
//     label: 'Choose Console Menu Icon',
//     id: 'reuse_button_iconpicker',
//     value: 'fa fa-sort-amount-desc', // don't use menuId if you haven't configured menu component
//   },

//   //  The below commented portion is for Sublisting settings

//   {
//     id: 'form_type',
//     type: 'select',
//     label: 'Form Type',
//     multiple: 'false',
//     clearable: 'false',
//     subtitle: 'Choose the form type',
//     options: {
//       parent: 'Show in sidebar menu',
//       // Child: "Child post not in menu"
//     },
//     value: 'parent',
//   },
//   // {
//   //   id: "child_posts",
//   //   type: "select",
//   //   label: "Child Post Types",
//   //   multiple: "true",
//   //   clearable: "false",
//   //   subtitle: "Choose the child posts from the console menu type",
//   //   options: USERPLACE_ADMIN.CONSOLE_MENU ? USERPLACE_ADMIN.CONSOLE_MENU : {},
//   //   value: ""
//   // },
//   // {
//   //   id: "form_post_type",
//   //   type: "select",
//   //   label: "Select Post Type of this Form",
//   //   multiple: "false",
//   //   options: USERPLACE_ADMIN.postTypes ? USERPLACE_ADMIN.postTypes : {},
//   //   value: ""
//   // }
// ];

export default class ConsoleBuilder extends Component {
  constructor(props) {
    super(props);
    this.state = {
      preValue: USERPLACE_ADMIN.CONSOLE_BUILDER
        ? JSON.parse(USERPLACE_ADMIN.CONSOLE_BUILDER)
        : {},
    };
  }
  render() {
    const getUpdatedFields = data => {
      const newData = {};
      fields.forEach(field => {
        const id = field.id.replace('ConsoleBuilder__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      document.getElementById(
        'userplace_console_builder_data'
      ).value = JSON.stringify(newData);
    };
    const reuseFormOption = {
      reuseFormId: 'ConsoleBuilder',
      fields,
      getUpdatedFields,
      errorMessages: {},
      preValue: this.state.preValue,
    };
    return (
      <div>
        <ReuseForm {...reuseFormOption} />
      </div>
    );
  }
}

const documentRoot = document.getElementById('userplace_console_builder');
if (documentRoot) {
  render(<ConsoleBuilder />, documentRoot);
}