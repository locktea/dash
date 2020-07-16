<?php

class Ld_Dashboard_Export {
	
	public function __construct( ) {
		
		add_action( 'init', array($this, 'ld_dashboard_export_course_progress'), 0 );
		add_action( 'init', array($this, 'ld_dashboard_export_student_progress'), 0 );
	}
	
	public function ld_dashboard_export_course_progress() {
		
		if ( isset($_GET['ld-export']) && $_GET['ld-export'] == 'course-progress' && isset($_GET['course-id']) && $_GET['course-id'] != '' ) {
			global $ld_plugin_public;
			
			$course_id			 = sanitize_text_field( $_GET[ 'course-id' ] );
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
				$course_access_users = $ld_plugin_public->ld_dashboard_get_instructor_students_by_id( $user->ID );
			}

			$course_userInfo = array();
			$uids			 = array();
			$user_data	     = array();
			if ( !empty( $course_access_users ) ) {
				foreach ( $course_access_users as $uid ) {
					$course_ids = learndash_user_get_enrolled_courses( $uid->ID );
					if ( !empty($course_ids) && in_array( $course_id, $course_ids)) {
						$course_userInfo[]	 = $ld_plugin_public->ld_dashboard_get_user_info( $uid->ID, $course_id );												
					}
				}
			}			
			
			$course_name = get_the_title($course_id);
			$file = sanitize_title($course_name) . "-student-progress.csv";
			$ld_dir_path = LD_DASHBOARD_PLUGIN_DIR. 'public/csv/'; // change the path to fit your websites document structure
			$fp = fopen($ld_dir_path.$file, "a")or die("Error Couldn't open $file for writing!");
			
			
			fputcsv($fp, array('Couse Name', 'User ID','UserName', 'User Email', 'Total Steps', 'Completed Steps','Progress', 'Completed On'));
			foreach( $course_userInfo as $user ) {				
				$fields = array($course_name, $user['userid'], $user['user_name'], $user['user_email'], $user['total_steps'], $user['completed_steps'], $user['completed_per']."%", $user['course_completed_on']);
				fputcsv($fp, $fields);
			}			
			
			fclose($fp); 

			ignore_user_abort(true);
			set_time_limit(0); // disable the time limit for this script			
			
			// change the path to fit your websites document structure
			$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $file); // simple file name validation
			$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
			$ld_dir_url = LD_DASHBOARD_PLUGIN_URL. 'public/csv/'; // change the path to fit your websites document structure
			$fullPath = $ld_dir_url.$dl_file;
		
			if ($fd = fopen ($fullPath, "r")) {
				$path_parts = pathinfo($fullPath);
				$ext = strtolower($path_parts["extension"]);
				switch ($ext) {
					case "csv":
					header("Content-type: application/csv");
					header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
					break;
					// add more headers for other content types here
					default;
					header("Content-type: application/octet-stream");
					header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
					break;
				}
				header("Cache-control: private"); //use this to open files directly
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
				}
			}
			fclose ($fd);
			unlink($ld_dir_path.$file);		
			exit;
		}
		
	}
	
	public function ld_dashboard_export_student_progress() {
		if ( isset($_GET['ld-export']) && $_GET['ld-export'] == 'student-progress' && isset($_GET['student-id']) && $_GET['student-id'] != '' ) {
			global $ld_plugin_public, $current_user;
			$user_id = get_current_user_id();
			$course_ids = array();
			$student_id = sanitize_text_field( $_GET[ 'student-id' ] );
			if ( learndash_is_group_leader_user() ) {
				$course_ids = learndash_get_group_leader_groups_courses();
			} elseif( in_array( 'ld_instructor', (array) $current_user->roles ) ){
				$args = array(
							'post_type'		 => 'sfwd-courses',
							'author'		 => $user_id,
							'post_status'	 => 'publish',
							'posts_per_page' => -1,
						);
				$courses = get_posts( $args );
				if ( !empty($courses) ) {
					foreach ( $courses as $index => $course ) {
						$course_ids[] = $course->ID;
					}
				}
				$student_course_ids = learndash_user_get_enrolled_courses( $student_id );
				$course_ids = array_intersect($course_ids, $student_course_ids); 

			}else {				
				$course_ids = learndash_user_get_enrolled_courses( $student_id );
			}
			
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
			
			
			$student_info = get_userdata( $student_id );			
			$file = $student_info->user_login . "-courses-progress.csv";
			$ld_dir_path = LD_DASHBOARD_PLUGIN_DIR. 'public/csv/'; // change the path to fit your websites document structure
			$fp = fopen($ld_dir_path.$file, "a")or die("Error Couldn't open $file for writing!");
			
			
			fputcsv($fp, array('Couse Name', 'Total Steps', 'Completed Steps','Progress'));
			foreach ( $student_courses as $course_id ) :
			
				$course_progress_data	 = $ld_plugin_public->ld_dashboard_check_course_progress_data( $student_id, $course_id );
				$course_progress		 = ( isset( $course_progress_data[ 'percentage' ] ) ) ? $course_progress_data[ 'percentage' ] : 0;
				$total_steps			 = ( isset( $course_progress_data[ 'total_steps' ] ) ) ? $course_progress_data[ 'total_steps' ] : 0;
				$completed_steps		 = ( isset( $course_progress_data[ 'completed_steps' ] ) ) ? $course_progress_data[ 'completed_steps' ] : 0;
				
				$fields = array(get_the_title( $course_id ), $total_steps, $completed_steps, $course_progress);
				fputcsv($fp, $fields);

			endforeach;
			fclose($fp); 

			ignore_user_abort(true);
			set_time_limit(0); // disable the time limit for this script			
			
			// change the path to fit your websites document structure
			$dl_file = preg_replace("([^\w\s\d\-_~,;:\[\]\(\].]|[\.]{2,})", '', $file); // simple file name validation
			$dl_file = filter_var($dl_file, FILTER_SANITIZE_URL); // Remove (more) invalid characters
			$ld_dir_url = LD_DASHBOARD_PLUGIN_URL. 'public/csv/'; // change the path to fit your websites document structure
			$fullPath = $ld_dir_url.$dl_file;
		
			if ($fd = fopen ($fullPath, "r")) {
				$path_parts = pathinfo($fullPath);
				$ext = strtolower($path_parts["extension"]);
				switch ($ext) {
					case "csv":
					header("Content-type: application/csv");
					header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
					break;
					// add more headers for other content types here
					default;
					header("Content-type: application/octet-stream");
					header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
					break;
				}
				header("Cache-control: private"); //use this to open files directly
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
				}
			}
			fclose ($fd);
			unlink($ld_dir_path.$file);		
			
			exit;
		}
	}
	
	
}

$plugin_public = new Ld_Dashboard_Export();