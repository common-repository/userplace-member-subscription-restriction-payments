import React, { Component } from 'react';
import { render } from 'react-dom';

export default function PlanMenuBuilder (props){
  const menus = [
    {
      key: 'plan',
      text: 'General'
    },
    {
      key: 'payment',
      text: 'Payment'
    },
  ];
  return(
    <div className={"scwp-tab-menu-wrapper"}>
      {
        menus.map(menu => {
          return(<button key={`plan_menu_${menu.key}`} type='button' className={ props.menuId === menu ? 'active scwp-tab-menu-button' : 'scwp-tab-menu-button' } onClick={props.changeMenu.bind(this, menu.key)}>{menu.text}</button>);
        })
      }
    </div>
  );
}
