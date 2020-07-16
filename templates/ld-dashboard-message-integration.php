<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Get User loggedin wise courses from ld dashboard course report template file
 * Get User loggedin wise Students from ld dashboard students status template file
 */
global $students;
if ( empty($student) ) {
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
					'orderby'    => 'user_nicename',
					'order'      => 'ASC',
					'fields'     => array( 'ID', 'display_name' ),
				);
	} else {
		$instructor_students = $this->ld_dashboard_get_instructor_students_by_id( $curr_user_id );
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

	$students     = get_users( $args );
}
?>

<div id="ld-dashboard-buddypress-message" class="ld-dashboard-buddypress-message ld-dashboard-buddypress-message-section">
	<div class="ld-dashboard-seperator">
		<span><?php esc_html_e( 'Message', 'ld-dashboard' ); ?></span>
	</div>
	<div class="ld-dashboard-buddypress-message-content">
		<form id="ld-dashboard-buddypress-message-frm" action="" method="post">			
			<fieldset>
				<select name="ld-buddypress-message-students[]" multiple id="ld-buddypress-message-students" class="ld-dashboard-select" data-placeholder="<?php esc_html_e( 'Select Students', 'ld-dashboard');?>">
					<?php foreach ( $students as $student ) { 
						$course_ids = learndash_user_get_enrolled_courses( $student->ID );
						if ( !empty($course_ids)) :?>
						<option value="<?php echo esc_attr($student->ID); ?>" ><?php echo esc_html($student->display_name); ?></option>
					<?php 
						endif;
					}?>
				</select>
			</fieldset>

			<fieldset>
				<input type="text" name="ld-buddypress-message-subject" value="" placeholder="<?php esc_html_e( 'Please enter message subject', 'ld-dashboard' );?>" id="ld-buddypress-message-subject" class="ld-buddypress-message-text"/>
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
				wp_editor( '', 'ld-buddypress-message-message', $args );
				?>
			</fieldset>

			<fieldset>
				<button name="submit" id="ld-buddypress-message-send" class="ld_buddypress-message_send" ><?php _e( 'Send Message', 'ld-dashboard' ); ?></button>
				<span id="ld-buddypress-message-loader" style="display:none;"><img src="<?php echo LD_DASHBOARD_PLUGIN_URL. "public/img/wpspin-2x.gif";?>" /></span>
			</fieldset>
		</form>

	</div>

</div>