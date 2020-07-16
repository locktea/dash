<?php
// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
define('EDD_LDD_STORE_URL', 'https://wbcomdesigns.com/'); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of your product. This should match the download name in EDD exactly
// you should use your own CONSTANT name, and be sure to replace it throughout this file
define('EDD_LDD_ITEM_NAME', 'Learndash Dashboard'); // you should use your own CONSTANT name, and be sure to replace it throughout this file

// the name of the settings page for the license input to be displayed
define('EDD_LDD_PLUGIN_LICENSE_PAGE', 'wbcom-license-page');

if (! class_exists('EDD_LDD_Plugin_Updater')) {
    // load our custom updater.
    include dirname(__FILE__) . '/EDD_LDD_Plugin_Updater.php';
}

function EDD_LDD_Plugin_Updater()
{

    // retrieve our license key from the DB.
    $license_key = trim(get_option('edd_wbcom_ldd_license_key'));

    // setup the updater
    $edd_updater = new EDD_LDD_Plugin_Updater(EDD_LDD_STORE_URL,LD_DASHBOARD_PLUGIN_FILE,
        array(
            'version'   => LD_DASHBOARD_VERSION,             // current version number.
            'license'   => $license_key,        // license key (used get_option above to retrieve from DB).
            'item_name' => EDD_LDD_ITEM_NAME,  // name of this plugin.
            'author'    => 'wbcomdesigns',  // author of this plugin.
            'url'		=> home_url(),
        )
    );
}
add_action('admin_init', 'EDD_LDD_Plugin_Updater', 0);


/************************************
 * the code below is just a standard
 * options page. Substitute with
 * your own.
 *************************************/

function edd_wbcom_ldd_license_menu()
{
    add_submenu_page('ld-dashboard', esc_html__('License', 'ld-dashboard'), esc_html__('License', 'ld-dashboard'), 'manage_options', 'edd_ldd_license_page', 'edd_wbcom_ldd_license_page');
}
add_action('admin_menu', 'edd_wbcom_ldd_license_menu', 50);

function edd_wbcom_ldd_license_page()
{
    $license = get_option('edd_wbcom_ldd_license_key', true);
    $status  = get_option('edd_wbcom_ldd_license_status');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Plugin License Options', 'ld-dashboard'); ?></h1>
        <form method="post" action="options.php">

            <?php settings_fields('edd_wbcom_ldd_license'); ?>

            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" valign="top">
                            <?php esc_html_e('License Key', 'ld-dashboard'); ?>
                        </th>
                        <td>
                            <input id="edd_wbcom_ldd_license_key" name="edd_wbcom_ldd_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
                            <label class="description" for="edd_wbcom_ldd_license_key"><?php esc_html_e('Enter your license key', 'ld-dashboard'); ?></label>
                        </td>
                    </tr>
                    <?php  if (false !== $license) { ?>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <?php esc_html_e('License Status', 'ld-dashboard'); ?>
                            </th>
                            <td>
                                <?php if ($status !== false && $status == 'valid') { ?>
                                    <span style="color:green;"><?php esc_html_e('active', 'ld-dashboard'); ?></span>
                                    <?php wp_nonce_field('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce'); ?>
                                <?php
} else {
    wp_nonce_field('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce'); ?>
                                <span style="color:red;"><?php esc_html_e('Inactive', 'ld-dashboard'); ?></span>
                                <?php  } ?>
                            </td>
                        </tr>
                        <?php if ($status !== false && $status == 'valid') { ?>
                        <tr valign="top">
                            <th scope="row" valign="top">
                                <?php esc_html_e('Deactivate License', 'ld-dashboard'); ?>
                            </th>
                            <td>
                                <input type="submit" class="button-secondary" name="edd_ldd_license_deactivate" value="<?php esc_html_e('Deactivate License', 'ld-dashboard'); ?>"/>
                                <p class="description"><?php esc_html_e('Click for deactivate license.', 'ld-dashboard'); ?></p>
                            </td>
                        </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
                <?php
                submit_button( esc_html__('Save Settings', 'ld-dashboard'), 'primary', 'edd_ldd_license_activate', true); ?>

        </form>
    <?php
}

function edd_wbcom_ldd_register_option()
{
    // creates our settings in the options table
    register_setting('edd_wbcom_ldd_license', 'edd_wbcom_ldd_license_key', 'edd_ldd_sanitize_license');
}
add_action('admin_init', 'edd_wbcom_ldd_register_option');

function edd_ldd_sanitize_license($new)
{
    $old = get_option('edd_wbcom_ldd_license_key');
    if ($old && $old != $new) {
        delete_option('edd_wbcom_ldd_license_status'); // new license has been entered, so must reactivate
    }
    return $new;
}



/************************************
 * this illustrates how to activate
 * a license key
 *************************************/

function edd_wbcom_ldd_activate_license()
{    
    // listen for our activate button to be clicked
    if (isset($_POST['edd_ldd_license_activate'])) {
        // run a quick security check
        if (! check_admin_referer('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce')) {
            return; // get out if we didn't click the Activate button
        }

        // retrieve the license from the database
        $license =  $_POST['edd_wbcom_ldd_license_key'];
        // data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_name'  => urlencode(EDD_LDD_ITEM_NAME), // the name of our product in EDD
            'url'        => home_url(),
        );

        // Call the custom API.
        $response = wp_remote_post(
            EDD_LDD_STORE_URL,
            array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params,
            )
        );

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $mlddage = $response->get_error_mlddage();
            } else {
                $mlddage = esc_html__('An error occurred, please try again.', 'ld-dashboard');
            }
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (false === $license_data->success) {
                switch ($license_data->error) {
                    case 'expired':
                        $mlddage = sprintf(
                            __('Your license key expired on %s.', 'ld-dashboard'),
                            date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;

                    case 'revoked':
                        $mlddage = esc_html__('Your license key has been disabled.', 'ld-dashboard');
                        break;

                    case 'missing':
                        $mlddage = esc_html__('Invalid license.', 'ld-dashboard');
                        break;

                    case 'invalid':
                    case 'site_inactive':
                        $mlddage = esc_html__('Your license is not active for this URL.', 'ld-dashboard');
                        break;

                    case 'item_name_mismatch':
                        $mlddage = sprintf(__('This appears to be an invalid license key for %s.', 'ld-dashboard'), EDD_LDD_ITEM_NAME);
                        break;

                    case 'no_activations_left':
                        $mlddage = esc_html__('Your license key has reached its activation limit.', 'ld-dashboard');
                        break;

                    default:
                        $mlddage = esc_html__('An error occurred, please try again.', 'ld-dashboard');
                        break;
                }
            }
        }

        // Check if anything passed on a mlddage constituting a failure
        if (! empty($mlddage)) {
            $base_url = admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE);
            $redirect = add_query_arg(
                array(
                    'ldd_activation' => 'false',
                    'mlddage'       => urlencode($mlddage),
                ),
                $base_url
            );
            $license = trim($license);
            update_option('edd_wbcom_ldd_license_key', $license);
            update_option('edd_wbcom_ldd_license_status', $license_data->license);
            wp_redirect($redirect);
            exit();
        }

        // $license_data->license will be either "valid" or "invalid"
        $license = trim($license);
        update_option('edd_wbcom_ldd_license_key', $license);
        update_option('edd_wbcom_ldd_license_status', $license_data->license);
        wp_redirect(admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE));
        exit();
    }
}
add_action('admin_init', 'edd_wbcom_ldd_activate_license');


/***********************************************
 * Illustrates how to deactivate a license key.
 * This will decrease the site count
 ***********************************************/

function edd_wbcom_LDD_deactivate_license()
{

    // listen for our activate button to be clicked
    if (isset($_POST['edd_ldd_license_deactivate'])) {
        // run a quick security check
        if (! check_admin_referer('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce')) {
            return; // get out if we didn't click the Activate button
        }

        // retrieve the license from the database
        $license = trim(get_option('edd_wbcom_ldd_license_key'));

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'deactivate_license',
            'license'    => $license,
            'item_name'  => urlencode(EDD_LDD_ITEM_NAME), // the name of our product in EDD
            'url'        => home_url(),
        );

        // Call the custom API.
        $response = wp_remote_post(
            EDD_LDD_STORE_URL,
            array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params,
            )
        );

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $mlddage = $response->get_error_mlddage();
            } else {
                $mlddage = esc_html__('An error occurred, please try again.', 'ld-dashboard');
            }

            $base_url = admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE);
            $redirect = add_query_arg(
                array(
                    'ldd_activation' => 'false',
                    'mlddage'       => urlencode($mlddage),
                ),
                $base_url
            );

            wp_redirect($redirect);
            exit();
        }

        // decode the license data
        $license_data = json_decode(wp_remote_retrieve_body($response));

        // $license_data->license will be either "deactivated" or "failed"
        if ($license_data->license == 'deactivated') {
            delete_option('edd_wbcom_ldd_license_status');
        }

        wp_redirect(admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE));
        exit();
    }
}
add_action('admin_init', 'edd_wbcom_LDD_deactivate_license');


/************************************
 * this illustrates how to check if
 * a license key is still valid
 * the updater does this for you,
 * so this is only needed if you
 * want to do something custom
 *************************************/

function edd_wbcom_ldd_check_license()
{

    global $wp_version;

    $license = trim(get_option('edd_wbcom_ldd_license_key'));

    $api_params = array(
        'edd_action' => 'check_license',
        'license'    => $license,
        'item_name'  => urlencode(EDD_LDD_ITEM_NAME),
        'url'        => home_url(),
    );

    // Call the custom API.
    $response = wp_remote_post(
        EDD_LDD_STORE_URL,
        array(
            'timeout'   => 15,
            'sslverify' => false,
            'body'      => $api_params,
        )
    );

    if (is_wp_error($response)) {
        return false;
    }

    $license_data = json_decode(wp_remote_retrieve_body($response));

    if ($license_data->license == 'valid') {
        echo 'valid';
        exit;
        // this license is still valid
    } else {
        echo 'invalid';
        exit;
        // this license is no longer valid
    }
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function edd_wbcom_ldd_admin_notices()
{
    if (isset($_GET['ldd_activation']) && ! empty($_GET['mlddage'])) {
        switch ($_GET['ldd_activation']) {
            case 'false':
                $mlddage = urldecode($_GET['mlddage']);
                ?>
                <div class="error">
                    <p><?php echo $mlddage; ?></p>
                </div>
                <?php
                break;

            case 'true':
            default:
                // Developers can put a custom success mlddage here for when activation is successful if they way.
                break;
        }
    }
}
add_action('admin_notices', 'edd_wbcom_ldd_admin_notices');

add_action( 'wbcom_add_plugin_license_code', 'wbcom_ldd_render_license_section' );
function wbcom_ldd_render_license_section() {

    $license = get_option( 'edd_wbcom_ldd_license_key' );
    $status  = get_option( 'edd_wbcom_ldd_license_status' );

    $plugin_data = get_plugin_data( LD_DASHBOARD_PLUGIN_DIR.'ld-dashboard.php', $markup = true, $translate = true );

    if ( $status !== false && $status == 'valid' ) {
        $status_class = 'active';
        $status_text = esc_html__( 'Active', 'ld-dashboard' );
    } else {
        $status_class = 'inactive';
        $status_text = esc_html__( 'Inactive', 'ld-dashboard' );
    }
    ?>
    <table class="form-table wb-license-form-table mobile-license-headings">
        <thead>
            <tr>
                <th class="wb-product-th"><?php esc_html_e( 'Product', 'ld-dashboard' ); ?></th>
                <th class="wb-version-th"><?php esc_html_e( 'Version', 'ld-dashboard' ); ?></th>
                <th class="wb-key-th"><?php esc_html_e( 'Key', 'ld-dashboard' ); ?></th>
                <th class="wb-status-th"><?php esc_html_e( 'Status', 'ld-dashboard' ); ?></th>
                <th class="wb-action-th"><?php esc_html_e( 'Action', 'ld-dashboard' ); ?></th>
                <th></th>
            </tr>
        </thead>
    </table>
    <form method="post" action="options.php">
        <?php settings_fields( 'edd_wbcom_ldd_license' ); ?>
        <table class="form-table wb-license-form-table">
            <tr>
                <td class="wb-plugin-name"><?php echo esc_html( $plugin_data['Name'] ); ?></td>
                <td class="wb-plugin-version"><?php echo esc_html( $plugin_data['Version'] ); ?></td>
                <td class="wb-plugin-license-key"><input id="edd_wbcom_ldd_license_key" name="edd_wbcom_ldd_license_key" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" /></td>
                <td class="wb-license-status <?php echo $status_class; ?>"><?php esc_html( $status_text ); ?></td>
                <td class="wb-license-action">
                    <?php if ($status !== false && $status == 'valid') {  
                        wp_nonce_field('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce'); ?>
                         <input type="submit" class="button-secondary" name="edd_ldd_license_deactivate" value="<?php esc_attr_e('Deactivate License', 'ld-dashboard'); ?>"/>
                        <?php
                    } else {
                        wp_nonce_field('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce'); ?>
                         <input type="submit" class="button-secondary" name="edd_ldd_license_activate" value="<?php esc_attr_e('Activate License', 'ld-dashboard'); ?>"/>
                    <?php  } ?>
                </td>
                <td><?php submit_button( __('Save Changes', 'ld-dashboard'), 'primary', 'edd_ldd_license_activate', true); ?></td>
            </tr>
        </table>
    </form>
    <?php   
}

function edd_wbcom_ldd_activate_license_button()
{

    // listen for our activate button to be clicked
    if (isset($_POST['edd_ldd_license_activate'])) {
        // run a quick security check
        if (! check_admin_referer('edd_wbcom_ldd_nonce', 'edd_wbcom_ldd_nonce')) {
            return; // get out if we didn't click the Activate button
        }

        // retrieve the license from the database
        $license =  trim(get_option('edd_wbcom_ldd_license_key'));

        // data to send in our API request
        $api_params = array(
            'edd_action' => 'activate_license',
            'license'    => $license,
            'item_name'  => urlencode(EDD_LDD_ITEM_NAME), // the name of our product in EDD
            'url'        => home_url(),
        );

        // Call the custom API.
        $response = wp_remote_post(
            EDD_LDD_STORE_URL,
            array(
                'timeout'   => 15,
                'sslverify' => false,
                'body'      => $api_params,
            )
        );

        // make sure the response came back okay
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            if (is_wp_error($response)) {
                $mlddage = $response->get_error_mlddage();
            } else {
                $mlddage = esc_html__('An error occurred, please try again.', 'ld-dashboard');
            }
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));

            if (false === $license_data->success) {
                switch ($license_data->error) {
                    case 'expired':
                        $mlddage = sprintf(
                            __('Your license key expired on %s.', 'ld-dashboard'),
                            date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
                        );
                        break;

                    case 'revoked':
                        $mlddage = esc_html__('Your license key has been disabled.', 'ld-dashboard');
                        break;

                    case 'missing':
                        $mlddage = esc_html__('Invalid license.', 'ld-dashboard');
                        break;

                    case 'invalid':
                    case 'site_inactive':
                        $mlddage = esc_html__('Your license is not active for this URL.', 'ld-dashboard');
                        break;

                    case 'item_name_mismatch':
                        $mlddage = sprintf(__('This appears to be an invalid license key for %s.', 'ld-dashboard'), EDD_LDD_ITEM_NAME);
                        break;

                    case 'no_activations_left':
                        $mlddage = esc_html__('Your license key has reached its activation limit.', 'ld-dashboard');
                        break;

                    default:
                        $mlddage = esc_html__('An error occurred, please try again.', 'ld-dashboard');
                        break;
                }
            }
        }

        // Check if anything passed on a mlddage constituting a failure
        if (! empty($mlddage)) {
            $base_url = admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE);
            $redirect = add_query_arg(
                array(
                    'ldd_activation' => 'false',
                    'mlddage'       => urlencode($mlddage),
                ),
                $base_url
            );
            $license = trim($license);
            update_option('edd_wbcom_ldd_license_key', $license);
            update_option('edd_wbcom_ldd_license_status', $license_data->license);
            wp_redirect($redirect);
            exit();
        }

        // $license_data->license will be either "valid" or "invalid"
        $license = trim($license);
        update_option('edd_wbcom_ldd_license_key', $license);
        update_option('edd_wbcom_ldd_license_status', $license_data->license);
        wp_redirect(admin_url('admin.php?page=' . EDD_LDD_PLUGIN_LICENSE_PAGE));
        exit();
    }
}
add_action('admin_init', 'edd_wbcom_ldd_activate_license_button');
