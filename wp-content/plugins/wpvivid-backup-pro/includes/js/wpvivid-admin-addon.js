(function ($) {
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
    $(document).ready(function () {

    });

})(jQuery);

function wpvivid_post_request_addon(ajax_data, callback, error_callback, time_out){
    if(typeof time_out === 'undefined')    time_out = 30000;
    ajax_data.nonce=wpvivid_ajax_object_addon.ajax_nonce;
    jQuery.ajax({
        type: "post",
        url: wpvivid_ajax_object_addon.ajax_url,
        data: ajax_data,
        success: function (data) {
            callback(data);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            error_callback(XMLHttpRequest, textStatus, errorThrown);
        },
        timeout: time_out
    });
}

var get_custom_table_retry = get_custom_table_retry || {};
get_custom_table_retry.manual_backup_retry = 0;
get_custom_table_retry.migration_backup_retry = 0;

function wpvivid_refresh_custom_backup_info(parent_id, type){
    wpvivid_get_custom_backup_info(parent_id, type);
    var exec_time = 30 * 60 * 1000;
    setTimeout(function(){
        wpvivid_refresh_custom_backup_info(parent_id, type);
    }, exec_time);
}

function wpvivid_get_custom_backup_info(parent_id, type)
{
    var ajax_data = {
        'action': 'wpvivid_get_database_themes_plugins_table',
        'type': type
    };
    wpvivid_post_request_addon(ajax_data, function (data)
    {
        var jsonarray = jQuery.parseJSON(data);
        if(jsonarray.result == 'success'){
            jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html('');
            jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html(jsonarray.database_html);
            jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
            jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html(jsonarray.themes_plugins_html);
        }
    }, function (XMLHttpRequest, textStatus, errorThrown) {
        var need_retry = false;
        if(type === 'manual_backup'){
            get_custom_table_retry.manual_backup_retry++;
            if(get_custom_table_retry.manual_backup_retry < 10){
                need_retry = true;
            }
        }
        if(need_retry){
            setTimeout(function(){
                wpvivid_get_custom_backup_info(parent_id, type);
            }, 3000);
        }
        else{
            var refresh_btn = '<input type="submit" class="button-primary" value="Refresh" onclick="wpvivid_refresh_custom_database(\''+parent_id+'\', \''+type+'\');">';
            jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html('');
            jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html(refresh_btn);
            jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
            jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html(refresh_btn);
        }
    });
}

function wpvivid_refresh_custom_database(parent_id, type){
    if(type === 'manual_backup'){
        get_custom_table_retry.manual_backup_retry = 0;
    }

    var custom_database_loading = '<div class="spinner is-active wpvivid-database-loading" style="margin: 0 5px 10px 0; float: left;"></div>' +
        '<div style="float: left;">Archieving database tables</div>' +
        '<div style="clear: both;"></div>';
    jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html('');
    jQuery('#'+parent_id).find('.wpvivid-custom-database-info').html(custom_database_loading);

    var custom_themes_plugins_loading = '<div class="spinner is-active wpvivid-themes-plugins-loading" style="margin: 0 5px 10px 0; float: left;"></div>' +
        '<div style="float: left;">Archieving themes and plugins</div>' +
        '<div style="clear: both;"></div>';
    jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html('');
    jQuery('#'+parent_id).find('.wpvivid-custom-themes-plugins-info').html(custom_themes_plugins_loading);

    wpvivid_get_custom_backup_info(parent_id, type);
}