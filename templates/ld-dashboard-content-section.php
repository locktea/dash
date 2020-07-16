<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user, $wpdb, $wp;

$function_obj				 = Ld_Dashboard_Functions::instance();
$ld_dashboard_settings_data	 = $function_obj->ld_dashboard_settings_data();
$ld_dashboard				 = $ld_dashboard_settings_data[ 'general_settings' ];
$user_id					 = get_current_user_id();
$is_student					 = get_user_meta( $user_id, 'is_student', true );
$user_name					 = $current_user->user_firstname . ' ' . $current_user->user_lastname;
$user_name					 = ( $current_user->user_firstname == '' && $current_user->user_lastname == '' ) ? $current_user->user_login : $user_name;


if ( !learndash_is_group_leader_user() && !learndash_is_admin_user() && !in_array( 'ld_instructor', (array) $current_user->roles ) ) {
	$is_student = 1;
}
?>
<div class="ld-dashboard-content">
	<?php do_action( 'ld_dashboard_before_content' ); ?>
	<div class="ld-dashboard-landing">
		<div class="ld-dashboard-landing-cover">
			<div class="ld-dashboard-landing-content">
				<div class="ld-dashboard-landing-text">
					<?php echo sprintf( __( 'Welcome back, %s', 'ld-dashboard' ), trim( $user_name ) ); ?>
				</div>
			</div>
		</div>
	</div>

	<?php
	$my_course = false;
	if ( isset( $wp->query_vars ) && !empty( $wp->query_vars ) ) {
		foreach ( $wp->query_vars as $key => $value ) {
			if ( $key == 'my-course' ) {
				$my_course = true;
			}
		}
	}
	if ( $my_course == true ) {
		echo do_shortcode( '[ld_profile]' );
	} else {
		/*
		 * Display total course.
		 */
		?>
		<div class="ld-dashboard-statistics-container">
			<?php
			/*
			 * Display total Student count
			 */
			if ( isset( $ld_dashboard[ 'ins-earning' ] ) && $ld_dashboard[ 'ins-earning' ] == 1 && $is_student != '1' && in_array( 'ld_instructor', (array) $current_user->roles ) ) {
				?>

				<div class="col-1-2 ld-dashboard-statistics ins-earning" <?php if ( isset( $ld_dashboard[ 'ins-earning-bgcolor' ] ) && $ld_dashboard[ 'ins-earning-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'ins-earning-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php esc_html_e( 'Instructor Earning', 'ld-dashboard' ); ?>
						</h2>
						<strong class="statistics"><?php
							$instructor_total_earning	 = (int) get_user_meta( $user_id, 'instructor_total_earning', true );
							$instructor_paid_earning	 = (int) get_user_meta( $user_id, 'instructor_paid_earning', true );
							$instructor_unpaid_earning	 = $instructor_total_earning - $instructor_paid_earning;

							if ( function_exists( 'get_woocommerce_currency_symbol' ) ) {
								$currency = get_woocommerce_currency_symbol();
							} else {
								$currency = '';
							}
							echo $currency . $instructor_total_earning;
							?></strong>
					</div>
				</div>

				<?php
			}

			if ( isset( $ld_dashboard[ 'course-count' ] ) && $ld_dashboard[ 'course-count' ] == 1 ) {

				$sfwd_courses_total = count_user_posts( $user_id, 'sfwd-courses' );
				?>

				<div class="col-1-2 ld-dashboard-statistics learndash-courses" <?php if ( isset( $ld_dashboard[ 'course-count-bgcolor' ] ) && $ld_dashboard[ 'course-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'course-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php echo _x( 'courses', 'Courses', 'ld-dashboard' ); ?>
						</h2>
						<strong class="learndash-statistics"><?php
							if ( learndash_is_group_leader_user() ) {
								$course_count = count( learndash_get_group_leader_groups_courses() );
							} else if ( learndash_is_admin_user() || ( is_user_logged_in() && $is_student != '1' ) ) {
								$course_count = $this->ld_dashboard_count_post_type( 'sfwd-courses' ); //learndash_get_courses_count();
							} else {
								$course_count = count( learndash_user_get_enrolled_courses( $user_id ) );
							}
							echo $course_count;
							?></strong>
					</div>
				</div>

				<?php
			}

			/*
			 * Display total Quizzes
			 */
			if ( isset( $ld_dashboard[ 'quizzes-count' ] ) && $ld_dashboard[ 'quizzes-count' ] == 1 ) {
				$sfwd_quiz_total = count_user_posts( $user_id, 'sfwd-quiz' );
				?>

				<div class="col-1-2 ld-dashboard-statistics learndash-quizzes" <?php if ( isset( $ld_dashboard[ 'quizzes-count-bgcolor' ] ) && $ld_dashboard[ 'quizzes-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'quizzes-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php echo _x( 'quizzes', 'Quizzes', 'ld-dashboard' ); ?>
						</h2>
						<strong class="learndash-statistics"><?php
							if ( learndash_is_group_leader_user() ) {
								$course_count = $sfwd_quiz_total;
							} else if ( learndash_is_admin_user() || ( is_user_logged_in() && $is_student != '1' ) ) {
								$course_count = $this->ld_dashboard_count_post_type( 'sfwd-quiz' ); //learndash_get_courses_count();
							} else {
								$total_quizzes	 = $this->ld_dashboard_get_student_data( $user_id );
								$course_count	 = $total_quizzes[ 'quiz_completed' ];
							}
							echo $course_count;
							?></strong>
					</div>
				</div>
				<?php
			}

			/*
			 * Display Assignments Pending Count
			 */
			if ( isset( $ld_dashboard[ 'assignments-count' ] ) && $ld_dashboard[ 'assignments-count' ] == 1 ) {
				?>
				<div class="col-1-2 ld-dashboard-statistics learndash-assignments" <?php if ( isset( $ld_dashboard[ 'assignments-count-bgcolor' ] ) && $ld_dashboard[ 'assignments-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'assignments-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php
							$assignments_completed = 0;
							if ( isset( $ld_dashboard[ 'assignments-completed-count' ] ) && $ld_dashboard[ 'assignments-completed-count' ] == 1 ) {
								esc_html_e( 'Assignments Completed', 'ld-dashboard' );
								$assignments_completed = 1;
							} else {
								esc_html_e( 'Assignments Pending', 'ld-dashboard' );
							}
							?>
						</h2>
						<strong class="learndash-statistics">
							<?php
							if ( $is_student == 1 ) {
								$assignments_args = array(
									'post_type'		 => 'sfwd-assignment',
									'post_status'	 => 'publish',
									'fields'		 => 'ids',
									'author'		 => $user_id,
									'meta_query'	 => array(
										array(
											'key'		 => 'approval_status',
											'compare'	 => ( $assignments_completed ) ? 1 : 'NOT EXISTS',
										),
									),
								);
								echo learndash_get_assignments_pending_count( $assignments_args );
							} else {

								$assignments_args = array(
									'post_type'		 => 'sfwd-assignment',
									'post_status'	 => 'publish',
									'fields'		 => 'ids',
									'meta_query'	 => array(
										array(
											'key'		 => 'approval_status',
											'compare'	 => ( $assignments_completed ) ? 1 : 'NOT EXISTS',
										),
									),
								);

								if ( in_array( 'ld_instructor', (array) $current_user->roles ) ) {
									$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

									$cousres	 = $wpdb->get_results( $get_courses_sql );
									$course_ids	 = array( 0 );
									if ( !empty( $cousres ) ) {
										$course_ids = array();
										foreach ( $cousres as $course ) {
											$course_ids[] = $course->ID;
										}
									}
									$assignments_args[ 'meta_query' ][] = array(
										'key'		 => 'course_id',
										'value'		 => $course_ids,
										'compare'	 => 'IN'
									);
								}
								echo learndash_get_assignments_pending_count( $assignments_args );
							}
							?>
						</strong>
					</div>
				</div>
				<?php
			}

			/*
			 * Display essays pending count
			 */
			if ( isset( $ld_dashboard[ 'essays-pending-count' ] ) && $ld_dashboard[ 'essays-pending-count' ] == 1 ) {
				?>
				<div class="col-1-2 ld-dashboard-statistics learndash-essays" <?php if ( isset( $ld_dashboard[ 'essays-pending-count-bgcolor' ] ) && $ld_dashboard[ 'essays-pending-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'essays-pending-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php esc_html_e( 'Essays Pending', 'ld-dashboard' ); ?>
						</h2>
						<strong class="learndash-statistics">
							<?php
							if ( $is_student == 1 ) {
								$essays_args = array(
									'post_type'		 => 'sfwd-essays',
									'post_status'	 => 'not_graded',
									'fields'		 => 'ids',
									'author'		 => $user_id,
								);
								echo learndash_get_essays_pending_count( $essays_args );
							} else {
								echo learndash_get_essays_pending_count();
							}
							?>
						</strong>
					</div>
				</div>
				<?php
			}

			/*
			 * Display total lessons count
			 */
			if ( isset( $ld_dashboard[ 'lessons-count' ] ) && $ld_dashboard[ 'lessons-count' ] == 1 && ( $is_student != '1' && !learndash_is_group_leader_user() ) ) {
				?>

				<div class="col-1-2 ld-dashboard-statistics learndash-lessons" <?php if ( isset( $ld_dashboard[ 'lessons-count-bgcolor' ] ) && $ld_dashboard[ 'lessons-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'lessons-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php echo _x( 'lessons', 'Lessons', 'ld-dashboard' ); ?>
						</h2>
						<strong class="learndash-statistics"><?php
							if ( learndash_is_admin_user() || ( is_user_logged_in() && $is_student != '1' ) ) {
								$course_count = $this->ld_dashboard_count_post_type( 'sfwd-lessons' ); //learndash_get_courses_count();
							} else {
								$course_count = 0;
							}
							echo $course_count;
							?></strong>
					</div>
				</div>

				<?php
			}

			/*
			 * Display total topics count
			 */
			if ( isset( $ld_dashboard[ 'topics-count' ] ) && $ld_dashboard[ 'topics-count' ] == 1 && ( $is_student != '1' && !learndash_is_group_leader_user() ) ) {
				?>

				<div class="col-1-2 ld-dashboard-statistics learndash-topics" <?php if ( isset( $ld_dashboard[ 'topics-count-bgcolor' ] ) && $ld_dashboard[ 'topics-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'topics-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php echo _x( 'topics', 'Topics', 'ld-dashboard' ); ?>
						</h2>
						<strong class="learndash-statistics"><?php
							if ( learndash_is_admin_user() || ( is_user_logged_in() && $is_student != '1' ) ) {
								$course_count = $this->ld_dashboard_count_post_type( 'sfwd-topic' ); //learndash_get_courses_count();
							} else {
								$course_count = 0;
							}
							echo $course_count;
							?></strong>
					</div>
				</div>

				<?php
			}


			/*
			 * Display total Student count
			 */
			if ( isset( $ld_dashboard[ 'student-count' ] ) && $ld_dashboard[ 'student-count' ] == 1 && $is_student != '1' ) {
				?>

				<div class="col-1-2 ld-dashboard-statistics learndash-students" <?php if ( isset( $ld_dashboard[ 'student-count-bgcolor' ] ) && $ld_dashboard[ 'student-count-bgcolor' ] != '' ) { ?> style="background-color: <?php echo esc_attr( $ld_dashboard[ 'student-count-bgcolor' ] ); ?>" <?php } ?>>
					<div class="statistics-inner">
						<h2 class="statistics-label">
							<?php esc_html_e( 'Students', 'ld-dashboard' ); ?>
						</h2>
						<strong class="statistics"><?php
							if ( learndash_is_group_leader_user() ) {
								$student_count = count( learndash_get_group_leader_groups_users() );
							} else if ( ( learndash_is_admin_user() ) && ( current_user_can( 'list_users' ) ) || ( is_user_logged_in() && $is_student != '1' ) ) {
								$student_count = $this->ld_dashboard_get_users_count();
							} else {
								$student_count = 0;
							}
							echo $student_count;
							?></strong>
					</div>
				</div>

				<?php
			}
			?>
		</div><!-- .ld-dashboard-statistics-container -->
		<?php
		if ( ld_check_if_author_is_instructor( get_current_user_id() ) ) {
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-instructor-earning-stats.php';
		}
		if ( isset( $ld_dashboard[ 'course-progress' ] ) && $ld_dashboard[ 'course-progress' ] == 1 && $is_student != 1 ) {
			/* Course Progress Report */
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-course-report.php';
		} else {
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-student-course-report.php';
		}

		if ( isset( $ld_dashboard[ 'instructor-statistics' ] ) && $ld_dashboard[ 'instructor-statistics' ] == 1 && $is_student != 1 ) {
			/* Insttuctor Statistics */
			//include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-instructor-stats.php';

			/* Insttuctor Student statistic */
			include LD_DASHBOARD_PLUGIN_DIR . 'templates/ld-dashboard-student-status.php';
		}

		if ( $is_student != 1 ) {
			echo do_shortcode( '[ld_email]' );
		}

		echo do_shortcode( '[ld_message]' );
		
		do_action( 'ld_dashboard_after_content' );
	}

	
	?>
</div>