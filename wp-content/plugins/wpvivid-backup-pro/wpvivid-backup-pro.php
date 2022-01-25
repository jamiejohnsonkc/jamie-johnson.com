<?php

/**
 * @link              https://wpvivid.com
 * @since             1.9.0
 * @package           wpvivid
 *
 * @wordpress-plugin
 * Plugin Name:       WPvivid Backup Pro
 * Description:       WPvivid Backup Pro works on top of the free version. It offers more advanced features for customizing WordPress website backup and migration.
 * Version:           2.0.7
 * Author:            wpvivid.com
 * Author URI:        https://wpvivid.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/copyleft/gpl.html
 * Text Domain:       wpvivid
 * Domain Path:       /languages
 */

define('WPVIVID_BACKUP_PRO_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('WPVIVID_BACKUP_PRO_PLUGIN_URL', plugins_url('/',__FILE__));
define('WPVIVID_BACKUP_PRO_VERSION','2.0.7');

define('WPVIVID_PRO_PLUGIN_SLUG','WPvivid');

define('WPVIVID_DEFAULT_LOCAL_BACKUP_COUNT', 30);
define('WPVIVID_DEFAULT_REMOTE_BACKUP_COUNT', 30);
define('WPVIVID_DEFAULT_INCREMENTAL_REMOTE_BACKUP_COUNT', 3);

define('WPVIVID_STAGING_DB_INSERT_COUNT', 10000);
define('WPVIVID_STAGING_DB_REPLACE_COUNT', 5000);
define('WPVIVID_STAGING_FILE_COPY_COUNT', 500);
define('WPVIVID_STAGING_MAX_FILE_SIZE', 30);
define('WPVIVID_STAGING_MEMORY_LIMIT', '256M');
define('WPVIVID_STAGING_MAX_EXECUTION_TIME', 900);
define('WPVIVID_STAGING_RESUME_COUNT', 6);
define('WPVIVID_STAGING_REQUEST_TIMEOUT_EX',100);

define('WPVIVID_PRO_RESTORE_INIT','init');
define('WPVIVID_PRO_RESTORE_COMPLETED','completed');
define('WPVIVID_PRO_RESTORE_ERROR','error');
define('WPVIVID_PRO_RESTORE_WAIT','wait');

define('WPVIVID_PRO_MEMORY_LIMIT','256M');
define('WPVIVID_PRO_RESTORE_MEMORY_LIMIT','256M');
define('WPVIVID_PRO_MIGRATE_SIZE', '2048');

define('WPVIVID_PRO_TASK_MONITOR_EVENT','wpvivid_task_monitor_event');
define('WPVIVID_PRO_RESUME_RETRY_TIMES',6);
define('WPVIVID_PRO_REMOTE_CONNECT_RETRY_TIMES','3');
define('WPVIVID_PRO_REMOTE_CONNECT_RETRY_INTERVAL','3');

define('WPVIVID_PRO_SUCCESS','success');
define('WPVIVID_PRO_FAILED','failed');

define('WPVIVID_PRO_BACKUP_TYPE_DB','backup_db');
define('WPVIVID_PRO_BACKUP_TYPE_THEMES','backup_themes');
define('WPVIVID_PRO_BACKUP_TYPE_PLUGIN','backup_plugin');
define('WPVIVID_PRO_BACKUP_TYPE_UPLOADS','backup_uploads');
define('WPVIVID_PRO_BACKUP_TYPE_UPLOADS_FILES','backup_uploads_files');
define('WPVIVID_PRO_BACKUP_TYPE_CONTENT','backup_content');
define('WPVIVID_PRO_BACKUP_TYPE_CORE','backup_core');
define('WPVIVID_PRO_BACKUP_TYPE_OTHERS','backup_others');
define('WPVIVID_PRO_BACKUP_TYPE_MERGE','backup_merge');
define('WPVIVID_PRO_BACKUP_ROOT_WP_CONTENT','wp-content');
define('WPVIVID_PRO_BACKUP_ROOT_CUSTOM','custom');
define('WPVIVID_PRO_BACKUP_ROOT_WP_ROOT','root');
define('WPVIVID_PRO_BACKUP_ROOT_WP_UPLOADS','uploads');


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
{
    die;
}

function wpvivid_pro_plugin_activate()
{
    $active_plugins = get_option('active_plugins');
    if(!function_exists('get_plugins'))
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $plugins=get_plugins();
    $free_wpvivid_slug='wpvivid-backuprestore/wpvivid-backuprestore.php';
    if(!empty($plugins))
    {
        if(isset($plugins[$free_wpvivid_slug]))
        {
            if(in_array($free_wpvivid_slug, $active_plugins))
            {
                add_option('wpvivid_pro_do_activation_redirect', true);
                add_option('wpvivid_pro_do_activation_set_data', true);
            }
        }
    }
}

function wpvivid_pro_do_activation_set_data(){
    if (get_option('wpvivid_pro_do_activation_set_data', false)){
        delete_option('wpvivid_pro_do_activation_set_data');
        $common_setting = get_option('wpvivid_common_setting', false);
        if($common_setting !== false){
            if(isset($common_setting['max_backup_count'])){
                $display_backup_count = WPVIVID_DEFAULT_LOCAL_BACKUP_COUNT;
                $common_setting['max_backup_count'] = intval($display_backup_count);
                update_option('wpvivid_common_setting',$common_setting);
            }
        }
    }
}

function wpvivid_pro_init_plugin_redirect()
{
    if (get_option('wpvivid_pro_do_activation_redirect', false))
    {
        delete_option('wpvivid_pro_do_activation_redirect');
        if (is_multisite())
        {
            wp_redirect(network_admin_url().'admin.php?page='.apply_filters('wpvivid_white_label_slug', WPVIVID_PRO_PLUGIN_SLUG));
        }
        else
        {
            wp_redirect(admin_url().'admin.php?page='.apply_filters('wpvivid_white_label_slug', WPVIVID_PRO_PLUGIN_SLUG));
        }
    }
}

register_activation_hook(__FILE__, 'wpvivid_pro_plugin_activate');
add_action('admin_init', 'wpvivid_pro_init_plugin_redirect');
add_action('admin_init', 'wpvivid_pro_do_activation_set_data');

require WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-backup-pro.php';

function run_wpvivid_backup_pro()
{
    $wpvivid_backup_pro=new WPvivid_backup_pro();
    $GLOBALS['wpvivid_backup_pro'] = $wpvivid_backup_pro;
}
run_wpvivid_backup_pro();