import React, { Component } from 'react';
import { render } from 'react-dom';
import Menu from './formHolder';
const ReuseForm = __REUSEFORM__;
import SubmitButton from './settingsSubmitButton';
const fields = USERPLACE_ADMIN.fields;

import createHistory from 'history/createBrowserHistory';

const history = createHistory();
const location = history.location;

export default class PaymentSettings extends Component {
  constructor(props) {
    super(props);
    let preValue = {};
    try {
      preValue = USERPLACE_ADMIN.PAYMENT_SETTINGS
        ? JSON.parse(USERPLACE_ADMIN.PAYMENT_SETTINGS)
        : {};
    } catch (e) {}
    if (location.search.indexOf('menuId') !== -1) {
      const urlPart = location.search.split('&');
      for (let i = 0; i < urlPart.length; i++) {
        if (urlPart[i].indexOf('menuId') !== -1) {
          const activeTab = urlPart[i].split('=')[1];
          this.state = {
            preValue,
            menuId: activeTab,
          };
        }
      }
    } else {
      this.state = {
        preValue,
        menuId: 'general',
      };
    }
  }
  render() {
    const { preValue, menuId } = this.state;
    const getUpdatedFields = data => {
      const newData = {};
      fields.forEach(field => {
        const id = field.id.replace('PaymentSettings__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      document.getElementById('_userplace_settings').value = JSON.stringify(
        newData
      );
    };
    const reuseFormOption = {
      reuseFormId: 'PaymentSettings',
      fields,
      menuId,
      getUpdatedFields,
      errorMessages: {},
      preValue,
    };
    const changeMenu = newMenuId => {
      history.push({
        ...location,
        search: `${location.search}&menuId=${newMenuId}`,
      });

      this.setState({
        menuId: newMenuId,
      });
    };
    return (
      <div>
        <div className="scwp-pageSettings-wrapper">
          <Menu
            fields={fields}
            changeMenu={changeMenu}
            menus={USERPLACE_ADMIN.SETTINGS_MENU}
            menuId={this.state.menuId}
          />
          <ReuseForm {...reuseFormOption} />
        </div>
        <SubmitButton />
      </div>
    );
  }
}

const documentRoot = document.getElementById('userplace_settings');
if (documentRoot) {
  render(<PaymentSettings />, documentRoot);
}
