<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$instructor_id = get_current_user_id();
$instructor_total_earning = (int)get_user_meta( $instructor_id, 'instructor_total_earning', true );
$instructor_paid_earning = (int)get_user_meta( $instructor_id, 'instructor_paid_earning', true );
$instructor_unpaid_earning = $instructor_total_earning - $instructor_paid_earning;
?>
<?php if( $instructor_total_earning ){ ?>
	<div id="ld-instructor-paid-unpaid-earning" style="width: 100%; height: 400px;"></div>
<?php } ?>
 