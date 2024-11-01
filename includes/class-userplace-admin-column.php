<?php

/**
 * Control Admin post list custom column
 */
namespace Userplace;


class AdminColumn
{

	public function __construct()
	{
		add_filter( 'manage_userplace_plan_posts_columns', array( $this, 'columns_head' ), 10, 1 );
    	add_action( 'manage_userplace_plan_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
	}

	function columns_head( $defaults ) {
		unset($defaults['date']);
    $defaults['plan_page_url'] = esc_html__( 'Plan Url', 'userplace' );
		$defaults['date'] = esc_html__( 'Date', 'userplace' );
    return $defaults;
  }

	function columns_content( $column_name, $post_ID ) {
    if ($column_name == 'plan_page_url') {
			$plan = get_post_meta( $post_ID, 'plan_id', true );
			$url = home_url().'/subscription/pay?plan=' .$plan;
    ?>
      <pre class="scwp-snippet wpt-scwp-snippet"><div class="scwp-clippy-icon" data-clipboard-snippet=""><img class="clippy" width="13" src="<?php print USERPLACE_IMG ?>clippy.svg" alt="Copy to clipboard"></div><code class="js hljs javascript"><?php echo esc_url( $url) ?></code></pre>
    <?php }
  }
}
