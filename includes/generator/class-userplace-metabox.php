<?php
/**
 * Generate MetaBox
 */

namespace Userplace;

class Generate_MetaBox {

  public function __construct( $args ) {
    $this->generate_metabox( $args );
  }

	public function generate_metabox( $args ) {

		if( is_array($args) && !empty( $args ) ){
			foreach ( $args as $key => $arg ) {

				if( isset( $arg['meta_preview'] ) ) {
					add_meta_box( $arg['id'], esc_html($arg['name']),
	      		array( $this , 'render_dynamic_meta_box') ,
	      		$arg['post_type'], $arg['position'], $arg['priority'], array( 'path' => $arg['template_path'], 'meta_preview' => $arg['meta_preview'] ) );
				} else {
					add_meta_box( $arg['id'],  esc_html($arg['name']),
      		array( $this , 'render_meta_box') ,
      		$arg['post_type'], $arg['position'], $arg['priority'], array( 'path' => $arg['template_path'] ) );
				}
			}
		}
	}

	public function render_meta_box( $post, $template ) {
		include_once( $this->template_dir($post->post_type).$template['args']['path'] );
	}

	public function render_dynamic_meta_box( $post, $template ) {
		require( $this->template_dir().$template['args']['path'] );
	}

	public function template_dir($post_type)
	{
		$template_dir = USERPLACE_DIR. '/admin-templates/';
		return apply_filters( 'userplace_template_dir', $template_dir, $post_type );
	}
}
