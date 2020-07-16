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
$settings					 = $ld_dashboard_settings_data[ 'general_settings' ];
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wbcom-tab-content">
	<div class="wrap ld-dashboard-settings">
		<div class="ld-dashboard-content container">
			<form method="post" action="options.php" enctype="multipart/form-data">
				<?php
				settings_fields( 'ld_dashboard_general_settings' );
				do_settings_sections( 'ld_dashboard_general_settings' );
				?>
				<div class="form-table">
					<div class="ld-grid-view-wrapper">
						<!--div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Instructor Total Sales', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php //esc_html_e( 'Enable this option if you want to show instructor total sales count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[instructor-total-sales]" value="1" <?php //checked( $settings[ 'instructor-total-sales' ], '1' ); ?> data-id="instructor-total-sales"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="instructor-total-sales-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[instructor-total-sales-bgcolor]" class="ld-dashboard-color" value="<?php //echo esc_attr( $settings[ 'instructor-total-sales-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div-->
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Course Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total course count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[course-count]" value="1" <?php checked( $settings[ 'course-count' ], '1' ); ?>  data-id="course-count" />
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="course-count-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text"  name="ld_dashboard_general_settings[course-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'course-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Quizzes Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total Quizzes count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[quizzes-count]" value="1" <?php checked( $settings[ 'quizzes-count' ], '1' ); ?> data-id="quizzes-count"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="quizzes-count-bgcolor"class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text"  name="ld_dashboard_general_settings[quizzes-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'quizzes-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Assignments Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show Assignments statistics.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[assignments-count]" value="1" <?php checked( $settings[ 'assignments-count' ], '1' ); ?> data-id="assignments-count" />
									<div class="ld-dashboard-setting round"></div>
								</label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total Completed Assignments count. Otherwise display total pending Assignments count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[assignments-completed-count]" value="1" <?php checked( $settings[ 'assignments-completed-count' ], '1' ); ?> />
									<div class="ld-dashboard-setting round"></div>
								</label>
								<br>
								<div id="assignments-count-bgcolor" class="ld-dashboard-colorpicker ld-assignments-count">
									<label class="ld-assignments-count-label"><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[assignments-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'assignments-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Essays Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total pending Essays count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[essays-pending-count]" value="1" <?php checked( $settings[ 'essays-pending-count' ], '1' ); ?> data-id="essays-pending-count" />
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="essays-pending-count-bgcolor"class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[essays-pending-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'essays-pending-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Lessons Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[lessons-count]" value="1" <?php checked( $settings[ 'lessons-count' ], '1' ); ?> data-id="lessons-count"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="lessons-count-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[lessons-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'lessons-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Topics Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total Topics count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[topics-count]" value="1" <?php checked( $settings[ 'topics-count' ], '1' ); ?> data-id="topics-count"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="topics-count-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[topics-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'topics-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Student Count', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show total Student count.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[student-count]" value="1" <?php checked( $settings[ 'student-count' ], '1' ); ?> data-id="student-count"/>
									<div class="ld-dashboard-setting round"></div>
								</label>

								<div id="student-count-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[student-count-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'student-count-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable Instructor Earning', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show instructor earning.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[ins-earning]" value="1" <?php checked( $settings[ 'ins-earning' ], '1' ); ?> data-id="ins-earning"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="ins-earning-bgcolor" class="ld-dashboard-colorpicker">
									<label><?php //esc_html_e( 'Block Color', 'ld-dashboard' );  ?></label>
									<input type="text" name="ld_dashboard_general_settings[ins-earning-bgcolor]" class="ld-dashboard-color" value="<?php echo esc_attr( $settings[ 'ins-earning-bgcolor' ] ); ?>" />
								</div>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable instructor statistics', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show instructor statistics.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" name="ld_dashboard_general_settings[instructor-statistics]" value="1" <?php checked( $settings[ 'instructor-statistics' ], '1' ); ?> />
									<div class="ld-dashboard-setting round"></div>
								</label>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Enable course progress', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to show course progress.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" name="ld_dashboard_general_settings[course-progress]" value="1" <?php checked( $settings[ 'course-progress' ], '1' ); ?> />
									<div class="ld-dashboard-setting round"></div>
								</label>
							</div>
						</div>
						<div class="ld-single-grid">
							<div class="ld-grid-label" scope="row">
								<label><?php esc_html_e( 'Global commission', 'ld-dashboard' ); ?></label>
							</div>
							<div class="ld-grid-content">
								<span class="ld-decription"><?php esc_html_e( 'Enable this option if you want to enable commission on courses.', 'ld-dashboard' ); ?></span>
								<label class="ld-dashboard-setting-switch">
									<input type="checkbox" class="ld-dashboard-setting" name="ld_dashboard_general_settings[enable-global-commission]" value="1" <?php checked( $settings[ 'enable-global-commission' ], '1' ); ?> data-id="enable-global-commission"/>
									<div class="ld-dashboard-setting round"></div>
								</label>
								<div id="enable-global-commission-bgcolor">
									<label><?php esc_html_e( 'Rate %', 'ld-dashboard' ); ?></label>
									<div class="ld-tooltip"><i class="fa fa-info-circle" aria-hidden="true"></i><span class="ld-tooltiptext"><?php esc_html_e( 'Enter global commission rate for all course author.', 'ld-dashboard' ); ?></span></div>
									<input type="number" min="0" max="100" name="ld_dashboard_general_settings[global-commission]"  value="<?php echo esc_attr( $settings[ 'global-commission' ] ); ?>" />
								</div>
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
