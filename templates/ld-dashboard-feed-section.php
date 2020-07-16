<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="ld-dashboard-sidebar-right">
	<div class="ld-dashboard-feed-wrapper">
		<h3 class="ld-dashboard-feed-title"><?php esc_html_e( 'Live  Feed', 'ld-dashboard' ); ?></h3>
		<div id="ld-dashboard-feed" class="ld-dashboard-feed">		
			<?php $this->ld_dashboard_activity_rows(); ?>
		</div>
	</div>
</div>