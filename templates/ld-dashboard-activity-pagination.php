<?php
/**
 * Activity Pagination
 * global vars - 
 * $post_args array This is the filtered version of the _$GET vars passed in from AJAX
 * $activity_query_args array Another array build from the $post_args. Used to call the LD reporting functions to query activty. 
 * $activities array query results. Contains elements 'results', 'pager, etc. 
 */
if ( $activities['pager']['total_items'] > 0 ) {
	if ( $activities['pager']['current_page'] == 1 )
		$pager_left_disabled = ' disabled="disabled" ';
	else
		$pager_left_disabled = '';

	if ( $activities['pager']['current_page'] == $activities['pager']['total_pages'] )
		$pager_right_disabled = ' disabled="disabled" ';
	else
		$pager_right_disabled = '';
	?>
	<p class="ld-dashboard-report-pager-info">
		<?php if ( $activities['pager']['current_page'] != 1 ): ?>
			<button class="ld-dashboard-button first" data-page="1" title="<?php esc_attr_e( 'First Page', 'ld-dashboard' ); ?>" <?php echo $pager_left_disabled; ?> >&laquo;</button>
			<button class="ld-dashboard-button prev" data-page="<?php echo ( $activities['pager']['current_page'] > 1 ) ? $activities['pager']['current_page'] - 1 : 1; ?>" title="<?php esc_attr_e( 'Previous Page', 'ld-dashboard' ); ?>" <?php echo $pager_left_disabled; ?> >&lsaquo;</button>
		<?php endif;?>
		
		<span><?php _e('page', 'ld-dashboard') ?> <span class="pagedisplay"><span class="current_page"><?php echo $activities['pager']['current_page'] ?></span> / <span class="total_pages"><?php echo $activities['pager']['total_pages'] ?></span> (<span class="total_items"><?php echo $activities['pager']['total_items']; ?></span>)</span></span>
		
		<?php if ( $activities['pager']['current_page'] != $activities['pager']['total_pages'] ) : ?>
			<button class="ld-dashboard-button next" data-page="<?php echo ( $activities['pager']['current_page'] < $activities['pager']['total_pages']) ? $activities['pager']['current_page'] + 1 : $activities['pager']['total_pages']; ?>" title="<?php esc_attr_e( 'Next Page', 'ld-dashboard' ); ?>" <?php echo $pager_right_disabled; ?> >&rsaquo;</button>
			<button class="ld-dashboard-button last" data-page="<?php echo $activities['pager']['total_pages'] ?>" title="<?php esc_attr_e( 'Last Page', 'ld-dashboard' ); ?>" <?php echo $pager_right_disabled; ?> >&raquo;</button>
		
		<?php endif; ?>
	</p>	
	<?php
}