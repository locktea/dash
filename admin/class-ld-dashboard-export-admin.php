<?php

class Ld_Dashboard_Export_Admin {
	
	public function __construct( ) {
		
		add_action( 'admin_init', array($this, 'ld_dashboard_export_instructor_commission'), 0 );		
	}
	
	public function ld_dashboard_export_instructor_commission() {
		
		if ( isset($_GET['ld-export']) && $_GET['ld-export'] == 'instructor-commission' && isset($_GET['instructor-id']) && $_GET['instructor-id'] != '' ) {
			global $ld_plugin_public;
			
			$instructor_id			 = sanitize_text_field( $_GET[ 'instructor-id' ] );
			$user				 = wp_get_current_user();
			
			$course_purchase_data = get_user_meta( $instructor_id, 'course_purchase_data', true );
						
			$instructor_info = get_userdata( $instructor_id );			
			$file = $instructor_info->user_login . "-instructor-commission.csv";
			$ld_dir_path = LD_DASHBOARD_PLUGIN_DIR. 'public/csv/'; // change the path to fit your websites document structure
			$fp = fopen($ld_dir_path.$file, "a")or die("Error Couldn't open $file for writing!");
			
			
			fputcsv($fp, array('Order#', 'Course Name','Your Earning','Actual Price', 'Admin Commission %', 'Payment Type' ));
			foreach( $course_purchase_data as $key => $value ) {
				$course_pricing = learndash_get_course_price( $value['course']);
				$instructor_earning = ( $course_pricing['price'] * (100- $value['commission']) ) / 100;
				$fields = array($value['order_id'], get_the_title($value['course']),$instructor_earning, $course_pricing['price'], $value['commission'], $value['payment_type']);
				fputcsv($fp, $fields);
			}
			$instructor_total_earning = (int)get_user_meta( $instructor_id, 'instructor_total_earning', true );
			$instructor_paid_earning = (int)get_user_meta( $instructor_id, 'instructor_paid_earning', true );
			$instructor_unpaid_earning = $instructor_total_earning - $instructor_paid_earning;
			
			$fields = array('', 'Total Earning', $instructor_total_earning, '', '');
			fputcsv($fp, $fields);
			$fields = array('', 'Paid Earning', $instructor_paid_earning, '', '');
			fputcsv($fp, $fields);
			$fields = array('', 'Unpaid Earning', $instructor_unpaid_earning, '', '');
			fputcsv($fp, $fields);			
			
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

if ( is_admin() ) {
	new Ld_Dashboard_Export_Admin();
}