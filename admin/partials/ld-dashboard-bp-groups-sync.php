<?php

/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com/plugins
 * @since      1.0.0
 *
 * @package    Ld_Dashboard
 * @subpackage Ld_Dashboard/admin/partials
 */

$ld_dashboard_bp_groups_settings = get_option('ld_dashboard_bp_groups_settings');

global $bp;
$saved_group_data = '';
$per_page         = 10;
$group_args = array(
	'order'    => 'DESC',
	'orderby'  => 'date_created',
	'per_page' => -1,
	'meta_query' => array(
		array(
			'key'     => '_reign_linked_course',
			'compare' => 'EXISTS'
		),
	)
);
$allgroups  = groups_get_groups( $group_args );
?>
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">
		<p class="description"><?php esc_html_e( 'Synchronize LearnDash Course students with the associated BuddyPress Groups to become group member.', 'reign-learndash-addon' ); ?></p>						
		<div class="ld-dashboard-content container">
<?php
echo '<table class="form-table reign-ld-linked-group-list"><tbody>';
if ( $allgroups['groups'] ) {
	$current_arr = array_slice( $allgroups['groups'], 0, $per_page );
	foreach( $current_arr as $single_group ) { ?>
		<tr>
			<th>
				<label><?php echo esc_html( $single_group->name ); ?></label>
			</th>
			<td>
				<a class="button-primary reign-ld-group-sync" attr-id="<?php echo esc_attr( $single_group->id ); ?>">
					<?php esc_html_e( 'Sync', 'reign-learndash-addon' ); ?>
				</a>
				<i class="dashicons dashicons-update reign-ld-spinner"></i>		
				<span>
					<?php esc_html_e( 'Completed', 'reign-learndash-addon' ); ?>
				</span>	
			</td>
		</tr>		
		<?php
	}
}	
echo '</tbody>';
echo '</table>';
echo '<div id="reign-ld-pagination-bar"></div>';
?>
</div>
</div>
</div>
<?php return; ?>
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">						
		<div class="ld-dashboard-content container">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ld_dashboard_bp_groups_settings' );
				do_settings_sections( 'ld_dashboard_bp_groups_settings' );
				?>
				<table class="form-table">
					<tbody>
						<tr>
							
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
</div>