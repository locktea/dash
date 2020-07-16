<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/includes
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Ld_Dashboard_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		
		/* Check My Dashboard page exists or not */		
		$my_dashboard_page = get_page_by_title( 'My Dashboard'  ) ;		
		if ( empty( $my_dashboard_page ) ) {
			// Manage Order Page
			$my_dashboard = wp_insert_post(
										array(
											'post_title'     => 'My Dashboard',
											'post_content'   => '[ld_dashboard]',
											'post_status'    => 'publish',
											'post_author'    => 1,
											'post_type'      => 'page',
											'comment_status' => 'closed'
										)
									);
			
			$reign_wbcom_metabox_data = array (
											  'layout' 			=> array (
																	'site_layout' => 'full_width',
																	'primary_sidebar' => '0',
																	'secondary_sidebar' => '0',
																),
											  'header_footer' 	=> array (
																	'elementor_topbar' => '0',
																	'elementor_header' => '0',
																	'elementor_footer' => '0',
																 ),
										);
			update_post_meta( $my_dashboard, 'reign_wbcom_metabox_data', $reign_wbcom_metabox_data );
		}
		
		/* Check Instructor Registration page exists or not */		
		$instructor_registration_page = get_page_by_title( 'Instructor Registration'  ) ;		
		if ( empty( $instructor_registration_page ) ) {
			// Manage Order Page
			$instructor_registration = wp_insert_post(
										array(
											'post_title'     => 'Instructor Registration',
											'post_content'   => '[ld_instructor_registration]',
											'post_status'    => 'publish',
											'post_author'    => 1,
											'post_type'      => 'page',
											'comment_status' => 'closed'
										)
									);
			
			$reign_wbcom_metabox_data = array (
											  'layout' 			=> array (
																	'site_layout' => 'full_width',
																	'primary_sidebar' => '0',
																	'secondary_sidebar' => '0',
																),
											  'header_footer' 	=> array (
																	'elementor_topbar' => '0',
																	'elementor_header' => '0',
																	'elementor_footer' => '0',
																 ),
										);
			update_post_meta( $instructor_registration, 'reign_wbcom_metabox_data', $reign_wbcom_metabox_data );
		}
		
		$general_settings    = get_option( 'ld_dashboard_general_settings' );				
		if ( empty($general_settings)) {
			$general_settings = array (
									  'instructor-total-sales' 			=> '1',
									  'instructor-total-sales-bgcolor' 	=> '#00A2E8',
									  'course-count' 					=> '1',
									  'course-count-bgcolor' 			=> '#00A2E8',
									  'quizzes-count' 					=> '1',
									  'quizzes-count-bgcolor' 			=> '#00A2E8',
									  'assignments-count' 				=> '1',
									  'assignments-completed-count' 	=> '1',
									  'assignments-count-bgcolor' 		=> '#00A2E8',
									  'essays-pending-count' 			=> '1',
									  'essays-pending-count-bgcolor' 	=> '#00A2E8',
									  'lessons-count' 					=> '1',
									  'lessons-count-bgcolor' 			=> '#00A2E8',
									  'topics-count' 					=> '1',
									  'topics-count-bgcolor' 			=> '#00A2E8',
									  'student-count' 					=> '1',
									  'student-count-bgcolor'	 		=> '#00A2E8',
									  'instructor-statistics' 			=> '1',
									  'course-progress' 				=> '1',
									  'enable-global-commission' 		=> '1',
									  'global-commission' 				=> '20',									  
									  'instructor_registration_page'	=> $instructor_registration
								);
			
			update_option( 'ld_dashboard_general_settings', $general_settings );
		}
		
		/*  LD Dashboard Integration*/
		$ld_dashboard_integration    = get_option( 'ld_dashboard_integration' );	
		if ( empty($ld_dashboard_integration)) {
			$ld_dashboard_integration = array(
											'enable-email-integration'		=> '0',
											'enable-group-integration'		=> '0',
											'enable-messaging-integration'	=> '0',
										);
			update_option( 'ld_dashboard_integration', $ld_dashboard_integration );
		}
		
		/*  LD Dashboard Page Mapping*/
		$ld_dashboard_page_mapping    = get_option( 'ld_dashboard_page_mapping' );	
		if ( empty($ld_dashboard_page_mapping)) {
			$ld_dashboard_page_mapping = array(
											'my_dashboard_page'				=> $my_dashboard,
											'instructor_registration_page'	=> $instructor_registration,
										);
			update_option( 'ld_dashboard_page_mapping', $ld_dashboard_page_mapping );
		}
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$charset_collate = $wpdb->get_charset_collate();
		
		
		/* Create LD Dashboard Email Logs table */
		$ld_dashboard_emails = $wpdb->prefix . 'ld_dashboard_emails';
		if($wpdb->get_var("show tables like '$ld_dashboard_emails'") != $ld_dashboard_emails) {

			$edd_sql = "CREATE TABLE $ld_dashboard_emails (
						id mediumint(9) NOT NULL AUTO_INCREMENT,						
						user_id mediumint(9) NOT NULL,
						email_subject text NOT NULL,
						email_message text NOT NULL,
						course_ids text NOT NULL,
						student_ids text NOT NULL,						
						created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,						
						UNIQUE KEY id (id)
			) $charset_collate;";
			dbDelta( $edd_sql );
		}		
	}

}


