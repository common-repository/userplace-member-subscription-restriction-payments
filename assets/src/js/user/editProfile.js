import React, { Component } from 'react';
import { render } from 'react-dom';
import { http } from '../utility/helper';
import Menu from '../payment/formHolder';
import createHistory from 'history/createBrowserHistory';

const history = createHistory();
const location = history.location;

const ReuseForm = __REUSEFORM__;
const fields = USERPLACE_ADMIN.fields;

export default class UserProfile extends Component {
  constructor(props) {
    super(props);
    let preValue = {};
    try {
      preValue = document.getElementById('userplace_user_settings_prevalue')
        .value
        ? JSON.parse(
            document.getElementById('userplace_user_settings_prevalue').value
          )
        : {};
    } catch (err) {
      console.log(err);
    }
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
        const id = field.id.replace('ReUserProfile__', '');
        if (data[id] === undefined) {
          newData[id] = field.value;
        } else {
          newData[id] = data[id];
        }
      });
      document.getElementById(
        'userplace_user_settings_prevalue'
      ).value = JSON.stringify(newData);
    };
    const saveUserProfile = formData => {
      let data = {};
      const userId = document
        .getElementById('userplace_user_settings_prevalue')
        .getAttribute('data-user-id');
      data.userId = userId;
      data.action = USERPLACE_PAYMENT_AJAX_DATA.action;
      data.nonce = USERPLACE_PAYMENT_AJAX_DATA.nonce;
      data.action_type = 'user_personal_profile_update';
      data.data = formData.data;
      data.data.userplace_user_settings_prevalue = document.getElementById(
        'userplace_user_settings_prevalue'
      ).value;
      http.post(data).end((err, res) => {
        if (res) {
          if (jQuery('.notification-container').hasClass('dismiss')) {
            jQuery('.notification-container')
              .removeClass('dismiss')
              .addClass('userplace-selected')
              .show()
              .delay(2000)
              .fadeOut(3000)
              .addClass('dismiss')
              .removeClass('userplace-selected');
          }

          // if (jQuery('.notification-container').hasClass('selected')) {
          // 	jQuery('.notification-container')
          // 		.removeClass('selected')
          // 		.addClass('dismiss')
          // 		.show();
          // }
        }
      });
    };
    const reuseFormOption = {
      reuseFormId: 'ReUserProfile',
      fields,
      getUpdatedFields,
      errorMessages: {},
      menuId,
      getButtonData: saveUserProfile,
      preValue,
    };

    const changeMenu = newMenuId => {
      history.replace({
        ...location,
        search: `menuId=${newMenuId}`,
      });

      this.setState({
        menuId: newMenuId,
      });
    };

    return (
      <div className="scwpFrontentSettingsCentre scwp-profile-form">
        <Menu
          fields={fields}
          changeMenu={changeMenu}
          menus={USERPLACE_ADMIN.USER_EDIT_MENU}
          menuId={this.state.menuId}
        />
        <ReuseForm {...reuseFormOption} />
      </div>
    );
  }
}

const documentRoot = document.getElementById('userplace_user_settings_form');
if (documentRoot) {
  render(<UserProfile />, documentRoot);
}
