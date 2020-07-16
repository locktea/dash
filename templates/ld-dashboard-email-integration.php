<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Get User loggedin wise courses from ld dashboard course report template file
 * Get User loggedin wise Students from ld dashboard students status template file
 */
global $courses, $students, $wpdb;
if ( empty($courses)) {
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
			'post__in'		 => ( !empty($group_course) ) ? $group_course : array(0)
		);
	} else {
		
		$get_courses_sql = "select ID from {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta pm6 ON ( {$wpdb->prefix}posts.ID = pm6.post_id ) where ( post_author={$user_id} OR ( pm6.meta_key = '_ld_instructor_ids' AND pm6.meta_value REGEXP '.*;s:[0-9]+:\"{$user_id}\"*' ) ) AND post_type='sfwd-courses' AND {$wpdb->prefix}posts.post_status = 'publish' Group By {$wpdb->prefix}posts.ID";

		$cousres = $wpdb->get_results( $get_courses_sql );
		$course_ids = array(0);
		if ( !empty($cousres) ) {
			$course_ids = array();
			foreach ($cousres as $course ) {
				$course_ids[] = $course->ID;
			}
		}
		$args = array(
			'post_type'		 => 'sfwd-courses',
			//'author'		 => get_current_user_id(),
			'post__in' 		 => $course_ids,
			'post_status'	 => 'publish',
			'posts_per_page' => -1,
		);
	}

	$courses = get_posts( $args );
}


if ( empty($student) ) {
	 $curr_user_id = get_current_user_id();
	if ( learndash_is_group_leader_user() ) {
		$group_student = learndash_get_group_leader_groups_users();
		
		$args = array(				
					'orderby'   => 'user_nicename',
					'order'     => 'ASC',
					'fields'    => array( 'ID', 'display_name' ),
					'include'	=> ( !empty($group_student) ) ? $group_student : array(0)
				);
		
	} else if ( learndash_is_admin_user()   ) {
		$args = array(
					//'meta_key'   => 'is_student',
					//'meta_value' => true,
					'orderby'    => 'user_nicename',
					'order'      => 'ASC',
					'fields'     => array( 'ID', 'display_name' ),
				);
	} else {
		$instructor_students = $this->ld_dashboard_get_instructor_students_by_id( $curr_user_id );
		$course_student_ids = array(0);
		if ( ! empty( $instructor_students ) ) {
			$course_student_ids = array();
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
	$students     = get_users( $args );
}
?>


<div id="ld-dashboard-email" class="ld-dashboard-email ld-dashboard-email-section">
	<div class="ld-dashboard-seperator">
		<span><?php esc_html_e( 'Email', 'ld-dashboard' ); ?></span>
	</div>
	<div class="ld-dashboard-email-content">
		<form id="ld-dashboard-email-frm" action="" method="post">
			<fieldset>
				<select name="ld-email-cource[]" multiple id="ld-email-cource" class="ld-dashboard-select" data-placeholder="<?php esc_html_e( 'Select Course', 'ld-dashboard');?>">
					<?php foreach ( $courses as $index => $course ) { ?>
						<option value="<?php echo esc_attr( $course->ID ); ?>">
							<?php echo esc_html( $course->post_title ); ?>
						</option>
					<?php } ?>
				</select>
			</fieldset>

			<fieldset class="ld-email-course-students">
				<select name="ld-email-students[]" multiple id="ld-email-students" class="ld-dashboard-select" data-placeholder="<?php esc_html_e( 'Select Students', 'ld-dashboard');?>">
					<?php foreach ( $students as $student ) { 
						$course_ids = learndash_user_get_enrolled_courses( $student->ID );
						if ( !empty($course_ids)) :?>
						<option value="<?php echo esc_attr($student->ID); ?>" ><?php echo esc_html($student->display_name); ?></option>
					<?php 
						endif;
					}?>
				</select>
				<span id="ld-email-student-loader" style="display:none;"><img src="<?php echo LD_DASHBOARD_PLUGIN_URL. "public/img/wpspin-2x.gif";?>" /></span>
			</fieldset>

			<fieldset>
				<input type="text" name="ld-email-subject" value="" placeholder="<?php esc_html_e( 'Please enter email subject', 'ld-dashboard' );?>" id="ld-email-subject" class="ld-email-text"/>
			</fieldset>

			<fieldset>
				<?php
				$args     = array(
								'media_buttons' => false,
								'editor_height' => 200,
								'tinymce'       => array(
														'toolbar1' => 'bold,italic,strikethrough,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,|,spellchecker,wp_adv',
														'toolbar2' => '',
													),
							);
				wp_editor( '', 'ld-email-message', $args );
				?>
			</fieldset>

			<fieldset>
				<button name="submit" id="ld-email-send" class="ld_email_send" ><?php _e( 'Send Email', 'ld-dashboard' ); ?></button>
				<span id="ld-email-loader" style="display:none;"><img src="<?php echo LD_DASHBOARD_PLUGIN_URL. "public/img/wpspin-2x.gif";?>" /></span>
			</fieldset>
		</form>

	</div>

</div>