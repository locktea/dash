(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 
	$(document).ready(function(){		
		$('.ld-dashboard-color').wpColorPicker();

		if( $('#ld-instructor-commission-update-tbl').length ){
			var table = $('#ld-instructor-commission-update-tbl').DataTable({
				deferRender: true,
				ordering:true
			});
		}

		if( $('#ld-instructor-commission-report').length ){
			var comm_table = $('#ld-instructor-commission-report').DataTable({
				searching: false,
				lengthChange: false,
				deferRender: true,
				ordering:true,
				paging:false,
				bInfo:false
			});
		}
		
		// $( '.ld-dashboard-setting' ).each(function() {
		// 	var colorpicker_id = $(this).data('id');			
		// 	if( $(this).prop("checked") == true ){
  //               $( '#' + colorpicker_id + '-bgcolor' ).show();
  //           } else {
		// 		$( '#' + colorpicker_id + '-bgcolor' ).hide();
		// 	}
		// });
		
		// $( '.ld-dashboard-setting' ).on( 'click', function () {
		// 	var colorpicker_id = $(this).data('id');			
		// 	if( $(this).prop("checked") == true ){
  //               $( '#' + colorpicker_id + '-bgcolor' ).show(1000);
  //           } else {
		// 		$( '#' + colorpicker_id + '-bgcolor' ).hide(1000);
		// 	}
		// });

		/*================================================================================
		=            Ajax request to update individual instructor commission.            =
		================================================================================*/
		
		$( document ).on( 'click', '.ld-update-instructor-commision', function (e) {
			e.preventDefault();
			var update_text = $(this).html();
			var update_btn = $(this);
			$(this).html('Please wait '+ '<i class="fa fa-spinner fa-spin"></i>');
			var input_id = $(this).data('id');
			var instructor_id = $(this).data('instructor-id');
			var instructor_commission = $('#'+input_id).val();

			if( instructor_id && instructor_commission ) {
				var data = {
					'action': 'ld_ajax_update_instructor_commission',
					'instructor_id': instructor_id,
					'instructor_commission': instructor_commission,
					'ajax_nonce': ld_dashboard_obj.ajax_nonce
				};

				$.post( ld_dashboard_obj.ajax_url, data, function ( response ) {
					update_btn.html(update_text);
					$('#'+input_id).val(instructor_commission);
				} );
			}
		});
		
		/*=====  End of Ajax request to update individual instructor commission.  ======*/

		/*============================================================================
		=            Instructor select event to generate commission data.            =
		============================================================================*/
		
		$('#ld-instructor-dropdown').on('change', function() {
			var instructor_id = this.value;
			if( instructor_id && instructor_id != 'select' ){
				$('#ld-instructor-commission-report tbody').html('<div class="load-commission-data"><i class="ld-load-result fa fa-refresh fa-spin"></i></div>');
				$('#ld-instructor-commission-report tfoot').html('');
				var data = {
					'action': 'ld_ajax_generate_instructor_data',
					'instructor_id': instructor_id,
					'ajax_nonce': ld_dashboard_obj.ajax_nonce
				};
				$.post( ld_dashboard_obj.ajax_url, data, function ( response ) {
					var fun_response = JSON.parse(response);
					$('#ld-instructor-commission-report tbody').html(fun_response.tr_html);
					$('#ld-instructor-commission-report tfoot').html(fun_response.tfoot_html);
				} );
			}
		});
		
		/*=====  End of Instructor select event to generate commission data.  ======*/
		
		/*====================================================
		=            Instructor pay unpaid amount            =
		====================================================*/
		
		
		$( document ).on( 'click', '.instructor-pay-amount', function (e) {
			e.preventDefault();
			$('.ld-instructor-dialog').addClass('visible');
			
			var instructor_id = $(this).attr('data-instructor-id');
			var unpaid_earning = $(this).attr('data-unpaid-amt');
			var paid_earning = $(this).attr('data-paid-amt');
			var total_earning = $(this).attr('data-total-earning');
			
			//dialog view html
			$('.ld-dialog-paid-earning').html( paid_earning );
			$('.ld-dialog-unpaid-earning').html( unpaid_earning );

			//set values in hidden input
			$("#ld-instructor-id").val(instructor_id);
			$("#ld-paid-earning").val(paid_earning);
			$("#ld-unpaid-earning").val(unpaid_earning);
			$("#ld-total-earning").val(total_earning);

			
		});
		
		/*=====  End of Instructor pay unpaid amount  ======*/

		$( document ).on( 'click', '.ld-instructor-trigger-pay', function () {
	 		var clk_obj = $(this);
	 		var pay_txt = $(this).html();

	 		var instructor_id = $("#ld-instructor-id").val();
			var unpaid_earning = $("#ld-unpaid-earning").val();
			var paid_earning = $("#ld-paid-earning").val();
			var total_earning = $("#ld-total-earning").val();

			var paying_amount = $("#ld-pay-amount").val();

			if( paying_amount > unpaid_earning ) {
				$('.ld-pay-error').addClass('visible');
			}

	 		if( paying_amount && instructor_id && (paying_amount < unpaid_earning || paying_amount == unpaid_earning) ) {
	 			$('.ld-pay-error').removeClass('visible');
	 			$(clk_obj).html( pay_txt + ' <i class="fa fa-spinner fa-spin"></i>');
	 			var data = {
					'action': 'ld_ajax_pay_instructor_amount',
					'instructor_id': instructor_id,
					'paid_earning': paid_earning,
					'paying_amount': paying_amount,
					'total_earning': total_earning,
					'ajax_nonce': ld_dashboard_obj.ajax_nonce
				};

	 			$.post( ld_dashboard_obj.ajax_url, data, function ( response ) {
	 				$('.ld-instructor-dialog').removeClass('visible');
	 				$("#ld-pay-amount").val('');
	 				$(clk_obj).html(pay_txt);
	 				window.location.reload();
	 			} );
	 		}
	 	});
	 	
	 	$( document ).on( 'click', '.ld-instructor-dialog-cancel', function () {
	 		$('.ld-instructor-dialog').removeClass('visible');
	 		//dialog view html
			$('.ld-dialog-paid-earning').html('');
			$('.ld-dialog-unpaid-earning').html('');

			//set values in hidden input
			$("#ld-instructor-id").val('');
			$("#ld-paid-earning").val('');
			$("#ld-unpaid-earning").val('');
			$("#ld-total-earning").val('');
			$("#ld-pay-amount").val('');
	 	});
		
		$(document).on('click', '.ld-add-instructor-btn', function(e){
			e.preventDefault();

			var $that = $(this);
			var post_id = $('#post_ID').val();

			$.ajax({
				url : ajaxurl,
				type : 'POST',
				data : {post_id : post_id, action: 'ld_dashboard_load_instructors_modal'},
				beforeSend: function () {
					$that.addClass('ld-dashboard-updating-message');
				},
				success: function (data) {
					if (data.success){
						$('.ld-instructors-modal-wrap .modal-container').html(data.data.output);
						$('.ld-instructors-modal-wrap').addClass('show');
						$('body').addClass('ld-modal-show');
					}
				},
				complete: function () {
					$that.removeClass('ld-dashboard-updating-message');
				}
			});
		});
		
		$(document).on('click', '.modal-close-btn', function(e){
			e.preventDefault();
			$('.ld-modal-wrap').removeClass('show');
			$('body').removeClass('ld-modal-show');
		});
		
		/* Delay Function */

		var ld_dashboard_delay = (function(){
			var timer = 0;
			return function(callback, ms){
				clearTimeout (timer);
				timer = setTimeout(callback, ms);
			};
		})();

		$(document).on('change keyup', '.ld-instructors-modal-wrap .ld-instructor-modal-search-input', function(e){
			e.preventDefault();

			var $that = $(this);
			var $modal = $('.ld-modal-wrap');

			ld_dashboard_delay(function(){
				var search_terms = $that.val();
				var post_id = $('#post_ID').val();

				$.ajax({
					url : ajaxurl,
					type : 'POST',
					data : {post_id : post_id, search_terms : search_terms, action: 'ld_dashboard_load_instructors_modal'},
					beforeSend: function () {
						$modal.addClass('loading');
					},
					success: function (data) {
						if (data.success){
							$('.ld-instructors-modal-wrap .modal-container').html(data.data.output);
							$('.ld-instructors-modal-wrap').addClass('show');
							$('body').addClass('ld-modal-show');
						}
					},
					complete: function () {
						$modal.removeClass('loading');
					}
				});

			}, 1000)
		});
		
		$(document).on('click', '.add_instructor_to_course_btn', function(e){
			e.preventDefault();

			var $that = $(this);
			var $modal = $('.ld-modal-wrap');
			var post_id = $('#post_ID').val();
			var data = $modal.find('input').serialize()+'&post_id='+post_id+'&action=ld_dashboard_add_instructors_to_course';

			$.ajax({
				url : ajaxurl,
				type : 'POST',
				data : data,
				beforeSend: function () {
					$that.addClass('ld-updating-message');
				},
				success: function (data) {
					if (data.success){
						$('.ld-available-instructors').html(data.data.output);
						$('.ld-modal-wrap').removeClass('show');
						$('body').removeClass('ld-modal-show');
					}
				},
				complete: function () {
					$that.removeClass('ld-updating-message');
				}
			});
		});
		
		$(document).on('click', '.ld-instructor-delete-btn,.ld-instructor-delete-btn .dashicons dashicons-no', function(e){
			e.preventDefault();

			var $that = $(this);
			var post_id = $('#post_ID').val();
			var instructor_id = $that.closest('.added-instructor-item').attr('data-instructor-id');

			$.ajax({
				url : ajaxurl,
				type : 'POST',
				data : {post_id:post_id, instructor_id:instructor_id, action : 'ld_dashboard_detach_instructor'},
				success: function (data) {
					if (data.success){
						$that.closest('.added-instructor-item').remove();
					}
				}
			});
		});
		
	});

})( jQuery );
