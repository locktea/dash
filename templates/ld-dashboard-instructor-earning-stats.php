<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$instructor_id = get_current_user_id();
$instructor_total_earning = (int)get_user_meta( $instructor_id, 'instructor_total_earning', true );
$instructor_paid_earning = (int)get_user_meta( $instructor_id, 'instructor_paid_earning', true );
$instructor_unpaid_earning = $instructor_total_earning - $instructor_paid_earning;

$monthly_earning = instructor_monthy_commission_earning( $instructor_id );

$course_wise_earning = ld_instructor_course_wise_admin_commission( $instructor_id );
$ins_course_earning = ld_instructor_course_wise_earning( $instructor_id );
// echo '<pre>'; print_r( $course_wise_earning ); echo '</pre>';
// echo '<pre>'; print_r( $ins_course_earning ); echo '</pre>';
// echo '<pre>'; print_r( $ins_earning ); echo '</pre>';

?>
<?php if( $monthly_earning || $ins_course_earning ): ?>
<div class="ld-dashboard-instructor-earning">
	<?php if( $monthly_earning ): ?>
		<h3 class="ldid-dashboard-title"><?php esc_html_e( 'Instructor Statistics', 'ld-dashboard' ); ?></h3>
		<div id="ins-earning-stats"></div>
	<?php endif; ?>
	<?php if( $ins_course_earning ): ?>
		<div class="ins-cw-earning-wrap">
			<div class="ins-cw-earning-table">
				<table>
					<thead>
						<th><?php esc_html_e( 'Course', 'ld-dashboard' ); ?></th>
						<th><?php esc_html_e( 'Earning', 'ld-dashboard' ); ?></th>
					</thead>
					<tbody>
						<?php foreach ($ins_course_earning as $course_id => $earning_data) { ?>
						<tr>
							<td><?php echo $earning_data['title']; ?></td>
							<td><?php echo $earning_data['earning']; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div id="ins-cw-earning-chart"></div>
		</div>
	<?php endif; ?>
</div>
<?php endif; ?>