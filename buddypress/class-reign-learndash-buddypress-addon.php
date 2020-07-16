<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_LearnDash_BuddyPress_Addon' ) ) :

/**
 * @class Reign_LearnDash_BuddyPress_Addon
 */
class Reign_LearnDash_BuddyPress_Addon {
	
	/**
	 * The single instance of the class.
	 *
	 * @var Reign_LearnDash_BuddyPress_Addon
	 */
	protected static $_instance = null;
	
	/**
	 * Main Reign_LearnDash_BuddyPress_Addon Instance.
	 *
	 * Ensures only one instance of Reign_LearnDash_BuddyPress_Addon is loaded or can be loaded.
	 *
	 * @return Reign_LearnDash_BuddyPress_Addon - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Reign_LearnDash_BuddyPress_Addon Constructor.
	 */
	public function __construct() {
		$this->init_hooks();
	}


	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		/* Action to log buddypress activity for enroll in a course. */
		add_action( 'learndash_update_course_access', array( $this, 'reign_learndash_enroll_in_course_activity' ), 10, 4 );

		/* Action to log buddypress activity on completion of a topic. */
		add_action( 'learndash_topic_completed', array( $this, 'reign_learndash_user_topic_completion_activity' ), 100, 1 );

		/* Action to log buddypress activity on completion of a lesson.	*/
		add_action( 'learndash_lesson_completed', array( $this, 'reign_learndash_user_lesson_end_activity' ), 100, 1 );

		/* Action to log buddypress activity on completion of a course. */
		add_action( 'learndash_course_completed', array( $this, 'reign_learndash_user_course_end_activity' ), 100, 1 );

		/* Action to log buddypress activity on completion of a quiz.	*/
		add_action( 'learndash_quiz_completed', array( $this, 'reign_learndash_complete_quiz_activity' ), 100, 2 );

		/* Action to log buddypress activity on post a comment when comment under moderation is on.	*/
		add_action( 'wp_set_comment_status', array( $this, 'reign_learndash_course_comment_approved' ), 100, 2 );

		/* Action to log buddypress activity on post a comment when comment under moderation is off.	*/
		add_action( 'comment_post', array( $this, 'reign_learndash_course_comment' ), 100, 2 );

		/**
		 * Add meta box
		 *
		 * @param post $post The post object
		 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
		 */
		add_action( 'add_meta_boxes_sfwd-courses', array( $this, 'reign_learndash_buddypress_add_meta_boxes' ), 100, 2 );


		/**
		 * Store custom field meta box data
		 *
		 * @param int $post_id The post ID.
		 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/save_post
		 */
		add_action( 'save_post_sfwd-courses', array( $this, 'ld_course_save_meta_box_data' ) );

		/**
		 * Add additional tab to profile settings buddypress.
		 *
		 */
		add_action( 'bp_setup_nav', array( $this, 'wb_add_activity_setting_menu' ), 77 );

		if(class_exists('BuddyPress')) {
			/* Add courses tab at buddypress profile page. */
			add_action( 'wp', array( $this, 'learndash_bp_profile_courses_tab' ) );
		}
		
		/* Add course unlinked bp group list metabox in single course. */
		add_action( 'add_meta_boxes', array( $this, 'reign_ld_select_group_metaboxes' ), 1 );

		/* Save course linked bp group id. */
		add_action( 'save_post', array( $this, 'save_reign_ld_bp_group_setting' ), 100, 1 );

		/* Hide join group button from buddypress linked course group. */
		add_filter( 'bp_get_group_join_button', array( $this, 'reign_ld_hide_join_btn' ), 10, 2 );

		/* Add/remove member from course linked bp group. */
		add_action( 'learndash_update_course_access', array( $this, 'reign_update_course_group_users' ), 10, 4 );

		add_action( 'wp_ajax_reign_ld_sync_group', array( $this, 'reign_ld_sync_group' ) );
		add_action( 'wp_ajax_nopriv_reign_ld_sync_group', array( $this, 'reign_ld_sync_group' ) );

		add_action( 'wp_ajax_reign_ld_get_linked_groups', array( $this, 'reign_ld_get_linked_groups' ) );
		add_action( 'wp_ajax_nopriv_reign_ld_get_linked_groups', array( $this, 'reign_ld_get_linked_groups' ) );
		
		if ( !class_exists('LearnMate_LearnDash_Addon') && class_exists('BuddyPress') && bp_is_active( 'groups' ) && class_exists('Bptodo_Profile_Menu') && ld_if_display_to_do_enabled() ) {
			add_action( 'ld_dashboard_show_course_to_do_list', array( $this, 'reign_ld_show_course_to_do_list' ) );
			add_action( 'wp_ajax_ld_generate_group_course_todo_list', array( $this, 'ld_generate_group_course_todo_list') );
		}		
	}

	public function reign_ld_show_course_to_do_list( $total_courses ) {
		global $bptodo;
		$user_id = get_current_user_id();

		foreach ( $total_courses as $key => $course_id ) {
			$reign_bp_group_field = get_post_meta( $course_id, 'reign_bp_group_field', true );
			if( !$reign_bp_group_field ) {
				unset( $total_courses[$key] );
			}
		}
		?>
		
		
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
			<div class="ld-dashboard-seperator"><span><?php _e( 'To Do', 'ld-dashboard' ); ?></span></div>
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
				<?php 
				echo '<div class="render-course-group-to-do-list">';
				echo $todo_list;
				echo '</div>';
				?>
			</div>
			<?php
			
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

	public function reign_ld_get_linked_groups() {
		$group_args = array(
				'order'    => 'DESC',
				'orderby'  => 'date_created',
				'per_page' => -1,
				'meta_query' => array(
						array(
							'key'     => '_reign_linked_course',
							'compare' => 'EXISTS'
						),
					)
			);
		$allgroups  = groups_get_groups( $group_args );
		$groups_arr = array();
		if ( $allgroups['groups'] ) {
			foreach( $allgroups['groups'] as $single_group ) {
				$groups_arr[$single_group->name.'_'.$single_group->id] = $single_group->id;
			}
		}
		echo json_encode( $groups_arr );
		die;	
	}

	public function reign_ld_sync_group() {
		$all_student_ids = '';
		$group_id        = filter_input( INPUT_POST, 'group_id' );
		$course_id       = groups_get_groupmeta( $group_id, '_reign_linked_course', true );
	    
		if ( ! empty( $course_id ) ) {
	        $all_users       = learndash_get_users_for_course( $course_id, array() );
	        if ( $all_users instanceof WP_User_Query ) {    
	            $all_student_ids = $all_users->get_results();
	        }
	        if ( ! empty( $all_student_ids ) ) {
	        	$chunk       = 5;
	        	$total_users = count( $all_student_ids );
	        	$total_chunk = ( count( $all_student_ids ) > $chunk ) ? ceil( count( $all_student_ids ) / $chunk ) : 1;
	        	$chunk_no    = groups_get_groupmeta( $group_id, '_reign_course_user_chunk', true );
	        	if ( ! $chunk_no ) {
	        		$chunk_no = 1;	        		
	        	}
	        	for( $i = $chunk_no; $i <=$total_chunk; $i++ ) {
	        		$start = ( $i - 1 ) * $chunk;
	        		$current_arr = array_slice( $all_student_ids, $start, $chunk );
	        		foreach( $current_arr as $uid ) {
		                groups_join_group( $group_id, $uid );
		            }
	        		groups_update_groupmeta( $group_id, '_reign_course_user_chunk', $i );
	        	}
	        	if( $chunk_no == $total_chunk ) {
	        		groups_delete_groupmeta( $group_id, '_reign_course_user_chunk' );
	        	}	           
	        }
	    }
	    exit;   
	}

	public function reign_update_course_group_users( $user_id, $course_id, $access_list, $remove ) {
		global $wbtm_reign_settings;
		if ( class_exists('BuddyPress') ) {
			if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
					$linked_group = get_post_meta( $course_id, 'reign_bp_group_field', true );
					if ( ! empty( $linked_group ) ) {
						if ( ! empty( $access_list ) ) {
							$access_list = explode(',', $access_list );
							if ( in_array( $user_id, $access_list ) ) {
								groups_join_group( $linked_group, $user_id );
							} else {
								groups_leave_group( $linked_group, $user_id );
							}
						} else {
							groups_leave_group( $linked_group, $user_id );
						}	
					}
				}	
			}
		}		
	}

	public function reign_ld_hide_join_btn( $button, $group ) {
		$course_id = groups_get_groupmeta( $group->id, '_reign_linked_course', true );
		if ( ! empty( $course_id ) ) {
			$button = '';
		}
		
		return $button;
	}

	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'jquery' );
		if ( ! wp_style_is( 'reign-learndash-admin-css', 'enqueued' ) ) {
			wp_enqueue_style( 'reign-learndash-admin-css', LD_DASHBOARD_PLUGIN_URL . 'buddypress/assets/css/reign-learndash-admin.css', array(), time() );
		}	
		if ( ! wp_script_is( 'jquery-ui-dialog', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
		}
		if ( ! wp_style_is( 'wp-jquery-ui-dialog', 'enqueued' ) ) {
    		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		}
		
		if ( ! wp_script_is( 'reign-learndash-pagination', 'enqueued' ) ) {
			wp_enqueue_script( 'reign-learndash-pagination', LD_DASHBOARD_PLUGIN_URL . 'buddypress/assets/js/pagination.min.js', array( 'jquery' ), time() );
		}	
		if ( ! wp_script_is( 'reign-learndash-admin', 'enqueued' ) ) {
			wp_enqueue_script( 'reign-learndash-admin', LD_DASHBOARD_PLUGIN_URL . 'buddypress/assets/js/reign-learndash-admin.js', array( 'jquery' ), time() );
			wp_localize_script(	
				'reign-learndash-admin',
				'ReignLdObj',
				array( 
					'ajaxurl'            => admin_url( 'admin-ajax.php', is_ssl() ? 'admin' : 'http' ),
					'dialog_ok_text'     => esc_html__( 'Proceed', 'reign-learndash-addon' ),
					'dialog_cancel_text' => esc_html__( 'Cancel', 'reign-learndash-addon' ),
					'sync_text' => esc_html__( 'Sync', 'reign-learndash-addon' ),
					'completed_text' => esc_html__( 'Completed', 'reign-learndash-addon' )
				)
			);
		}
	}

	public function save_reign_ld_bp_group_setting( $post_id ) {
		global $wbtm_reign_settings;	
		if ( ! isset( $_POST['reign_bp_group_field_nonce'] ) ) {
	        return;
	    }	 
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return;
	    }	 
	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }
	    if ( class_exists('BuddyPress') ) {
			if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
				    if ( isset( $_POST['post_type'] ) && 'sfwd-courses' === $_POST['post_type'] ) {
				    		    	
				    	// Remove previous linked courses id from group meta. 
				    	$saved_group = get_post_meta( $post_id, 'reign_bp_group_field', true );
				    	groups_delete_groupmeta( $saved_group, '_reign_linked_course' );

				    	// Save linked group id in current course & removes previous group id. 
				       	$ld_group_field = $_POST['reign_bp_group_field'];
				 		update_post_meta( $post_id, 'reign_bp_group_field', $ld_group_field );
				 		$args = array( 
						    'group_id'   => $ld_group_field,
						    'group_role' => array( 'member', 'mod' )
						);
						$group_members_result = groups_get_group_members( $args );

						// Remove existing group members except group admins. 
						if ( $saved_group != $ld_group_field ) {
					 		if ( ! empty( $group_members_result['members'] ) && ! empty( $ld_group_field ) ) {
						 		foreach( $group_members_result['members'] as $member ) {
									groups_leave_group( $ld_group_field, $member->ID );
								}
							}
						}	

						// Save linked courses id in group meta. 
				 		groups_add_groupmeta( $ld_group_field, '_reign_linked_course', $post_id );
				    }
				}
			}
		}		    
	}

	public function reign_ld_select_group_metaboxes() {
		global $bp;
		global $wbtm_reign_settings;
		if ( class_exists('BuddyPress') ) {
			if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
					add_meta_box(
						'reign-ld-bp-group',
						esc_html__( 'BuddyPress Group', 'reign-learndash-addon' ),
						array( $this, 'reign_ld_bp_group_setting_display' ),
						'sfwd-courses',
						'side',
						'high'
					);
				}
			}
		}			
	}

	/**
	 * Output the HTML for the metabox.
	 */
	public function reign_ld_bp_group_setting_display( $post ) {
		global $bp;
		$saved_group_data = '';
		$saved_group      = get_post_meta( $post->ID, 'reign_bp_group_field', true );
		
		$group_args = array(
				'order'    => 'DESC',
				'orderby'  => 'date_created',
				'per_page' => -1,
				'meta_query' => array(
						array(
							'key'     => '_reign_linked_course',
							'compare' => 'NOT EXISTS'
						),
					)
			);
		$allgroups  = groups_get_groups( $group_args );
		$groups_arr = array( '' => esc_html__( 'None', 'reign-learndash-addon' ) );
		if ( ! empty( $saved_group ) ) {
			$saved_group_data         = groups_get_group( $saved_group );
			$groups_arr[$saved_group] = $saved_group_data->name;
		}
		
		if ( $allgroups ) {
			foreach ( $allgroups['groups'] as $group ) {
				$groups_arr[$group->id] = $group->name;
			}
		}
		wp_nonce_field( basename( __FILE__ ), 'reign_bp_group_field_nonce' );		
		?>
		<div id="reign-bp-group-confirm" title="<?php esc_attr_e( 'Do you want to associate a BuddyPress group with this course?', 'reign-learndash-addon' ); ?>" style="display: none;">
		  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span><?php esc_html_e( 'This action remove all existing users in this group. Are you sure?', 'reign-learndash-addon' ); ?></p>
		</div>
		<select name="reign_bp_group_field" id="reign_bp_group_field">
			<?php foreach( $groups_arr as $group_id => $group_name ) { ?>
				<option value="<?php echo esc_attr( $group_id ); ?>" <?php selected( $saved_group, esc_attr( $group_id ), true ); ?>><?php echo esc_html( $group_name ); ?></option>
			<?php } ?>
		</select>
		<p class="description"><?php esc_html_e( 'If you want to associate with new group then please create group first.', 'reign-learndash-addon' ); ?></p>
		<?php
	}

	public function learndash_bp_profile_courses_tab() {
		global $bp;
		$bp_pages    = bp_core_get_directory_pages();
		$member_slug = $bp_pages->members->slug;

		$menu_label  = apply_filters( 'reign_learndash_primary_bp_tab_name', LearnDash_Custom_Label::get_label( 'courses' ) );
		$menu_slug   = 'rla-courses';
		$name        = bp_get_displayed_user_username();

		$user_id = bp_displayed_user_id();
		$tab_args = array(
			'name'                    => $menu_label,
			'slug'                    => $menu_slug,
			'screen_function'         => array( $this, 'learndash_tab_show_courses_screen' ),
			'position'                => 75,
			'default_subnav_slug'     => 'store',
			'show_for_displayed_user' => true,
		);
		bp_core_new_nav_item( $tab_args );

		$parent_slug = $menu_slug;

		bp_core_new_subnav_item(
			array(
				'name'            => apply_filters( 'reign_learndash_secondary_bp_tab_name', LearnDash_Custom_Label::get_label( 'courses' ) ),
				'slug'            => 'rla-courses',
				'parent_url'      => $bp->loggedin_user->domain . $parent_slug . '/',
				'parent_slug'     => $parent_slug,
				'screen_function' => array( $this, 'learndash_tab_show_courses_screen' ),
				'position'        => 100,
				'link'            => site_url() . "/$member_slug/$name/$parent_slug/",
			)
		);
	}

	public function learndash_tab_show_courses_screen() {
		add_action( 'bp_template_title', array( $this, 'learndash_tab_show_screen_title' ) );
		add_action( 'bp_template_content', array( $this, 'learndash_tab_show_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	public function learndash_tab_show_screen_title() {
		echo sprintf( esc_html_x( 'Enrolled %s', 'Enrolled Courses Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'courses' ) );
	}

	public function learndash_tab_show_screen_content() {
		$per_row     = apply_filters( 'reign_learndash_buddypress_course_per_row', 3 );
		$plugins_url = plugins_url() . '/learndash-course-grid/';
		
		wp_enqueue_style( 'learndash_course_grid_css', $plugins_url . 'style.css' );
		wp_enqueue_script( 'learndash_course_grid_js', $plugins_url . 'script.js', array('jquery' ) );
		wp_enqueue_style( 'ld-cga-bootstrap', $plugins_url . 'bootstrap.min.css' );

		echo '<div id="ld_course_list">';
		echo do_shortcode( '[ld_course_list mycourses="enrolled" col="'. $per_row .'"]' );
		echo '</div>';
	}

	public function wb_add_activity_setting_menu() {
		global $bp;
		if ( bp_is_my_profile() ) {
			bp_core_new_subnav_item( array(
					'name' => esc_html__( 'Learning Activities', 'reign-learndash-addon' ),
					'slug' => 'learning-activities',
					'parent_url' => trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() ),
					'parent_slug' => bp_get_settings_slug(),
					'screen_function' => array( $this, 'wbbpp_show_activity_setting_screen' ),
					'position' => 30
				)
			);
		}
	}

	public function wbbpp_show_activity_setting_screen() {
		add_action( 'bp_template_content', array( $this, 'wb_activity_setting_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	public function wb_activity_setting_screen_content() {
		$course_enroll_label     = sprintf( esc_html_x( '%s Enrollment Activity', 'Course Enrollment Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'course' ) );
		$course_completion_label = sprintf( esc_html_x( '%s Completion Activity', 'Course Completion Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'course' ) );
		$lesson_completion_label = sprintf( esc_html_x( '%s Completion Activity', 'Lesson Completion Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'lesson' ) );
		$topic_completion_label  = sprintf( esc_html_x( '%s Completion Activity', 'Topic Completion Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'topic' ) );
		$quiz_passed_label       = sprintf( esc_html_x( '%s Passed Activity', 'Quiz Passed Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'quiz' ) );
		$comment_course_label    = sprintf( esc_html_x( 'Comment Single %s Activity', 'Comment Single Course Activity Label', 'reign-learndash-addon' ), LearnDash_Custom_Label::get_label( 'course' ) );

		$activity_array = array(
			'reign_learndash_course_enrollment_activity' => array(
				'label' => $course_enroll_label
			),
			'reign_learndash_course_completion_activity' => array(
				'label' => $course_completion_label
			),
			'reign_learndash_lesson_completion_activity' => array(
				'label' => $lesson_completion_label
			),
			'reign_learndash_topic_completion_activity' => array(
				'label' => $topic_completion_label
			),
			'reign_learndash_quiz_passed_activity' => array(
				'label' => $quiz_passed_label
			),
			'reign_learndash_comment_single_course_activity' => array(
				'label' => $comment_course_label
			),
		);
		?>
		<h2 class="screen-heading profile-settings-screen"><?php _e( 'Learning Activities Settings', 'reign-learndash-addon' ); ?></h2>
		<p class="bp-help-text profile-visibility-info"><?php _e( 'Manage your learning activities settings below.', 'reign-learndash-addon' ); ?></p>
		<?php
		if( isset( $_POST['reign-learndash-buddypress-submit'] ) ) {
			foreach ( $activity_array as $activity_key => $activity_info ) {
				if( isset( $_POST[$activity_key] ) ) {
					update_user_meta( get_current_user_id(), $activity_key, $_POST[$activity_key] );
				}
			}
			if ( function_exists( 'bp_get_theme_package_id' ) ) {
				$theme_package_id = bp_get_theme_package_id();
			} else {
				$theme_package_id = 'legacy';
			}
			if ( 'nouveau' === $theme_package_id ) {
				?>
				<aside class="bp-feedback bp-messages bp-template-notice success">
					<span class="bp-icon" aria-hidden="true"></span>
					<p><?php esc_html_e( 'Your profile settings have been saved.', 'reign-learndash-addon' ); ?></p>
				</aside>
				<?php
			}
			else {
				?>
				<div id="template-notices" role="alert" aria-atomic="true">
					<div id="message" class="bp-template-notice updated">
						<p><?php esc_html_e( 'Your profile settings have been saved.', 'reign-learndash-addon' ); ?></p>
					</div>
				</div>
				<?php
			}
		}
		?>
		<form method="POST">
			<table>
				<?php
				foreach ( $activity_array as $activity_key => $activity_info ) {
					$activity_enable_disable_options = array(
						'enable' => esc_html__( 'Enable', 'reign-learndash-addon' ),
						'disable' => esc_html__( 'Disable', 'reign-learndash-addon' )
					);
					$activity_enable_disable = get_user_meta( get_current_user_id(), $activity_key, true );
					if( empty( $activity_enable_disable ) ) {
						$activity_enable_disable = 'enable';
					}
					echo '<tr>';
						echo '<td>';
							echo '<label>' . sprintf( esc_html__( 'Enable "%s"', 'reign-learndash-addon' ), $activity_info['label'] ) . '</label>';
						echo '</td>';
						echo '<td>';
							echo '<select name="'.$activity_key.'" class="">';
								foreach ( $activity_enable_disable_options as $key => $value ) {
									$_selected = ( $activity_enable_disable == $key ) ? 'selected="selected"' : '';
									echo '<option value="' . $key . '" '. $_selected .'>' . $value . '</option>';
								}
							echo '</select>';
						echo '</td>';	
					echo '</tr>';
				}
				?>
			</table>
			<div class="submit">
				<input name="reign-learndash-buddypress-submit" id="submit" value="<?php esc_html_e( 'Save Changes', 'reign-learndash-addon' ); ?>" class="auto" type="submit">
			</div>
		</form>
		<?php
	}

	public function reign_learndash_buddypress_add_meta_boxes( $post ){
		add_meta_box(
			'render_reign_learndash_buddypress_meta_box',
			esc_html__( 'LearnDash BuddyPress Activities', 'reign-learndash-addon' ),
			array( $this, 'render_reign_learndash_buddypress_meta_box' ),
			'sfwd-courses',
			'side',
			'high'
		);
	}

	/**
	 * Build custom field meta box
	 *
	 * @param post $post The post object
	 */
	function render_reign_learndash_buddypress_meta_box( $post ){
		// make sure the form request comes from WordPress
		wp_nonce_field( basename( __FILE__ ), 'render_reign_learndash_buddypress_meta_box_nonce' );
		
		$reign_ld_buddypress_activities_enable = get_post_meta( $post->ID, 'reign_ld_buddypress_activities_enable', true );
		if ( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		?>
		<div class='inside'>

			<h3><?php esc_html_e( 'LearnDash BuddyPress Activities', 'reign-learndash-addon' ); ?></h3>
			<p>
				<input type="radio" name="reign_ld_buddypress_activities_enable" value="enable" <?php checked( $reign_ld_buddypress_activities_enable, 'enable' ); ?> /> <?php esc_html_e( 'Enable BuddyPress Activities', 'reign-learndash-addon' ); ?>
				<br />
				<input type="radio" name="reign_ld_buddypress_activities_enable" value="disable" <?php checked( $reign_ld_buddypress_activities_enable, 'disable' ); ?> /> <?php esc_html_e( 'Disable BuddyPress Activities', 'reign-learndash-addon' ); ?>
			</p>
		</div>
		<?php
	}

	public function ld_course_save_meta_box_data( $post_id ){
		// verify meta box nonce
		if ( !isset( $_POST['render_reign_learndash_buddypress_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['render_reign_learndash_buddypress_meta_box_nonce'], basename( __FILE__ ) ) ){
			return;
		}
		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		}
	  	// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		}
		// store custom fields values
		if ( isset( $_REQUEST['reign_ld_buddypress_activities_enable'] ) ) {
			update_post_meta( $post_id, 'reign_ld_buddypress_activities_enable', sanitize_text_field( $_POST['reign_ld_buddypress_activities_enable'] ) );
		}
	}


	/**
	* Function to create activity in buddypress activity stream.
	*/
	public function execute_shortcodes_in_activity_content( $activity_content = '', $learndash_activity_info = array() ) {
		
		switch ( $learndash_activity_info['activity_type'] ) {

			case 'user_enrolled_in_course':
				$course_title = get_the_title( $learndash_activity_info['object_id'] );
				$course_link  = get_permalink( $learndash_activity_info['object_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				break;

			case 'user_completed_course':
				$course_title = get_the_title( $learndash_activity_info['object_id'] );
				$course_link  = get_permalink( $learndash_activity_info['object_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				break;

			case 'user_completed_lesson':
				$lesson_title = get_the_title( $learndash_activity_info['object_id'] );
				$lesson_link = get_permalink( $learndash_activity_info['object_id'] );
				$course_title = get_the_title( $learndash_activity_info['course_id'] );
				$course_link  = get_permalink( $learndash_activity_info['course_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				$activity_content      = str_replace( '{lesson_name}', '<strong><a href="' . esc_url( $lesson_link ) . '">' . esc_html( $lesson_title ) . '</a></strong>', $activity_content );
				break;

			case 'user_completed_topic':
				$topic_title  = get_the_title( $learndash_activity_info['object_id'] );
				$topic_link   = get_permalink( $learndash_activity_info['object_id'] );
				$course_title = get_the_title( $learndash_activity_info['course_id'] );
				$course_link  = get_permalink( $learndash_activity_info['course_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				$activity_content      = str_replace( '{topic_name}', '<strong><a href="' . esc_url( $topic_link ) . '">' . esc_html( $topic_title ) . '</a></strong>', $activity_content );
				break;

			case 'user_completed_quiz':
				$quiz_title   = get_the_title( $learndash_activity_info['object_id'] );
				$quiz_link    = get_permalink( $learndash_activity_info['object_id'] );
				$course_title = get_the_title( $learndash_activity_info['course_id'] );
				$course_link  = get_permalink( $learndash_activity_info['course_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				$activity_content      = str_replace( '{quiz_name}', '<strong><a href="' . esc_url( $quiz_link ) . '">' . esc_html( $quiz_title ) . '</a></strong>', $activity_content );
				break;

			case 'user_commented_on_course':
				$course_title = get_the_title( $learndash_activity_info['object_id'] );
				$course_link  = get_permalink( $learndash_activity_info['object_id'] );
				$activity_content      = str_replace( '{course_name}', '<strong><a href="' . esc_url( $course_link ) . '">' . esc_html( $course_title ) . '</a></strong>', $activity_content );
				break;

			default:
				$activity_content = apply_filter( 'reign_learndash_modify_activity_content', $activity_content );
				break;
		}

		$activity_content = str_replace( '{username_name}', bp_core_get_userlink( $learndash_activity_info['user_id'] ), $activity_content );

		return $activity_content;
    }

	/**
	* Function to create activity in buddypress activity stream.
	*/
	public function create_buddypress_activity( $activity_content = '', $user_id = '' ) {
		if( !empty( $activity_content ) ) {
			if( empty( $user_id ) ) {
				$user_id = bp_loggedin_user_id();
			}
			// add activity
	        $prep_args = array(
	            'id'                => false,
	            'content'           => $activity_content,
	            'component'         => 'activity',
	            'type'              => 'activity_update',
	            'user_id'           => $user_id,
	        );
	        $activity_id = bp_activity_add( $prep_args );
	        return $activity_id;
		}
		return false;
    }

	/**
	 * All default activity messages.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function get_default_reign_learndash_activity_content( $key = '' ) {
		$activity_contents = array(
			'reign_learndash_course_enrollment_activity_content' => '{username_name} enrolled for a course {course_name}.',
			'reign_learndash_course_completion_activity_content' => '{username_name} completed a course {course_name}.',
			'reign_learndash_lesson_completion_activity_content' => '{username_name} completed a lesson {lesson_name} of course {course_name}.',
			'reign_learndash_topic_completion_activity_content' => '{username_name} completed a topic {topic_name} of course {course_name}.',
			'reign_learndash_quiz_passed_activity_content' => '{username_name} completed a quiz {quiz_name} of course {course_name}.',
			'reign_learndash_comment_single_course_activity_content' => '{username_name} commented on course {course_name}.',
		);
		$activity_contents = apply_filters( 'get_default_reign_learndash_activity_content', $activity_contents );
		if( $key ) {
			return $activity_contents[$key];
		}
		return $activity_contents;
	}

	/**
	 * All activity messages.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function get_reign_learndash_activity_content( $key = '' ) {
		global $wbtm_reign_settings;
		$activity_content = isset( $wbtm_reign_settings[ 'learndash' ][ $key ] ) ? $wbtm_reign_settings[ 'learndash' ][ $key ] : $this->get_default_reign_learndash_activity_content( $key );
		return $activity_content;
	}

	/**
	 * Check if this kind of activity is allowed by admin in backend.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function is_reign_learndash_activity_allowed_from_backend( $key = '' ) {
		global $wbtm_reign_settings;
		$allowed = isset( $wbtm_reign_settings[ 'learndash' ][ $key ] ) ? $wbtm_reign_settings[ 'learndash' ][ $key ] : 'enable';
		if( 'enable' === $allowed ) {
			$allowed = true;
		}
		else {
			$allowed = false;
		}
		return $allowed;
	}

	/**
	 * Check if this kind of activity is allowed by admin in backend.
	 *
	 * @since    1.0.0
	 * @access public
	 */
	public function is_reign_learndash_activity_allowed_by_user( $key = '', $user_id = '' ) {
		$allowed = get_user_meta( $user_id, $key, true );
		if( empty( $allowed ) ) {
			$allowed = 'enable';
		}
		if( 'enable' === $allowed ) {
			$allowed = true;
		}
		else {
			$allowed = false;
		}
		return $allowed;
	}

	/**
	 * Used for create course comment activity when comment moderation is on.
	 *
	 * @since    1.0.0
	 * @param int    $comment_id The comment id.
	 * @param object $comment_status The comment status.
	 */
	public function reign_learndash_course_comment_approved( $comment_id, $comment_status ) {
		global $wbtm_reign_settings;
		$comment_obj = get_comment( $comment_id );
		$object_id   = $comment_obj->comment_post_ID;
		$object_type = get_post_type( $object_id );
		$user_id     = $comment_obj->user_id;

		$reign_ld_buddypress_activities_enable = get_post_meta( $object_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		if ( 'sfwd-courses' === $object_type && 'approve' === $comment_status ) {

			$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_comment_single_course_activity' );
			$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_comment_single_course_activity', $user_id );

			$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_comment_single_course_activity_content_enable', $user_id );

			if ( ! $allowed_from_backend || ! $allowed_by_user ) {
				return;
			}

			$activity_type    = 'user_commented_on_course';
			$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_comment_single_course_activity_content' );

			// add activity
		    $learndash_group_activity_info = $learndash_activity_info = array(
				'activity_type'  => $activity_type,
				'object_type'    => $object_type,
				'object_id'      => $object_id,
				'user_id'        => $user_id,
			);

		    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
		    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
			
			if( $activity_id ) {
				$learndash_activity_info['activity_id'] = $activity_id;
				bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
			}

			if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
				    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
		    		$prep_args  = array(
					                'id'                => false,
					                'action'            => $activity_content,
					                'component'         => 'groups',
					                'type'              => 'activity_update',
					                'user_id'           => $user_id,
					                'item_id'           => $linked_group,
					                'secondary_item_id' => false,
					                'recorded_time'     => bp_core_current_time(),
					                'hide_sitewide'     => true,
					                'is_spam'           => false,
					                'error_type'        => 'bool'
					            );
		            $group_activity_id = bp_activity_add( $prep_args );

		            if ( $group_activity_id ) {
		            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
						bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
		            }
		        }
		    }

		}
	}

	/**
	 * Used for create course comment activity.
	 *
	 * @since    1.0.0
	 * @param int    $comment_id The comment id.
	 * @param object $commentdata The comment object with all comment details.
	 */
	public function reign_learndash_course_comment( $comment_id, $commentdata ) {
		global $wbtm_reign_settings;
		$comment_obj = get_comment( $comment_id );
		$object_id   = $comment_obj->comment_post_ID;
		$object_type = get_post_type( $object_id );
		$user_id     = $comment_obj->user_id;

		$reign_ld_buddypress_activities_enable = get_post_meta( $object_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		if ( ( 'sfwd-courses' === $object_type ) && $commentdata ) {
			$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_comment_single_course_activity' );
			$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_comment_single_course_activity', $user_id );

			$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_comment_single_course_activity_content_enable', $user_id );

			if ( ! $allowed_from_backend || ! $allowed_by_user ) {
				return;
			}

			$activity_type    = 'user_commented_on_course';
			$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_comment_single_course_activity_content' );

			// add activity
		    $learndash_group_activity_info = $learndash_activity_info = array(
				'activity_type'  => $activity_type,
				'object_type'    => $object_type,
				'object_id'      => $object_id,
				'user_id'        => $user_id,
			);

		    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
		    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
			
			if( $activity_id ) {
				$learndash_activity_info['activity_id'] = $activity_id;
				bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
			}

			if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
				    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
		    		$prep_args  = array(
					                'id'                => false,
					                'action'            => $activity_content,
					                'component'         => 'groups',
					                'type'              => 'activity_update',
					                'user_id'           => $user_id,
					                'item_id'           => $linked_group,
					                'secondary_item_id' => false,
					                'recorded_time'     => bp_core_current_time(),
					                'hide_sitewide'     => true,
					                'is_spam'           => false,
					                'error_type'        => 'bool'
					            );
		            $group_activity_id = bp_activity_add( $prep_args );

		            if ( $group_activity_id ) {
		            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
						bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
		            }
		        }
		    }
		}
	}

	/**
	 * Used for enroll in course activity.
	 *
	 * @since    1.0.0
	 * @param int    $user_id The user id.
	 * @param int    $course_id The course id.
	 * @param string $access_list The course access list of user ids.
	 * @param bool   $remove Value if remove id.
	 */
	public function reign_learndash_enroll_in_course_activity( $user_id, $course_id, $access_list, $remove ) {
		global $wbtm_reign_settings;
		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}
		
		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		$access_list = explode( ',', $access_list );
		if ( is_array( $access_list ) && in_array( $user_id, $access_list ) ) {
			$object_type                               = 'sfwd-courses';
			$object_id                                 = $course_id;
			
			$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_course_enrollment_activity' );
			$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_course_enrollment_activity', $user_id );

			$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_course_enrollment_activity_enable', $user_id );
			if ( ! $allowed_from_backend || ! $allowed_by_user ) {
				return;
			}

			$activity_type           = 'user_enrolled_in_course';
			$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_course_enrollment_activity_content' );

			// add activity
		    $learndash_group_activity_info = $learndash_activity_info = array(
				'activity_type'  => $activity_type,
				'object_type'    => $object_type,
				'object_id'      => $object_id,
				'user_id'        => $user_id,
			);

		    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
		    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );       
			
			if ( $activity_id ) {
				$learndash_activity_info['activity_id'] = $activity_id;
				bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
				bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
			}

		    if ( bp_is_active( 'groups' ) ) {
				if ( ld_if_ldbp_group_intgrtn_enabled() ) {
				    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
		    		$prep_args  = array(
					                'id'                => false,
					                'action'            => $activity_content,
					                'component'         => 'groups',
					                'type'              => 'activity_update',
					                'user_id'           => $user_id,
					                'item_id'           => $linked_group,
					                'secondary_item_id' => false,
					                'recorded_time'     => bp_core_current_time(),
					                'hide_sitewide'     => true,
					                'is_spam'           => false,
					                'error_type'        => 'bool'
					            );
		            $group_activity_id = bp_activity_add( $prep_args );

		            if ( $group_activity_id ) {
		            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
						bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
						bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
		            }
		        }
		    }

		}
	}

	/**
	 * Used for Create Course Completion activity.
	 *
	 * @since    1.0.0
	 * @param array $course_arr The course array.
	 */
	public function reign_learndash_user_course_end_activity( $course_arr ) {
		global $wbtm_reign_settings;
		$user_id     = $course_arr['user']->ID;
		$course_id   = $course_arr['course']->ID;
		$object_type = 'sfwd-courses';
		$object_id   = $course_id;

		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_course_completion_activity' );
		$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_course_completion_activity', $user_id );

		$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_course_completion_activity_enable', $user_id );

		if ( ! $allowed_from_backend || ! $allowed_by_user ) {
			return;
		}

		$activity_type    = 'user_completed_course';
		$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_course_completion_activity_content' );

		// add activity
	    $learndash_group_activity_info = $learndash_activity_info = array(
			'activity_type'  => $activity_type,
			'object_type'    => $object_type,
			'object_id'      => $object_id,
			'user_id'        => $user_id,
		);

	    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
	    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
		
		if( $activity_id ) {
			$learndash_activity_info['activity_id'] = $activity_id;
			bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
		}

		if ( bp_is_active( 'groups' ) ) {
			if ( ld_if_ldbp_group_intgrtn_enabled() ) {
			    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
	    		$prep_args  = array(
				                'id'                => false,
				                'action'            => $activity_content,
				                'component'         => 'groups',
				                'type'              => 'activity_update',
				                'user_id'           => $user_id,
				                'item_id'           => $linked_group,
				                'secondary_item_id' => false,
				                'recorded_time'     => bp_core_current_time(),
				                'hide_sitewide'     => true,
				                'is_spam'           => false,
				                'error_type'        => 'bool'
				            );
	            $group_activity_id = bp_activity_add( $prep_args );

	            if ( $group_activity_id ) {
	            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
					bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
	            }
	        }
	    } 

	}

	/**
	 * Used for Create Lesson completion activity.
	 *
	 * @since    1.0.0
	 * @param array $course_arr The course array.
	 */
	public function reign_learndash_user_lesson_end_activity( $course_arr ) {
		global $wbtm_reign_settings;
		$user_id     = $course_arr['user']->ID;
		$lesson_id   = $course_arr['lesson']->ID;
		$object_type = 'sfwd-lessons';
		$object_id   = $lesson_id;
		$course_id   = $course_arr['course']->ID;

		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_lesson_completion_activity' );
		$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_lesson_completion_activity', $user_id );

		$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_lesson_completion_activity_enable', $user_id );

		if ( ! $allowed_from_backend || ! $allowed_by_user ) {
			return;
		}

		$activity_type    = 'user_completed_lesson';
		$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_lesson_completion_activity_content' );

		// add activity
	    $learndash_group_activity_info = $learndash_activity_info = array(
			'activity_type'  => $activity_type,
			'object_type'    => $object_type,
			'object_id'      => $object_id,
			'user_id'        => $user_id,
			'course_id'      => $course_id,
		);

	    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
	    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
		
		if( $activity_id ) {
			$learndash_activity_info['activity_id'] = $activity_id;
			bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
		}

		if ( bp_is_active( 'groups' ) ) {
			if ( ld_if_ldbp_group_intgrtn_enabled() ) {
			    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
	    		$prep_args  = array(
				                'id'                => false,
				                'action'            => $activity_content,
				                'component'         => 'groups',
				                'type'              => 'activity_update',
				                'user_id'           => $user_id,
				                'item_id'           => $linked_group,
				                'secondary_item_id' => false,
				                'recorded_time'     => bp_core_current_time(),
				                'hide_sitewide'     => true,
				                'is_spam'           => false,
				                'error_type'        => 'bool'
				            );
	            $group_activity_id = bp_activity_add( $prep_args );

	            if ( $group_activity_id ) {
	            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
					bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
	            }
	        }
	    }

	}

	/**
	 * Used for Create Topic completion activity.
	 *
	 * @since    1.0.0
	 * @param array $course_arr The course array.
	 */
	public function reign_learndash_user_topic_completion_activity( $course_arr ) {
		global $wbtm_reign_settings;
		$user_id     = $course_arr['user']->ID;
		$topic_id    = $course_arr['topic']->ID;
		$lesson_id   = $course_arr['lesson']->ID;
		$object_type = 'sfwd-topic';
		$object_id   = $topic_id;
		$course_id   = $course_arr['course']->ID;

		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}

		$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_topic_completion_activity' );
		$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_topic_completion_activity', $user_id );

		$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_topic_completion_activity_enable', $user_id );

		if ( ! $allowed_from_backend || ! $allowed_by_user ) {
			return;
		}

		$activity_type    = 'user_completed_topic';
		$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_topic_completion_activity_content' );

		// add activity
	    $learndash_group_activity_info = $learndash_activity_info = array(
			'activity_type'  => $activity_type,
			'object_type'    => $object_type,
			'object_id'      => $object_id,
			'user_id'        => $user_id,
			'course_id'      => $course_id,
		);

	    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
        $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
		
		if( $activity_id ) {
			$learndash_activity_info['activity_id'] = $activity_id;
			bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
		}

		if ( bp_is_active( 'groups' ) ) {
			if ( ld_if_ldbp_group_intgrtn_enabled() ) {
			    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
	    		$prep_args  = array(
				                'id'                => false,
				                'action'            => $activity_content,
				                'component'         => 'groups',
				                'type'              => 'activity_update',
				                'user_id'           => $user_id,
				                'item_id'           => $linked_group,
				                'secondary_item_id' => false,
				                'recorded_time'     => bp_core_current_time(),
				                'hide_sitewide'     => true,
				                'is_spam'           => false,
				                'error_type'        => 'bool'
				            );
	            $group_activity_id = bp_activity_add( $prep_args );

	            if ( $group_activity_id ) {
	            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
					bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
	            }
	        }
	    }

	}

	/**
	 * Used for Create Quiz completion activity.
	 *
	 * @since    1.0.0
	 * @param array  $quizdata The Quiz data array.
	 * @param object $user The user details object.
	 */
	public function reign_learndash_complete_quiz_activity( $quizdata, $user ) {
		global $wbtm_reign_settings;
		$quiz_passed = $quizdata['pass'];
		$user_id     = $user->ID;
		$object_type = 'sfwd-quiz';
		$quiz_grade  = $quizdata['score'];
		$course_id   = $quizdata['course']->ID;
		if ( '1' != $quiz_passed ) {
			return;
		}
		$quiz_id                                   = $quizdata['quiz']->ID;


		$reign_ld_buddypress_activities_enable = get_post_meta( $course_id, 'reign_ld_buddypress_activities_enable', true );
		if( empty( $reign_ld_buddypress_activities_enable ) ) {
			$reign_ld_buddypress_activities_enable = 'enable';
		}
		if( 'enable' != $reign_ld_buddypress_activities_enable ) {
			return;
		}


		$allowed_from_backend = $this->is_reign_learndash_activity_allowed_from_backend( 'reign_learndash_quiz_passed_activity' );
		$allowed_by_user = $this->is_reign_learndash_activity_allowed_by_user( 'reign_learndash_quiz_passed_activity', $user_id );

		$allowed_by_user = apply_filters( 'alter_reign_learndash_activity_allowed_by_user', $allowed_by_user, 'reign_learndash_quiz_passed_activity_enable', $user_id );

		if ( ! $allowed_from_backend || ! $allowed_by_user ) {
			return;
		}

		$activity_type    = 'user_completed_quiz';
		$activity_content = $this->get_reign_learndash_activity_content( 'reign_learndash_quiz_passed_activity_content' );

		// add activity
	    $learndash_group_activity_info = $learndash_activity_info = array(
			'activity_type'  => $activity_type,
			'object_type'    => $object_type,
			'object_id'      => $quiz_id,
			'user_id'        => $user_id,
			'course_id'      => $course_id,
		);

	    $activity_content = $this->execute_shortcodes_in_activity_content( $activity_content, $learndash_activity_info );
	    $activity_id = $this->create_buddypress_activity( $activity_content, $user_id );
		
		if( $activity_id ) {
			$learndash_activity_info['activity_id'] = $activity_id;
			bp_activity_update_meta( $activity_id, 'is_reign_learndash_activity', true );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_type', $activity_type );
			bp_activity_update_meta( $activity_id, 'reign_learndash_activity_info', $learndash_activity_info );
		}

		if ( bp_is_active( 'groups' ) ) {
			if ( ld_if_ldbp_group_intgrtn_enabled() ) {
			    $linked_group = get_post_meta( $object_id, 'reign_bp_group_field', true );
	    		$prep_args  = array(
				                'id'                => false,
				                'action'            => $activity_content,
				                'component'         => 'groups',
				                'type'              => 'activity_update',
				                'user_id'           => $user_id,
				                'item_id'           => $linked_group,
				                'secondary_item_id' => false,
				                'recorded_time'     => bp_core_current_time(),
				                'hide_sitewide'     => true,
				                'is_spam'           => false,
				                'error_type'        => 'bool'
				            );
	            $group_activity_id = bp_activity_add( $prep_args );

	            if ( $group_activity_id ) {
	            	$learndash_group_activity_info['activity_id'] = $group_activity_id;
					bp_activity_update_meta( $group_activity_id, 'is_reign_learndash_activity', true );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_type', $activity_type );
					bp_activity_update_meta( $group_activity_id, 'reign_learndash_activity_info', $learndash_group_activity_info );
	            }
	        }
	    }

	}
		
}

endif;

/**
 * Main instance of Reign_LearnDash_BuddyPress_Addon.
 * @return Reign_LearnDash_BuddyPress_Addon
 */
Reign_LearnDash_BuddyPress_Addon::instance();