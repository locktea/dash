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
			'role' => 'ld_instructor',
		);
$instructors = get_users( $args );
//echo '<pre>'; print_r( $instructors ); echo '</pre>';
?>
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">						
		<div class="ld-dashboard-content container">
			<?php if( $instructors && is_array( $instructors )){ ?>
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'ld_dashboard_general_settings' );
				do_settings_sections( 'ld_dashboard_general_settings' );
				?>
				<table id="ld-instructor-commission-update-tbl" class="ld-instructor-commission-update-tbl">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Name', 'ld-dashboard' ); ?></th>
							<th><?php esc_html_e( 'User email', 'ld-dashboard' ); ?></th>
							<th><?php esc_html_e( 'Commission %', 'ld-dashboard' ); ?></th>
							<th><?php esc_html_e( 'Update', 'ld-dashboard' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $instructors as $key => $ins_data ) { ?>
							<?php 
								$instructor_commission = ld_if_instructor_course_commission_set( $ins_data->ID );
								if( !$instructor_commission ) {
									$instructor_commission = ld_get_global_commission_rate();
								}
							?>
							<tr>
								<td><?php echo ( isset( $ins_data->data->display_name ) )?esc_html( $ins_data->data->display_name ):esc_html( $ins_data->data->user_login ); ?></td>
								<td><?php echo esc_html( $ins_data->data->user_email ); ?></td>
								<td>
									<input type="number" id="ld-commission-<?php echo $ins_data->ID; ?>" class="ld-commission-val" value="<?php echo esc_attr($instructor_commission); ?>" min="0" max="100">
								</td>
								<td>
									<a class="button button-primary ld-update-instructor-commision" data-id="ld-commission-<?php echo $ins_data->ID; ?>" data-instructor-id="<?php echo esc_attr( $ins_data->ID ); ?>"><?php esc_html_e( 'Update', 'ld-dashboard' ); ?></a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</form>
			<?php }else{ ?>
				<div class="no-instructors-found"><?php esc_html_e('No instructors are registered on the site.','ld-dashboard'); ?></div>
			<?php } ?>
		</div>
	</div>
</div>