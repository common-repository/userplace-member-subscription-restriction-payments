<?php

/**
 * Admin Column Builder
 */

namespace Userplace;

class Admin_Column_Builder
{
    public function __construct()
    {
        add_filter('manage_userplace_form_builder_posts_columns', array($this, 'columns_head'), 10, 1);
        add_action('manage_userplace_form_builder_posts_custom_column', array($this, 'columns_content'), 10, 2);
    }

    // CREATE TWO FUNCTIONS TO HANDLE THE COLUMN
    function columns_head($defaults)
    {
        unset($defaults['date']);
        $defaults['shortcode'] = esc_html__('Shortcode', 'userplace');
        $defaults['date'] = esc_html__('Date', 'userplace');

        return $defaults;
    }
    function columns_content($column_name, $post_ID)
    {
        if ($column_name == 'shortcode') { ?>
            <pre class="scwp-snippet"><div class="scwp-clippy-icon" data-clipboard-snippet=""><img class="clippy" width="13" src="<?php print USERPLACE_IMG ?>clippy.svg" alt="<?php esc_html_e('Copy to clipboard', 'userplace') ?>"></div><code class="js hljs javascript">[USERPLACE_form key="<?php echo esc_attr($post_ID) ?>"]</code></pre>
<?php }
    }
}
