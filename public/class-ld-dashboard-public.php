<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Ld_Dashboard_Public {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name	 = $plugin_name;
		$this->version		 = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ld_Dashboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ld_Dashboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'select2-css', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ld-dashboard-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;

		if ( is_a( $post, 'WP_Post' ) && ( !has_shortcode( $post->post_content, 'ld_dashboard' ) && !has_shortcode( $post->post_content, 'ld_instructor_registration' ) ) ) {
			return;
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ld_Dashboard_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ld_Dashboard_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( 'select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'highcharts-js', plugin_dir_url( __FILE__ ) . 'js/highcharts.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'select2-js', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array( 'jquery' ), $this->version, false );
		//wp_enqueue_script( 'highcharts-js-0', 'https://code.highcharts.com/highcharts.js');
		//wp_enqueue_script( 'highcharts-js-1', 'https://code.highcharts.com/modules/exporting.js');
		//wp_enqueue_script( 'highcharts-js-2', 'https://code.highcharts.com/modules/export-data.js');
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ld-dashboard-public.js', array( 'jquery' ), $this->version, false );

		$ins_month_earning = instructor_monthy_commission_earning( get_current_user_id() );

		$ins_course_earning = ld_instructor_course_wise_earning( get_current_user_id() );
		if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
			$currency = get_woocommerce_currency_symbol();
		} else {
			$currency = '$';
		}
		$ld_dashboard_js_object = array(
			'ajaxurl'	 => admin_url( 'admin-ajax.php' ),
			'nonce'		 => wp_create_nonce( 'ld-dashboard' ),
			'ins_monthly_earning' => $ins_month_earning,
			'ins_course_earning'  => $ins_course_earning,
			'ins_curreny_symbol'  => $currency,
		);
		wp_localize_script( $this->plugin_name, 'ld_dashboard_js_object', $ld_dashboard_js_object );
	}

	/*
	 * Exclude admin users
	 */

	public function ld_dashboard_exclude_admin_users() {
		$reports_exclude_admin_users = false;

		if ( version_compare( LEARNDASH_VERSION, '2.4.0' ) >= 0 ) {
			$reports_exclude_admin_users = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'reports_include_admin_users' );
			if ( $reports_exclude_admin_users == 'yes' )
				$reports_exclude_admin_users = false;
			else
				$reports_exclude_admin_users = true;
		}

		return apply_filters( 'ld_dashboard_exclude_admin_users', $reports_exclude_admin_users );
	}

	/*
	 * Auto enroll admin users
	 */

	public function ld_dashboard_auto_enroll_admin_users() {
		$auto_enroll_admin_users = false;

		if ( version_compare( LEARNDASH_VERSION, '2.4.0' ) >= 0 ) {
			$auto_enroll_admin_users = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
			if ( $auto_enroll_admin_users == 'yes' )
				$auto_enroll_admin_users = true;
			else
				$auto_enroll_admin_users = false;
		}

		return apply_filters( 'ld_dashboard_auto_enroll_admin_users', $auto_enroll_admin_users );
	}

	/*
	 * Get admin user ids
	 */

	public function ld_dashboard_get_admin_user_ids( $return_count = false ) {
		$admin_user_query_args = array(
			'fields' => 'ID',
			'role'	 => 'administrator',
		);

		if ( $return_count === true ) {
			$admin_user_query_args[ 'count_total' ] = true;
		}

		$admin_user_query = new WP_User_Query( $admin_user_query_args );
		if ( $return_count === true ) {
			return $admin_user_query->get_total();
		} else {
			$admin_user_ids = $admin_user_query->get_results();
			if ( !empty( $admin_user_ids ) ) {
				$admin_user_ids = array_map( 'intval', $admin_user_ids );
			}
			return $admin_user_ids;
		}
	}

	/*
	 * Count Post Type
	 */

	public function ld_dashboard_count_post_type( $post_type ) {
		global $wpdb;
		if ( !empty( $post_type ) ) {
			global $current_user;
			$user_id	 = get_current_user_id();
			if ( in_array( 'administrator', (array) $current_user->roles ) ) {
				$query_args	 = array(
					'post_type'		 => $post_type,
					'post_status'	 => 'publish',
				);

			} else {
				/* Post Type Not Courses */

				$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

				$cousres = $wpdb->get_results( $get_courses_sql );
				$course_ids = array();
				if ( !empty($cousres) ) {
					$course_ids = array();
					foreach ($cousres as $course ) {
						$course_ids[] = $course->ID;
					}
				}

				$query_args	 = array(
					'post_type'		 => $post_type,
					'post_status'	 => 'publish',
					'author__in'	 => array( $user_id ),
					'post__in' 		 => $course_ids,
				);
				if ($post_type != 'sfwd-courses' ) {
					unset($query_args['post__in']);
					if ( !empty( $course_ids ) ) {
						$query_args['meta_key'] = 'course_id';
						$query_args['orderby'] = 'meta_value_num';
						$query_args['order'] = 'ASC';
						unset($query_args['author__in']);
						$query_args['meta_query'] = array(
														'key' 	=> 'course_id',
														'value' => $course_ids,
														'compare' => 'IN',
													);
					}
				} else {
					unset($query_args['author__in']);
				}
				if ( empty($course_ids) && isset($query_args['post__in'])) {
					$query_args['post__in'] = array(0);
				}
			}
			return learndash_get_courses_count( $query_args );
		}
	}

	/*
	 * Get Total Users Count
	 */

	public function ld_dashboard_get_users_count() {
		global $wpdb, $current_user;
		$all_user_ids = array();

		$return_total_users = 0;

		$exclude_admin_users	 = $this->ld_dashboard_exclude_admin_users();
		$auto_enroll_admin_users = $this->ld_dashboard_auto_enroll_admin_users();

		$ld_open_courses = learndash_get_open_courses();
		$admin_user_ids	 = $this->ld_dashboard_get_admin_user_ids();
		if ( $this->ld_dashboard_count_post_type( 'sfwd-courses' ) ) {
			// If we have any OPEN courses then we just use the WP_User_Query to get all users.
			if ( !empty( $ld_open_courses ) && ! in_array( 'ld_instructor', (array) $current_user->roles ) ) {
				$user_query_args = array(
					'count_total'	 => true,
					'fields'		 => 'ID'
				);

				$user_query_args = apply_filters( 'ld_dashboard_overview_students_count_args', $user_query_args );
				if ( !empty( $user_query_args ) ) {
					$user_query = new WP_User_Query( $user_query_args );
					if ( $user_query instanceof WP_User_Query ) {
						$all_user_ids = $user_query->get_results();
					}
				}
			} else {

				// Else if there are no open courses we the query users with 'learndash_group_users_%' OR 'course_%_access_from' meta_keys

				$user_id	 = get_current_user_id();
				if ( in_array( 'ld_instructor', (array) $current_user->roles ) ) {

					$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

					$users_courses_sql		 = "SELECT DISTINCT users.ID FROM {$wpdb->users} as users
					LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )
					LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )
					WHERE 1=1
					AND (
						um1.meta_key = '{$wpdb->prefix}capabilities'
						AND ( um2.meta_key IN
							(
								SELECT DISTINCT CONCAT('course_', p.ID, '_access_from') FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( p.ID = pm6.post_id ) WHERE p.post_type='sfwd-courses' AND p.post_status='publish' AND ( p.post_author = '".$user_id."' OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) )
								UNION ALL
								SELECT DISTINCT CONCAT('course_completed_', p.ID, '') FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( p.ID = pm6.post_id ) WHERE p.post_type='sfwd-courses' AND p.post_status='publish' AND ( p.post_author = '".$user_id."' OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) )
								UNION ALL
								SELECT DISTINCT CONCAT('learndash_course_expired_', p.ID, '') FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( p.ID = pm6.post_id ) WHERE p.post_type='sfwd-courses' AND p.post_status='publish' AND ( p.post_author = '".$user_id."' OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) )
							)
						)
					)";

					$users_courses_results	 = $wpdb->get_col( $users_courses_sql );
					$users_groups_sql		 = "SELECT DISTINCT users.ID FROM {$wpdb->users} users
					LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )
					LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )
					WHERE 1=1
					AND (
						um1.meta_key = '{$wpdb->prefix}capabilities'
						AND ( um2.meta_key IN
								(
									SELECT CONCAT('learndash_group_users_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='groups' AND p.post_status='publish' AND p.post_author = '".$user_id."'
								)
							)
						)";

					$users_groups_results	 = $wpdb->get_col( $users_groups_sql );

					$all_user_ids = array_merge( $users_courses_results, $users_groups_results );

				} else {
					// Else if there are no open courses we the query users with 'learndash_group_users_%' OR 'course_%_access_from' meta_keys
					$users_courses_sql		 = "SELECT DISTINCT users.ID FROM {$wpdb->users} as users
					LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )
					LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )
					WHERE 1=1
					AND (
						um1.meta_key = '{$wpdb->prefix}capabilities'
						AND ( um2.meta_key IN
							(
								SELECT DISTINCT CONCAT('course_', p.ID, '_access_from') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
								UNION ALL
								SELECT DISTINCT CONCAT('course_completed_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
								UNION ALL
								SELECT DISTINCT CONCAT('learndash_course_expired_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='sfwd-courses' AND p.post_status='publish'
							)
						)
					)";

					$users_courses_results	 = $wpdb->get_col( $users_courses_sql );

					$users_groups_sql		 = "SELECT DISTINCT users.ID FROM {$wpdb->users} users
					LEFT JOIN {$wpdb->usermeta} as um1 ON ( users.ID = um1.user_id )
					LEFT JOIN {$wpdb->usermeta} as um2 ON ( users.ID = um2.user_id )
					WHERE 1=1
					AND (
						um1.meta_key = '{$wpdb->prefix}capabilities'
						AND ( um2.meta_key IN
								(
									SELECT CONCAT('learndash_group_users_', p.ID, '') FROM {$wpdb->prefix}posts p WHERE p.post_type='groups' AND p.post_status='publish'
								)
							)
						)";

					$users_groups_results	 = $wpdb->get_col( $users_groups_sql );

					$all_user_ids = array_merge( $users_courses_results, $users_groups_results );
				}
			}

			if ( ( $exclude_admin_users !== true ) && ( $auto_enroll_admin_users === true ) && (!empty( $admin_user_ids ) ) ) {
				$all_user_ids = array_merge( $all_user_ids, $admin_user_ids );
			} else if ( ( $exclude_admin_users === true ) && (!empty( $admin_user_ids ) ) ) {
				$all_user_ids = array_diff( $all_user_ids, $admin_user_ids );
			}

			if ( (!empty( $all_user_ids ) ) && ( is_array( $all_user_ids ) ) ) {
				$all_user_ids		 = array_map( 'intval', $all_user_ids );
				$all_user_ids		 = array_unique( $all_user_ids );
				$return_total_users	 = count( $all_user_ids );
			}
		}

		return $return_total_users;
	}

	/*
	 * Get  Students by Insttuctor ID
	 */

	public function ld_dashboard_get_instructor_students_by_id( $user_id ) {
		if ( empty( $user_id ) ) {
			return;
		}
		global $wpdb;
		$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";
		$cousres = $wpdb->get_results( $get_courses_sql );
		$course_ids = array(0);
		if ( !empty($cousres) ) {
			$course_ids = array();
			foreach ($cousres as $course ) {
				$course_ids[] = $course->ID;
			}
		}

		$args			 = array(
			'post_type'		 => 'sfwd-courses',
			'post_status'	 => 'publish',
			'fields'		 => 'ids',
			//'author'		 => $user_id,
			'post__in' 		 => $course_ids,
		);
		//$my_courses		 = get_posts( $args );
		$my_courses		 = new WP_Query( $args );
		$total_students	 = array();
		$arg			 = array(
			//'meta_key'	 => 'is_student',
			//'meta_value' => true,
			'fields'	 => array( 'ID', 'display_name' ),
		);

		if ( $my_courses->have_posts()) {
			while ( $my_courses->have_posts() ){
					$my_courses->the_post();
				$ins_student = learndash_get_users_for_course( get_the_ID(), $arg, true );
				if ( !is_array( $ins_student ) ) {
					$total_restudents = $ins_student->get_results();
					if ( !empty( $total_restudents ) ) {
						foreach ( $total_restudents as $key => $total_restudent ) {
							$total_students[] = $total_restudent;
						}
					}
				}
			}
		}
		wp_reset_postdata();

		if ( !empty( $total_students ) ) {
			$total_students	 = array_unique( $total_students, SORT_REGULAR );
			$total_students	 = array_values( $total_students );
		}
		return apply_filters( 'ld_dashboard_get_instructor_students_by_id', $total_students );
	}

	/*
	 * Get Groups Query vars
	 */

	public function ld_dashboard_get_groups_query_vars() {
		$vars					 = array();
		$user					 = wp_get_current_user();
		$get_group_leader_groups = learndash_get_administrators_group_ids( $user->ID );
		if ( !empty( $get_group_leader_groups ) ) {
			foreach ( $get_group_leader_groups as $key => $group_id ) {
				$group						 = get_post( $group_id );
				$vars[ $group->post_name ]	 = $group_id;
			}
		}
		return $vars;
	}

	/*
	 * Get Course ost Items
	 */

	public function ld_dashboard_get_course_post_items( $course_id = 0,
		$post_types = array( 'sfwd-courses', 'sfwd-quiz', 'sfwd-lessons', 'sfwd-topic' ) ) {
		if ( !empty( $course_id ) ) {
			$query_course_args = array(
				'post_type'		 => $post_types,
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'fields'		 => 'ids',
				'meta_query'	 => array(
					'relation' => 'OR',
					array(
						'key'		 => 'course_id',
						'value'		 => $course_id,
						'compare'	 => '=',
					)
				)
			);

			if ( version_compare( LEARNDASH_VERSION, '2.4.9.9' ) >= 0 ) {
				$query_course_args[ 'meta_query' ][] = array(
					'key'		 => 'ld_course_' . $course_id,
					'value'		 => $course_id,
					'compare'	 => '=',
				);
			}
			$query_course = new WP_Query( $query_course_args );
			if ( !empty( $query_course->posts ) ) {
				return $query_course->posts;
			}
		}
	}

	/**
	 * @param $activity
	 *
	 * @return array|null|WP_Post
	 */
	public function ld_dashboard_get_activity_course( $activity ) {
		if ( ( isset( $activity->activity_course_id ) ) && (!empty( $activity->activity_course_id ) ) ) {
			$course_id = intval( $activity->activity_course_id );
		} else {
			$course_id = learndash_get_course_id( $activity->post_id );
		}

		if ( !empty( $course_id ) ) {
			$course = get_post( $course_id );
			if ( ( $course ) && ( $course instanceof WP_Post ) ) {
				return $course;
			}
		}
	}

	/**
	 * @param $activity
	 *
	 * @return bool
	 */
	public function ld_dashboard_quiz_activity_is_pending( $activity ) {
		if ( (!empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {

			if ( ( isset( $activity->activity_meta[ 'has_graded' ] ) ) && ( true === $activity->activity_meta[ 'has_graded' ] ) && ( true === LD_QuizPro::quiz_attempt_has_ungraded_question( $activity->activity_meta ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $activity
	 *
	 * @return bool
	 */
	public function ld_dashboard_quiz_activity_is_passing( $activity ) {
		if ( (!empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {

			if ( isset( $activity->activity_meta[ 'pass' ] ) ) {
				return (bool) $activity->activity_meta[ 'pass' ];
			}
		}

		return false;
	}

	public function ld_dashboard_get_quiz_statistics_link( $activity ) {
		$stats_url = '';

		if ( ( $activity->user_id == get_current_user_id() ) || ( learndash_is_admin_user() ) || ( learndash_is_group_leader_user() ) ) {
			if ( ( isset( $activity->activity_meta[ 'statistic_ref_id' ] ) ) && (!empty( $activity->activity_meta[ 'statistic_ref_id' ] ) ) ) {

				if ( apply_filters(
				'show_user_profile_quiz_statistics', get_post_meta( $activity->activity_meta[ 'quiz' ], '_viewProfileStatistics', true ), $activity->user_id, $activity->activity_meta, 'learndash-dashboard-activity' ) ) {
					$stats_url = '<a class="user_statistic" data-statistic_nonce="' . wp_create_nonce( 'statistic_nonce_' . $activity->activity_meta[ 'statistic_ref_id' ] . '_' . get_current_user_id() . '_' . $activity->user_id ) . '" data-user_id="' . $activity->user_id . '" data-quiz_id="' . $activity->activity_meta[ 'pro_quizid' ] . '" data-ref_id="' . intval( $activity->activity_meta[ 'statistic_ref_id' ] ) . '" href="#" title="' . __( 'View Quiz Statistics', 'ld-dashboard' ) . '">' . __( 'Statistics', 'ld-dashboard' ) . '</a>';
				}
			}
		}

		return $stats_url;
	}

	/**
	 * @param $activity
	 *
	 * @return int
	 */
	public function ld_dashboard_quiz_activity_points_percentage( $activity ) {
		$awarded_points	 = intval( $this->ld_dashboard_quiz_activity_awarded_points( $activity ) );
		$total_points	 = intval( $this->ld_dashboard_quiz_activity_total_points( $activity ) );
		if ( (!empty( $awarded_points ) ) && (!empty( $total_points ) ) ) {
			return round( 100 * ( intval( $awarded_points ) / intval( $total_points ) ) );
		}
	}

	/**
	 * @param $activity
	 *
	 * @return mixed
	 */
	public function ld_dashboard_quiz_activity_total_points( $activity ) {
		if ( (!empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
			if ( isset( $activity->activity_meta[ 'total_points' ] ) ) {
				return intval( $activity->activity_meta[ 'total_points' ] );
			}
		}
	}

	/**
	 * @param $activity
	 *
	 * @return mixed
	 */
	public function ld_dashboard_quiz_activity_awarded_points( $activity ) {
		if ( (!empty( $activity ) ) && ( property_exists( $activity, 'activity_meta' ) ) ) {
			if ( isset( $activity->activity_meta[ 'points' ] ) ) {
				return intval( $activity->activity_meta[ 'points' ] );
			}
		}
	}

	public function ld_dashboard_activity_rows_ajax() {
		global $wp;
		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$activities_settings        = $ld_dashboard_settings_data['activities_settings'];

		$act_limit	                =  $activities_settings['activity-limit'];
		$output		                = '';
		$user		                = wp_get_current_user();

		/**
		 * Build $activity_query_args from info passed as AJAX
		 */
		$activity_query_args = array(
			'per_page'		 => $act_limit,
			'activity_types' => array( 'course', 'quiz', 'lesson', 'topic','access' ),
			'post_types'	 => array( 'sfwd-courses', 'sfwd-quiz', 'sfwd-lessons', 'sfwd-topic' ),
			'post_status'	 => 'publish',
			'orderby_order'	 => 'ld_user_activity.activity_updated DESC',
			'date_format'	 => 'Y-m-d H:i:s',
			'export_buttons' => true,
			'nav_top'		 => true,
		);
		ob_start();
		$paged = 1;
		if ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) {
			$activity_query_args['paged'] = abs( intval( $_GET['paged'] ) );
			$paged = intval( $_GET['paged'] );
		}else {
			$activity_query_args['paged'] = $paged;
		}

		// If apecific post_ids are provided we want to inlcude in all the lessons, topics, quizzes for display
		if ( ( isset( $activity_query_args[ 'post_ids' ] ) ) && (!empty( $activity_query_args[ 'post_ids' ] ) ) ) {
			if ( version_compare( LEARNDASH_VERSION, '2.4.9.9' ) >= 0 ) {
				$activity_query_args[ 'course_ids' ] = $activity_query_args[ 'post_ids' ];
				$activity_query_args[ 'post_ids' ]	 = '';
			} else {
				$post_ids = $activity_query_args[ 'post_ids' ];
				foreach ( $post_ids as $course_id ) {
					$course_post_status = get_post_status( $course_id );
					if ( $course_post_status == 'publish' ) {
						$course_post_ids = ld_dashboard_get_course_post_items( $course_id, $activity_query_args[ 'post_types' ] );
						if ( !empty( $course_post_ids ) ) {
							$activity_query_args[ 'post_ids' ]	 = array_merge( $activity_query_args[ 'post_ids' ], $course_post_ids );
							$activity_query_args[ 'post_ids' ]	 = array_unique( $activity_query_args[ 'post_ids' ] );
						}
					}
				}
			}
		}
		$activity_query_args[ 'activity_status' ] = array( 'IN_PROGRESS', 'COMPLETED' );
		add_filter( 'learndash_user_activity_query_where', function ($sql_str_where) {	
				$sql_str_where = str_replace( 'ld_user_activity.activity_status IN (0,1)', ' ( ld_user_activity.activity_status IS NULL OR ld_user_activity.activity_status IN (0,1))', $sql_str_where);
				return $sql_str_where;
			});
		if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
			$course_students	 = array();
			$course_student_ids	 = array();
			$course_students	 = $this->ld_dashboard_get_instructor_students_by_id( $user->ID );

			if ( !empty( $course_students ) ) {
				foreach ( $course_students as $key => $course_student ) {
					$course_student_ids[] = $course_student->ID;
				}
			}
			array_push( $course_student_ids, $user->ID );
			/* Get The Insttuctor Course */
			$args = array(
						'post_type'		 => array( 'sfwd-courses' ),
						'author'		 => $user->ID,
						'post_status'	 => 'publish',
						'posts_per_page' => -1,
					);
			$courses = get_posts( $args );
			$course_ids = array();
			foreach ( $courses as $index => $course ) {
				$course_ids[] =  $course->ID;
			}
			$activity_query_args[ 'per_page' ]	 	= $act_limit;
			$activity_query_args[ 'user_ids' ]	 	= $course_student_ids;
			$activity_query_args[ 'course_ids' ]	= $course_ids;
			$activity_query_args[ 'is_post_ids' ] 	= true;
			$activity_query_args[ 'post_ids' ] 		= true;
		}
		if ( learndash_is_group_leader_user() ) {
			$group_courses = learndash_get_group_leader_groups_courses();
			$group_users = learndash_get_group_leader_groups_users();
			$activity_query_args[ 'per_page' ]	 = $act_limit;
			$activity_query_args[ 'user_ids' ]	 = ( !empty($group_users)) ? $group_users : array(0);
			$activity_query_args[ 'course_ids' ] = ( !empty($group_courses)) ? $group_courses : array(0);
		}

		if ( get_user_meta( $user->ID, 'is_student', true ) || ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $user->roles ) ) ) {
				$activity_query_args[ 'user_ids' ]	 = $user->ID;
				$activity_query_args[ 'per_page' ]	 = $act_limit;
				$activity_query_args[ 'post_ids' ]	 = array();
				$activity_query_args[ 'is_post_ids' ] = true;
			}

		add_filter( 'learndash_user_activity_query_where', array($this, 'ld_dashboard_user_activity_query_where'), 10, 2 );		
		
		$activities = learndash_reports_get_activity( $activity_query_args );

		$activity_row_date_time_format = apply_filters( 'ld_dashboard_activity_row_date_time_format', get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
		foreach ( $activities[ 'results' ] as $activity ) {
			$activity->activity_started_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_started ), 'Y-m-d H:i:s' );
			$activity->activity_started_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_started_formatted ), false );

			$activity->activity_completed_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_completed ), 'Y-m-d H:i:s' );
			$activity->activity_completed_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_completed_formatted ), false );

			$activity->activity_updated_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_updated ), 'Y-m-d H:i:s' );
			$activity->activity_updated_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_updated_formatted ), false );

			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-activity-rows.php';
		}
		if ( isset( $activities['pager'] ) ) {
			$activities['pager']['current_page'] = $activity_query_args['paged'];
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-activity-pagination.php';
		}

		echo $html = ob_get_clean();

		wp_die();
	}
	/*
	 * Learndash dashboard activity Row
	 */

	public function ld_dashboard_activity_rows() {
		global $wp,$wpdb;

		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$activities_settings        = $ld_dashboard_settings_data['activities_settings'];

		$act_limit	=  $activities_settings['activity-limit'];
		$output		 = '';
		$user		 = wp_get_current_user();

		/**
		 * Build $activity_query_args from info passed as AJAX
		 */
		$activity_query_args = array(
			'per_page'		 => $act_limit,
			'activity_types' => array( 'course', 'quiz', 'lesson', 'topic','access' ),
			'post_types'	 => array( 'sfwd-courses', 'sfwd-quiz', 'sfwd-lessons', 'sfwd-topic' ),
			'post_status'	 => 'publish',
			'orderby_order'	 => 'ld_user_activity.activity_updated DESC',
			'date_format'	 => 'Y-m-d H:i:s',
			'export_buttons' => true,
			'nav_top'		 => true,
		);

		$paged = 1;
		if ( isset( $_GET['args']['paged'] ) && ! empty( $_GET['args']['paged'] ) ) {
			$activity_query_args['paged'] = abs( intval( $_GET['args']['paged'] ) );
			$paged = intval( $_GET['args']['paged'] );
		}else {
			$activity_query_args['paged'] = $paged;
		}

		if ( !empty( $activity_query_args ) ) {

			// If apecific post_ids are provided we want to inlcude in all the lessons, topics, quizzes for display
			if ( ( isset( $activity_query_args[ 'post_ids' ] ) ) && (!empty( $activity_query_args[ 'post_ids' ] ) ) ) {
				if ( version_compare( LEARNDASH_VERSION, '2.4.9.9' ) >= 0 ) {
					$activity_query_args[ 'course_ids' ] = $activity_query_args[ 'post_ids' ];
					$activity_query_args[ 'post_ids' ]	 = '';
				} else {
					$post_ids = $activity_query_args[ 'post_ids' ];
					foreach ( $post_ids as $course_id ) {
						$course_post_status = get_post_status( $course_id );
						if ( $course_post_status == 'publish' ) {
							$course_post_ids = ld_dashboard_get_course_post_items( $course_id, $activity_query_args[ 'post_types' ] );
							if ( !empty( $course_post_ids ) ) {
								$activity_query_args[ 'post_ids' ]	 = array_merge( $activity_query_args[ 'post_ids' ], $course_post_ids );
								$activity_query_args[ 'post_ids' ]	 = array_unique( $activity_query_args[ 'post_ids' ] );
							}
						}
					}
				}
			}
			$activity_query_args[ 'activity_status' ] = array( 'IN_PROGRESS', 'COMPLETED' );
			add_filter( 'learndash_user_activity_query_where', function ($sql_str_where) {	
				$sql_str_where = str_replace( 'ld_user_activity.activity_status IN (0,1)', ' ( ld_user_activity.activity_status IS NULL OR ld_user_activity.activity_status IN (0,1))', $sql_str_where);
				return $sql_str_where;
			});
			

			if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
				$course_students	 = array();
				$course_student_ids	 = array();
				$course_students	 = $this->ld_dashboard_get_instructor_students_by_id( $user->ID );

				if ( !empty( $course_students ) ) {
					foreach ( $course_students as $key => $course_student ) {
						$course_student_ids[] = $course_student->ID;
					}
				}
				array_push( $course_student_ids, $user->ID );
				/* Get The Insttuctor Course */
				$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user->ID} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user->ID}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

				$cousres = $wpdb->get_results( $get_courses_sql );
				$course_ids = array();
				if ( !empty($cousres) ) {
					$course_ids = array();
					foreach ($cousres as $course ) {
						$course_ids[] = $course->ID;
					}
				}
				$args = array(
							'post_type'		 => array( 'sfwd-courses' ),
							'author'		 => $user->ID,
							'post_status'	 => 'publish',
							'posts_per_page' => -1,
						);
				if ( !empty( $course_ids ) ) {
					unset($args['author']);
					$args['post__in'] = $course_ids;
				}

				$courses = get_posts( $args );
				$course_ids = array();
				foreach ( $courses as $index => $course ) {
					$course_ids[] =  $course->ID;
				}

				$activity_query_args[ 'per_page' ]	 	= $act_limit;
				$activity_query_args[ 'user_ids' ]	 	= $course_student_ids;
				$activity_query_args[ 'course_ids' ]	= $course_ids;
				$activity_query_args[ 'is_post_ids' ] 	= true;
				$activity_query_args[ 'post_ids' ] 		= true;
			}
			if ( learndash_is_group_leader_user() ) {
				$group_courses = learndash_get_group_leader_groups_courses();
				$group_users = learndash_get_group_leader_groups_users();
				$activity_query_args[ 'per_page' ]	 = $act_limit;
				$activity_query_args[ 'user_ids' ]	 = ( !empty($group_users)) ? $group_users : array(0);
				$activity_query_args[ 'course_ids' ] = ( !empty($group_courses)) ? $group_courses : array(0);
			}
			if ( get_user_meta( $user->ID, 'is_student', true ) || ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $user->roles ) ) )
			{
				$activity_query_args[ 'user_ids' ]	 = $user->ID;
				$activity_query_args[ 'per_page' ]	 = $act_limit;
				$activity_query_args[ 'post_ids' ]	 = array();
				$activity_query_args[ 'is_post_ids' ] = true;
			}
			add_filter( 'learndash_user_activity_query_where', array($this, 'ld_dashboard_user_activity_query_where'), 10, 2 );			
			$activities = learndash_reports_get_activity( $activity_query_args );
			if ( empty( $activities[ 'results' ] ) ) {
				?>
				<div class="ld-dashboard-activity-empty">
					<?php
					if ( get_user_meta( $user->ID, 'is_student', true ) || ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $user->roles ) ) ) {

						echo apply_filters( 'ld_dashboard_no_activity_text', esc_html__( 'Sorry, We are not able to find any course related activities, Please complete some lessons, topics or quizzes.', 'ld-dashboard' ) );
					} else {

						echo apply_filters( 'ld_dashboard_no_activity_text', esc_html__( 'Sorry, We are not able to find any course related activities, Please encourage your students to complete some lessons, topics or quizzes. ', 'ld-dashboard' ) );
					}
					?>
				</div>
				<?php
			} else {
				$activity_row_date_time_format = apply_filters( 'ld_dashboard_activity_row_date_time_format', get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
				foreach ( $activities[ 'results' ] as $activity ) {
					$activity->activity_started_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_started ), 'Y-m-d H:i:s' );
					$activity->activity_started_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_started_formatted ), false );

					$activity->activity_completed_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_completed ), 'Y-m-d H:i:s' );
					$activity->activity_completed_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_completed_formatted ), false );

					$activity->activity_updated_formatted	 = get_date_from_gmt( date( 'Y-m-d H:i:s', $activity->activity_updated ), 'Y-m-d H:i:s' );
					$activity->activity_updated_formatted	 = date_i18n( $activity_row_date_time_format, strtotime( $activity->activity_updated_formatted ), false );

					include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-activity-rows.php';
				}
				if ( isset( $activities['pager'] ) ) {
					$activities['pager']['current_page'] = $activity_query_args['paged'];
					include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-activity-pagination.php';
				}
			}
		} else {
			?>
			<div class="ld-dashbard-activity-empty">
				<?php
				if ( get_user_meta( $user->ID, 'is_student', true ) || ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $user->roles ) ) ) {

					echo apply_filters( 'ld_dashboard_no_activity_text', esc_html__( 'Sorry, We are not able to find any course related activities, Please complete some lessons, topics or quizzes.', 'ld-dashboard' ) );
				} else {

					echo apply_filters( 'ld_dashboard_no_activity_text', esc_html__( 'Sorry, We are not able to find any course related activities, Please encourage your students to complete some lessons, topics or quizzes. ', 'ld-dashboard' ) );
				}
				?>
			</div>
			<?php
		}
	}

	/*
	 * Get the Insttuctor Overview statistic
	 */

	public function ld_get_overview_instructor_states() {

		$user							 = wp_get_current_user();
		$ld_dashboard_instructors_stds	 = get_users(
		array(
			'fields'	 => array( 'ID', 'display_name' ),
			'role__in'	 => array( 'ld_instructor', 'administrator' ),
		)
		);
		if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
			$ld_dashboard_instructors_stds	 = array();
			$obj							 = new stdClass();
			$obj->ID						 = $user->ID;
			$obj->display_name				 = $user->display_name;
			$ld_dashboard_instructors_stds[] = $obj;
		}
		$instructor_stat_data = array();
		if ( !empty( $ld_dashboard_instructors_stds ) ) {
			foreach ( $ld_dashboard_instructors_stds as $udata ) {
				$temp							 = array();
				$temp[ 'instructor_id' ]		 = $udata->ID;
				$temp[ 'display_name' ]			 = $udata->display_name;
				$commission_percent				 = 'NA';
				$temp[ 'commission_percent' ]	 = $commission_percent;
				/**
				 * Count Courses
				 */
				$course_count					 = 0;
				$course_args					 = array(
					'post_type'		 => 'sfwd-courses',
					'posts_per_page' => -1,
					'author'		 => $udata->ID,
					'post_status'	 => 'publish',
				);
				$courses						 = get_posts( $course_args );
				if ( !empty( $courses ) ) {
					$course_count = count( $courses );
				}
				$temp[ 'course_count' ]	 = $course_count;
				/**
				 * Count Lessons
				 */
				$lesson_count			 = 0;
				$lesson_args			 = array(
					'post_type'		 => 'sfwd-lessons',
					'posts_per_page' => -1,
					'author'		 => $udata->ID,
					'post_status'	 => 'publish',
				);
				$lessons				 = get_posts( $lesson_args );
				if ( !empty( $lessons ) ) {
					$lesson_count = count( $lessons );
				}
				$temp[ 'lesson_count' ]	 = $lesson_count;
				/**
				 * Count Topics
				 */
				$topic_count			 = 0;
				$topic_args				 = array(
					'post_type'		 => 'sfwd-topic',
					'posts_per_page' => -1,
					'author'		 => $udata->ID,
					'post_status'	 => 'publish',
				);
				$topics					 = get_posts( $topic_args );
				if ( !empty( $topics ) ) {
					$topic_count = count( $topics );
				}
				$temp[ 'topic_count' ]	 = $topic_count;
				/**
				 * Count Quizzes
				 */
				$quiz_count				 = 0;
				$quiz_args				 = array(
					'post_type'		 => 'sfwd-quiz',
					'posts_per_page' => -1,
					'author'		 => $udata->ID,
					'post_status'	 => 'publish',
				);
				$quizzes				 = get_posts( $quiz_args );
				if ( !empty( $quizzes ) ) {
					$quiz_count = count( $quizzes );
				}
				$temp[ 'quiz_count' ]	 = $quiz_count;
				/**
				 * Count Assignments
				 */
				$assignment_count		 = 0;
				$assignment_args		 = array(
					'post_type'		 => 'sfwd-assignment',
					'posts_per_page' => -1,
					'author'		 => $udata->ID,
					'post_status'	 => 'publish',
				);
				$assignments			 = get_posts( $assignment_args );
				if ( !empty( $assignments ) ) {
					$assignment_count = count( $assignments );
				}
				$temp[ 'assignment_count' ]	 = $assignment_count;
				$instructor_stat_data[]		 = $temp;
			}
		}
		return $instructor_stat_data;
	}

	/**
	 * Retrieve the complete details of the student.
	 */
	public function ld_dashboard_get_student_data( $user_id ) {
		$sfwd_course_progress	 = get_user_meta( $user_id, '_sfwd-course_progress', true );
		$student_data			 = array();
		$course_completed		 = 0;
		if ( !empty( $sfwd_course_progress ) ) {
			foreach ( $sfwd_course_progress as $cid => $data ) {
				if ( get_user_meta( $user_id, 'course_completed_' . $cid, true ) ) {
					$course_completed ++;
				}
			}
		}

		$sfwd_quizzes	 = get_user_meta( $user_id, '_sfwd-quizzes', true );
		$quiz_completed	 = 0;
		if ( !empty( $sfwd_quizzes ) ) {
			foreach ( $sfwd_quizzes as $key => $quiz ) {
				$quiz_completed += learndash_get_user_quiz_attempts_count( $user_id, $quiz[ 'quiz' ] );
			}
		}

		$assignment_args = array(
			'post_type'		 => 'sfwd-assignment',
			'post_status'	 => 'publish',
			'author'		 => $user_id,
			'meta_key'		 => 'approval_status',
			'meta_value'	 => 1,
		);
		$assignment		 = get_posts( $assignment_args );
		$course_count	 = 0;
		if ( count( $assignment ) > 0 ) {
			$course_count = count( $assignment );
		}

		$student_data[ 'course_completed' ]		 = $course_completed;
		$student_data[ 'quiz_completed' ]		 = $quiz_completed;
		$student_data[ 'assignment_completed' ]	 = $course_count;
		return $student_data;
	}

	/*
	 * Add shortcode functuon
	 */

	public function ld_dashboard_register_shortcodes() {
		add_shortcode( 'ld_dashboard', array( $this, 'ld_dashboard_functions' ) );
		add_shortcode( 'ld_email', array( $this, 'ld_dashboard_email_functions' ) );
		add_shortcode( 'ld_message', array( $this, 'ld_dashboard_message_functions' ) );
		add_shortcode( 'ld_instructor_registration', array( $this, 'ld_instructor_registration_functions' ) );
	}

	public function ld_dashboard_functions( $atts, $content ) {

		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$ld_dashboard               = $ld_dashboard_settings_data['general_settings'];

		$user_id		 = get_current_user_id();
		$is_student		 = get_user_meta( $user_id, 'is_student', true );

		ob_start();
		if ( !is_user_logged_in() ) {
			?>

			<p><?php esc_html_e( 'Please try to login to website to access dashboard. Dashboard are disabled for logout members. ', 'ld-dashboard' ); ?></p>
			<?php
			return ob_get_clean();
		}
		include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard.php';

		return ob_get_clean();
	}

	/**
	 * Check and return course progress data
	 *
	 * @param type $user_id
	 * @param type $course_id
	 * @return array
	 */
	public function ld_dashboard_check_course_progress_data( $user_id, $course_id ) {
		if ( empty( $user_id ) || empty( $course_id ) ) {
			return;
		}

		$percentage				 = 0;
		$cours_completed_date	 = '-';
		$user_meta				 = get_user_meta( $user_id, '_sfwd-course_progress', true );
		$user_quizze			 = get_user_meta( $user_id, '_sfwd-quizzes', true );
		if ( !empty( $user_meta ) ) {
			if ( isset( $user_meta[ $course_id ] ) ) {
				$percentage				 = floor( ( $user_meta[ $course_id ][ 'completed' ] / $user_meta[ $course_id ][ 'total' ] ) * 100 );
				$cours_completed_meta	 = get_user_meta( $user_id, 'course_completed_' . $course_id, true );
				$cours_completed_date	 = (!empty( $cours_completed_meta ) ) ? date( 'F j, Y H:i:s', $cours_completed_meta ) : '';
			}
			$ld_course_steps = get_post_meta( $course_id, 'ld_course_steps', true);
			$lessons_ids = $topic_ids = array();
			if ( !empty($ld_course_steps) && isset($ld_course_steps['h']['sfwd-lessons'])) {

				foreach( $ld_course_steps['h']['sfwd-lessons'] as $key=>$topic){
					$lessons_ids[] = $key;
					foreach( $topic['sfwd-topic'] as $topic_id=>$quiz) {
						$topic_ids[] = $topic_id;
					}
				}

			}

			/* Get the Number of Assignments from Leasson */
			$query_lessons_args			 = array(
				'post_type'		 => 'sfwd-lessons',
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'fields'		 => 'ids',
				'meta_query'	 => array(
					'relation' => 'OR',
					array(
						'key'		 => 'course_id',
						'value'		 => $course_id,
						'compare'	 => '=',
					)
				)
			);
			if ( !empty($lessons_ids)) {
				unset($query_lessons_args['meta_query']);
				$query_lessons_args['post__in'] = $lessons_ids;
			}
			$query_lessons				 = new WP_Query( $query_lessons_args );
			$course_assignment_counts	 = 0;
			$total_assignment_counts	 = 0;
			if ( $query_lessons->have_posts() ) {
				while ( $query_lessons->have_posts() ) {
					$query_lessons->the_post();
					$_sfwd_lessons = get_post_meta( get_the_ID(), '_sfwd-lessons', true );
					if ( isset( $_sfwd_lessons[ 'sfwd-lessons_lesson_assignment_upload' ] ) && $_sfwd_lessons[ 'sfwd-lessons_lesson_assignment_upload' ] == 'on' ) {
						$total_assignment_counts = ++$course_assignment_counts;
					}
				}
			}
			wp_reset_postdata();

			/* Get the Number of Assignments from sfwd-topic */
			$query_topic_args			 = array(
				'post_type'		 => 'sfwd-topic',
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'fields'		 => 'ids',
				'meta_query'	 => array(
					'relation' => 'OR',
					array(
						'key'		 => 'course_id',
						'value'		 => $course_id,
						'compare'	 => '=',
					)
				)
			);
			if ( !empty($topic_ids)) {
				unset($query_topic_args['meta_query']);
				$query_topic_args['post__in'] = $topic_ids;
			}
			$query_topic				 = new WP_Query( $query_topic_args );
			if ( $query_topic->have_posts() ) {
				while ( $query_topic->have_posts() ) {
					$query_topic->the_post();
					$_sfwd_topic = get_post_meta( get_the_ID(), '_sfwd-topic', true );
					if ( isset( $_sfwd_topic[ 'sfwd-topic_lesson_assignment_upload' ] ) && $_sfwd_topic[ 'sfwd-topic_lesson_assignment_upload' ] == 'on' ) {
						$total_assignment_counts = ++$course_assignment_counts;
					}
				}
			}
			wp_reset_postdata();

			/* Get the Number of Assignments From user uploaded */
			$query_assignment_args	 = array(
				'post_type'		 => 'sfwd-assignment',
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'fields'		 => 'ids',
				'meta_query'	 => array(
					'relation' => 'AND',
					array(
						'key'		 => 'course_id',
						'value'		 => $course_id,
						'compare'	 => '=',
					),
					array(
						'key'		 => 'user_id',
						'value'		 => $user_id,
						'compare'	 => '=',
					)
				)
			);
			$query_assignment		 = new WP_Query( $query_assignment_args );

			$number_of_assignment_counts		 = 0;
			$number_of_approve_assignment_counts = 0;
			$assignment_percentage				 = 0;
			if ( $query_assignment->have_posts() ) {
				while ( $query_assignment->have_posts() ) {
					$query_assignment->the_post();
					$number_of_assignment_counts = ++$number_of_assignment_counts;
					if ( get_post_meta( get_the_ID(), 'approval_status', true ) == 1 ) {
						$number_of_approve_assignment_counts = ++$number_of_approve_assignment_counts;
					}
				}
				$assignment_percentage = floor( ( $number_of_approve_assignment_counts / $total_assignment_counts ) * 100 );
			}
			wp_reset_postdata();

			/* User Quize Progress */
			$quizze_percentage = 0;

			$query_quizze_args	 = array(
				'post_type'		 => 'sfwd-quiz',
				'post_status'	 => 'publish',
				'posts_per_page' => -1,
				'fields'		 => 'ids',
				'meta_query'	 => array(
					'relation' => 'AND',
					array(
						'key'		 => 'course_id',
						'value'		 => $course_id,
						'compare'	 => '=',
					)
				)
			);
			$query_quizze = new WP_Query( $query_quizze_args );
			$total_quizze = $query_quizze->post_count;
			$total_completed_quizze = 0;
			if ( !empty( $user_quizze ) ) {
				$quizze_lesson = array();
				foreach ( $user_quizze as $quizze ) {
					if ( $course_id == $quizze[ 'course' ]   ) {
						$quizze_percentage = $quizze[ 'percentage' ];
						if ( $quizze[ 'percentage' ] == 100 && !in_array($quizze['lesson'], $quizze_lesson )) {
							++$total_completed_quizze;
							$quizze_lesson[] = $quizze['lesson'];
						}
					}
				}
			}
			wp_reset_postdata();

			$course_arr = array(
				'total_steps'				=> learndash_get_course_steps_count( $course_id ),
				'completed_steps'			=> isset( $user_meta[ $course_id ][ 'completed' ] ) ? $user_meta[ $course_id ][ 'completed' ] : '0',
				'percentage'				=> $percentage,
				'course_completed_on'		=> $cours_completed_date,
				'total_course_assignment'	=> $total_assignment_counts,
				'total_assignment'			=> $number_of_assignment_counts,
				'total_approve_assignment'	=> $number_of_approve_assignment_counts,
				'assignment_percentage'		=> $assignment_percentage,
				'quizze_percentage'			=> ( $total_quizze != 0 && $total_completed_quizze != 0) ? ($total_completed_quizze/ $total_quizze ) * 100 : 0,
				'total_quizze'			 	=> $total_quizze,
				'total_completed_quizze'	=> $total_completed_quizze
			);
			return $course_arr;
		} else {
			$course_arr = array(
				'total_steps'				 => learndash_get_course_steps_count( $course_id ),
				'completed_steps'			 => learndash_course_get_completed_steps( $user_id, $course_id ),
				'percentage'				 => $percentage,
				'course_completed_on'		 => '',
				'total_course_assignment'	 => 0,
				'total_assignment'			 => 0,
				'total_approve_assignment'	 => 0,
				'assignment_percentage'		 => 0,
				'quizze_percentage'			 => 0
			);
			return $course_arr;
		}
	}

	/**
	 * Check course progress data is set for single course
	 *
	 * @param type $course_progress_data
	 * @param type $course_id
	 * @return int
	 */
	function ld_dashboard_check_isset( $course_progress_data, $course_id = null ) {
		if ( isset( $course_progress_data ) ) {
			return $course_progress_data;
		} elseif ( $course_id != '' ) {
			$total_steps	 = 0;
			$total_quizs	 = learndash_get_global_quiz_list( $course_id );
			$total_lessons	 = learndash_get_lesson_list( $course_id );
			if ( !empty( $total_quizs ) ) {
				$total_steps = 1;
			}
			if ( !empty( $total_lessons ) ) {
				$total_steps += count( $total_lessons );
			}

			return $total_steps;
		}
		return 0;
	}

	/**
	 * Get all users ids for course
	 *
	 * @param type $course_id
	 * @return array
	 */
	public function ld_dashboard_get_user_info( $user_id, $course_id ) {
		$ld_dashboard_course_users					 = array();
		$user_meta									 = get_userdata( $user_id );
		$ld_dashboard_course_users[ 'userid' ]		 = $user_id;
		$ld_dashboard_course_users[ 'user_name' ]	 = $user_meta->data->display_name;
		$ld_dashboard_course_users[ 'username' ]	 = $user_meta->data->user_login;
		$ld_dashboard_course_users[ 'user_email' ]	 = $user_meta->data->user_email;

		$course_progress = $this->ld_dashboard_check_course_progress_data( $user_id, $course_id );

		$ld_dashboard_course_users[ 'completed_per' ]		 = $this->ld_dashboard_check_isset( $course_progress[ 'percentage' ] );
		$ld_dashboard_course_users[ 'total_steps' ]			 = $this->ld_dashboard_check_isset( $course_progress[ 'total_steps' ], $course_id );
		$ld_dashboard_course_users[ 'completed_steps' ]		 = $this->ld_dashboard_check_isset( $course_progress[ 'completed_steps' ] );
		$ld_dashboard_course_users[ 'course_completed_on' ]	 = ( isset( $course_progress[ 'course_completed_on' ] ) ? $course_progress[ 'course_completed_on' ] : '-' );
		return $ld_dashboard_course_users;
	}

	/**
	 * Single user chart data for single course
	 *
	 * @param type $user_id
	 * @param type $course_id
	 * @return array
	 */
	public function ld_dashboard_get_student_info_chart( $user_id, $course_id ) {
		$ld_dashboard_course_users							 = array();
		$user_meta											 = get_userdata( $user_id );
		$ld_dashboard_course_users[ 'user_id' ]				 = $user_id;
		$ld_dashboard_course_users[ 'name' ]				 = $user_meta->data->user_login;
		$ld_dashboard_course_users[ 'email' ]				 = $user_meta->data->user_email;
		$ld_dashboard_course_users[ 'course_id' ]			 = $course_id;
		$course_progress									 = $this->ld_dashboard_check_course_progress_data( $user_id, $course_id );
		$ld_dashboard_course_users[ 'total_steps' ]			 = $this->ld_dashboard_check_isset( $course_progress[ 'total_steps' ], $course_id );
		$ld_dashboard_course_users[ 'completed_steps' ]		 = $this->ld_dashboard_check_isset( $course_progress[ 'completed_steps' ] );
		$ld_dashboard_course_users[ 'course_completed_on' ]	 = ( isset( $course_progress[ 'course_completed_on' ] ) ? $course_progress[ 'course_completed_on' ] : '-' );
		return $ld_dashboard_course_users;
	}

	/**
	 * Percentage Calculate
	 *
	 * @param type $completed
	 * @param type $total
	 * @return int
	 */
	function ld_dashboard_calculate_percentage_completion( $completed, $total ) {
		if ( empty( $completed ) ) {
			return 0;
		}

		$percentage	 = intVal( $completed * 100 / $total );
		$percentage	 = ( $percentage > 100 ) ? 100 : $percentage;
		return $percentage;
	}

	/**
	 * Return selected course data
	 *
	 * @param type $data
	 * @return array
	 */
	function ld_dashboard_rearrange_course_progress_data( $data ) {
		$course_progress_data = array();

		if ( !empty( $data ) ) {
			foreach ( $data as $d ) {
				$course_id	 = $d[ 'course_id' ];
				$user_id	 = $d[ 'user_id' ];

				if ( empty( $course_progress_data[ $course_id ] ) ) {
					$course_progress_data[ $course_id ] = array(
						'course_title'	 => get_the_title( $course_id ),
						'users'			 => array(),
						'not_started'	 => 0,
						'progress'		 => 0,
						'completed'		 => 0,
					);
				}

				if ( empty( $course_progress_data[ $course_id ][ 'users' ][ $user_id ] ) ) {
					$d[ 'percentage' ] = $this->ld_dashboard_calculate_percentage_completion( $d[ 'completed_steps' ], $d[ 'total_steps' ] );
					if ( empty( $d[ 'percentage' ] ) ) {
						$course_progress_data[ $course_id ][ 'not_started' ] ++;
					} elseif ( $d[ 'percentage' ] > 0 && $d[ 'percentage' ] < 100 ) {
						$course_progress_data[ $course_id ][ 'progress' ] ++;
					} elseif ( $d[ 'percentage' ] >= 100 ) {
						$course_progress_data[ $course_id ][ 'completed' ] ++;
					} else {
						$course_progress_data[ $course_id ][ 'not_started' ] ++;
					}

					$course_progress_data[ $course_id ][ 'users' ][ $user_id ] = $d;
				}
			}
		}

		if ( !empty( $course_progress_data ) ) {
			foreach ( $course_progress_data as $key => $value ) {
				if ( $count = count( $course_progress_data[ $key ][ 'users' ] ) ) {
					$course_progress_data[ $key ][ 'not_started' ]	 = $course_progress_data[ $key ][ 'not_started' ] * 100 / $count;
					$course_progress_data[ $key ][ 'progress' ]		 = $course_progress_data[ $key ][ 'progress' ] * 100 / $count;
					$course_progress_data[ $key ][ 'completed' ]	 = $course_progress_data[ $key ][ 'completed' ] * 100 / $count;
				}
			}
		}

		return $course_progress_data;
	}

	/**
	 * Get all users ids for course
	 *
	 * @param type $course_id
	 * @return array
	 */
	public function ld_dashboard_course_selected( $course_id ) {
		$data				 = array();
		$course_access_users = get_users(
					array(
						'fields'	 => 'ID',
						'meta_key'	 => 'is_student',
						'meta_value' => true,
					)
				);
		if ( !empty( $course_access_users ) ) {
			foreach ( $course_access_users as $key => $id ) {
				$data[] = $this->ld_dashboard_get_student_info_chart( $id, $course_id );
			}
		}

		return $this->ld_dashboard_rearrange_course_progress_data( $data );
	}

	/*
	 * get the Course Details from ajax
	 */

	public function ld_dashboard_course_details() {
		check_ajax_referer( 'ld-dashboard', 'nonce' );

		$course_id			 = sanitize_text_field( $_POST[ 'course_id' ] );
		$user				 = wp_get_current_user();

		/* Get Group Leader user ID only */
		$student_ids = array();
		if ( learndash_is_group_leader_user() ) {
			$student_ids = learndash_get_group_leader_groups_users();
			$course_count = learndash_get_group_leader_groups_courses();
		}

		$course_access_users = get_users(
									array(
										'fields'	 => array( 'ID', 'display_name' ),
										//'meta_key'	 => 'is_student',
										//'meta_value' => true,
										'include'	=> $student_ids
									)
								);
		if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
			$course_access_users = $this->ld_dashboard_get_instructor_students_by_id( $user->ID );
		}

		$course_userInfo = array();
		$uids			 = array();
		$user_data	     = array();
		if ( !empty( $course_access_users ) ) {
			foreach ( $course_access_users as $uid ) {
				$course_ids = learndash_user_get_enrolled_courses( $uid->ID );
				if ( !empty($course_ids) && in_array( $course_id, $course_ids)) {
					$course_userInfo[]	 = $this->ld_dashboard_get_user_info( $uid->ID, $course_id );
					$uids[]				 = $uid->ID;
					$user_data[] 			 = $this->ld_dashboard_get_student_info_chart( $uid->ID, $course_id );
				}
			}
		}

		/* Pagination Date: 2020-06-11 */
		$page 			= ! empty( $_POST['page'] ) ? (int) $_POST['page'] : 1;
		$total 			= count( $course_userInfo ); //total items in array
		$limit 			= apply_filters('ld_dashboard_course_details_per_page', 10 );
		$total_pages 	= ceil( $total/ $limit ); //calculate total pages
		$page 			= max($page, 1); //get 1 page when $_GET['page'] <= 0
		$page 			= min($page, $total_pages); //get last page when $_GET['page'] > $total_pages
		$offset 		= ($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;

		$course_userInfo = array_slice( $course_userInfo, $offset, $limit );
		$user_data 		 = array_slice( $user_data, $offset, $limit );
		/* */

		if ( !empty( $course_userInfo ) ) {
			$student_report_html = '';
			$student_report_html .= '<tbody>';
			foreach ( $course_userInfo as $key => $data ) {
				$email				 = $data[ 'user_email' ];
				$user_name			 = isset( $data[ 'user_name' ] ) ? $data[ 'user_name' ] : '-';
				$username			 = isset( $data[ 'username' ] ) ? $data[ 'username' ] : '-';
				$user_email			 = isset( $data[ 'user_email' ] ) ? $data[ 'user_email' ] : '-';
				$completed_per		 = isset( $data[ 'completed_per' ] ) ? $data[ 'completed_per' ] : '-';
				$total_steps		 = isset( $data[ 'total_steps' ] ) ? $data[ 'total_steps' ] : '-';
				$completed_steps	 = isset( $data[ 'completed_steps' ] ) ? $data[ 'completed_steps' ] : '-';
				$course_completed_on = isset( $data[ 'course_completed_on' ] ) ? $data[ 'course_completed_on' ] : '-';
				$user_id			 = $data[ 'userid' ];

				$student_report_html .= '<tr>';
				$student_report_html .= '<td>' . $user_name . '</td>';
				$student_report_html .= '<td>' . $total_steps . '</td>';
				$student_report_html .= '<td>' . $completed_steps . '</td>';
				$student_report_html .= '<td>' . $completed_per . '%<div class="ld-dashboard-progress progress_bar_wrap" data-course="' . $course_id . '"><div class="ld-dashboard-progressbar ld-dashboard-animate ld-dashboard-stretch-right" data-percentage-value="' . $completed_per . '" style="background-color:#7266ba; width: ' . $completed_per . '%"></div></div></td>';
				$student_report_html .= '<td>' . $course_completed_on . '</td>';
				$student_report_html .= '</tr>';
			}
			$student_report_html .= '</tbody>';
		}
		$data = $this->ld_dashboard_rearrange_course_progress_data( $user_data );
		//$data			 = $this->ld_dashboard_course_selected( $course_userInfo );
		$progress_data	 = $data[ $course_id ];
		$not_started	 = isset( $progress_data[ 'not_started' ] ) ? $progress_data[ 'not_started' ] : 100;
		$progress		 = isset( $progress_data[ 'progress' ] ) ? $progress_data[ 'progress' ] : 0;
		$complete		 = isset( $progress_data[ 'completed' ] ) ? $progress_data[ 'completed' ] : 0;

		/* Start Page Container */
		$page_container = '';
		if( $total_pages != 0 ) {
			$link = 'index.php?page=%d';

			$loader = includes_url( 'images/spinner-2x.gif' );
			$page_container ='<div class="ld-dashboard-loader" style="display:none;">
		<img src="'. apply_filters( 'ld_dashboard_loader_img_url', $loader ).'">
		<p>'. apply_filters( 'ld_dashboard_waiting_text', __( 'Please wait, while details are loading...', 'ld-dashboard' ) ).'</p>
	</div>';

			$page_container .= '<div class="ld-course-details ld-dashboard-pagination">';
			if( $page == 1 ) {
				$page_container .= '';
			} else {
				$page_container .= sprintf( '<a class="ld-pagination" href="#"  data-page="%d" data-course="%d"> ' . esc_html__( '&#171; prev', 'ld-dashboard' ) . '</a>', $page - 1 , $course_id);
			}
			$page_container .= ' <span>' . esc_html__('page', 'ld-dashboard' ) .' <strong>' . $page . '</strong> ' . esc_html__('from', 'ld-dashboard') . ' ' . $total_pages . '</span> ';
			if( $page == $total_pages ) {
				$page_container .= '';
			} else {
				$page_container .= sprintf( '<a class="ld-pagination" href="#"  data-page="%d" data-course="%d">' . esc_html__( 'next &#187;', 'ld-dashboard' ) . '</a>', $page + 1, $course_id );
			}
			$page_container .= '</div>';
		}
		/* */
		$ld_dashboard_page_mapping    = get_option( 'ld_dashboard_page_mapping' );
		$my_dashboard_page = get_permalink($ld_dashboard_page_mapping['my_dashboard_page']);
		$html		 = '';
		$html		 .= '
			<div class="ld-dashboard-course-chart">
				<div id="ld-dashboard-chart-data">
					<input id="ld-dashboard-not-started" value="' . $not_started . '" type="hidden">
					<input id="ld-dashboard-progress" value="' . $progress . '" type="hidden">
					<input id="ld-dashboard-complete" value="' . $complete . '" type="hidden">
				</div>
				<div id="ld-dashboard-instructor-highchart-student-progress" style="width: 100%; height: 400px;"></div>
			</div>
			<div class="ld-dashboard-overview-course-students">
				<h3>' . __( 'Student Information', 'ld-dashboard' ) . '</h3>
				<table id="ld-dashboard-overview-course-students">
					<thead>
						<tr>
							<th>' . __( 'Name', 'ld-dashboard' ) . '</th>
							<th>' . __( 'Total Steps', 'ld-dashboard' ) . '</th>
							<th>' . __( 'Completed Steps', 'ld-dashboard' ) . '</th>
							<th>' . __( 'Progress %', 'ld-dashboard' ) . '</th>
							<th>' . __( 'Completed On', 'ld-dashboard' ) . '</th>
						</tr>
					</thead>
					' . $student_report_html . '
				</table>
				' . $page_container . '
				<span class="ld-dashboard-export"><a class="ld-dashboard-export-course ld-dashboard-btn" href="' . $my_dashboard_page . '?ld-export=course-progress&course-id='. $course_id.'&export-format=csv" target="Blank">' . __( 'Export CSV', 'ld-dashboard') . '</a></span>
			</div>
		';
		$check_instrucor = false;
		$course = get_post( $course_id );
		if ( $course && isset( $course->post_author ) ) {
			$course_author = $course->post_author;
			$check_instrucor = ld_check_if_author_is_instructor( $course_author );
			if( $check_instrucor ) {
				$instructor_total_earning = (int)get_user_meta( $course_author, 'instructor_total_earning', true );
				$instructor_paid_earning = (int)get_user_meta( $course_author, 'instructor_paid_earning', true );
				$instructor_unpaid_earning = $instructor_total_earning - $instructor_paid_earning;

			}
		}
		$_instructor_chart_display = false;
		if( $check_instrucor ) {
			if( $instructor_total_earning ) {
				$_instructor_chart_display = true;
			}
		}



		$response	 = array(
			'html' => $html,
			'instructor_chart_display' => $_instructor_chart_display,
			'instructor_total_earning' => $instructor_total_earning,
			'instructor_paid_earning' => $instructor_paid_earning,
			'instructor_unpaid_earning' => $instructor_unpaid_earning
		);
		wp_send_json_success( $response );
		wp_die();
	}

	/*
	 * get the student wise details report
	 */

	public function ld_dashboard_student_details() {
		global $current_user, $wpdb;;
		$user_id = get_current_user_id();
		check_ajax_referer( 'ld-dashboard', 'nonce' );

		$course_ids = array();
		$student_id = sanitize_text_field( $_POST[ 'student_id' ] );
		if ( learndash_is_group_leader_user() ) {
			$course_ids = learndash_get_group_leader_groups_courses();
		} elseif( in_array( 'ld_instructor', (array) $current_user->roles ) ){
			$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

			$cousres = $wpdb->get_results( $get_courses_sql );
			$course_ids = array(0);
			if ( !empty($cousres) ) {
				$course_ids = array();
				foreach ($cousres as $course ) {
					$course_ids[] = $course->ID;
				}
			}
			/*
			$args = array(
						'post_type'		 => 'sfwd-courses',
						//'author'		 => $user_id,
						'post_status'	 => 'publish',
						'posts_per_page' => -1,
						'post__in' 		 => $course_ids,
					);
			$courses = get_posts( $args );
			if ( !empty($courses) ) {
				foreach ( $courses as $index => $course ) {
					$course_ids[] = $course->ID;
				}
			}*/

			$student_course_ids = learndash_user_get_enrolled_courses( $student_id );
			$course_ids = array_intersect($course_ids, $student_course_ids);

		}else {
			$course_ids = learndash_user_get_enrolled_courses( $student_id );
		}
		/* */
		$page 		 = ! empty( $_POST['page'] ) ? (int) $_POST['page'] : 1;
		$total 		 = count( $course_ids ); //total items in array
		$limit 		 = apply_filters('ld_dashboard_student_course_details_per_page', 10 );
		$total_pages = ceil( $total/ $limit ); //calculate total pages
		$page 		 = max($page, 1); //get 1 page when $_POST['page'] <= 0
		$page 		 = min($page, $total_pages); //get last page when $_POST['page'] > $total_pages
		$offset	 	 = ($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;

		$course_ids = array_slice( $course_ids, $offset, $limit );

		/* */

		$student_courses				 = $course_ids;
		$total_courses					 = count( $student_courses );
		$completed_course				 = 0;
		$in_progress_course				 = 0;
		$not_started_course				 = 0;
		$completed_assignment			 = 0;
		$total_assignment				 = 0;
		$approved_assignment			 = 0;
		$unapproved_assignment			 = 0;
		$pending_assignment_percentage	 = 0;
		$completed_quizze				 = 0;
		$total_quizze				 	 = 0;
		$ld_dashboard_page_mapping	= get_option( 'ld_dashboard_page_mapping' );
		$my_dashboard_page 			= get_permalink($ld_dashboard_page_mapping['my_dashboard_page']);
		if ( !empty( $student_courses ) ) :
			$student_courses_html = '
			<ul class="ld-dashboard-student-courses">';
			foreach ( $student_courses as $course_id ) :

				$course_progress_data	 = $this->ld_dashboard_check_course_progress_data( $student_id, $course_id );
				$course_progress		 = ( isset( $course_progress_data[ 'percentage' ] ) ) ? $course_progress_data[ 'percentage' ] : 0;
				$total_steps			 = ( isset( $course_progress_data[ 'total_steps' ] ) ) ? $course_progress_data[ 'total_steps' ] : 0;
				$completed_steps		 = ( isset( $course_progress_data[ 'completed_steps' ] ) ) ? $course_progress_data[ 'completed_steps' ] : 0;


				/* Course Progress */
				if ( $course_progress_data[ 'completed_steps' ] == 0 ) {
					++$not_started_course;
				} elseif ( $course_progress_data[ 'total_steps' ] == $course_progress_data[ 'completed_steps' ] ) {
					++$completed_course;
				} else {
					++$in_progress_course;
				}

				/* Course Assignments */
				$total_assignment		 += $course_progress_data[ 'total_course_assignment' ];
				$approved_assignment	 += $course_progress_data[ 'total_approve_assignment' ];
				$unapproved_assignment	 += $course_progress_data[ 'total_assignment' ] - $course_progress_data[ 'total_approve_assignment' ];

				/* Quize Progress */
				$total_quizze		 	 += $course_progress_data[ 'total_quizze' ];
				$completed_quizze		 += $course_progress_data[ 'total_completed_quizze' ];

				$student_courses_html .= '<li>
					<strong>
						<a href="' . get_the_permalink( $course_id ) . '">' . get_the_title( $course_id ) . '</a>&nbsp;
						<span class="ld-dashboard-progress-percentage">' . sprintf( __( '
                %1$s%% Complete', 'ld-dashboard' ), $course_progress ) . '</span>
					<span class="ld-dashboard-progress-steps">' . sprintf( __( '
                %1$s/%2$s Steps', 'ld-dashboard' ), $completed_steps, $total_steps ) . '</span>
					</strong>
					<div class="ld-dashboard-progress progress_bar_wrap" data-course="' . $course_id . '">
						<div class="ld-dashboard-progressbar ld-dashboard-animate ld-dashboard-stretch-right" data-percentage-value="' . esc_attr( $course_progress ) . '" style="background-color:#7266ba; width:0;"></div>
					</div>
				</li>';

			endforeach;
		endif;

		$completed_course_percentage	 =  ($completed_course !=0 && $total_courses != 0 ) ? ( $completed_course / $total_courses ) * 100 : 0 ;
		$in_progress_course_percentage	 = ($in_progress_course !=0 && $total_courses != 0 ) ? ( $in_progress_course / $total_courses ) * 100 : 0;
		$not_started_course_percentage	 = ($not_started_course !=0 && $total_courses != 0 ) ? ( $not_started_course / $total_courses ) * 100 : 0;
		//echo $not_started_course . " == " . $total_courses;
		if ( $total_assignment != 0 ) {
			$approved_assignment_percentage		 = ( $approved_assignment / $total_assignment ) * 100;
			$unapproved_assignment_percentage	 = ( $unapproved_assignment / $total_assignment ) * 100;
			$pending_assignment_percentage		 = ( ( $total_assignment - $approved_assignment - $unapproved_assignment ) / $total_assignment ) * 100;
		} else {
			$approved_assignment_percentage		 = 0;
			$unapproved_assignment_percentage	 = 0;
			$pending_assignment_percentage		 = 100;
		}

		if ( $total_quizze != 0 ) {
			$completed_quizze_percentage = ( $completed_quizze / $total_quizze ) * 100;
			$uncompleted_quizze_percentage = ( ( $total_quizze - $completed_quizze )/ $total_quizze ) * 100;
		} else {
			$completed_quizze_percentage = 0;
			$uncompleted_quizze_percentage = 0;
		}
		/* Pagination */
		/* Start Page Container */
		$page_container = '';
		if( $total_pages != 0 ) {
			$link = 'index.php?page=%d';
			$loader = includes_url( 'images/spinner-2x.gif' );
			$page_container = '<div class="ld-dashboard-student-loader" style="display:none;">
				<img src="' . apply_filters( 'ld_dashboard_loader_img_url', $loader ) . '">
				<p>' . apply_filters( 'ld_dashboard_waiting_text', __( 'Please wait, while details are loading...', 'ld-dashboard' ) ) . '</p>
			</div>';
			$page_container .= '<div class="ld-student-course-details ld-dashboard-pagination">';
			if( $page == 1 ) {
				$page_container .= '';
			} else {
				$page_container .= sprintf( '<a class="ld-pagination" href="#"  data-page="%d" data-student="%d"> ' . esc_html__( '&#171; prev', 'ld-dashboard' ) . '</a>', $page - 1 , $student_id);
			}
			$page_container .= ' <span>' . esc_html__('page', 'ld-dashboard' ) .' <strong>' . $page . '</strong> ' . esc_html__('from', 'ld-dashboard') . ' ' . $total_pages . '</span> ';
			if( $page == $total_pages ) {
				$page_container .= '';
			} else {
				$page_container .= sprintf( '<a class="ld-pagination" href="#"  data-page="%d" data-student="%d">' . esc_html__( 'next &#187;', 'ld-dashboard' ) . '</a>', $page + 1, $student_id );
			}
			$page_container .= '</div>';
		}
		/* */

		$html = '<div>
					<div id="ld-dashboard-chart-data">
						<input id="ld-dashboard-student-course-not-started" value="' . $not_started_course_percentage . '" type="hidden">
						<input id="ld-dashboard-student-course-progress" value="' . $in_progress_course_percentage . '" type="hidden">
						<input id="ld-dashboard-student-course-complete" value="' . $completed_course_percentage . '" type="hidden">

						<input id="ld-dashboard-student-approved-assignment" value="' . $approved_assignment_percentage . '" type="hidden">
						<input id="ld-dashboard-student-unapproved-assignment" value="' . $unapproved_assignment_percentage . '" type="hidden">
						<input id="ld-dashboard-student-pending-assignment" value="' . $pending_assignment_percentage . '" type="hidden">

						<input id="ld-dashboard-student-completed-quizze" value="' . $completed_quizze_percentage . '" type="hidden">
						<input id="ld-dashboard-student-uncompleted-quizze" value="' . $uncompleted_quizze_percentage . '" type="hidden">
					</div>
					<div class="ld-dashboard-student-course-wrapper">
						<div id="ld-dashboard-student-course-progress-highchart" style="width: 100%; height: 300px; margin-bottom: 20px;"></div>
						<div id="ld-dashboard-student-course-assignment-progress-highchart" style="width: 100%; height: 300px; margin-bottom: 20px;"></div>
						<div id="ld-dashboard-student-course-quizze-progress-highchart" style="width: 100%; height: 300px; margin-bottom: 20px;"></div>
					</div>
					' . $student_courses_html . '
					' . $page_container . '
					<span class="ld-dashboard-export"><a class="ld-dashboard-export-users ld-dashboard-btn" href="' . $my_dashboard_page . '?ld-export=student-progress&student-id='. $student_id.'&export-format=csv" target="Blank">' . __( 'Export CSV', 'ld-dashboard') . '</a></span>
			</div>';
		$response	 = array(
			'html' => $html,
		);
		wp_send_json_success( $response );
		wp_die();
	}

	/*
	 * Update author earning on course access.
	 */
	public function ld_dashboard_update_author_earning( $user_id, $course_id, $access_list, $remove ) {

		if( $remove ){
			return;
		}

		$course_pricing = learndash_get_course_price( $course_id );
		if ( $course_pricing['type'] == 'paynow' || $course_pricing['type'] == 'closed' || $course_pricing['type'] == 'subscribe' ) {

			$course = get_post( $course_id );
			if ( $course && isset( $course->post_author ) ) {
				$course_author = $course->post_author;
				$check_instrucor = ld_check_if_author_is_instructor( $course_author );
				$commission_enabled = ld_if_commission_enabled();

				$_commission = 0;
				if ( $check_instrucor ) {
					if( $commission_enabled ) {
						$_commission = ld_if_instructor_course_commission_set( $course_author );
						if( !$_commission ){
							$_commission = ld_get_admin_course_commission( $course_id );
						}
						if( !$_commission ){
							$_commission = ld_get_global_commission_rate( $course_id );
						}
					}
				}
				if( $_commission ) {

					$course_price = (int)$course_pricing['price'];
					//cep - course earning percentage
					$instructor_cep = 100 - $_commission;

					//ce - instructor course earning
					$instructor_ce = (int)($course_price*$instructor_cep)/100;

					$instructor_total_earning = (int)get_user_meta( $course_author, 'instructor_total_earning', true );
					if( $instructor_total_earning ) {

						$total_earning = (int)$instructor_total_earning + $instructor_ce;
						update_user_meta( $course_author, 'instructor_total_earning', $total_earning );
					}else{
						update_user_meta( $course_author, 'instructor_total_earning', $instructor_ce );
					}
				}
			}
		}
	}

	/*
	 * Update instructor course order on stripe payment.
	 */
	public function ld_dashboard_update_instructor_course_purchase( $post_ID, $post, $update ) {
		if( $update ){
			return;
		}
		if( $post && isset( $post->post_type ) && $post->post_type == 'sfwd-transactions' ){
			ld_dashboard_update_on_stripe_payment( $post_ID, $post );
		}
	}

	public function ld_get_course_data() {
		$course_purchase_data = get_user_meta( '32', 'course_purchase_data', true );
		//echo '<pre>'; print_r( $course_purchase_data ); echo '</pre>';
	}

	public function ld_dashboard_updated_sfwd_transaction_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if( $meta_key == 'stripe_course_id' ) {
			$course_id = $meta_value;
			$course_pricing = learndash_get_course_price( $course_id );
			if ( $course_pricing['type'] == 'paynow' || $course_pricing['type'] == 'closed' ||  $course_pricing['type'] == 'subscribe') {
				$course = get_post( $course_id );
				if ( $course && isset( $course->post_author ) ) {
					$course_author = $course->post_author;
					$check_instrucor = ld_check_if_author_is_instructor( $course_author );
					$commission_enabled = ld_if_commission_enabled();

					$_commission = 0;
					if ( $check_instrucor ) {
						if( $commission_enabled ) {
							$_commission = ld_if_instructor_course_commission_set( $course_author );
							if( !$_commission ){
								$_commission = ld_get_admin_course_commission( $course_id );
							}
							if( !$_commission ){
								$_commission = ld_get_global_commission_rate( $course_id );
							}
						}
						$course_purchase_data = get_user_meta( $course_author , 'course_purchase_data', true );
						if( !is_array( $course_purchase_data ) ) {
							$course_purchase_data = array();
						}
						$course_purchase_data[$object_id] = array(
							'course' => $course_id,
							'commission' => $_commission,
							'payment_type' => 'Stripe',
							'order_id' => $object_id
						);
						update_user_meta( $course_author, 'course_purchase_data', $course_purchase_data );
					}
				}
			}
		}
	}

	public function ld_dashboard_wc_update_sfwd_transaction_meta( $order_id, $posted_data, $order ) {
		// echo '<pre>'; print_r( $order_id ); echo '</pre>';
		// echo '<pre>'; print_r( $posted_data ); echo '</pre>';
		// echo '<pre>'; print_r( $order ); echo '</pre>';die();
	}

	public function ld_update_instructor_meta_wc_course_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order !== false ) {
			$products = $order->get_items();

			foreach ( $products as $product ){
				if ( isset( $product['variation_id'] ) && ! empty( $product['variation_id'] ) ) {
					$courses_id = get_post_meta( $product['variation_id'], '_related_course', true );
				} else {
					$courses_id = get_post_meta( $product['product_id'], '_related_course', true );
				}

				if ( $courses_id && is_array( $courses_id ) ) {
					foreach ( $courses_id as $cid ) {
						$course = get_post( $cid );
						//print_r( $course );
						if ( $course && isset( $course->post_author ) ) {
							$course_author = $course->post_author;
							$check_instrucor = ld_check_if_author_is_instructor( $course_author );
							$commission_enabled = ld_if_commission_enabled();

							$_commission = 0;
							if ( $check_instrucor ) {
								if( $commission_enabled ) {
									$_commission = ld_if_instructor_course_commission_set( $course_author );
									if( !$_commission ){
										$_commission = ld_get_admin_course_commission( $cid );
									}
									if( !$_commission ){
										$_commission = ld_get_global_commission_rate( $cid );
									}
								}
								$course_purchase_data = get_user_meta( $course_author , 'course_purchase_data', true );
								if( !is_array( $course_purchase_data ) ) {
									$course_purchase_data = array();
								}
								if( !array_key_exists( $order_id, $course_purchase_data ) ) {
									$course_purchase_data[$order_id] = array(
										'course' => $cid,
										'commission' => $_commission,
										'payment_type' => 'WC',
										'order_id' => $order_id
									);
								}
								update_user_meta( $course_author, 'course_purchase_data', $course_purchase_data );
							}
						}
					}
				}
			}
		}
	}

	public function ld_remove_course_order_from_instructor_meta( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order !== false ) {
			$products = $order->get_items();

			foreach ( $products as $product ) {
				$courses_id = get_post_meta( $product['product_id'], '_related_course', true );
				if ( $courses_id && is_array( $courses_id ) ) {
					foreach ( $courses_id as $cid ) {
						$course = get_post( $cid );
						if ( $course && isset( $course->post_author ) ) {
							$course_author = $course->post_author;
							$check_instrucor = ld_check_if_author_is_instructor( $course_author );
							if ( $check_instrucor ) {
								$course_purchase_data = get_user_meta( $course_author , 'course_purchase_data', true );
								if( is_array( $course_purchase_data ) ) {
									if( array_key_exists( $order_id, $course_purchase_data) ){
										unset( $course_purchase_data[$order_id] );
									}
								}
								update_user_meta( $course_author, 'course_purchase_data', $course_purchase_data );
							}
						}
					}
				}
			}
		}
	}

	/*
	 * Post IDS pass empty array when student loggedin
	 *
	 */
	public function ld_dashboard_get_activity_query_args($query_args) {
		if ( isset($query_args['is_post_ids']) && $query_args['is_post_ids'] == true && isset($query_args['post_ids']) && !empty($query_args['post_ids'])) {
			$query_args['post_ids'] = array();
		}
		return $query_args;
	}

	public function reign_ld_show_course_to_do_list( $total_courses ) {
		global $bptodo;
		$user_id = get_current_user_id();
		$profile_menu_slug         = $bptodo->profile_menu_slug;
		$profile_menu_label        = $bptodo->profile_menu_label;
		$profile_menu_label_plural = $bptodo->profile_menu_label_plural;
		foreach ( $total_courses as $key => $course_id ) {
			$reign_bp_group_field = get_post_meta( $course_id, 'reign_bp_group_field', true );
			if( !$reign_bp_group_field ) {
				unset( $total_courses[$key] );
			}
		}
		?>
		<div class="ld-dashboard-seperator"><span><?php _e( 'To Do', 'ld-dashboard' ); ?></span></div>

		<?php

		if( $total_courses ){

			$first_element = reset($total_courses);
			$first_course_id = $first_element;
			$group_id = get_post_meta( $first_course_id, 'reign_bp_group_field', true );
			$can_modify = false;
			if( groups_is_user_admin( $user_id, $group_id ) ) {
				$can_modify = true;
			}
			if( groups_is_user_mod( $user_id, $group_id ) ) {
				$mod_can_modify = bptodo_if_moderator_modification_enabled( $group_id, $current_user );
				if( $mod_can_modify ) {
					$can_modify = true;
				}
			}

			$todo_list = new_ld_bptodo_get_course_list( $first_course_id, $user_id, $group_id, $can_modify );

			?>
			<div class="ld-dashboard-course-progress">
				<select id="ldid-show-course-todo">
					<?php
					if ( ! empty( $total_courses ) ) :
						foreach ( $total_courses as $course_id ) :
							echo "<option value='".$course_id."'>".get_the_title( $course_id)."</option>";
						endforeach;
					endif;
					?>
				</select>
				<div class="render-course-group-to-do-list">
					<?php echo $todo_list; ?>
				</div>
			</div>
			<?php

			// echo '<div class="render-course-group-to-do-list">';
			// echo $todo_list;
			// echo '</div>';
		} else {
			echo '<div class="ld-dashboard-course-progress render-course-group-to-do-list">';
			echo sprintf( esc_html__( 'Sorry, no %1$s found.', 'wb-todo' ), esc_html( $profile_menu_label ) );
			echo '</div>';
		}
	}

	public function ld_generate_group_course_todo_list() {
		if ( isset( $_POST['action'] ) && 'ld_generate_group_course_todo_list' == $_POST['action'] ) {
			$course_id = $_POST['course_id'];

			$user_id = get_current_user_id();
			$group_id = get_post_meta( $course_id, 'reign_bp_group_field', true );
			$can_modify = false;
			if( groups_is_user_admin( $user_id, $group_id ) ) {
				$can_modify = true;
			}
			if( groups_is_user_mod( $user_id, $group_id ) ) {
				$mod_can_modify = bptodo_if_moderator_modification_enabled( $group_id, $current_user );
				if( $mod_can_modify ) {
					$can_modify = true;
				}
			}

			$todo_list = new_ld_bptodo_get_course_list( $course_id, $user_id, $group_id, $can_modify );

			echo $todo_list;die;
		}
	}

	/*
	 * Display LD Email Integation section on Dashboard
	 */
	public function ld_dashboard_email_functions() {
		global $current_user;

		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$ld_dashboard               = $ld_dashboard_settings_data['general_settings'];
		$ld_dashboard_integration   = $ld_dashboard_settings_data['ld_dashboard_integration'];

		$is_student = 0;
		if ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $current_user->roles ) ) {
			$is_student = 1;
		}
		ob_start();
		if ( $is_student != 1 &&  isset($ld_dashboard_integration['enable-email-integration']) && $ld_dashboard_integration['enable-email-integration'] == 1   ) {
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-email-integration.php';
		}

		return ob_get_clean();
	}

	public function ld_dashboard_message_functions() {
		global $current_user;

		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$ld_dashboard               = $ld_dashboard_settings_data['general_settings'];
		$ld_dashboard_integration   = $ld_dashboard_settings_data['ld_dashboard_integration'];
		$is_student = 0;
		if ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $current_user->roles ) ) {
			$is_student = 1;
		}
		ob_start();
		if ( $is_student != 1 && isset($ld_dashboard_integration['enable-messaging-integration']) && $ld_dashboard_integration['enable-messaging-integration'] == 1 && is_plugin_active( 'buddypress/bp-loader.php' )   ) {
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-message-integration.php';
		}

		return ob_get_clean();
	}

	/*
	 * return course wise studnets lists
	 *
	 */
	public function ld_dashboard_couse_students() {
		check_ajax_referer( 'ld-dashboard', 'nonce' );

		$course_id	=  $_POST[ 'course_id' ];
		$user		= wp_get_current_user();

		/* Get Group Leader user ID only */
		$student_ids = array();
		if ( learndash_is_group_leader_user() ) {
			$student_ids = learndash_get_group_leader_groups_users();
		}
		$course_access_users = get_users(
									array(
										'fields'	 => array( 'ID', 'display_name' ),
										'include'	=> $student_ids
									)
								);
		if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
			$course_access_users = $this->ld_dashboard_get_instructor_students_by_id( $user->ID );
		}
		$course_userInfo = array();
		$uids			 = array();
		$user_data	     = array();
		if ( !empty( $course_access_users ) ) {
			foreach ( $course_access_users as $uid ) {
				$course_ids = learndash_user_get_enrolled_courses( $uid->ID );
				$match_courseids = array_intersect($course_id, $course_ids);
				if ( !empty($match_courseids)) {
					$course_userInfo[] = array( 'user_id' 	=> $uid->ID,
												'user_name'	=> $uid->display_name
											) ;
				}
			}
		}

		wp_send_json_success( $course_userInfo );
		wp_die();
	}

	/*
	 * Email Send base on selected course wise students & also selcted students
	 */
	public function ld_dashboard_email_send() {
		check_ajax_referer( 'ld-dashboard', 'nonce' );
		global $wpdb, $current_user;
		$user_id	   	= get_current_user_id();

		/* Email Subject blank then return error message  */
		if ( isset($_POST['ld-email-subject']) && $_POST['ld-email-subject'] == '') {
			$results = array( 'error' 	=> 1,
							  'message'	=> esc_html__( 'Please add email subject', 'ld-dashboard'),
						);
			wp_send_json_success( $results );
			wp_die();
		}

		/* Email Body blank then return error message  */
		if ( isset($_POST['ld-email-message']) && $_POST['ld-email-message'] == '') {
			$results = array( 'error' 	=> 1,
							  'message'	=> esc_html__( 'Please add email message', 'ld-dashboard'),
						);
			wp_send_json_success( $results );
			wp_die();
		}


		$email_subject 	= $_POST['ld-email-subject'];
		$email_message 	= $_POST['ld-email-message'];

		if ( !isset($_POST['ld-email-students']) ) {
			/*
			 * Get Loggedin users lists
			 */
			if ( learndash_is_group_leader_user() ) {
				$group_student = learndash_get_group_leader_groups_users();

				$args = array(
							'orderby'   => 'user_nicename',
							'order'     => 'ASC',
							'fields'    => array( 'ID', 'display_name' ),
							'include'	=> $group_student
						);

			} else if ( learndash_is_admin_user()   ) {
				$args = array(
							'orderby'    => 'user_nicename',
							'order'      => 'ASC',
							'fields'     => array( 'ID', 'display_name' ),
						);
			} else {
				$instructor_students = $this->ld_dashboard_get_instructor_students_by_id( $user_id );
				$course_student_ids = array();
				if ( ! empty( $instructor_students ) ) {
					foreach ( $instructor_students as $key => $course_student ) {
						$course_student_ids[] = $course_student->ID;
					}
				}
				$args = array(
							'orderby'    => 'user_nicename',
							'order'      => 'ASC',
							'fields'     => array( 'ID', 'display_name' ),
							'include'	=> $course_student_ids
						);
			}
			$students = get_users( $args );
		}


		/* Course and students both selected then get the selected Students  */
		if ( isset($_POST['ld-email-cource']) && isset($_POST['ld-email-students']) ) {

			$course_ids 	= $_POST['ld-email-cource'];
			$student_ids	= $_POST['ld-email-students'];

		}elseif ( isset($_POST['ld-email-cource']) && !isset($_POST['ld-email-students']) ) {
			/* Only Course selected all get the selected course enrolled students */

			$course_ids	 = $_POST['ld-email-cource'];
			$student_ids = array();
			foreach ( $students as $student ) {
				$courseids = learndash_user_get_enrolled_courses( $student->ID );
				$match_courseids = array_intersect($course_ids, $courseids);
				if ( !empty($match_courseids)) {
					$student_ids[] = $student->ID;
				}
			}

		} else {
			/* Course and Student not selected then get the loggedin users students and send message to all students */

			/*
			 * Get Loggedin User Course Lists
			 */
			if ( learndash_is_admin_user() ) {
				$args = array(
							'post_type'		 => 'sfwd-courses',
							'post_status'	 => 'publish',
							'posts_per_page' => -1,
						);
			} elseif ( learndash_is_group_leader_user() ) {
				$group_course = learndash_get_group_leader_groups_courses();

				$args = array(
							'post_type'		 => 'sfwd-courses',
							'post_status'	 => 'publish',
							'posts_per_page' => -1,
							'post__in'		 => $group_course
						);
			} else {
				$args = array(
							'post_type'		 => 'sfwd-courses',
							'author'		 => $user_id,
							'post_status'	 => 'publish',
							'posts_per_page' => -1,
						);
			}

			$courses = get_posts( $args );
			$course_ids = array();
			foreach ( $courses as $course ) {
				$course_ids[] = $course->ID;
			}


			$student_ids = array();
			foreach ( $students as $student ) {
				$courseids = learndash_user_get_enrolled_courses( $student->ID );
				$match_courseids = array_intersect($course_ids, $courseids);
				if ( !empty($match_courseids)) {
					$student_ids[] = $student->ID;
				}
			}
		}

		/* Insert Email logs */
		$wpdb->insert(
					$wpdb->prefix . 'ld_dashboard_emails',
					array(
						'user_id'  		=> $user_id,
						'email_subject' => $email_subject,
						'email_message' => $email_message,
						'course_ids'	=> wp_json_encode( $course_ids ),
						'student_ids'	=> wp_json_encode( $student_ids ),
						'created' 		=> date('Y-m-d H:i:s'),
					),
					array( '%d', '%s', '%s', '%s', '%s', '%s' )
				);

		$from_name 	= apply_filters( 'ld_dashboard_email_from_name', get_option( 'blogname' ) );
		$from_mail	= apply_filters( 'ld_dashboard_email_from_email', get_option( 'admin_email' ) );
		$headers[]	= 'MIME-Version: 1.0' . "\r\n";
		$headers[] 	= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers[] 	= "X-Mailer: PHP \r\n";
		$headers[] 	= 'From: '.$from_name.' < '.$from_mail.'>' . "\r\n";
		$current_time = time();
		foreach( $student_ids as $student ) {
			$student_info 	= get_userdata($student);
			$to_email 		= $student_info->user_email;
			$student_name	= $student_info->user_firstname . ' ' . $student_info->user_lastname;
			$student_name	= ( $student_info->user_firstname == '' && $student_info->user_lastname == '' ) ? $student_info->user_login : $student_name;
			$search_string	= array('{student-name}');
			$replace_string	= array( $student_name );

			$emailmessage 	= str_replace( $search_string, $replace_string, wpautop($email_message) );

			$current_time += 10;
			wp_schedule_single_event( $current_time, 'ld_dashboard_send_email', array( $to_email, $email_subject, $emailmessage, $headers ) );
		}

		wp_send_json_success( array('email_sent' => esc_html__('Email Sent Successfully.', 'ld-dashboard') ) );
		wp_die();
	}

	/*
	 * Send email on single event schedule
	 */
	public function ld_dashboard_send_single_email( $to_email, $email_subject, $email_message, $headers ) {
		wp_mail( $to_email, $email_subject, $email_message, $headers );
	}


	/*
	 * Message Send base on selected students wise
	 */
	public function ld_dashboard_buddypress_message_send() {
		check_ajax_referer( 'ld-dashboard', 'nonce' );
		global $wpdb, $current_user;
		$user_id	   	= get_current_user_id();


		/* Email Subject blank then return error message  */
		if ( !isset($_POST['ld-buddypress-message-students']) ) {
			$results = array( 'error' 	=> 1,
							  'message'	=> esc_html__( 'Please add at least one recipient.', 'ld-dashboard'),
						);
			wp_send_json_success( $results );
			wp_die();
		}
		/* Email Subject blank then return error message  */
		if ( isset($_POST['ld-buddypress-message-subject']) && $_POST['ld-buddypress-message-subject'] == '') {
			$results = array( 'error' 	=> 1,
							  'message'	=> esc_html__( 'Please add a subject to your message.', 'ld-dashboard'),
						);
			wp_send_json_success( $results );
			wp_die();
		}

		/* Email Body blank then return error message  */
		if ( isset($_POST['ld-buddypress-message-message']) && $_POST['ld-buddypress-message-message'] == '') {
			$results = array( 'error' 	=> 1,
							  'message'	=> esc_html__( 'Please add some content to your message.', 'ld-dashboard'),
						);
			wp_send_json_success( $results );
			wp_die();
		}

		$recipients = array();
		$student_ids = $_POST['ld-buddypress-message-students'];
		foreach( $student_ids as $student ) {
			$student_info 	= get_userdata($student);
			$recipients[]	= $student_info->user_login;
		}

		$send = messages_new_message( array(
										'recipients' => $recipients,
										'subject'    => $_POST['ld-buddypress-message-subject'],
										'content'    => $_POST['ld-buddypress-message-message'],
										'error_type' => 'wp_error',
									) );

		// Send the message and redirect to it.
		if ( true === is_int( $send ) ) {
			$success     = true;
			$feedback    = __( 'Message successfully sent.', 'ld-dashboard' );

		// Message could not be sent.
		} else {
			$success  = false;
			$feedback = $send->get_error_message();
		}
		wp_send_json_success( array( 'success'=> $success, 'message_sent' => $feedback ) );
		wp_die();
	}

	/*
	 * get the student Course wise Progress report
	 */

	public function ld_dashboard_student_course_progress() {
		check_ajax_referer( 'ld-dashboard', 'nonce' );

		$course_id 	= sanitize_text_field( $_POST[ 'course_id' ] );
		$user_id	= get_current_user_id();
		$course_progress_data	 = $this->ld_dashboard_check_course_progress_data( $user_id, $course_id );
		$course_name			 = get_the_title( $course_id );
		$course_progress		 = $course_progress_data[ 'percentage' ];
		$quizze_progress		 = $course_progress_data[ 'quizze_percentage' ];
		$assignment_progress	 = $course_progress_data[ 'assignment_percentage' ];



		$html = '<input id="ld-dashboard-student-course" value="' . $course_name . '" type="hidden">
				<input id="ld-dashboard-student-course-progress" value="' . $course_progress . '" type="hidden">
				<input id="ld-dashboard-student-quizee-progress" value="' . $quizze_progress . '" type="hidden">
				<input id="ld-dashboard-student-assignment-progress" value="' . $assignment_progress . '" type="hidden">';
		$response	 = array(
			'html' => $html,
		);
		wp_send_json_success( $response );
		wp_die();
	}

	/*
	 * LD Instructor Registration Form to Register Instructor
	 *
	 */
	public function ld_instructor_registration_functions( $atts, $content ) {
		$function_obj               = Ld_Dashboard_Functions::instance();
		$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
		$ld_dashboard               = $ld_dashboard_settings_data['general_settings'];

		$user_id		 = get_current_user_id();
		ob_start();

		include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-instructor-registration.php';

		return ob_get_clean();
	}

	/*
	 * Create LD Instructor Registration process as pending
	 *
	 */
	public function ld_dashboard_register_instructor() {
		if ( ! isset($_POST['ld_instructor_reg_action'])  ||  $_POST['ld_instructor_reg_action'] !== 'ld_dashboard_instructor_registration' ){
			return;
		}

		global $ld_instructor_error_msgs;
		$first_name     = sanitize_text_field($_POST['first_name']);
		$last_name      = sanitize_text_field($_POST['last_name']);
		$email          = sanitize_text_field($_POST['email']);
		$user_login     = sanitize_text_field($_POST['user_login']);
		$password       = sanitize_text_field($_POST['password']);

		$userdata = array(
			'user_login'    =>  $user_login,
			'user_email'    =>  $email,
			'first_name'    =>  $first_name,
			'last_name'     =>  $last_name,
			'role'          =>  'ld_instructor_pending',
			'user_pass'     =>  $password,
		);

		$user_id = wp_insert_user( $userdata ) ;
		if ( ! is_wp_error($user_id)){
            do_action('ld_dashboard_new_instructor_after', $user_id);

            $user = get_user_by( 'id', $user_id );
			if( $user ) {
				wp_set_current_user( $user_id, $user->user_login );
				wp_set_auth_cookie( $user_id );
			}
		} else {
			$ld_instructor_error_msgs = $user_id->get_error_messages();
		}
	}

	public function ld_dashboard_user_activity_query_where( $sql_str_where, $query_args ) {
		$sql_str_where .= ' AND ( ld_user_activity.activity_completed != 0 OR  ld_user_activity.activity_completed IS NULL) ';
		return $sql_str_where;
	}

	public function ld_dashboard_auto_enroll_instructor_courses($access, $post_id, $user_id) {
		if (! is_user_logged_in() || ! $post_id) {
			return $access;
		}

		global $current_user;

		if (empty($user_id)) {
			$user_id = get_current_user_id();
		}

		// Check if instructor.

		if ( !in_array( 'ld_instructor', (array) $current_user->roles ) ) {
			return $access;
		}

		$post = get_post($post_id);
		$ld_post_types = array('sfwd-courses', 'sfwd-lessons', 'sfwd-question', 'sfwd-quiz', 'sfwd-topic');

		if (! in_array($post->post_type, $ld_post_types)) {
			return $access;
		}

		// Check if shared course
		$course_id = $post_id;
		if ('sfwd-courses' != $post->post_type) {
			$course_id = learndash_get_course_id($post_id);
		}
		$_ld_instructor_ids = get_post_meta($post_id, '_ld_instructor_ids', true);

		if (!empty($shared_courses) && in_array($course_id, $_ld_instructor_ids)) {
			return true;
		}

		if ($user_id == $post->post_author) {
			return true;
		}

		return $access;
	}

	/*
	 * Edd Points for ess single order.
	 *
	 * @since    3.1.0
	 */

	public function add_endpoint() {
		add_rewrite_endpoint( 'my-course', EP_PAGES );
	}

	public function query_vars( $vars ) {

		$this->query_vars = array(
			'my-course',
		);

		$this->query_vars = apply_filters( 'ld_dashboard_query_vars', $this->query_vars );

		foreach ( $this->query_vars as $var ) {
			$vars[] = $var;
		}

		return $vars;
	}

	public function ld_dashboard_apply_instructor(){
		if ( ! isset($_POST['ld_dashboard_action'])  ||  $_POST['ld_dashboard_action'] !== 'ld_apply_instructor' ){
			return;
		}
		global $current_user;
		$user_id = get_current_user_id();

		if ($user_id){
			$userdata = array(
				'ID' 	=> $user_id,
				'role'  =>  'ld_instructor_pending',
			);

			$user_id = wp_update_user( $userdata ) ;
			$current_user->roles[0] = 'ld_instructor_pending';
		}else{
			die(__('Permission denied', 'ld-dashboard'));
		}
	}

}
