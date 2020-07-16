<?php

/**
 * Provide a admin area view for setting instructor commission.
 *
 * This file is used to markup the instructor commission aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/admin/partials
 */

$args = array(
	'fields'     => array('ID','display_name'),
	'role'		 => 'ld_instructor',
	'posts_per_page' => -1,
);
$instructors = get_users( $args );
//echo '<pre>'; print_r( $instructors ); echo '</pre>';
?>
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">						
		<div class="ld-dashboard-content container">
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'ld_dashboard_comm_report_settings' );
				do_settings_sections( 'ld_dashboard_comm_report_settings' );
				?>
				<?php if( is_array( $instructors ) ){ ?>
					<select name="ld-instructor-dropdown" id="ld-instructor-dropdown">
						<option value='select'><?php esc_html_e('Select Instructor','ld-dashboard') ?></option>
						<?php
							foreach ( $instructors as $instructor ) {
								echo '<option value="'.$instructor->ID.'">'.$instructor->display_name.'</option>';
							}
						?>
					</select>
					<p class="description"><?php esc_html_e('Select instructor to display commission report.','ld-dashboard'); ?></p>
					<table id="ld-instructor-commission-report" class="ld-instructor-commission-report">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Order ID', 'ld-dashboard' ); ?></th>
								<th><?php esc_html_e( 'Course Name', 'ld-dashboard' ); ?></th>
								<th><?php esc_html_e( 'Actual Price', 'ld-dashboard' ); ?></th>
								<th><?php esc_html_e( 'Admin Commission %', 'ld-dashboard' ); ?></th>
								<th><?php esc_html_e( 'Payment Type', 'ld-dashboard' ); ?></th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				<?php }else{ ?>
					<div class="no-instructors"><?php esc_html_e('No instructors registered in the site', 'ld-dashboard') ?></div>
				<?php } ?>	
			</form>
			<div class="ld-instructor-dialog">
				<div class="ld-instructor-dialog-container">
					<div class="ld-instructor-dialog-header">
						<?php esc_html_e('Instructor Data','ld-dashboard'); ?><i class="fa fa-check"></i>
					</div>
					<div class="ld-instructor-dialog-msg">
						<div class="ld-instructor-dialog-desc">
							<div class="ld-instructor-dialog-div">
								<div class="ld-dialog-label move-left">
									<?php esc_html_e('Paid Earning','ld-dashboard'); ?>
								</div>
								<div class="ld-dialog-paid-earning move-right"></div>
								<div class="ld-dialog-label move-left">
									<?php esc_html_e('Unpaid Earning','ld-dashboard'); ?>
								</div>
								<div class="ld-dialog-unpaid-earning move-right"></div>
								<div class="ld-dialog-label move-left">
									<?php esc_html_e('Enter amount','ld-dashboard'); ?>
								</div>
								<div class="ld-dialog-pay-amount move-right">
									<input type="number" name="ld-pay-amount" id="ld-pay-amount" min="0">
								</div>
							</div>
							<input type="hidden" id="ld-instructor-id" value="">
							<input type="hidden" id="ld-paid-earning" value="">
							<input type="hidden" id="ld-unpaid-earning" value="">
							<input type="hidden" id="ld-total-earning" value="">
							<div class="ld-pay-error"><?php esc_html_e('Please enter an amount less than unpaid amount.','ld-dashboard'); ?></div>
						</div>
					</div>
					<ul class="ld-instructor-dialog-buttons">
						<li>
							<a class="ld-instructor-trigger-pay">
								<?php esc_html_e( 'Pay', 'ld-dashboard' ); ?>
							</a>
						</li>
						<li>
							<a class="ld-instructor-dialog-cancel">
								<?php esc_html_e( 'Cancel', 'ld-dashboard' ); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>