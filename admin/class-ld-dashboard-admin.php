<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/admin
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Ld_Dashboard_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * All tabs of settings page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $plugin_settings_tabs  The tabs of plugin's admin settings.
	 */
	private $plugin_settings_tabs;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'wp-color-picker' );
		if ( !wp_style_is( 'font-awesome', 'enqueued' ) ) {
			wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
		}
		wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/ld-dashboard-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'wp-color-picker');

		wp_enqueue_script( $this->plugin_name.'-1a', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ) );
        wp_enqueue_style( $this->plugin_name.'-2a', plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css' );

        // wp_enqueue_script( 'plugin-jquery-ui-js', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.js' );
        // wp_enqueue_style( 'plugin-jquery-ui-css', plugin_dir_url( __FILE__ ) . '/css/jquery-ui.css', array(), $this->version, 'all' );

		wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/ld-dashboard-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script($this->plugin_name . '-admin', 'ld_dashboard_obj', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'ajax_nonce' => wp_create_nonce('ld_dashboard_ajax_security')
			)
		);

	}

	/*
	 * Add Instructor user Role.
	 */
	public function ld_dashboard_add_instructor_role() {
		global $wp_roles;
				
		if ( $GLOBALS['wp_roles']->is_role( 'ld_instructor' ) === false ) {
			$instructor_caps = array(
										'wpProQuiz_show'               => true,
										'wpProQuiz_add_quiz'           => true,
										'wpProQuiz_edit_quiz'          => true,
										'wpProQuiz_delete_quiz'        => true,
										'wpProQuiz_show_statistics'    => true,
										'wpProQuiz_change_settings'    => true,
										'read_course'                  => true,
										'publish_courses'              => true,
										'edit_courses'                 => true,
										'edit_others_courses'          => true,
										'edit_private_courses'         => true,										
										'delete_courses'               => true,
										'edit_course'                  => true,										
										'delete_course'                => true,
										'edit_published_courses'       => true,
										'delete_published_courses'     => true,
										'edit_assignment'              => true,
										'edit_assignments'             => true,
										'publish_assignments'          => true,
										'read_assignment'              => true,
										'delete_assignment'            => true,
										'edit_published_assignments'   => true,
										'delete_published_assignments' => true,
										'read_group'                   => true,
										'edit_groups'                  => true,
										'propanel_widgets'             => true,
										'read'                         => true,
										'edit_others_assignments'      => true,
										'instructor_reports'           => true,
										'instructor_page'              => true,
										'manage_categories'            => true,
										'wpProQuiz_toplist_edit'       => true,
										'upload_files'                 => true,
										'delete_essays'                => true,
										'delete_others_essays'         => true,
										'delete_private_essays'        => true,
										'delete_published_essays'      => true,
										'edit_essays'                  => true,
										'edit_others_essays'           => true,
										'edit_private_essays'          => true,
										'edit_published_essays'        => true,
										'publish_essays'               => true,
										'read_essays'                  => true,
										'read_private_essays'          => true,
										'edit_posts'                   => true,
										'edit_post'                   => true,
										'publish_posts'                => true,
										'edit_published_posts'         => true,
										'delete_posts'                 => true,
										'delete_published_posts'       => true,
										'delete_product'               => true,
										'delete_products'              => true,
										'delete_published_products'    => true,
										'edit_product'                 => true,
										'edit_products'                => true,
										'edit_published_products'      => true,
										'publish_products'             => true,
										'read_product'                 => true,
										'assign_product_terms'         => true,
									);

			$instructor_caps_woo = array(
										'delete_product'            => true,
										'delete_products'           => true,
										'delete_published_products' => true,
										'edit_product'              => true,
										'edit_products'             => true,
										'edit_published_products'   => true,
										'publish_products'          => true,
										'read_product'              => true,
										'assign_product_terms'      => true,
									);

			$ld_instructor_caps = array_merge( $instructor_caps_woo, $instructor_caps );
			$wp_roles->add_role( 'ld_instructor', 'Instructor', $ld_instructor_caps );
		}
		
		$role = get_role( 'ld_instructor' ); 
		// Add a new capability.
		$role->add_cap( 'edit_others_courses', true );
		$role->add_cap( 'edit_private_courses', true );
		
		if ( $GLOBALS['wp_roles']->is_role( 'ld_instructor_pending' ) === false ) {
			$wp_roles->add_role( 'ld_instructor_pending', 'Instructor Pending', array() );
		}
		
	}

	/**
	 *
	 */
	public function ld_dashboard_nav_menus() {
		register_nav_menu( 'ld-dashboard-profile-menu', esc_html__( 'LD Dashboard Profile Menu', 'ld-dashboard' ) );
	}

	/**
	 * Add submenu for ld dashboard setting.
	 *
	 */
	public function ld_dashboard_menu_page() {

		if ( empty ( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
			add_menu_page( esc_html__( 'WB Plugins', 'ld-dashboard' ), esc_html__( 'WB Plugins', 'ld-dashboard' ), 'manage_options', 'wbcomplugins', array( $this, 'ld_dashboard_settings_page' ), 'dashicons-lightbulb', 59 );
		 	add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'ld-dashboard' ), esc_html__( 'General', 'ld-dashboard' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'LD Dashboard', 'ld-dashboard' ), esc_html__( 'LD Dashboard', 'ld-dashboard' ), 'manage_options', 'ld-dashboard-settings', array( $this, 'ld_dashboard_settings_page' ) );
	}

	/**
	 * Actions performed to create tabs on the sub menu page.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_plugin_settings_tabs() {
		$current = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'ld-dashboard-general';

		$tab_html = '<div class="wbcom-tabs-section"><h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $ldd_tab => $tab_name ) {
			$class     = ( $ldd_tab === $current ) ? 'nav-tab-active' : '';
			$page      = 'ld-dashboard-settings';
			$tab_html .= '<a id="' . $ldd_tab . '" class="nav-tab ' . $class . '" href="admin.php?page=' . $page . '&tab=' . $ldd_tab . '">' . $tab_name . '</a>';
		}
		$tab_html .= '</h2></div>';
		echo $tab_html;
	}

	/**
	 * Get general settings html.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_general_settings() {
		include 'partials/ld-dashboard-general-settings.php';
	}

	public function ld_dashboard_integration() {
		include 'partials/ld-dashboard-integration.php';
	}
	public function ld_dashboard_page_mapping() {
		include 'partials/ld-dashboard-page-mapping.php';
	}

	/**
	 * Get instructor settings html.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_instructor_settings() {
		include 'partials/ld-dashboard-instructor-settings.php';
	}

	public function ld_dashboard_commission_report_settings() {
		include 'partials/ld-dashboard-commission-report.php';
	}

	public function ld_dashboard_bp_groups_settings() {
		include 'partials/ld-dashboard-bp-groups-sync.php';
	}

	/**
	 * Register all settings.
	 *
	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_register_admin_setting() {
		$this->plugin_settings_tabs['ld-dashboard-general'] = esc_html__( 'General', 'ld-dashboard' );
		$this->plugin_settings_tabs['ld-dashboard-integration'] = esc_html__( 'Integration', 'ld-dashboard' );
		$this->plugin_settings_tabs['ld-dashboard-page-mapping'] = esc_html__( 'Page Mapping', 'ld-dashboard' );
		$this->plugin_settings_tabs['ld-dashboard-instructors'] = esc_html__( 'Instructors', 'ld-dashboard' );
		$this->plugin_settings_tabs['ld-dashboard-commission-report'] = esc_html__( 'Commission Report', 'ld-dashboard' );
		register_setting( 'ld_dashboard_general_settings', 'ld_dashboard_general_settings' );
		register_setting( 'ld_dashboard_integration', 'ld_dashboard_integration' );
		register_setting( 'ld_dashboard_page_mapping', 'ld_dashboard_page_mapping' );
		register_setting( 'ld_dashboard_instructor_settings', 'ld_dashboard_instructor_settings' );
		register_setting( 'ld_dashboard_comm_report_settings', 'ld_dashboard_comm_report_settings' );
		
		add_settings_section( 'ld-dashboard-general', ' ', array( $this, 'ld_dashboard_general_settings' ), 'ld-dashboard-general' );
		add_settings_section( 'ld-dashboard-integration', ' ', array( $this, 'ld_dashboard_integration' ), 'ld-dashboard-integration' );
		add_settings_section( 'ld-dashboard-page-mapping', ' ', array( $this, 'ld_dashboard_page_mapping' ), 'ld-dashboard-page-mapping' );
		add_settings_section( 'ld-dashboard-instructors', ' ', array( $this, 'ld_dashboard_instructor_settings' ), 'ld-dashboard-instructors' );
		add_settings_section( 'ld-dashboard-commission-report', ' ', array( $this, 'ld_dashboard_commission_report_settings' ), 'ld-dashboard-commission-report' );

		if( ld_if_ldbp_group_intgrtn_enabled() ) {
			// learndash buddypress groups integration tab
			$this->plugin_settings_tabs['ld-dashboard-bp-groups-sync'] = esc_html__( 'Sync Course-Group', 'ld-dashboard' );
			// learndash buddypress groups integration tab
			register_setting( 'ld_dashboard_bp_groups_settings', 'ld_dashboard_bp_groups_settings' );
			// learndash buddypress groups integration tab
			add_settings_section( 'ld-dashboard-bp-groups-sync', ' ', array( $this, 'ld_dashboard_bp_groups_settings' ), 'ld-dashboard-bp-groups-sync' );
		}
	}

	/**
	 * Display Learndash Instructor Dashboard settings.
	 *
 	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_settings_page() {
		$current = ( filter_input( INPUT_GET, 'tab' ) !== null ) ? filter_input( INPUT_GET, 'tab' ) : 'ld-dashboard-general';
		?>
		<div class="wrap">
			<div class="ld-dashboard-admin-header">
				<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
				<h1 class="wbcom-plugin-heading">
					<?php esc_html_e( 'LD Dashboard Settings', 'ld-dashboard' ); ?>
				</h1>
			</div>
			<div class="wbcom-admin-settings-page">
				<?php
				$this->ld_dashboard_plugin_settings_tabs();
				settings_fields( $current );
				do_settings_sections( $current );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Add admin commission meta box for course post type.
	 *
 	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_add_post_commission_meta_box() {
		if( current_user_can('administrator') ){
			add_meta_box(
				'admin-course-commission',
				__( 'Admin Commission', 'ld-dashboard' ),
				array( $this, 'ld_dashboard_admin_commission_meta_box_callback' ),
				'sfwd-courses',
				'side',
				'high'
			);
		}
		
		add_meta_box(
				'admin-course-commission',
				__( 'Admin Commission', 'ld-dashboard' ),
				array( $this, 'ld_dashboard_admin_commission_meta_box_callback' ),
				'sfwd-courses',
				'side',
				'high'
			);
			
		add_meta_box( 'ld-dashboard-instructors', __( 'LD Instructors', 'ld-dashboard' ), array( $this, 'ld_dashboard_admin_instructors_metabox' ), 'sfwd-courses' );
		
	}

	/**
	 * Callback for admin commission meta box for course post type.
	 *
 	 * @since  1.0.0
	 * @author Wbcom Designs
	 * @access public
	 */
	public function ld_dashboard_admin_commission_meta_box_callback() {

		global $post;
		// Add a nonce field so we can check for it later.
	    wp_nonce_field( 'admin-course-commission-nonce', 'admin-course-commission-nonce' );

	    $value = get_post_meta( $post->ID, 'admin-course-commission', true );

	    echo '<input name="admin-course-commission" type="number" min="0" max="100" value="'.esc_attr( $value ).'" placeholder="0">';
	    echo '<p class="howto">'.esc_html('Enter commission in percentage.', 'ld-dashboard').'</p>';
	}

	/**
	 * When the course is saved, saves admin course commission.
	 *
	 * @param int $post_id
	 */
	function ld_dashboard_save_post_commission_meta_box( $post_id ) {

        // Check if our nonce is set.
		if ( ! isset( $_POST['admin-course-commission-nonce'] ) ) {
			return;
		}

        // Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['admin-course-commission-nonce'], 'admin-course-commission-nonce' ) ) {
			return;
		}

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

        // Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'sfwd-courses' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		}
		else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
		if ( ! isset( $_POST['admin-course-commission'] ) ) {
			return;
		}

        // Sanitize user input.
		$my_data = sanitize_text_field( $_POST['admin-course-commission'] );

        // Update the meta field in the database.
		update_post_meta( $post_id, 'admin-course-commission', $my_data );
	}


	public function ld_ajax_update_instructor_commission() {
		if (isset($_POST[ 'action' ]) && $_POST[ 'action' ] == 'ld_ajax_update_instructor_commission') {
			check_ajax_referer( 'ld_dashboard_ajax_security', 'ajax_nonce' );

			$instructor_id = $_POST['instructor_id'];
			$instructor_commission = (int)$_POST['instructor_commission'];

			update_user_meta( $instructor_id, 'instructor-course-commission', $instructor_commission );
		}
	}

	public function ld_ajax_generate_instructor_data() {
		if (isset($_POST[ 'action' ]) && $_POST[ 'action' ] == 'ld_ajax_generate_instructor_data') {
			check_ajax_referer( 'ld_dashboard_ajax_security', 'ajax_nonce' );

			$instructor_id = $_POST['instructor_id'];
			$course_purchase_data = get_user_meta( $instructor_id, 'course_purchase_data', true );
			$tr_html = '';
			$tfoot_html = '';
			if( is_array( $course_purchase_data ) ) {
				$count = 0;
				foreach ($course_purchase_data as $key => $value) {
					if( $count%2 == 0 ){
						$class = 'even';
					}else{
						$class = 'odd';
					}
					$course_pricing = learndash_get_course_price( $value['course']);
					$tr_html .= '<tr class="'.$class.'" role="row">';
					$tr_html .= '<td>#'.$value['order_id'].'</td>';
					$tr_html .= '<td><a href="'.get_the_permalink($value['course']).'">'.get_the_title($value['course']).'</a></td>';
					$tr_html .= '<td>'.$course_pricing['price'].'</td>';
					$tr_html .= '<td>'.$value['commission'].'</td>';
					$tr_html .= '<td>'.$value['payment_type'].'</td>';
					$tr_html .= '</tr>';
					$count++;
				}

				$instructor_total_earning = (int)get_user_meta( $instructor_id, 'instructor_total_earning', true );
				$instructor_paid_earning = (int)get_user_meta( $instructor_id, 'instructor_paid_earning', true );
				$instructor_unpaid_earning = $instructor_total_earning - $instructor_paid_earning;

				$tfoot_html .= '<tr>';
				$tfoot_html .= '<td></td>';
				$tfoot_html .= '<td>'.esc_html('Paid Earning','ld-dashboard').'</td>';
				$tfoot_html .= '<td>'.$instructor_paid_earning.'</td>';
				$tfoot_html .= '<td></td>';
				$tfoot_html .= '<td></td>';
				$tfoot_html .= '</tr>';
				$tfoot_html .= '<tr>';
				$tfoot_html .= '<td></td>';
				$tfoot_html .= '<td>'.esc_html('Unpaid Earning','ld-dashboard').'</td>';
				$tfoot_html .= '<td>'.$instructor_unpaid_earning.'</td>';
				$tfoot_html .= '<td>
				<a class="instructor-pay-amount" href="#" data-instructor-id="'.$instructor_id.'" data-unpaid-amt="'.$instructor_unpaid_earning.'" data-paid-amt="'.$instructor_paid_earning.'" data-total-earning="'.$instructor_total_earning.'">'.esc_html('Pay','ld-dashboard').'</a>
				
				<span class="ld-dashboard-export"><a class="ld-dashboard-export-instructor-commission ld-dashboard-btn" href="' . admin_url( 'admin.php?page=ld-dashboard-settings&tab=ld-dashboard-commission-report&ld-export=instructor-commission&instructor-id='. $instructor_id.'&export-format=csv') . '" target="Blank">' . __( 'Export CSV', 'ld-dashboard') . '</a></span>
				
				
				</td>';
				$tfoot_html .= '<td></td>';
				$tfoot_html .= '</tr>';
			}else{
				$tr_html .= '<tr>';
				$tr_html .= '<td></td>';
				$tr_html .= '<td></td>';
				$tr_html .= '<td>'.__( 'No courses has been purchased yet', 'ld-dashboard' ).'</td>';
				$tr_html .= '<td></td>';
				$tr_html .= '<td></td>';
				$tr_html .= '</tr>';
			}
			$response_array = array(
				'tr_html' => $tr_html,
				'tfoot_html' => $tfoot_html
			);
			echo json_encode( $response_array );exit();
		}
	}

	public function ld_ajax_pay_instructor_amount() {
		if (isset($_POST[ 'action' ]) && $_POST[ 'action' ] == 'ld_ajax_pay_instructor_amount') {
			check_ajax_referer( 'ld_dashboard_ajax_security', 'ajax_nonce' );

			$instructor_id = $_POST['instructor_id'];
			$paid_earning = $_POST['paid_earning'];
			$paying_amount = $_POST['paying_amount'];
			$instructor_id = $_POST['instructor_id'];

			$instructor_paid_earning = (int)$paid_earning + (int)$paying_amount;
			update_user_meta( $instructor_id, 'instructor_paid_earning', $instructor_paid_earning );
			exit();
		}
	}

	/**
	 * Display form field with list of authors and instructor.
	 *
	 * @param array $query_args
	 */
	public function ld_dashboard_dropdown_users_args( $query_args ) {
		$allowed_post_type = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic','sfwd-quiz', 'sfwd-question', 'sfwd-certificates', 'product' );
		if ( in_array( get_post_type(), $allowed_post_type ) ) {
			unset($query_args['who']);
			$query_args['role__in'] = array('Administrator', 'ld_instructor');
		}
		return $query_args;
	}
	
	/**
	 * Set posts query clauses
	 *
	 * @param array $clauses
	 */
	
	public function ld_dahsboard_admin_posts_clauses( $clauses ) {
		global $current_user, $wpdb;
		
		if ( !is_admin() ) {
			return $clauses;
		}
		
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'sfwd-courses' ) {
			$clauses['join'] .= "INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id )";
			
			if ( isset($_GET['post_status']) && $_GET['post_status'] != '' ) {
					$post_status_where = "{$wpdb->prefix}posts.post_status = '". $_GET['post_status']  ."'";
			} else {
				$post_status_where = "{$wpdb->prefix}posts.post_status = 'publish' OR {$wpdb->prefix}posts.post_status = 'graded' OR {$wpdb->prefix}posts.post_status = 'not_graded' OR {$wpdb->prefix}posts.post_status = 'future' OR {$wpdb->prefix}posts.post_status = 'draft' OR {$wpdb->prefix}posts.post_status = 'pending' OR {$wpdb->prefix}posts.post_author = {$current_user->ID} AND {$wpdb->prefix}posts.post_status = 'private'";
			}
			$clauses['where'] .= " OR ( ({$post_status_where}) AND pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$current_user->ID}\"*' )";
			
			$clauses['groupby'] .= " {$wpdb->prefix}posts.ID";		
		
		}
		
		if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array('sfwd-lessons','sfwd-quiz', 'sfwd-topic','sfwd-assignment'))) {
			
			$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$current_user->ID} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$current_user->ID}\"*' ) ) AND post_type='sfwd-courses' Group By {$wpdb->prefix}posts.ID";
			
			$cousres = $wpdb->get_results( $get_courses_sql );			
			if ( empty($cousres) ) {
				$cousres[] = (object)array('ID' => 0);				
			}
			
			if ( !empty($cousres) ) {
				$course_ids = array();
				foreach ($cousres as $course ) {					
					$course_ids[] = $course->ID;
				}				
				$course_ids = implode("','",$course_ids );
				if ( isset($_GET['post_status']) && $_GET['post_status'] != '' ) {
					$post_status_where = "{$wpdb->prefix}posts.post_status = '". $_GET['post_status']  ."'";
				} else {
					$post_status_where = "{$wpdb->prefix}posts.post_status = 'publish' OR {$wpdb->prefix}posts.post_status = 'graded' OR {$wpdb->prefix}posts.post_status = 'not_graded' OR {$wpdb->prefix}posts.post_status = 'future' OR {$wpdb->prefix}posts.post_status = 'draft' OR {$wpdb->prefix}posts.post_status = 'pending' OR {$wpdb->prefix}posts.post_author = {$current_user->ID} AND {$wpdb->prefix}posts.post_status = 'private'";
				}
				
				$clauses['join'] .= "INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id )";
			
				$clauses['where'] .= " AND {$wpdb->prefix}posts.post_author = {$current_user->ID} OR ( pm6.meta_key = 'course_id' AND pm6.meta_value IN ('{$course_ids}') AND {$wpdb->prefix}posts.post_type = '".$_GET['post_type']."' AND ({$post_status_where}) )";
				
				$clauses['groupby'] .= " {$wpdb->prefix}posts.ID";	
				
			}			
			
		}
		remove_filter( 'posts_clauses', array( $this, 'ld_dahsboard_admin_posts_clauses'), 99);		
		
		return $clauses;
	}

	/**
	 * Display Instructor role related couse, lesson, topics and etc
	 *
	 * @param array $query
	 */
	public function ld_dahsboard_admin_pre_get_posts( $query ) {
		global $current_user, $wpdb;

		if ( is_admin() && in_array( 'ld_instructor', (array) $current_user->roles ) ) {

			if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array( 'sfwd-courses', 'sfwd-question', 'sfwd-certificates', 'product' ))) {
				$query->set( 'author', $current_user->ID );
				add_filter( 'posts_clauses', array( $this, 'ld_dahsboard_admin_posts_clauses'), 99);
			} else {

				if ( isset( $_GET['post_type'] ) && in_array( $_GET['post_type'], array('sfwd-lessons','sfwd-quiz', 'sfwd-topic','sfwd-assignment'))) {
					
					add_filter( 'posts_clauses', array( $this, 'ld_dahsboard_admin_posts_clauses'), 99);
					$_REQUEST['all_posts'] = 1;
					/*
					$get_courses_sql = "select ID from {$wpdb->prefix}posts where post_author={$current_user->ID} AND post_type='sfwd-courses'";
					$cousres = $wpdb->get_results( $get_courses_sql );
					if ( !empty($cousres) ) {
						$course_ids = array();
						foreach ($cousres as $course ) {
							$course_ids[] = $course->ID;
						}

						$query->set('meta_query', array(												 
												array(
													'key'     => 'course_id',
													'value'   => $course_ids,
													'compare' => 'IN'
												)
											)
										);

					} else {
						$query->set( 'author', $current_user->ID );
					}
					*/
				}
			}


			$allowed_post_type = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic','sfwd-quiz', 'sfwd-question', 'sfwd-certificates', 'product' );

			foreach( $allowed_post_type as $post_type) {
				//add_filter('views_edit-' . $post_type, array( $this, 'ld_dashboard_fix_lms_post_type_counts' ) );
			}
		}
	}

	/*
	 * Count Custom post type for instructor user rol
	 *
	 * @param array $views
	 */
	public function ld_dashboard_fix_lms_post_type_counts( $views ) {
		global $current_user, $wp_query, $wpdb;

		$allowed_post_type = array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic','sfwd-quiz', 'sfwd-question', 'sfwd-certificates', 'product' );


		if ( isset($_REQUEST['post_type']) && $_REQUEST['post_type'] != '' ) {
			$posttype = $_REQUEST['post_type'];
		} else {
			$posttype = get_post_type();
		}


		if ( !in_array( $posttype, $allowed_post_type ) ) {

			return $views;
		}

		unset($views['mine']);

		$types = array(
			array( 'status' =>  NULL ),
			array( 'status' => 'publish' ),
			array( 'status' => 'draft' ),
			array( 'status' => 'pending' ),
			array( 'status' => 'trash' )
		);
		$meta_query = array();
		if ( in_array( $posttype, array('sfwd-lessons', 'sfwd-topic'))) {

			$get_courses_sql = "select ID from {$wpdb->prefix}posts where post_author={$current_user->ID} AND post_type='sfwd-courses'";
			$cousres = $wpdb->get_results( $get_courses_sql );
			if ( !empty($cousres) ) {
				$course_ids = array();
				foreach ($cousres as $course ) {
					$course_ids[] = $course->ID;
				}

				$meta_query['meta_query'] = array(
										'relation' => 'AND',
										array(
											'key'     => 'course_id',
											'value'   => $course_ids,
											'compare' => 'IN'
										)
									);

			}
		}

		foreach( $types as $type ) {

			$query = array(
				'author'   		=> $current_user->ID,
				'post_type'   	=> $posttype,
				'post_status' 	=> $type['status']

			);

			if ( in_array( $posttype, array('sfwd-lessons', 'sfwd-topic'))) {
				unset($query['author']);
			}
			$query = array_merge( $query, $meta_query);
			$result = new WP_Query($query);

			if( $type['status'] == NULL ):

				$class = ($wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';

				$views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'ld-dashboard'),

					admin_url( 'edit.php?post_type=' . $posttype ),

					$result->found_posts);

			elseif( $type['status'] == 'publish' ):

				$class = ($wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';

				$views['publish'] = sprintf(__('<a href="%s"'. $class .'>Published <span class="count">(%d)</span></a>', 'ld-dashboard'),

					   admin_url('edit.php?post_status=publish&post_type=' . $posttype ),

					$result->found_posts);

			elseif( $type['status'] == 'draft' ):

				$class = ($wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';

				$views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'ld-dashboard'),

					admin_url('edit.php?post_status=draft&post_type=' . $posttype ),

					$result->found_posts);

			elseif( $type['status'] == 'pending' ):

				$class = ($wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';

				$views['pending'] = sprintf(__('<a href="%s"'. $class .'>Pending <span class="count">(%d)</span></a>', 'ld-dashboard'),

					   admin_url('edit.php?post_status=pending&post_type=' . $posttype ),

					$result->found_posts);

			elseif( $type['status'] == 'trash' ):

				$class = ($wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';

				$views['trash'] = sprintf(__('<a href="%s"'. $class .'>Trash <span class="count">(%d)</span></a>', 'ld-dashboard'),

					admin_url('edit.php?post_status=trash&post_type=' . $posttype ),

					$result->found_posts);

			endif;

		}

		return $views;
	}
	
	/*
	 * Assign Multi Instructor
	 *	 
	 */
	public function ld_dashboard_admin_instructors_metabox( $post ) {
		global $post;		
		$instructors_ids = get_post_meta($post->ID, '_ld_instructor_ids', true );
		
		$args = array(				
					'orderby'   => 'user_nicename',
					'role__in'	=> 'ld_instructor',
					'order'     => 'ASC',
					'fields'    => array( 'ID', 'display_name' ),
					'include'	=> ( !empty($instructors_ids)) ? $instructors_ids : array(0),
				);
					
		$instructors     = get_users( $args );
		?>
		<div class="ld-instructors-metabox-wrap">
			<div class="ld-available-instructors">
				<?php			
				if (is_array($instructors) && count($instructors)){
					foreach ($instructors as $instructor){ ?>
						<div id="added-instructor-id-<?php echo $instructor->ID; ?>" class="added-instructor-item added-instructor-item-<?php echo $instructor->ID; ?>" data-instructor-id="<?php echo $instructor->ID; ?>">
							<span class="instructor-icon">
								<?php echo get_avatar($instructor->ID, 30); ?>
							</span>
							<span class="instructor-name"> <?php echo $instructor->display_name; ?> </span>
							<span class="instructor-control">
								<a href="javascript:;" class="ld-instructor-delete-btn"><i class="dashicons dashicons-no"></i></a>
							</span>
						</div>
						<?php
					}
				}
				?>
			</div>
			 <div class="ld-add-instructor-button-wrap">
				<button type="button" class="ld-btn ld-add-instructor-btn bordered-btn"> <?php esc_html_e('Add More Instructors', 'ld-dashboard'); ?> </button>
			</div>
			
			<div class="ld-modal-wrap ld-instructors-modal-wrap">
				<div class="ld-modal-content">
					<div class="modal-header">
						<div class="modal-title">
							<h1><?php esc_html_e("Add instructors", "ld-dashboard") ?></h1>
						</div>
						<div class="lesson-modal-close-wrap">
							<a href="javascript:;" class="modal-close-btn"><i class="dashicons dashicons-no"></i></a>
						</div>
					</div>
					<div class="modal-content-body">

						<div class="search-bar">
							<input type="text" class="ld-instructor-modal-search-input" placeholder="<?php esc_html_e('Search instructors...',"ld-dashboard"); ?>">
						</div>
					</div>
					<div class="modal-container"></div>
					<div class="modal-footer has-padding">
						<button type="button" class="ld-btn add_instructor_to_course_btn"><?php esc_html_e('Add Instructors', 'ld-dashboard'); ?></button>
					</div>
				</div>
			</div>
					
		</div>
		<?php
	}
	
	public function ld_dashboard_load_instructors_modal() {
		global $wpdb;

		$post_id = (int) sanitize_text_field($_POST['post_id']);
		$search_terms = sanitize_text_field($_POST['search_terms']);

		$saved_instructors =array();
		
		$args = array(				
					'orderby'   => 'user_nicename',
					'role__in'	=> 'ld_instructor',
					'order'     => 'ASC',
					'fields'    => array( 'ID', 'display_name' ),
				);
			
		if ( $saved_instructors != '' ){
			$args['exclude']	= $saved_instructors_ids;
		}
		if ( $search_terms != '' ) {
			$args['search'] 		= '*' . $search_terms . '*';
			$args['search_columns'] = array('user_login','user_nicename','display_name');
		}
			
		$instructors     = get_users( $args );		
		if (is_array($instructors) && count($instructors)){
			$instructor_output = '';
			foreach ($instructors as $instructor){
				$instructor_output .= "<p><label><input type='radio' name='ld_instructor_ids[]' value='{$instructor->ID}' > {$instructor->display_name} </label></p>";
			}

			$output .= $instructor_output;

		}else{
			$output .= __('<p>No instructor available or you have already added maximum instructors</p>', 'ld-dashboard');
		}
		
		wp_send_json_success(array('output' => $output));
	}
	
	public function ld_dashboard_add_instructors_to_course() {
		$post_id = (int) sanitize_text_field($_POST['post_id']);
		$instructor_ids = $_POST['ld_instructor_ids'];
		
		$_ld_instructor_ids = get_post_meta($post_id, '_ld_instructor_ids', true );
		if ( is_array($_ld_instructor_ids) && count($_ld_instructor_ids)){
			foreach ($_ld_instructor_ids as $instructor_id){
				$instructor_ids[] = $instructor_id;
			}
		}
		update_post_meta($post_id, '_ld_instructor_ids', array_unique ($instructor_ids) );		
		$args = array(				
					'orderby'   => 'user_nicename',
					'role__in'	=> 'ld_instructor',
					'order'     => 'ASC',
					'fields'    => array( 'ID', 'display_name' ),
					'include'	=> $instructor_ids,
				);
					
		$saved_instructors     = get_users( $args );	
		
		$output = '';

		if ( !empty($saved_instructors) ) {
			foreach ($saved_instructors as $t ){

				$output .= '<div id="added-instructor-id-'.$t->ID.'" class="added-instructor-item added-instructor-item-'.$t->ID.'" data-instructor-id="'.$t->ID.'">
                    <span class="instructor-icon">'.get_avatar($t->ID, 30).'</span>
                    <span class="instructor-name"> '.$t->display_name.' </span>
                    <span class="instructor-control">
                        <a href="javascript:;" class="ld-instructor-delete-btn"><i class="dashicons dashicons-no"></i></a>
                    </span>
                </div>';
			}
		}

		wp_send_json_success(array('output' => $output));
	}
	
	public function ld_dashboard_detach_instructor() {
		global $wpdb;

		$instructor_id = (int) sanitize_text_field($_POST['instructor_id']);
		$post_id = (int) sanitize_text_field($_POST['post_id']);
		$_ld_instructor_ids = get_post_meta($post_id, '_ld_instructor_ids', true );
		if ( is_array($_ld_instructor_ids) && count($_ld_instructor_ids)){
			foreach ($_ld_instructor_ids as $key=>$inst_id){
				if ( $instructor_id == $inst_id) {
					unset( $_ld_instructor_ids[$key]);
				}
			}
		}
		update_post_meta($post_id, '_ld_instructor_ids', array_unique ($_ld_instructor_ids) );	
		
		wp_send_json_success();
	}

}
