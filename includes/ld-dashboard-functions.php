<?php

/**
 * Function to find source of commission for admin.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_if_commission_enabled() {
	$function_obj               = Ld_Dashboard_Functions::instance();
	$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
	$settings                   = $ld_dashboard_settings_data['general_settings'];
	$enable_commission          = ( isset( $settings['enable-global-commission'] ) ) ? $settings['enable-global-commission'] : '';
	if ( $enable_commission == '1' ) {
		return apply_filters( 'ld_get_global_commission_rate', true );
	}
	return false;
}

function ld_get_global_commission_rate() {
	$function_obj               = Ld_Dashboard_Functions::instance();
	$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
	$settings                   = $ld_dashboard_settings_data['general_settings'];
	$global_commission          = ( isset( $settings['global-commission'] ) ) ? $settings['global-commission'] : 0;
	return apply_filters( 'ld_get_global_commission_rate', (int) $global_commission );
}

/**
 * Function to find source of commission for admin.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_if_instructor_course_commission_set( $instrcutor_id ) {
	$value = get_user_meta( $instrcutor_id, 'instructor-course-commission', true );
	if ( $value ) {
		return apply_filters( 'ld_if_instructor_course_commission_set', (int) $value );
	}
	return false;
}

/**
 * Function to return admin commuission on course.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_get_admin_course_commission( $course_id ) {
	$value = get_post_meta( $course_id, 'admin-course-commission', true );
	return apply_filters( 'ld_get_admin_course_commission', (int) $value );
}

/**
 * Function to check if author is instructor.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_check_if_author_is_instructor( $course_author ) {
	$course_author_data  = get_userdata( $course_author );
	$course_author_roles = $course_author_data->roles;
	if ( in_array( 'ld_instructor', (array) $course_author_roles ) ) {
		return apply_filters( 'ld_check_if_author_is_instructor', true );
	}
	return false;
}

function ld_dashboard_update_on_stripe_payment( $post_id, $post ) {
	$stripe_course_id = get_post_meta( $post_id, 'stripe_course_id', true );
	update_option( 'test_stripe_payment', $stripe_course_id );
}

function ld_get_instructor_data( $instructor_id ) {
	$course_purchase_data = get_user_meta( $instructor_id, 'course_purchase_data', true );
	$monthly_data         = array();
	if ( is_array( $course_purchase_data ) ) {
		foreach ( $course_purchase_data as $order_id => $data ) {
			if ( $data['payment_type'] == 'WC' ) {

				$order = new WC_Order( $order_id );				
				$order_month     = $order->get_date_created()->date( 'm' );
				$monthly_data[ $order_month ]['order_ids'][] = $order_id;

				$course_pricing = learndash_get_course_price( $data['course'] );

				if ( ! isset( $monthly_data[ $order_month ]['total_commission'] ) ) {

					$monthly_data[ $order_month ]['total_commission'] = ( $course_pricing['price'] * ( 100 - $data['commission'] ) ) / 100;
				} else {
					$monthly_data[ $order_month ]['total_commission'] += ( $course_pricing['price'] * ( 100 - $data['commission'] ) ) / 100;
				}
			} elseif ( $data['payment_type'] == 'Stripe' ) {

				$order_month = (int) get_the_date( 'm', $order_id );

				$course_pricing = learndash_get_course_price( $data['course'] );
				if ( ! isset( $monthly_data[ $order_month ]['total_commission'] ) ) {

					$monthly_data[ $order_month ]['total_commission'] = ($course_pricing['price'] * ( 100 - $data['commission'] ) ) / 100;
				} else {
					$monthly_data[ $order_month ]['total_commission'] += ($course_pricing['price'] * ( 100 - $data['commission'] ) ) / 100;
				}
			}
		}
	}

	return $monthly_data;
}

function instructor_monthy_commission_earning( $instructor_id ) {

	$monthy_data = ld_get_instructor_data( $instructor_id );	
	$monthly_earning = array();

	$months = array( '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' );
	if ( is_array( $monthy_data ) && ! empty( $monthy_data ) ) {
		foreach ( $months as $key => $value ) {
			if ( array_key_exists( $value, $monthy_data ) ) {
				$monthly_earning[ $value ] = $monthy_data[ $value ]['total_commission'];
			} else {
				$monthly_earning[ $value ] = 0;
			}
		}
	}
	return $monthly_earning;
}

function ld_instructor_course_wise_admin_commission( $instructor_id ) {
	$course_purchase_data = get_user_meta( $instructor_id, 'course_purchase_data', true );
	$course_wise_earning  = array();
	if ( is_array( $course_purchase_data ) ) {
		foreach ( $course_purchase_data as $order_id => $data ) {
			if ( isset( $course_wise_earning[ $data['course'] ] ) ) {
				$course_wise_earning[ $data['course'] ][] = $data['commission'];
			} else {
				$course_wise_earning[ $data['course'] ][] = $data['commission'];
			}
		}
	}
	return $course_wise_earning;
}

function ld_instructor_course_wise_earning( $instructor_id ) {
	$course_wise_earning = ld_instructor_course_wise_admin_commission( $instructor_id );
	$ins_course_earning  = array();
	if ( is_array( $course_wise_earning ) ) {
		foreach ( $course_wise_earning as $course_id => $commission_arr ) {
			foreach ( $commission_arr as $commission_key => $commission_value ) {
				$course_pricing = learndash_get_course_price( $course_id );
				if ( isset( $ins_course_earning[ $course_id ]['earning'] ) ) {
					$ins_course_earning[ $course_id ]['earning'] += (int) $course_pricing['price'] * ( ( 100 - (int) $commission_value ) / 100 );
					$ins_course_earning[ $course_id ]['title']    = get_the_title( $course_id );
				} else {
					$ins_course_earning[ $course_id ]['earning'] = (int) $course_pricing['price'] * ( ( 100 - (int) $commission_value ) / 100 );
					$ins_course_earning[ $course_id ]['title']   = get_the_title( $course_id );
				}
			}
		}
	}
	return $ins_course_earning;
}

/**
 * Function to find source of commission for admin.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_if_ldbp_group_intgrtn_enabled() {
	$function_obj               = Ld_Dashboard_Functions::instance();
	$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
	$settings                   = $ld_dashboard_settings_data['general_settings'];
	$ld_dashboard_integration   = $ld_dashboard_settings_data['ld_dashboard_integration'];
	$enable_grp_intgrtn         = ( isset( $ld_dashboard_integration['enable-group-integration'] ) ) ? $ld_dashboard_integration['enable-group-integration'] : '';
	if ( $enable_grp_intgrtn == '1' && class_exists( 'BuddyPress' ) && function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) ) {
		return apply_filters( 'ld_if_ldbp_group_intgrtn_enabled', true );
	}
	return false;
}

function new_ld_bptodo_get_course_list( $first_course_id, $user_id, $group_id, $can_modify ) {
	ob_start();
	include LD_DASHBOARD_PLUGIN_DIR . '/buddypress/ld-dashboard-todo-list.php';
	$content = ob_get_clean();
	return $content;
}

function ld_bptodo_get_course_list( $course_id, $user_id, $group_id, $can_modify ) {
	$args               = array(
		'post_type'      => 'bp-todo',
		'post_status'    => 'publish',
		'author'         => $user_id,
		'posts_per_page' => -1,
		'meta_key'       => 'todo_group_id',
		'meta_value'     => $group_id,
	);
	$todos              = get_posts( $args );
	$todo_list          = array();
	$all_todo_count     = 0;
	$all_completed_todo = 0;
	$all_remaining_todo = 0;
	$completed_todo_ids = array();
	foreach ( $todos as $todo ) {
		$curr_date   = date_create( date( 'Y-m-d' ) );
		$due_date    = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
		$todo_status = get_post_meta( $todo->ID, 'todo_status', true );
		$diff        = date_diff( $curr_date, $due_date );
		$diff_days   = $diff->format( '%R%a' );
		if ( $diff_days < 0 ) {
			$todo_list['past'][] = $todo->ID;
		} elseif ( 0 == $diff_days ) {
			$todo_list['today'][] = $todo->ID;
		} elseif ( 1 == $diff_days ) {
			$todo_list['tomorrow'][] = $todo->ID;
		} else {
			$todo_list['future'][] = $todo->ID;
		}
	}
	return apply_filters( 'alter_ld_bptodo_get_course_list', $todo_list );
}

// function ld_generate_send_course_todo_button( $course_id, $user_id ) {

// $course_todo_send_btn = '';
// $course_user_ids = learndash_get_users_for_course( $course_id );
// return $course_user_ids;

// return apply_filters( 'ld_generate_send_course_todo_button', $course_todo_send_btn, $course_id, $user_id );
// }

function ld_generate_tbody_for_ld_course_todos( $todo_list, $can_modify, $group_id ) {
	global $bptodo;
	$profile_menu_slug = $bptodo->profile_menu_slug;

	$group       = groups_get_group( array( 'group_id' => $group_id ) );
	$groups_link = bp_get_group_permalink( $group );
	$admin_link  = trailingslashit( $groups_link . $profile_menu_slug );

	$all_remaining_todo = 0;
	$all_completed_todo = 0;
	ob_start();
	?>

	<!-- PAST TASKS -->
	<?php
	if ( ! empty( $todo_list['past'] ) ) {
		$count = 1;
		foreach ( $todo_list['past'] as $tid ) {
			?>
			<?php
			$todo          = get_post( $tid );
			$todo_title    = $todo->post_title;
			$todo_edit_url = $admin_link . '/add?args=' . $tid;

			$todo_status    = get_post_meta( $todo->ID, 'todo_status', true );
			$todo_priority  = get_post_meta( $todo->ID, 'todo_priority', true );
			$due_date_str   = $due_date_td_class = '';
			$curr_date      = date_create( date( 'Y-m-d' ) );
			$due_date       = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
			$diff           = date_diff( $curr_date, $due_date );
			$diff_days      = $diff->format( '%R%a' );
			$priority_class = '';
			if ( $diff_days < 0 ) {
				$due_date_str      = sprintf( esc_html__( 'Expired %d days ago!', 'wb-todo' ), abs( $diff_days ) );
				$due_date_td_class = 'bptodo-expired';
			} elseif ( 0 == $diff_days ) {
				$due_date_str      = esc_html__( 'Today is the last day to complete. Hurry Up!', 'wb-todo' );
				$due_date_td_class = 'bptodo-expires-today';
			} else {
				if ( $diff_days == 1 ) {
					$day_string = __( 'day', 'wb-todo' );
				} else {
					$day_string = __( 'days', 'wb-todo' );
				}
				$due_date_str = sprintf( esc_html__( '%1$d %2$s left to complete the task!', 'wb-todo' ), abs( $diff_days ), $day_string );
										// $all_remaining_todo++;
			}
			if ( 'complete' == $todo_status ) {
				$due_date_str      = esc_html__( 'Completed!', 'wb-todo' );
				$due_date_td_class = '';
				$all_completed_todo++;
			}
			if ( ! empty( $todo_priority ) ) {
				if ( 'critical' == $todo_priority ) {
					$priority_class = 'bptodo-priority-critical';
					$priority_text  = esc_html__( 'Critical', 'wb-todo' );
				} elseif ( 'high' == $todo_priority ) {
					$priority_class = 'bptodo-priority-high';
					$priority_text  = esc_html__( 'High', 'wb-todo' );
				} else {
					$priority_class = 'bptodo-priority-normal';
					$priority_text  = esc_html__( 'Normal', 'wb-todo' );
				}
			}
			?>
			<tr id="bptodo-row-<?php echo esc_attr( $tid ); ?>">
				<td class="bptodo-priority"><span class="<?php echo esc_attr( $priority_class ); ?>"><?php echo $priority_text; ?></span></td>
				<td class="
				<?php
				if ( 'complete' == $todo_status ) {
					echo esc_attr( $class );
				}
				?>
				"><?php echo esc_html( $todo_title ); ?></td>
				<td class="
				<?php
				echo esc_attr( $due_date_td_class );
				if ( 'complete' == $todo_status ) {
					echo esc_attr( $class );
				}
				?>
				"><?php echo $due_date_str; ?></td>
				<td class="bp-to-do-actions">
					<ul>
						<?php if ( $can_modify ) { ?>
							<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo esc_attr( $tid ); ?>"    title="<?php echo sprintf( esc_html__( 'Remove: %s', 'wb-todo' ), $todo_title ); ?>"
								><i class="fa fa-times"></i></a></li>
							<?php } ?>
							<?php if ( 'complete' !== $todo_status ) { ?>
								<?php if ( $can_modify ) { ?>
									<li><a href="<?php echo esc_attr( $todo_edit_url ); ?>" title="<?php echo sprintf( esc_html__( 'Edit: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-edit"></i></a></li>
								<?php } ?>
								<li id="bptodo-complete-li-<?php echo esc_attr( $tid ); ?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-check"></i></a></li>
							<?php } else { ?>
								<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Undo Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-undo"></i></a></li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			<?php } ?>

		<?php } ?>
		<!-- TASKS FOR TODAY -->
		<?php if ( ! empty( $todo_list['today'] ) ) { ?>
			<?php $count = 1; ?>
			<?php foreach ( $todo_list['today'] as $tid ) { ?>
				<?php
				$todo          = get_post( $tid );
				$todo_title    = $todo->post_title;
				$todo_edit_url = $admin_link . '/add?args=' . $tid;

				$todo_status   = get_post_meta( $todo->ID, 'todo_status', true );
				$todo_priority = get_post_meta( $todo->ID, 'todo_priority', true );
				$due_date_str  = $due_date_td_class  = '';
				$curr_date     = date_create( date( 'Y-m-d' ) );
				$due_date      = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
				$diff          = date_diff( $curr_date, $due_date );
				$diff_days     = $diff->format( '%R%a' );
				if ( $diff_days < 0 ) {
					$due_date_str      = sprintf( esc_html__( 'Expired %d days ago!', 'wb-todo' ), abs( $diff_days ) );
					$due_date_td_class = 'bptodo-expired';
				} elseif ( 0 == $diff_days ) {
					$due_date_str      = esc_html__( 'Today is the last day to complete. Hurry Up!', 'wb-todo' );
					$due_date_td_class = 'bptodo-expires-today';
					$all_remaining_todo++;
				} else {
					if ( $diff_days == 1 ) {
						$day_string = __( 'day', 'wb-todo' );
					} else {
						$day_string = __( 'days', 'wb-todo' );
					}
					$due_date_str = sprintf( esc_html__( '%1$d %2$s left to complete the task!', 'wb-todo' ), abs( $diff_days ), $day_string );
					$all_remaining_todo++;
				}
				if ( 'complete' == $todo_status ) {
					$due_date_str      = esc_html__( 'Completed!', 'wb-todo' );
					$due_date_td_class = '';
					$all_completed_todo++;
				}
				if ( ! empty( $todo_priority ) ) {
					if ( 'critical' == $todo_priority ) {
						$priority_class = 'bptodo-priority-critical';
						$priority_text  = esc_html__( 'Critical', 'wb-todo' );
					} elseif ( 'high' == $todo_priority ) {
						$priority_class = 'bptodo-priority-high';
						$priority_text  = esc_html__( 'High', 'wb-todo' );
					} else {
						$priority_class = 'bptodo-priority-normal';
						$priority_text  = esc_html__( 'Normal', 'wb-todo' );
					}
				}
				?>
				<tr id="bptodo-row-<?php echo esc_attr( $tid ); ?>">
					<td class="bptodo-priority"><span class="<?php echo esc_attr( $priority_class ); ?>"><?php echo $priority_text; ?></span></td>
					<td class="
					<?php
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo esc_html( $todo_title ); ?></td>
					<td class="
					<?php
					echo esc_attr( $due_date_td_class );
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo $due_date_str; ?></td>
					<td class="bp-to-do-actions">
						<ul>
							<?php if ( $can_modify ) { ?>
								<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Remove: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-times"></i></a></li>
							<?php } ?>
							<?php if ( 'complete' !== $todo_status ) { ?>
								<?php if ( $can_modify ) { ?>
									<li><a href="<?php echo esc_attr( $todo_edit_url ); ?>" title="<?php echo sprintf( esc_html__( 'Edit: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-edit"></i></a></li>
								<?php } ?>
								<li id="bptodo-complete-li-<?php echo esc_attr( $tid ); ?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-check"></i></a></li>
							<?php } else { ?>
								<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Undo Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-undo"></i></a></li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			<?php } ?>

		<?php } ?>
		<!-- TASKS FOR TOMORROW -->
		<?php if ( ! empty( $todo_list['tomorrow'] ) ) { ?>

			<?php $count = 1; ?>
			<?php foreach ( $todo_list['tomorrow'] as $tid ) { ?>
				<?php
				$todo          = get_post( $tid );
				$todo_title    = $todo->post_title;
				$todo_edit_url = $admin_link . '/add?args=' . $tid;

				$todo_status   = get_post_meta( $todo->ID, 'todo_status', true );
				$todo_priority = get_post_meta( $todo->ID, 'todo_priority', true );
				$due_date_str  = $due_date_td_class = '';
				$curr_date     = date_create( date( 'Y-m-d' ) );
				$due_date      = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
				$diff          = date_diff( $curr_date, $due_date );
				$diff_days     = $diff->format( '%R%a' );
				if ( $diff_days < 0 ) {
					$due_date_str      = sprintf( esc_html__( 'Expired %d days ago!', 'wb-todo' ), abs( $diff_days ) );
					$due_date_td_class = 'bptodo-expired';
				} elseif ( 0 == $diff_days ) {
					$due_date_str      = esc_html__( 'Today is the last day to complete. Hurry Up!', 'wb-todo' );
					$due_date_td_class = 'bptodo-expires-today';
					$all_remaining_todo++;
				} else {
					if ( $diff_days == 1 ) {
						$day_string = __( 'day', 'wb-todo' );
					} else {
						$day_string = __( 'days', 'wb-todo' );
					}
					$due_date_str = sprintf( esc_html__( '%1$d %2$s left to complete the task!', 'wb-todo' ), abs( $diff_days ), $day_string );
					$all_remaining_todo++;
				}
				if ( 'complete' == $todo_status ) {
					$due_date_str      = esc_html__( 'Completed!', 'wb-todo' );
					$due_date_td_class = '';
					$all_completed_todo++;
				}
				if ( ! empty( $todo_priority ) ) {
					if ( 'critical' == $todo_priority ) {
						$priority_class = 'bptodo-priority-critical';
						$priority_text  = esc_html__( 'Critical', 'wb-todo' );
					} elseif ( 'high' == $todo_priority ) {
						$priority_class = 'bptodo-priority-high';
						$priority_text  = esc_html__( 'High', 'wb-todo' );
					} else {
						$priority_class = 'bptodo-priority-normal';
						$priority_text  = esc_html__( 'Normal', 'wb-todo' );
					}
				}
				?>
				<tr id="bptodo-row-<?php echo esc_attr( $tid ); ?>">
					<td class="bptodo-priority"><span class="<?php echo esc_attr( $priority_class ); ?>"><?php echo $priority_text; ?></span></td>
					<td class="
					<?php
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo esc_html( $todo_title ); ?></td>
					<td class="
					<?php
					echo esc_attr( $due_date_td_class );
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo $due_date_str; ?></td>
					<td class="bp-to-do-actions">
						<ul>
							<?php if ( $can_modify ) { ?>
								<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Remove: %s ', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-times"></i></a></li>
							<?php } ?>
							<?php if ( 'complete' !== $todo_status ) { ?>
								<?php if ( $can_modify ) { ?>
									<li><a href="<?php echo esc_attr( $todo_edit_url ); ?>" title="<?php echo sprintf( esc_html__( 'Edit: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-edit"></i></a></li>
								<?php } ?>
								<li id="bptodo-complete-li-<?php echo esc_attr( $tid ); ?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-check"></i></a></li>
							<?php } else { ?>
								<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Undo Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-undo"></i></a></li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			<?php } ?>

		<?php } ?>

		<!-- TASKS FOR SOMEDAY. -->
		<?php if ( ! empty( $todo_list['future'] ) ) { ?>
			<?php $count = 1; ?>
			<?php foreach ( $todo_list['future'] as $tid ) { ?>
				<?php
				$todo          = get_post( $tid );
				$todo_title    = $todo->post_title;
				$todo_edit_url = $admin_link . '/add?args=' . $tid;

				$todo_status   = get_post_meta( $todo->ID, 'todo_status', true );
				$todo_priority = get_post_meta( $todo->ID, 'todo_priority', true );
				$due_date_str  = $due_date_td_class    = '';
				$curr_date     = date_create( date( 'Y-m-d' ) );
				$due_date      = date_create( get_post_meta( $todo->ID, 'todo_due_date', true ) );
				$diff          = date_diff( $curr_date, $due_date );
				$diff_days     = $diff->format( '%R%a' );
				if ( $diff_days < 0 ) {
					$due_date_str      = sprintf( esc_html__( 'Expired %d days ago!', 'wb-todo' ), abs( $diff_days ) );
					$due_date_td_class = 'bptodo-expired';
				} elseif ( 0 == $diff_days ) {
					$due_date_str      = esc_html__( 'Today is the last day to complete. Hurry Up!', 'wb-todo' );
					$due_date_td_class = 'bptodo-expires-today';
					$all_remaining_todo++;
				} else {
					if ( $diff_days == 1 ) {
						$day_string = __( 'day', 'wb-todo' );
					} else {
						$day_string = __( 'days', 'wb-todo' );
					}
					$due_date_str = sprintf( esc_html__( '%1$d %2$s left to complete the task!', 'wb-todo' ), abs( $diff_days ), $day_string );
					$all_remaining_todo++;
				}
				if ( 'complete' == $todo_status ) {
					$due_date_str      = esc_html__( 'Completed!', 'wb-todo' );
					$due_date_td_class = '';
					$all_completed_todo++;
				}
				if ( ! empty( $todo_priority ) ) {
					if ( 'critical' == $todo_priority ) {
						$priority_class = 'bptodo-priority-critical';
						$priority_text  = esc_html__( 'Critical', 'wb-todo' );
					} elseif ( 'high' == $todo_priority ) {
						$priority_class = 'bptodo-priority-high';
						$priority_text  = esc_html__( 'High', 'wb-todo' );
					} else {
						$priority_class = 'bptodo-priority-normal';
						$priority_text  = esc_html__( 'Normal', 'wb-todo' );
					}
				}
				?>
				<tr id="bptodo-row-<?php echo esc_attr( $tid ); ?>">
					<td class="bptodo-priority"><span class="<?php echo esc_attr( $priority_class ); ?>"><?php echo $priority_text; ?></span></td>
					<td class="
					<?php
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo esc_html( $todo_title ); ?></td>
					<td class="
					<?php
					echo esc_attr( $due_date_td_class );
					if ( 'complete' == $todo_status ) {
						echo esc_attr( $class );
					}
					?>
					"><?php echo $due_date_str; ?></td>
					<td class="bp-to-do-actions">
						<ul>
							<?php if ( $can_modify ) { ?>
								<li><a href="javascript:void(0);" class="bptodo-remove-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Remove: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-times"></i></a></li>
							<?php } ?>
							<?php if ( 'complete' != $todo_status ) { ?>
								<?php if ( $can_modify ) { ?>
									<li><a href="<?php echo esc_attr( $todo_edit_url ); ?>" title="<?php echo sprintf( esc_html__( 'Edit: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-edit"></i></a></li>
								<?php } ?>
								<li id="bptodo-complete-li-<?php echo esc_attr( $tid ); ?>"><a href="javascript:void(0);" class="bptodo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-check"></i></a></li>
							<?php } else { ?>
								<li><a href="javascript:void(0);" class="bptodo-undo-complete-todo" data-tid="<?php echo esc_attr( $tid ); ?>" title="<?php echo sprintf( esc_html__( 'Undo Complete: %s', 'wb-todo' ), $todo_title ); ?>"><i class="fa fa-undo"></i></a></li>
							<?php } ?>
						</ul>
					</td>
				</tr>
			<?php } ?>

		<?php } ?>
	<?php
	$tbody_html = ob_get_clean();
	return apply_filters( 'alter_ld_generate_tbody_for_ld_course_todos', $tbody_html );
}

function ld_generate_course_group_to_do_list_table( $todo_tbody ) {
	$tbody  = '<tbody>';
	$tbody .= $todo_tbody;
	$tbody .= '</tbody>';

	$thead  = '<thead>';
	$thead .= '<tr>';
	$thead .= '<th>' . __( 'Priority', 'ld-dashboard' ) . '</th>';
	$thead .= '<th>' . __( 'Task', 'ld-dashboard' ) . '</th>';
	$thead .= '<th>' . __( 'Due Date', 'ld-dashboard' ) . '</th>';
	$thead .= '<th>' . __( 'Actions', 'ld-dashboard' ) . '</th>';
	$thead .= '</tr>';
	$thead .= '</thead>';

	$html  = '';
	$html .= '<div id="bptodo-all">';
	$html .= '<div class="bptodo-admin-row">';
	$html .= '<div class="todo-panel">';
	$html .= '<div class="todo-detail">';
	$html .= '<table class="bp-todo-reminder">';
	$html .= $thead;
	$html .= $tbody;
	$html .= '</table>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '</div>';

	return $html;
}

function ld_is_envt_ready_for_to_do() {
	if ( class_exists( 'BuddyPress' ) && bp_is_active( 'groups' ) && class_exists( 'Bptodo_Profile_Menu' ) ) {
		return true;
	}
	return false;
}

/**
 * Function to find if learndash groups is enabled.
 *
 * @since  1.0.0
 * @author Wbcom Designs
 */
function ld_if_display_to_do_enabled() {
	$function_obj               = Ld_Dashboard_Functions::instance();
	$ld_dashboard_settings_data = $function_obj->ld_dashboard_settings_data();
	$settings                   = $ld_dashboard_settings_data['general_settings'];
	$ld_dashboard_integration   = $ld_dashboard_settings_data['ld_dashboard_integration'];
	$display_todo               = ( isset( $ld_dashboard_integration['display-to-do'] ) ) ? $ld_dashboard_integration['display-to-do'] : '';
	if ( $display_todo == '1' && class_exists( 'BuddyPress' ) && function_exists( 'bp_is_active' ) && bp_is_active( 'groups' ) && class_exists( 'Bptodo_Profile_Menu' ) ) {
		return apply_filters( 'ld_if_display_to_do_enabled', true );
	}
	return false;
}

if ( ! function_exists( 'ld_todo_get_user_average_todos' ) ) {
	/**
	 * Display average todo percentage of each member
	 *
	 * @param  integer $todoID  The id of post(TO DO)
	 * @return float         Average percentage of todo
	 */
	function ld_todo_get_user_average_todos( $todoID ) {
		global $bp, $post;
		$group_id        = get_post_meta( $todoID, 'todo_group_id', true );
		$todo_primary_id = get_post_meta( $todoID, 'todo_primary_id', true );

		$total_args = array(
			'post_type'      => 'bp-todo',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'todo_group_id',
					'value'   => $group_id,
					'compare' => '=',
				),
				array(
					'key'     => 'todo_primary_id',
					'value'   => $todo_primary_id,
					'compare' => '=',
				),
			),
		);

		$todos = get_posts( $total_args );
		// print_r( count( $todos ) );
		$total_count = 0;
		if ( ! empty( $todos ) ) {
			$total_count = count( $todos );
		}

		$args = array(
			'group_id'            => $group_id,
			'exclude_admins_mods' => true,
		);

		$group_members_result = groups_get_group_members( $args );
		$group_members_ids    = array();

		foreach ( $group_members_result['members'] as $member ) {
			$group_members_ids[] = $member->ID;
		}

		$member_count = count( $group_members_ids );

		$completed_count = ld_todo_completed_todo_count( $group_id, $todo_primary_id );

		$avg_rating = 0;

		if ( ! empty( $member_count ) ) {
			$avg_rating = ( $completed_count * 100 ) / $member_count;
			$avg_rating = round( $avg_rating, 2 ) . '% ';
		}
		return $avg_rating;

		wp_reset_postdata();

	}
}

if ( ! function_exists( 'ld_todo_completed_todo_count' ) ) {
	/**
	 * Get the completed to do count
	 *
	 * @param  [int] $group_id        Accosiated group id
	 * @param  [int] $todo_primary_id Primary to-do id
	 * @return [float]                Count of completed to-dos
	 */
	function ld_todo_completed_todo_count( $group_id, $todo_primary_id ) {
		$associated_todo = get_post_meta( $todo_primary_id, 'botodo_associated_todo', true );

		$completed_args  = array(
			'post_type'      => 'bp-todo',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'     => 'todo_status',
					'value'   => 'complete',
					'compare' => '=',
				),
				array(
					'key'     => 'todo_group_id',
					'value'   => $group_id,
					'compare' => '=',
				),
				array(
					'key'     => 'todo_primary_id',
					'value'   => $todo_primary_id,
					'compare' => '=',
				),
			),
		);
		$completed_todos = get_posts( $completed_args );
		$completed_count = 0;
		if ( ! empty( $completed_todos ) ) {
			$completed_count = count( $completed_todos );
		}
		return $completed_count;
		wp_reset_postdata();
	}
}
