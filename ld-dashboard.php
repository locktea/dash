<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wbcomdesigns.com/plugins
 * @since             1.0.0
 * @package           Ld_Dashboard
 *
 * @wordpress-plugin
 * Plugin Name:       Learndash Dashboard
 * Plugin URI:        https://github.com/vapvarun/ld-dashboard
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           3.2.0
 * Author:            Wbcom Designs
 * Author URI:        https://wbcomdesigns.com/plugins
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ld-dashboard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LD_DASHBOARD_VERSION', '3.2.0' );

define( 'LD_DASHBOARD_PLUGIN_DIR', plugin_dir_path(__FILE__) );
define( 'LD_DASHBOARD_PLUGIN_URL', plugins_url('/', __FILE__) );
if ( ! defined( 'LD_DASHBOARD_PLUGIN_FILE' ) ) {
	define( 'LD_DASHBOARD_PLUGIN_FILE', __FILE__ );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ld-dashboard-activator.php
 */
function activate_ld_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ld-dashboard-activator.php';
	Ld_Dashboard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ld-dashboard-deactivator.php
 */
function deactivate_ld_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ld-dashboard-deactivator.php';
	Ld_Dashboard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ld_dashboard' );
register_deactivation_hook( __FILE__, 'deactivate_ld_dashboard' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ld-dashboard.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ld_dashboard() {

require plugin_dir_path(__FILE__) . 'edd-license/edd-plugin-license.php';
	$plugin = new Ld_Dashboard();
	$plugin->run();

}
//run_ld_dashboard();

/**
 * Include needed files if required plugin is active
 *  @since   1.0.0
 *  @author  Wbcom Designs
 */
add_action( 'plugins_loaded', 'ld_dashboard_plugin_init' );
function ld_dashboard_plugin_init() {
	if ( !in_array( 'sfwd-lms/sfwd_lms.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'admin_notices', 'ld_dashboard_admin_notice' );
	} else {
		run_ld_dashboard();
	}
}

/**
 * Show admin notice when Learndash not active or install.
 *  @since   1.0.0
 *  @author  Wbcom Designs
 */
function ld_dashboard_admin_notice() {
	?>
	<div class="error notice is-dismissible">
		<p><?php echo sprintf( __( 'The %s plugin requires %s plugin to be installed and active.', 'ld-dashboard' ), '<b>LearnDash Dashboard</b>', '<b>LearnDash</b>' ); ?></p>
	</div>
	<?php
}
