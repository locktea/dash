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
$settings					 = $ld_dashboard_settings_data[ 'ld_dashboard_page_mapping' ];
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">
		<div class="ld-dashboard-content container">
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'ld_dashboard_page_mapping' );
				do_settings_sections( 'ld_dashboard_page_mapping' );
				?>
				<div class="form-table">
					<div class="ld-grid-view-wrapper ld-page-mapping-tab-content">
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'My Dashboard Page', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<?php
								$args						 = array(
									'name'				 => 'ld_dashboard_page_mapping[my_dashboard_page]',
									'id'				 => 'my_dashboard_page',
									'sort_column'		 => 'menu_order',
									'sort_order'		 => 'ASC',
									'show_option_none'	 => ' ',
									'class'				 => 'my_dashboard_page',
									'echo'				 => false,
									'selected'			 => absint( ( isset( $settings[ 'my_dashboard_page' ] ) ) ? $settings[ 'my_dashboard_page' ] : 0 ),
									'post_status'		 => 'publish',
								);

								if ( isset( $value[ 'args' ] ) ) {
									$args = wp_parse_args( $value[ 'args' ], $args );
								}

								echo wp_dropdown_pages( $args ); // WPCS: XSS ok.
								?>

								<?php if ( isset( $settings[ 'my_dashboard_page' ] ) && $settings[ 'my_dashboard_page' ] != 0 ): ?>
									<a href="<?php echo get_permalink( $settings[ 'my_dashboard_page' ] ); ?>" class="button-secondary" target="_bp">
										<?php esc_html_e( 'View', 'ld-dashboard' ); ?>
										<span class="dashicons dashicons-external" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'ld-dashboard' ); ?></span>
									</a>
								<?php endif; ?>
								<p class="description"><?php esc_html_e( 'This sets the page used to LearnDash user dashboard. This page should contain the following shortcode. [ld_dashboard]', 'ld-dashboard' ); ?></p>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Instructor Registration Page', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<?php
								$args = array(
									'name'				 => 'ld_dashboard_page_mapping[instructor_registration_page]',
									'id'				 => 'instructor_registration_page',
									'sort_column'		 => 'menu_order',
									'sort_order'		 => 'ASC',
									'show_option_none'	 => ' ',
									'class'				 => 'instructor_registration_page',
									'echo'				 => false,
									'selected'			 => absint( ( isset( $settings[ 'instructor_registration_page' ] ) ) ? $settings[ 'instructor_registration_page' ] : 0 ),
									'post_status'		 => 'publish',
								);

								if ( isset( $value[ 'args' ] ) ) {
									$args = wp_parse_args( $value[ 'args' ], $args );
								}

								echo wp_dropdown_pages( $args ); // WPCS: XSS ok.
								?>
								<?php if ( isset( $settings[ 'instructor_registration_page' ] ) && $settings[ 'instructor_registration_page' ] != 0 ): ?>
									<a href="<?php echo get_permalink( $settings[ 'instructor_registration_page' ] ); ?>" class="button-secondary" target="_bp">
										<?php esc_html_e( 'View', 'ld-dashboard' ); ?>
										<span class="dashicons dashicons-external" aria-hidden="true"></span>
										<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'ld-dashboard' ); ?></span>
									</a>
								<?php endif; ?>

								<p class="description"><?php esc_html_e( 'This sets the page used to register Instructor user. This page should contain the following shortcode. [ld_instructor_registration]', 'ld-dashboard' ); ?></p>
							</div>
						</div>
					</div>
				</div>
				<?php submit_button(); ?>
				<?php wp_nonce_field( 'ld-dashboard-settings-submit', 'ld-dashboard-settings-submit' ); ?>
			</form>
		</div>
	</div>
</div>
