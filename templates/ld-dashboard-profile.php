<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user;
$user_id		 = get_current_user_id();
$current_user	 = wp_get_current_user();
$user_name		 = $current_user->user_firstname . ' ' . $current_user->user_lastname;
$user_name		 = ( $current_user->user_firstname == '' && $current_user->user_lastname == '' ) ? $current_user->user_login : $user_name;
?>

<div class="ld-dashboard-left-section ld-dashboard-sidebar-left">
	<section id="ld-dashboard-profile" class="widget ld-dashboard-profile">
		<div class="ld-dashboard-profile-summary">
			<div class="ld-dashboard-profile">
				<div class="ld-dashboard-profile-avatar">
					<?php echo wp_kses_post( get_avatar( $user_id ) ); ?>
				</div>
				<?php if ( $user_name != '' ): ?>
					<div class="ld-dashboard-profile-name">
						<?php echo esc_html( $user_name ); ?>
					</div>
					<?php
				endif;

				if ( !empty( $current_user->user_email ) ):
					?>
					<div class="ld-dashboard-profile-email">
						<?php echo esc_html( $current_user->user_email ); ?>
					</div>
				<?php endif; ?>


				<a class="ld-profile-edit-link" href='<?php echo get_edit_user_link(); ?>'><?php esc_html_e( 'Edit profile', 'ld-dashboard' ); ?></a>

			</div>
		</div>
		<div class="ld-dashboard-location">
			<?php
			$dashboard_page		 = get_option( 'ld_dashboard_page_mapping' );
			$my_dashboard_page	 = $dashboard_page[ 'my_dashboard_page' ];

			$menu_items[ 'my-dashboard' ] = array(
				'url'	 => ( $my_dashboard_page != '') ? get_the_permalink( $my_dashboard_page ) : get_the_permalink(),
				'label'	 => esc_html__( 'My Dashboard', 'ld-dashboard' ),
			);

			$menu_items[ 'my-course' ] = array(
				'url'	 => ( ( $my_dashboard_page != '') ? get_the_permalink( $my_dashboard_page ) : get_the_permalink() ) . 'my-course',
				'label'	 => LearnDash_Custom_Label::get_label( 'courses' ),
			);

			$theme_locations = get_nav_menu_locations();
			if ( isset( $theme_locations[ 'ld-dashboard-profile-menu' ] ) ) {
				$menu_obj = get_term( $theme_locations[ 'ld-dashboard-profile-menu' ], 'nav_menu' );

				if ( $menu_obj ) {
					$custom_menu_items = wp_get_nav_menu_items( $menu_obj->term_id );

					foreach ( $custom_menu_items as $menu_item ):

						$menu_items[ $menu_item->post_name ] = array(
							'url'	 => $menu_item->url,
							'label'	 => $menu_item->title
						);
					endforeach;
				}
			}

			$menu_items[ 'logout' ] = array(
				'url'	 => wp_logout_url( get_the_permalink() ),
				'label'	 => __( 'Logout', 'ld-dashboard' )
			);
			if ( $menu_items && !empty( $menu_items ) ):
				echo "<ul>";
				foreach ( $menu_items as $slug => $item ):
					?>
					<li>
						<a class="<?php echo esc_attr( 'ld-focus-menu-link ld-focus-menu-' . $slug ); ?>" href="<?php echo esc_url( $item[ 'url' ] ); ?>"><?php echo esc_html( $item[ 'label' ] ); ?></a>
					</li>
					<?php
				endforeach;
				echo "</ul>";
			endif;
			?>
		</div>
	</section>
</div>