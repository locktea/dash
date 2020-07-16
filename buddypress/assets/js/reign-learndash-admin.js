jQuery( document ).ready( function() {

    jQuery('.reign-ld-spinner').hide();

    jQuery( '#reign_bp_group_field' ).on( 'change', function() {
        jQuery( "#reign-bp-group-confirm" ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons:   [
            {
                text: ReignLdObj.dialog_ok_text,
                click: function() {
                    jQuery( this ).dialog( "close" );
                }
            },
            {
                text: ReignLdObj.dialog_cancel_text,
                click: function() {
                    jQuery( '#reign_bp_group_field' ).val("");
                    jQuery( this ).dialog( "close" );
                }
            }
            ]
        });
    });

    jQuery( document ).on( 'click', '.reign-ld-group-sync', function() {
        var sync = jQuery( this );
        var group_id = sync.attr( 'attr-id' );
        sync.next().next().hide();
        sync.next().addClass('spin');
        sync.next().show();
        jQuery.post(
            ReignLdObj.ajaxurl,
            {
                'action'   : 'reign_ld_sync_group',
                'group_id' :  group_id
            },
            function(response) {
                sync.next().hide();
                sync.next().removeClass('spin');
                sync.next().next().show();
            }
        );
    });

    function template(data) {
        var html = '<tbody>';
        jQuery.each( data, function(index, item){
            var items = item[0].split('_');
            html += '<tr><th><label>'+ items[0] +'</label></th><td><a class="button-primary reign-ld-group-sync" attr-id="'+ item[1] +'">' + ReignLdObj.sync_text + '</a><i class="dashicons dashicons-update reign-ld-spinner"></i><span>' + ReignLdObj.completed_text +'</span></td></tr>';
        });
        html += '</tbody>';
        return html;
    }

    function log(content) {
      window.console && console.log(content);
    }

    var container = jQuery('#reign-ld-pagination-bar');
    if( container.length > 0 ) {     
        container.pagination({
          dataSource: function(done) {
            jQuery.post(
                ReignLdObj.ajaxurl,
                {
                    'action'       : 'reign_ld_get_linked_groups',
                },
                function(response) {
                    obj = jQuery.parseJSON(response);
                    var result = Object.keys(obj).map(function(key) {
                      return [key, obj[key]];
                    });
                    done(result);
                }
            )},
          pageSize: 10,
          autoHidePrevious: true,
          autoHideNext: true,
          triggerPagingOnInit: false,
          callback: function(data, pagination) {
            var html = template(data);
            jQuery('.reign-ld-linked-group-list').html(html);
            jQuery('.reign-ld-spinner').hide();
          }
        });
    }  
} );
