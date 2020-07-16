<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $students;

$curr_user_id = get_current_user_id();
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
				//'meta_key'   => 'is_student',
				//'meta_value' => true,
				'orderby'    => 'user_nicename',
				'order'      => 'ASC',
				'fields'     => array( 'ID', 'display_name' ),
				'include'	=> $course_student_ids
			);	
}
$students     = get_users( $args );
if ( learndash_is_group_leader_user() && empty($group_student) ) {
	$students = array();
}

/* Check Student enrolled in any courses */
$isstudent = false;
if ( !empty($students)) { 
	foreach ( $students as $student ) { 
		$course_ids = learndash_user_get_enrolled_courses( $student->ID );
		if ( !empty($course_ids)) {
			$isstudent = true;
			break;
		}
	}
}


$loader  = includes_url( 'images/spinner-2x.gif' );
?>
<div class="ld-dashboard-student-status">
	<div class="ld-dashboard-seperator"><span><?php esc_html_e( 'Student Details', 'ld-dashboard' ); ?></span></div>

	<?php do_action( 'ld_dashboard_student_status_before', $curr_user_id ); ?>
	<?php if ( ! empty( $students ) && $isstudent == true ) { ?>
		<div class="ld-dashboard-student-status-block">
			<div class="ld-dashboard-student-lists">
				<select name="ld-dashboard-student" class="ld-dashboard-student">
					<?php foreach ( $students as $student ) { 
						$course_ids = learndash_user_get_enrolled_courses( $student->ID );
						if ( !empty($course_ids)) :?>
						<option value="<?php echo esc_attr($student->ID); ?>" ><?php echo esc_html($student->display_name); ?></option>
					<?php 
						endif;
					}?>
				</select>
			</div>
			<div class="ld-dashboard-student-loader">
				<img src="<?php echo apply_filters( 'ld_dashboard_loader_img_url', $loader ); ?>">
				<p><?php echo apply_filters( 'ld_dashboard_waiting_text', __( 'Please wait, while details are loading...', 'ld-dashboard' ) ); ?></p>
			</div>
			<div class="ld-dashboard-student-details"></div>
		</div>
	<?php } else { ?>
		<div class="ld-dashboard-student-status-no-students ld-dashboard-info">
			<p>
			<?php
			if ( in_array( 'ld_instructor', (array) $user->roles ) ) {
				echo apply_filters( 'ld_dashboard_student_status_no_student_instructor_message', __( 'Please make sure your course is enrolled by students.', 'ld-dashboard' ) );
			} else {
				echo apply_filters( 'ld_dashboard_student_status_no_student_admin_message', __( 'No registered student on the site.', 'ld-dashboard' ) );
			}
				?>
			</p>
		</div>
	<?php } ?>
	<?php do_action( 'ld_dashboard_student_status_after', $curr_user_id ); ?>
</div>
