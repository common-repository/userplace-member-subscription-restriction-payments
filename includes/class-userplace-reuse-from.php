<?php
namespace Userplace;
/**
 *
 */

class Reuse_Builder {

  public static function load($localPath) {
    $reuseform_scripts = json_decode(file_get_contents( USERPLACE_DIR . "/resource/reuse.json"),true);
    
    if (isset($reuseform_scripts['vendor'])) {
      wp_register_script( 'reuse_vendor', $localPath. $reuseform_scripts['vendor']['js'], array(), $ver = false, true);
      wp_enqueue_script( 'reuse_vendor' );
    }
    if (isset($reuseform_scripts['reuse'])) {
      wp_register_script( 'reuse-builder-js', $localPath. $reuseform_scripts['reuse']['js'], array('jquery', 'underscore'), $ver = false, true);
      wp_enqueue_script( 'reuse-builder-js' );
    }
    
    wp_localize_script( 'reuse-builder-js', 'REUSE_ADMIN', array(
      'LANG'                  => Reuse_Builder::reuse_form_language(),
      'ERROR_MESSAGE'         => Reuse_Builder::reuse_form_error_messages(),
      '_WEBPACK_PUBLIC_PATH_' => $localPath,
      'base_url'              => apply_filters( 'reuse_image_base_url',  USERPLACE_DIR ),
    ));
    
  }

  public static function reuse_form_language() {
    /**
     * Localize language files for reuse form rendering
     */
    $lang = array(
      'BUNDLE_COMPONENT'        => esc_html__('Bundle Component', 'userplace'),
      'PICK_COLOR'              => esc_html__('Pick Color','userplace'),
      'NO_RESULT_FOUND'         => esc_html__('No result found', 'userplace'),
      'SEARCH'                  => esc_html__('search','userplace'),
      'OPEN_ON_SELECTED_HOURS'  => esc_html__('Open on selected hours', 'userplace'),
      'ALWAYS_OPEN'             => esc_html__('Always open', 'userplace'),
      'NO_HOURS_AVAILABLE'      => esc_html__('No hours available', 'userplace'),
      'PERMANENTLY_CLOSE'       => esc_html__('Permanently closed', 'userplace'),
      'MONDAY'                  => esc_html__('Monday', 'userplace'),
      'TUESDAY'                 => esc_html__('Tuesday', 'userplace'),
      'WEDNESDAY'               => esc_html__('Wednesday', 'userplace'),
      'THURSDAY'                => esc_html__('Thursday', 'userplace'),
      'FRIDAY'                  => esc_html__('Friday', 'userplace'),
      'SATURDAY'                => esc_html__('Saturday', 'userplace'),
      'SUNDAY'                  => esc_html__('Sunday', 'userplace'),
      'WRONG_PASS'              => esc_html__('Wrong Password', 'userplace'),
      'PASS_MATCH'              => esc_html__('Password Matched', 'userplace'),
      'CONFIRM_PASS'            => esc_html__('Confirm Password', 'userplace'),
      'CURRENTLY_WORK'          => esc_html__('I currently work here', 'userplace'),
    );

    return $lang;
  }

  public static function reuse_form_error_messages() {
    /**
     * Localize Error Message files for js rendering
     */
    $error_message_list = array(
      'notNull'   => esc_html__('The field should not be empty', 'userplace'),
      'email'     => esc_html__('The field should be email', 'userplace' ),
      'isNumeric' => esc_html__('The field should be numeric', 'userplace' ),
      'isURL'     => esc_html__('The field should be Url', 'userplace' ),
    );
    return $error_message_list;
  }

}