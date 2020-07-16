<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Ld_Dashboard {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ld_Dashboard_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LD_DASHBOARD_VERSION' ) ) {
			$this->version = LD_DASHBOARD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ld-dashboard';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ld_Dashboard_Loader. Orchestrates the hooks of the plugin.
	 * - Ld_Dashboard_i18n. Defines internationalization functionality.
	 * - Ld_Dashboard_Admin. Defines all hooks for the admin area.
	 * - Ld_Dashboard_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-dashboard-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-dashboard-i18n.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-admin-settings.php';

		/* Enqueue plugins essential functions file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/ld-dashboard-functions.php';

		/* Enqueue wbcom plugin folder file. */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/wbcom/wbcom-paid-plugin-settings.php';

		/**
		 * The class responsible for defining functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-dashboard-functions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ld-dashboard-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ld-dashboard-public.php';
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ld-dashboard-export.php';
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ld-dashboard-export-admin.php';

		/**
		 * The class responsible for defining all actions for learndash-buddypress
		 * group integration.
		 */
		if( class_exists('BuddyPress') ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'buddypress/class-reign-learndash-buddypress-addon.php';
		}
		
		$this->loader = new Ld_Dashboard_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ld_Dashboard_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ld_Dashboard_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ld_Dashboard_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ld_dashboard_menu_page', 20  );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ld_dashboard_add_instructor_role' );
		$this->loader->add_action( 'init', $plugin_admin, 'ld_dashboard_nav_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'ld_dashboard_register_admin_setting' );

		//Add commission meta box to course.
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'ld_dashboard_add_post_commission_meta_box' );

		//Save commission meta box of course.
		$this->loader->add_action( 'save_post', $plugin_admin, 'ld_dashboard_save_post_commission_meta_box' );

		//Ajax request to update individual instrcutor commission
		$this->loader->add_action( 'wp_ajax_ld_ajax_update_instructor_commission', $plugin_admin, 'ld_ajax_update_instructor_commission' );

		//Ajax request to generate commission data.
		$this->loader->add_action( 'wp_ajax_ld_ajax_generate_instructor_data', $plugin_admin, 'ld_ajax_generate_instructor_data' );

		//Ajax request to pay unpaid amout.
		$this->loader->add_action( 'wp_ajax_ld_ajax_pay_instructor_amount', $plugin_admin, 'ld_ajax_pay_instructor_amount' );
		
		
		$this->loader->add_filter( 'wp_dropdown_users_args', $plugin_admin, 'ld_dashboard_dropdown_users_args' );
		
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'ld_dahsboard_admin_pre_get_posts' );
		
		$this->loader->add_action( 'wp_ajax_ld_dashboard_load_instructors_modal', $plugin_admin, 'ld_dashboard_load_instructors_modal' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_add_instructors_to_course', $plugin_admin, 'ld_dashboard_add_instructors_to_course' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_detach_instructor', $plugin_admin, 'ld_dashboard_detach_instructor' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		global $ld_plugin_public;
		$ld_plugin_public = $plugin_public = new Ld_Dashboard_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_public, 'ld_dashboard_register_shortcodes' );
		
		$this->loader->add_action( 'wp_ajax_ld_dashboard_course_details', $plugin_public, 'ld_dashboard_course_details' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_student_details', $plugin_public, 'ld_dashboard_student_details' );
		
		$this->loader->add_action( 'wp_ajax_ld_dashboard_save_tasks', $plugin_public, 'ld_dashboard_save_tasks' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_activity_rows_ajax', $plugin_public, 'ld_dashboard_activity_rows_ajax' );

		//update on course access
		$this->loader->add_action( 'learndash_update_course_access', $plugin_public, 'ld_dashboard_update_author_earning', 10, 4 );

		//$this->loader->add_action( 'init', $plugin_public, 'ld_get_course_data' );

		//$post_ID, $post, $update
		//$this->loader->add_action( 'wp_insert_post', $plugin_public, 'ld_dashboard_update_instructor_course_purchase', 10, 3 );

		$this->loader->add_action( 'added_post_meta', $plugin_public, 'ld_dashboard_updated_sfwd_transaction_meta', 99, 4 );

		$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_public, 'ld_update_instructor_meta_wc_course_order', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'ld_update_instructor_meta_wc_course_order', 10, 1 );
		$this->loader->add_action( 'woocommerce_payment_complete', $plugin_public, 'ld_update_instructor_meta_wc_course_order', 10, 1 );
		//$this->loader->add_action( 'woocommerce_order_status_refunded', $plugin_public, 'ld_remove_course_order_from_instructor_meta', 10, 1 );
		
		$this->loader->add_filter( 'learndash_get_activity_query_args', $plugin_public, 'ld_dashboard_get_activity_query_args', 10, 1 );
		
		if ( class_exists('LearnMate_LearnDash_Addon') && class_exists('BuddyPress') && bp_is_active( 'groups' ) && class_exists('Bptodo_Profile_Menu') && ld_if_display_to_do_enabled() ) {
			$this->loader->add_action( 'ld_dashboard_show_course_to_do_list', $plugin_public, 'reign_ld_show_course_to_do_list');
			$this->loader->add_action( 'wp_ajax_ld_generate_group_course_todo_list', $plugin_public, 'ld_generate_group_course_todo_list' );
		}
		
		$this->loader->add_action( 'wp_ajax_ld_dashboard_couse_students', $plugin_public, 'ld_dashboard_couse_students' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_email_send', $plugin_public, 'ld_dashboard_email_send' );
		$this->loader->add_action( 'wp_ajax_ld_dashboard_buddypress_message_send', $plugin_public, 'ld_dashboard_buddypress_message_send' );
		$this->loader->add_action( 'ld_dashboard_send_email', $plugin_public, 'ld_dashboard_send_single_email', 10, 4 );
		
		$this->loader->add_action( 'wp_ajax_ld_dashboard_student_course_progress', $plugin_public, 'ld_dashboard_student_course_progress' );
		
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'ld_dashboard_register_instructor' );
		$this->loader->add_action( 'wp_loaded', $plugin_public, 'ld_dashboard_apply_instructor' );
		$this->loader->add_filter( 'sfwd_lms_has_access', $plugin_public, 'ld_dashboard_auto_enroll_instructor_courses', 10, 3 );
		
		$this->loader->add_action( 'init', $plugin_public, 'add_endpoint' );
		$this->loader->add_action( 'query_vars', $plugin_public, 'query_vars' );
		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ld_Dashboard_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
