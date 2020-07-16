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
?>
<?php
$function_obj				 = Ld_Dashboard_Functions::instance();
$ld_dashboard_settings_data	 = $function_obj->ld_dashboard_settings_data();
$settings					 = $ld_dashboard_settings_data[ 'ld_dashboard_integration' ];
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">
		<div class="ld-dashboard-content container">
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'ld_dashboard_integration' );
				do_settings_sections( 'ld_dashboard_integration' );
				?>
				<div class="form-table">
					<div class="ld-grid-view-wrapper">						
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'LearnDash Email integration', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to enable email integration.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" name="ld_dashboard_integration[enable-email-integration]" value="1" <?php checked( $settings[ 'enable-email-integration' ], '1' ); ?> />
									<div class="ld-dashboard-setting round"></div>
								</label>
							</div>
						</div>
						<?php if ( class_exists( 'BuddyPress' ) ) { ?>
							<div class="ld-single-grid">
								<div class="ld-grid-label" scope="row">
									<label><?php esc_html_e( 'LearnDash BuddyPress group integration', 'ld-dashboard' ); ?></label>
								</div>
								<div class="ld-grid-content">
									<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to enable buddypress group integration.', 'ld-dashboard' ); ?></span>
									<label class="ld-dashboard-setting-switch">
										<input type="checkbox" name="ld_dashboard_integration[enable-group-integration]" value="1" <?php checked( $settings[ 'enable-group-integration' ], '1' ); ?>/>
										<div class="ld-dashboard-setting round"></div>
									</label>
								</div>
							</div>
							<div class="ld-single-grid">
								<div class="ld-grid-label" scope="row">
									<label><?php esc_html_e( 'LearnDash BuddyPress Messaging integration', 'ld-dashboard' ); ?></label>
								</div>
								<div class="ld-grid-content">
									<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to enable buddypress messaging integration.', 'ld-dashboard' ); ?></span>
									<label class="ld-dashboard-setting-switch">
										<input type="checkbox" name="ld_dashboard_integration[enable-messaging-integration]" value="1" <?php checked( $settings[ 'enable-messaging-integration' ], '1' ); ?>/>
										<div class="ld-dashboard-setting round"></div>
									</label>
								</div>
							</div>
						<?php } ?>

						<?php if ( ld_is_envt_ready_for_to_do() ) { ?>
							<div class="ld-single-grid">
								<div class="ld-grid-label" scope="row">
									<label><?php esc_html_e( 'Display course To Do', 'ld-dashboard' ); ?></label>
								</div>
								<div class="ld-grid-content">
									<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to display To Do widget at dashboard page.', 'ld-dashboard' ); ?></span>
									<label class="ld-dashboard-setting-switch">
										<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_integration[display-to-do]" value="1" <?php checked( $settings[ 'display-to-do' ], '1' ); ?> />
										<div class="ld-dashboard-setting round"></div>
									</label>
								</div>
							</div>
						<?php } ?>						
						
					</div>
				</div>
				<?php submit_button(); ?>
				<?php wp_nonce_field( 'ld-dashboard-settings-submit', 'ld-dashboard-settings-submit' ); ?>
			</form>
		</div>
	</div>
</div>
