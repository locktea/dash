<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $courses, $wpdb;
$user_id = get_current_user_id();
$user	 = wp_get_current_user();



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
		//'author'		 => $user_id,
		'post__in' 		 => $course_ids,
		'post_status'	 => 'publish',
		'posts_per_page' => -1,
	);
}

$courses = get_posts( $args );

if ( learndash_is_group_leader_user() && empty($group_course) ) {
	$courses = array();
}

$loader = includes_url( 'images/spinner-2x.gif' );
?>
<div class="ld-dashboard-course-report">
	<div class="ld-dashboard-seperator"><span><?php esc_html_e( 'Course Details', 'ld-dashboard' ); ?></span></div>

	<?php
	do_action( 'ld_dashboard_course_report_before', $user_id );

	if ( !empty( $courses ) ) {
		?>

		<div class="ld-dashboard-courses">
			<select id="ld-dashboard-courses-id">
				<?php foreach ( $courses as $index => $course ) { ?>
					<option value="<?php echo esc_attr( $course->ID ); ?>"><?php echo esc_html( $course->post_title ); ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="ld-dashboard-loader">
			<img src="<?php echo apply_filters( 'ld_dashboard_loader_img_url', $loader ); ?>">
			<p><?php echo apply_filters( 'ld_dashboard_waiting_text', __( 'Please wait, while details are loading...', 'ld-dashboard' ) ); ?></p>
		</div>
		<div class="ld-dashboard-course-details"></div>

	<?php } else { ?>

		<div class="ld-dashboard-no-courses ld-dashboard-info">
			<p><?php echo apply_filters( 'ld_dashboard_no_course_created_text', sprintf( __( 'There is no %s yet!', 'ld-dashboard' ), 'course' ) ); ?></p>
		</div>

		<?php
	}

	do_action( 'ld_dashboard_course_report__after', $user_id );
	?>

</div>