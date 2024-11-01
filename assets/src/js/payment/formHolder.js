import React, { Component } from 'react';
import { render } from 'react-dom';

export default function MenuBuilder(props) {
  const { menus } = props;
  const menuArray = Object.keys(menus);
  if (!menus.general) {
    menuArray.unshift('general');
  }
  return (
    <div className={'scwp-tab-menu-wrapper'}>
      {menuArray.map(menu => {
        return (
          <button
            key={`menu_${menu}`}
            type="button"
            className={
              props.menuId === menu
                ? 'active scwp-tab-menu-button'
                : 'scwp-tab-menu-button'
            }
            onClick={props.changeMenu.bind(this, menu)}
          >
            {menu === 'general' && !menus[menu] ? 'General' : menus[menu]}
          </button>
        );
      })}
    </div>
  );
}
