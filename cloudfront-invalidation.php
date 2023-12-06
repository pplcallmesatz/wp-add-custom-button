<?php
/**
* Plugin Name: Cloudfront Invalidation 
* Plugin URI: https://www.mallow-tech.com/
* Description: To add the cloudfront invalidation button on the top bar of the wordpress.
* Version: 0.1
* Author: Satheesh Kumar S
* Author URI: https://github.com/pplcallmesatz
**/


// Register Settings
function custom_navbar_button_settings_init() {
    register_setting(
        'custom-navbar-button-settings',
        'navbar_button_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
        )
    );

    register_setting(
        'custom-navbar-button-settings',
        'navbar_button_name',
        array(
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
}

add_action('admin_init', 'custom_navbar_button_settings_init');

// Add the admin page
function custom_navbar_button_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom-navbar-button-settings'); ?>
            <?php do_settings_sections('custom-navbar-button-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Button URL</th>
                    <td><input type="text" name="navbar_button_url" value="<?php echo esc_attr(get_option('navbar_button_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Button Name</th>
                    <td><input type="text" name="navbar_button_name" value="<?php echo esc_attr(get_option('navbar_button_name')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add the settings page to the admin menu
function custom_navbar_button_menu() {
    add_options_page(
        'Custom Navbar Button Settings',
        'Cloudfront Invalidate URL Settings',
        'manage_options',
        'custom-navbar-button',
        'custom_navbar_button_page'
    );
}

add_action('admin_menu', 'custom_navbar_button_menu');

// Handle form submission
function save_custom_navbar_button() {
    if (isset($_POST['submit_button'])) {
        // Save the form data
        update_option('navbar_button_url', esc_url_raw($_POST['navbar_button_url']));
        update_option('navbar_button_name', sanitize_text_field($_POST['navbar_button_name']));
    }
}

add_action('admin_post_save_custom_navbar_button', 'save_custom_navbar_button');

// Display the button in the navbar
function add_custom_navbar_button() {
    $button_url = get_option('navbar_button_url');
    $button_name = get_option('navbar_button_name');

    if ($button_url && $button_name) {
        global $wp_admin_bar;
        $wp_admin_bar->add_menu(
            array(
                'id'    => 'custom_navbar_button',
                'title' => $button_name,
		'href'  => 'javascript:void(0)',
//		 'meta'   => array('target' => '_blank'), // Open in a new tab or window
            )
	);
	 // Add jQuery to the footer of the admin page
        add_action('admin_footer', function () use ($button_url) {
            ?>
              <script type="text/javascript">
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('wp-admin-bar-custom_navbar_button').addEventListener('click', function() {
                        // Trigger a GET request
                        fetch('<?php echo esc_url($button_url); ?>', { method: 'GET' })
                            .then(response => {
                                // Handle the response if needed
                                console.log(response);
                            })
                            .catch(error => {
                                // Handle errors if needed
                                console.error(error);
                            });
                    });
                });
            </script>
            <?php
        });
    }
}

add_action('wp_before_admin_bar_render', 'add_custom_navbar_button');

