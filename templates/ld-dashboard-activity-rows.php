<?php
/**
 * LD Dashboard Activity Template
 */

$activity_steps_completed = ( isset($activity->activity_meta['steps_completed']) )?intval( $activity->activity_meta['steps_completed'] ):''; 
$activity_steps_total  = ( isset($activity->activity_meta['steps_total']) )?intval( $activity->activity_meta['steps_total'] ):'';

if ( current_user_can( 'edit_user', $activity->user_id ) ) { 
	$user_link = get_edit_user_link( $activity->user_id ) ."#ld_course_info";
} else {
	$user_link = "#";
}

if ( ( !empty( $activity->activity_completed ) ) && ( !empty( $activity->activity_started ) ) ) { 
	$activity_diff_completed = learndash_get_activity_human_time_diff( $activity->activity_started, $activity->activity_completed, 1 );
} else {
	$activity_diff_completed = 0;
}

if ( !empty( $activity_diff_completed ) ) {
	$activity_abbr_label_completed = __('Completed Date (Duration)', 'ld-dashboard');
} else {
	$activity_abbr_label_completed = __('Completed Date', 'ld-dashboard');
}

if ( !empty( $activity->activity_started ) ) { 
	$activity_diff_started = learndash_get_activity_human_time_diff( $activity->activity_started, time(), 1 );
} else {
	$activity_diff_started = 0;
	$activity_diff_started = learndash_get_activity_human_time_diff( $activity->activity_updated, time(), 1 );
}

if ( !empty( $activity_diff_started ) ) {
	$activity_abbr_label_started = __('Started Date (Duration)', 'ld-dashboard');
} else {
	$activity_abbr_label_started = __('Started Date', 'ld-dashboard');
}

if ( 'quiz' == $activity->activity_type ) : ?>
	<div class="activity-item quiz">
		<?php 
		$course = $this->ld_dashboard_get_activity_course( $activity );
		if ( $course ){
			echo "<p>" . sprintf( __( '%1$s completed a quiz %2$s of course %3$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title, get_the_title( $course->ID )  ) . "</p>";
		}else{
			echo "<p>" . sprintf( __( '%1$s completed a quiz %2$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title) . "</p>";
		}
		
		echo "<i>" . sprintf( __( '%1$s ago','ld-dashboard'), $activity_diff_started ) . "</i>" ;
		?>
	</div>

<?php endif; ?>



<?php if ( 'course' == $activity->activity_type && $activity_steps_completed == $activity_steps_total) : ?>

	<div class="activity-item course">
		<?php echo sprintf( __( '%1$s completed a course %2$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title  );?>
	</div>

<?php endif; ?>

<?php if ( 'access' == $activity->activity_type ) : ?>

	<div class="activity-item course">
		<?php
		echo "<p>" . sprintf( __( '%1$s enrolled for a course %2$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title ) . "</p>";;
		echo "<i>" . sprintf( __( '%1$s ago','ld-dashboard'), $activity_diff_started ) . "</i>" ;
		?>		
	</div>

<?php endif; ?>



<?php if ( 'lesson' == $activity->activity_type ) : ?>

	<div class="activity-item lesson">
		<?php 
		$course = $this->ld_dashboard_get_activity_course( $activity );
		
		echo "<p>" . sprintf( __( '%1$s completed a lesson %2$s of course %3$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title, get_the_title( $course->ID )  ) . "</p>";;
		echo "<i>" . sprintf( __( '%1$s ago','ld-dashboard'), $activity_diff_started ) . "</i>" ;
		?>
	</div>

<?php endif; ?>



<?php if ( 'topic' == $activity->activity_type ) : ?>
	<div class="activity-item topic">
		<?php 
		$course = $this->ld_dashboard_get_activity_course( $activity );
		
		echo "<p>" . sprintf( __( '%1$s completed a topic %2$s of course %3$s' , 'ld-dashboard') , $activity->user_display_name, $activity->post_title, get_the_title( $course->ID )  ) . "</p>";;
		echo "<i>" . sprintf( __( '%1$s ago','ld-dashboard'), $activity_diff_started ) . "</i>" ;
		?>		
	</div>

<?php endif; ?>


<?php if ( is_null( $activity->activity_type ) ) : ?>

	<div class="activity-item not-started">
		<div class="header">
			<span class="user"><i class="fa fa-user"></i><a href="<?php echo $user_link; ?>" title="<?php esc_attr_e( 'See User Progress', 'ld-dashboard' ); ?>"><?php echo $activity->user_display_name; ?></a></span>
		</div>

		<div class="content">
			<?php if ( $course = $this->ld_dashboard_get_activity_course( $activity ) ) : ?>
				<i class="fa fa-graduation-cap"></i>
				<strong><?php echo sprintf( _x('%s:', 'Course:', 'ld-dashboard' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></strong> <strong><a href="<?php echo get_permalink( $course->ID ); ?>" class="link"><?php echo get_the_title( $course->ID ); ?></a></strong><?php
					edit_post_link(
						sprintf(
							/* translators: %s: Name of current post */
							__( ' (edit<span class="screen-reader-text"> "%s"</span>)', 'ld-dashboard' ),
							get_the_title( $course->ID )
						),
						'<span class="ld-dashboard-edit-link edit-link">',
						'</span>',
						$course->ID
					);
				?><br/>
			<?php endif; ?>

			<strong><?php esc_html_e( 'Result:', 'ld-dashboard' ); ?> </strong><?php esc_html_e( 'Not Started', 'ld-dashboard' ); ?>
		</div>
	</div>

<?php endif; ?>
