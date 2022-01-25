<?php

if (!defined('WPVIVID_BACKUP_PRO_PLUGIN_DIR'))
{
    die;
}

class WPvivid_backup_pro
{
    public $addons;
    public $license_page;

    public function __construct()
    {
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-backup-pro-addon-loader.php';
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-custom-interface-addon.php';
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-backup-pro-function.php';
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-remote-addon.php';
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-pro-page.php';
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-crypt-addon.php';

        if(!file_exists(WPVIVID_BACKUP_PRO_PLUGIN_DIR.'addons/class-wpvivid-multisite.php'))
        {
            if(!class_exists('WPvivid_multisite'))
            {
                if (is_multisite())
                {
                    add_filter('wpvivid_get_screen_ids', array($this, 'get_network_screen_ids'),99);
                    add_filter('wpvivid_get_toolbar_menus', array($this, 'get_network_toolbar_menus'),99);
                    add_filter('wpvivid_get_admin_url',array($this,'get_network_admin_url'),11);
                    add_action( 'network_admin_notices', array( $this, 'check_wpvivid_plugin_active' ) );
                }
            }
        }

        $this->addons=new WPvivid_backup_pro_addon_loader();
        $this->addons->load_local_addon();
        $this->func=new WPvivid_backup_pro_function();
        $this->license_page= new WPvivid_pro_page();

        if(is_admin())
        {
            $data = $this->addons->get_local_addon_data('wpvivid-backup-pro-all-in-one');
            if ($data !== false)
            {
                add_action('wpvivid_display_page',array($this,'display_ex'),9);
            }

            add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'), 11);
            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'), 11);

            add_filter('wpvivid_get_screen_ids', array($this, 'get_screen_ids'),20);
            add_filter('wpvivid_get_main_admin_menus', array($this,'get_main_admin_menus'),20);
            add_filter('wpvivid_get_toolbar_menus',array($this,'get_toolbar_menus'),20);

            add_action('admin_notices', array($this, 'check_wpvivid_plugin_active'));

            add_action('wpvivid_add_sidebar', array($this, 'add_sidebar'));
        }

        add_filter('wpvivid_get_admin_url',array($this,'get_admin_url'),10);

        add_action( 'after_plugin_row_wpvivid-backup-pro/wpvivid-backup-pro.php', array($this, 'pro_update_row'),10, 2  );
        if($this->check_need_update_pro())
        {
            add_action( 'admin_notices', array($this,'show_update_notices'));
        }
        add_action('wp_ajax_wpvivid_hide_need_update_pro_notice', array($this, 'wpvivid_hide_need_update_pro_notice'));
        add_filter('wpvivid_review_addon', array($this, 'wpvivid_review_addon'), 11);
        add_action('wpvivid_before_setup_page',array($this,'migrate_notice'), 9);
        add_action('wpvivid_before_setup_page', array($this, 'check_schedule_last_running'));
        add_action('wpvivid_before_setup_page',array($this,'check_extensions'));
        add_action('wpvivid_before_setup_page', array($this, 'check_custom_backup_default_exclude'));
    }

    public function get_network_screen_ids($screen_ids)
    {
        if(is_multisite())
        {
            $new_screen_ids=array();
            foreach ($screen_ids as $screen_id)
            {
                if(substr($screen_id,-8)=='-network')
                    continue;
                $new_screen_ids[]=$screen_id.'-network';
            }
            return $new_screen_ids;
        }
        else
        {
            return $screen_ids;
        }
    }

    public function get_network_toolbar_menus($toolbar_menus)
    {
        if(is_multisite())
        {
            $new_toolbar_menus=array();
            $admin_url = network_admin_url();
            foreach ($toolbar_menus as $menu)
            {
                if(isset($menu['child']))
                {
                    foreach ($menu['child'] as $child_menu)
                    {
                        $child_menu['href']=$admin_url. $child_menu['tab'];
                        $menu['child'][$child_menu['id']]=$child_menu;
                    }
                }
                $new_toolbar_menus[$menu['id']]=$menu;
            }
            return $new_toolbar_menus;
        }
        else
        {
            return $toolbar_menus;
        }
    }

    public static function get_network_admin_url($admin_url)
    {
        if (is_multisite())
        {
            return network_admin_url();
        }
        else
        {
            return admin_url();
        }
    }

    public function get_admin_url($admin_url)
    {
        if(is_multisite())
        {
            $admin_url = network_admin_url();
        }
        else
        {
            $admin_url =admin_url();
        }

        return $admin_url;
    }

    public function check_wpvivid_plugin_active()
    {
        if (is_multisite())
        {
            if(!is_network_admin())
            {
                return ;
            }
        }

        if(!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $free_wpvivid_slug='wpvivid-backuprestore/wpvivid-backuprestore.php';
        if (is_multisite())
        {
            $active_plugins = array();
            //network active
            $mu_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            if(!empty($mu_active_plugins)){
                foreach ($mu_active_plugins as $plugin_name => $data){
                    $active_plugins[] = $plugin_name;
                }
            }
            $plugins=get_mu_plugins();
            if(count($plugins) == 0 || !isset($plugins[$free_wpvivid_slug])){
                $plugins=get_plugins();
            }
        }
        else
        {
            $active_plugins = get_option('active_plugins');
            $plugins=get_plugins();
        }

        if(!empty($plugins))
        {
            if(isset($plugins[$free_wpvivid_slug]))
            {
                if(version_compare('0.9.29',$plugins[$free_wpvivid_slug]['Version'],'>'))
                {
                    ?>
                    <div class="notice notice-warning" style="padding: 11px 15px;">
                        <?php echo sprintf(__('The free version of %s is required higher version to use %s pro. Please update the free version first.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup Plugin'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                if(!in_array($free_wpvivid_slug, $active_plugins))
                {
                    ?>
                    <div class="notice notice-warning" style="padding: 11px 15px;">
                        <?php echo sprintf(__('The free version of %s is required to use %s Pro. Please activate the free version first.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup Plugin'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>
                    </div>
                    <?php
                }
            }
            else
            {
                ?>
                <div class="notice notice-warning" style="padding: 11px 15px;">
                    <?php echo sprintf(__('The free version of %s is required to use %s Pro. Please install and activate the free version first.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup Plugin'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?> Click <a href="<?php esc_attr_e(apply_filters('wpvivid_get_admin_url', '').'plugin-install.php?s=WPvivid&tab=search&type=term'); ?>">here</a> to install.
                </div>
                <?php
            }
        }
    }

    public function display_ex()
    {
        global $wpvivid_plugin;
        remove_action('wpvivid_display_page',array($wpvivid_plugin->admin,'display'));

        if(isset($_REQUEST['auto_backup'])&&$_REQUEST['auto_backup']==1)
        {
            return;
        }
        $slug = apply_filters('wpvivid_access_white_label_slug', 'wpvivid_white_label');
        if(isset($_REQUEST[$slug])&&$_REQUEST[$slug]==1)
        {
            return;
        }
        do_action('show_notice');
        ?>
        <!--<div class="wrap">-->
            <?php
            //$this->display_tabs();
            ?>
        <!--</div>-->
        <?php
    }

    public function display_tabs()
    {
        if(!class_exists('WPvivid_Tab_Page_Container_Ex'))
            include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'includes/class-wpvivid-tab-page-container-ex.php';
        $this->main_tab=new WPvivid_Tab_Page_Container_Ex();

        $tabs = apply_filters('wpvivid_add_tab_page_ex', array());
        foreach ($tabs as $tab)
        {
            if(current_user_can('administrator'))
            {
                $this->main_tab->add_tab($tab['title'],$tab['slug'],$tab['func']);
            }
            else
            {
                foreach ($tab['caps'] as $cap)
                {
                    if(current_user_can($cap))
                    {
                        $this->main_tab->add_tab($tab['title'],$tab['slug'],$tab['func']);
                        break;
                    }
                }
            }
        }
        $this->main_tab->display();
        ?>
        <script>
            function switch_main_tab(id)
            {
                jQuery( document ).trigger( '<?php echo $this->main_tab->container_id ?>-show',id);
            }
            jQuery(document).ready(function($)
            {
                <?php
                if(isset($_REQUEST['tabs']))
                {
                ?>
                switch_main_tab('<?php echo $_REQUEST['tabs'];?>');
                <?php
                }
                $request_page = apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-remote-page-mainwp');
                if(isset($_REQUEST[$request_page]))
                {
                ?>
                switch_main_tab('remote_storage');
                <?php
                }
                $request_page = apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-restore-page-mainwp');
                if(isset($_REQUEST[$request_page]))
                {
                ?>
                switch_main_tab('backuplist');
                <?php
                }
                ?>
            });
        </script>
        <?php
    }

    public function enqueue_styles()
    {
        $screen_ids=array();
        $screen_ids=apply_filters('wpvivid_get_screen_ids',$screen_ids);
        if(in_array(get_current_screen()->id,$screen_ids))
        {
            wp_enqueue_style(WPVIVID_PRO_PLUGIN_SLUG.'jstree', WPVIVID_BACKUP_PRO_PLUGIN_URL . 'includes/js/jstree/dist/themes/default/style.min.css', array(), WPVIVID_BACKUP_PRO_VERSION, 'all');
            wp_enqueue_style(WPVIVID_PRO_PLUGIN_SLUG.'addon', WPVIVID_BACKUP_PRO_PLUGIN_URL . 'includes/css/wpvividdashboard-style.css', array(), WPVIVID_BACKUP_PRO_VERSION, 'all');
            wp_enqueue_style(WPVIVID_PRO_PLUGIN_SLUG.'staging', WPVIVID_BACKUP_PRO_PLUGIN_URL . 'includes/css/wpvivid-staging-custom.css', array(), WPVIVID_BACKUP_PRO_VERSION, 'all');
        }
    }

    public function enqueue_scripts()
    {
        $screen_ids=array();
        $screen_ids=apply_filters('wpvivid_get_screen_ids',$screen_ids);
        if(in_array(get_current_screen()->id,$screen_ids))
        {
            wp_enqueue_script(WPVIVID_PRO_PLUGIN_SLUG.'jstree', WPVIVID_BACKUP_PRO_PLUGIN_URL . 'includes/js/jstree/dist/jstree.min.js', array('jquery'), WPVIVID_BACKUP_PRO_VERSION, false);
            wp_enqueue_script(WPVIVID_PRO_PLUGIN_SLUG.'jsaddon', WPVIVID_BACKUP_PRO_PLUGIN_URL . 'includes/js/wpvivid-admin-addon.js', array('jquery'), WPVIVID_BACKUP_PRO_VERSION, false);
            wp_localize_script(WPVIVID_PRO_PLUGIN_SLUG, 'wpvivid_ajax_object_addon', array('ajax_url' => admin_url('admin-ajax.php'),'ajax_nonce'=>wp_create_nonce('wpvivid_ajax')));
        }
    }

    public function get_screen_ids($screen_ids)
    {
        $screen_ids[]='toplevel_page_'.apply_filters('wpvivid_white_label_slug', WPVIVID_PRO_PLUGIN_SLUG);
        return $screen_ids;
    }

    public function get_main_admin_menus($menu)
    {
        $menu['page_title']=apply_filters('wpvivid_white_label_display', 'WPvivid Backup');
        $html=apply_filters('wpvivid_white_label_display', 'WPvivid Backup');
        $menu['menu_title']=$html;
        $menu['menu_slug']= apply_filters('wpvivid_white_label_slug', WPVIVID_PRO_PLUGIN_SLUG);
        return $menu;
    }

    public function get_toolbar_menus($toolbar_menus)
    {
        $id='wpvivid_admin_menu';
        $title=apply_filters('wpvivid_white_label_display', 'WPvivid Backup');
        $toolbar_menus[$id]['title']=$title;
        return $toolbar_menus;
    }

    public function add_sidebar()
    {
        if(class_exists( 'WPvivid_Staging' )){
            $staging_class = 'wpvivid-dashicons-blue';
            $staging_url = esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-staging');
        }
        else{
            $staging_class = 'wpvivid-dashicons-grey';
            $staging_url = '#';
        }

        ?>
        <div id="postbox-container-1" class="postbox-container">

            <div class="meta-box-sortables ui-sortable">

                <div class="postbox  wpvivid-sidebar">

                    <h2 style="margin-top:0.5em;"><span class="dashicons dashicons-sticky wpvivid-dashicons-orange"></span>
                        <span><?php esc_attr_e(
                                'Troubleshooting', 'WpAdminStyle'
                            ); ?></span></h2>
                    <div class="inside" style="padding-top:0;">
                        <ul class="" >
                            <li style="border-top:1px solid #f1f1f1;"><span class="dashicons dashicons-editor-help wpvivid-dashicons-orange" ></span>
                                <a href="https://wpvivid.com/troubleshooting-issues-on-wpvivid-backup-pro"><b>Troubleshooting</b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li style="border-top:1px solid #f1f1f1;"><span class="dashicons dashicons-admin-generic wpvivid-dashicons-orange" ></span>
                                <a href="https://wpvivid.com/wpvivid-backup-plugin-advanced-settings.html"><b>Adjust Advanced Settings </b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>

                        </ul>
                    </div>

                    <h2><span class="dashicons dashicons-book-alt wpvivid-dashicons-orange" ></span>
                        <span><?php esc_attr_e(
                                'Documentation', 'WpAdminStyle'
                            ); ?></span></h2>
                    <div class="inside" style="padding-top:0;">
                        <ul class="">
                            <li style="border-top:1px solid #f1f1f1;"><span class="dashicons dashicons-backup  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/backup-migration-overview"><b>Backup</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=WPvivid'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-migrate  wpvivid-dashicons-blue"></span>
                                <a href="https://wpvivid.com/custom-migration-overview"><b>Auto-Migration</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-migration'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-editor-ul  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/wpvivid-backup-pro-backups-restore-overview"><b>Backups & Restoration</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-backup-and-restore'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-calendar-alt  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/wpvivid-backup-pro-schedule-overview"><b>Schedule</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-schedule'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-admin-site-alt3  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/wpvivid-backup-pro-remote-storage-overview"><b>Cloud Storage</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-remote'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-randomize  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/export-content"><b>Export/Import</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-export-import'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li style="display:none;"><span class="dashicons dashicons-format-gallery  wpvivid-dashicons-red"></span>
                                <a href="https://meowapps.com/plugin/meow-analytics/"><b>Image Bulk Optimization(beta)</b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li style="display:none;"><span class="dashicons dashicons-update  wpvivid-dashicons-green"></span>
                                <a href="https://meowapps.com/plugin/wplr-sync/"><b>Lazyload(beta)</b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-code-standards  wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/wpvivid-backup-pro-unused-images-cleaner"><b>Unused Image Cleaner (beta)</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url(apply_filters('wpvivid_get_admin_url', '').'admin.php?page=wpvivid-cleaner'); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li style="display:none;"><span class="dashicons dashicons-cloud  wpvivid-dashicons-orange"></span>
                                <a href="https://meowapps.com/plugin/meow-analytics/"><b>CDN Integration (coming soon)</b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li style="color:#eee; display:none;"><span class="dashicons dashicons-admin-site" ></span>
                                <a href="https://meowapps.com/plugin/wp-retina-2x/"><b>Cache (coming soon)</b></a>
                                <small><span style="float: right;"><a href="#" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                            <li><span class="dashicons dashicons-welcome-view-site <?php esc_attr_e($staging_class); ?>"></span>
                                <a href="https://wpvivid.com/wpvivid-backup-pro-create-staging-site"><b>WPvivid Staging</b></a>
                                <small><span style="float: right;"><a href="<?php echo esc_url($staging_url); ?>" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                            </li>
                        </ul>
                    </div>
                    <h2><span class="dashicons dashicons-businesswoman wpvivid-dashicons-green"></span>
                        <span><?php esc_attr_e(
                                'Support', 'WpAdminStyle'
                            ); ?></span></h2>
                    <div class="inside">

                        <ul class="">
                            <li><span class="dashicons dashicons-admin-comments wpvivid-dashicons-green"></span>
                                <a href="https://wpvivid.com/submit-ticket"><b>Submit A Ticket</b></a>
                                <br>
                                The ticket system is for WPvivid Pro users only. If you need any help with our plugin, submit a ticket and we will respond shortly.
                            </li>
                        </ul>

                    </div>

                    <!-- .inside -->

                </div>
                <!-- .postbox -->

            </div>
            <!-- .meta-box-sortables -->

        </div>
        <?php
    }

    public function check_need_update_pro()
    {
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        if($addons_cache===false)
            return false;
        if(isset($addons_cache['addons'])&&is_array($addons_cache['addons']))
        {
            foreach ($addons_cache['addons'] as $addon)
            {
                $data = $this->addons->get_local_addon_data($addon['slug']);
                if ($data === false)
                {
                    return true;
                } else {
                    if ($addon['active'])
                    {
                        if (version_compare($addon['version'], $data['Version'], '>'))
                        {
                            return true;
                        }
                    }
                }
            }
            if (version_compare(WPVIVID_BACKUP_PRO_VERSION, $addons_cache['pro']['version'], '<'))
            {
                return true;
            }
        }
        return false;
    }

    public function pro_update_row($file, $plugin_data)
    {
        if($this->check_need_update_pro())
        {
            $wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );

            if ( is_network_admin() || ! is_multisite() )
            {
                if ( is_network_admin() )
                {
                    $active_class = is_plugin_active_for_network( $file ) ? ' active' : '';
                } else {
                    $active_class = is_plugin_active( $file ) ? ' active' : '';
                }

                echo '<tr class="plugin-update-tr' . $active_class . '" data-plugin="' . esc_attr( $file ) . '"><td colspan="' . esc_attr( $wp_list_table->get_column_count() ) . '" class="plugin-update colspanchange"><div class="update-message notice inline notice-warning notice-alt"><p>';

                $admin_url = apply_filters('wpvivid_get_admin_url', ''). 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro');

                printf(
                    __( 'There is a new version of %1$s available.<a href="%2$s" %3$s>update now</a>' ),
                    $plugin_data['Name'],
                    $admin_url,
                    sprintf(
                        'aria-label="%s"',
                        /* translators: %s: plugin name */
                        esc_attr( sprintf( __( 'Update %s now' ), $plugin_data['Name'] ) )
                    )
                );

                echo '</p></div></td></tr>';
            }

            ?>
            <script>
                jQuery(document).ready(function ()
                {
                    jQuery('tr[data-slug=wpvivid-backup-pro]').addClass('update');
                });
            </script>
            <?php
        }
    }

    public function show_update_notices()
    {
        if (is_multisite())
        {
            if(!is_network_admin())
            {
                return ;
            }
        }
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        if($addons_cache!==false)
        {
            $need_update=false;
            $need_install=false;
            if(isset($addons_cache['addons'])&&is_array($addons_cache['addons']))
            {
                foreach ($addons_cache['addons'] as $addon)
                {
                    $data = $this->addons->get_local_addon_data($addon['slug']);
                    if ($data === false)
                    {
                        $need_install= true;
                    } else {
                        if ($addon['active'])
                        {
                            if (version_compare($addon['version'], $data['Version'], '>'))
                            {
                                $need_update= true;
                            }
                        }
                    }
                }
                if (version_compare(WPVIVID_BACKUP_PRO_VERSION, $addons_cache['pro']['version'], '<'))
                {
                    $need_update= true;
                }
            }

            $version=$addons_cache['pro']['version'];
            $show_time = get_option('wpvivid_need_update_pro_notice', false);

            if(time()>$show_time)
            {
                if($need_install)
                {
                    $message = '<div class="notice notice-warning notice-need-update-pro is-dismissible" style="padding: 11px 15px;">';
                    $message .= __('There is a Pro addon available to install.', 'wpvivid').' <a href="' . apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro') . '">Install now</a></div>';
                    echo wp_kses_post($message);
                }
                else if($need_update)
                {
                    $message = '<div class="notice notice-warning notice-need-update-pro is-dismissible" style="padding: 11px 15px;">';
                    $message .= sprintf(__('There is a new version of %s Pro available.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')).' <a href="' . apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro') . '">Update now</a> to Version ' . $version . ' </div>';
                    echo wp_kses_post($message);
                }

            }
        }
        ?>
        <script>
            jQuery(document).on('click', '.notice-need-update-pro .notice-dismiss', function(){
                var ajax_data = {
                    'action': 'wpvivid_hide_need_update_pro_notice'
                };
                var time_out = 30000;
                jQuery.ajax({
                    type: "post",
                    url: '<?php echo admin_url('admin-ajax.php');?>',
                    data: ajax_data,
                    success: function (data) {
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                    },
                    timeout: time_out
                });
            });
        </script>
        <?php
    }

    public function wpvivid_hide_need_update_pro_notice()
    {
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try {
            WPvivid_Setting::update_option('wpvivid_need_update_pro_notice', time() + 604800);
            $ret['result'] = 'success';
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_review_addon($json)
    {
        $default = false;
        $review = get_option('wpvivid_need_update_pro_notice', $default);
        $json['data']['wpvivid_need_update_pro_notice'] = $review;
        return $json;
    }

    public function migrate_notice()
    {
        global $wpvivid_plugin;
        remove_action('wpvivid_before_setup_page', array($wpvivid_plugin->admin, 'migrate_notice'));
        $migrate_notice=false;
        $migrate_status=WPvivid_Setting::get_option('wpvivid_migrate_status');
        if(!empty($migrate_status) && $migrate_status == 'completed')
        {
            $migrate_notice=true;
            echo '<div class="notice notice-warning is-dismissible"><p>'.__('Migration is complete and htaccess file is replaced. In order to successfully complete the migration, you\'d better reinstall 301 redirect plugin, firewall and security plugin, and caching plugin if they exist.').'</p></div>';
            WPvivid_Setting::delete_option('wpvivid_migrate_status');
        }
        $restore = new WPvivid_restore_data();
        if ($restore->has_restore())
        {
            $restore_status = $restore->get_restore_status();
            if ($restore_status === WPVIVID_PRO_RESTORE_COMPLETED)
            {
                $restore->clean_restore_data();
                do_action('wpvivid_rebuild_backup_list');
                $need_review=WPvivid_Setting::get_option('wpvivid_need_review');
                if($need_review=='not')
                {
                    WPvivid_Setting::update_option('wpvivid_need_review','show');
                    $msg = sprintf(__('Cheers! %s has successfully restored your website. If you found the plugin helpful, we would really appreciate a 5-star rating, which would motivate us to keep providing great features.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup plugin'));
                    WPvivid_Setting::update_option('wpvivid_review_msg',$msg);
                }
                else{
                    if(!$migrate_notice)
                    {
                        echo '<div class="notice notice-success is-dismissible"><p>'.__('Restore completed successfully.').'</p></div>';
                    }
                }
            }
        }
    }

    public function check_schedule_last_running()
    {
        if(defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON) {
            $default = array();
            $schedules = get_option('wpvivid_schedule_addon_setting', $default);
            foreach ($schedules as $schedule_id => $schedule_value) {
                if ($schedule_value['status'] == 'Active') {
                    $next_timestamp = wp_next_scheduled($schedule_value['id'], array($schedule_value['id']));
                    if ($next_timestamp === false) {
                        if (isset($schedule_value['week'])) {
                            $time['start_time']['week'] = $schedule_value['week'];
                        } else
                            $time['start_time']['week'] = 'mon';

                        if (isset($schedule_value['day'])) {
                            $schedule_data['day'] = $schedule_value['day'];
                        } else
                            $time['start_time']['day'] = '01';

                        if (isset($schedule_value['current_day'])) {
                            $schedule_data['current_day'] = $schedule_value['current_day'];
                        } else
                            $time['start_time']['current_day'] = "00:00";

                        $next_timestamp = WPvivid_Schedule_addon::get_start_time($time);
                    }
                    $current_timestamp = time();
                    if ($current_timestamp - $next_timestamp >= 86400) {
                        _e('<div class="notice notice-warning is-dismissible">
                                <p>We have detected that a backup was not triggered as scheduled. Please check whether your server-level cron is working properly.</p>
                            </div>');
                        break;
                    }
                }
            }
        }
    }

    public function check_extensions()
    {
        $common_setting = WPvivid_Setting::get_setting(false, 'wpvivid_common_setting');
        $db_connect_method = isset($common_setting['options']['wpvivid_common_setting']['db_connect_method']) ? $common_setting['options']['wpvivid_common_setting']['db_connect_method'] : 'wpdb';
        $need_php_extensions = array();
        $need_extensions_count = 0;
        $extensions=get_loaded_extensions();
        if(!function_exists("curl_init")){
            $need_php_extensions[$need_extensions_count] = 'curl';
            $need_extensions_count++;
        }
        if(!class_exists('PDO')){
            $need_php_extensions[$need_extensions_count] = 'PDO';
            $need_extensions_count++;
        }
        if(!function_exists("gzopen"))
        {
            $need_php_extensions[$need_extensions_count] = 'zlib';
            $need_extensions_count++;
        }
        if(!array_search('pdo_mysql',$extensions) && $db_connect_method === 'pdo')
        {
            $need_php_extensions[$need_extensions_count] = 'pdo_mysql';
            $need_extensions_count++;
        }
        if(!array_search('mbstring',$extensions))
        {
            $need_php_extensions[$need_extensions_count] = 'mbstring';
            $need_extensions_count++;
        }
        if(!empty($need_php_extensions)){
            $msg = '';
            $figure = 0;
            foreach ($need_php_extensions as $extension){
                $figure++;
                if($figure == 1){
                    $msg .= $extension;
                }
                else if($figure < $need_extensions_count) {
                    $msg .= ', '.$extension;
                }
                else if($figure == $need_extensions_count){
                    $msg .= ' and '.$extension;
                }
            }
            if($figure == 1){
                echo '<div class="notice notice-error"><p>'.sprintf(__('The %s extension is not detected. Please install the extension first.', 'wpvivid-backuprestore'), $msg).'</p></div>';
            }
            else{
                echo '<div class="notice notice-error"><p>'.sprintf(__('The %s extensions are not detected. Please install the extensions first.', 'wpvivid-backuprestore'), $msg).'</p></div>';
            }
        }

        if (!class_exists('PclZip')) include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
        if (!class_exists('PclZip')) {
            echo '<div class="notice notice-error"><p>'.__('Class PclZip is not detected. Please update or reinstall your WordPress.', 'wpvivid-backuprestore').'</p></div>';
        }

        $hide_notice = get_option('wpvivid_hide_wp_cron_notice', false);
        if(defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON && $hide_notice === false){
            echo '<div class="notice notice-error notice-wp-cron is-dismissible"><p>'.__('In order to execute the scheduled backups properly, please set the DISABLE_WP_CRON constant to false.', 'wpvivid-backuprestore').'</p></div>';
        }
    }

    public function check_custom_backup_default_exclude()
    {
        $custom_backup_history = get_option('wpvivid_custom_backup_history');

        $default_exclude = array();
        $uploads_exclude = get_option('wpvivid_custom_backup_default_exclude_uploads', $default_exclude);
        $upload_dir = wp_upload_dir();
        $upload_path = str_replace('\\','/', $upload_dir['basedir']);
        $upload_path = explode('/', $upload_path);
        $upload_path = implode(DIRECTORY_SEPARATOR, $upload_path);

        $check_upload_array = array('backwpup', 'ShortpixelBackups', 'backup', 'backup-guard');
        foreach ($check_upload_array as $upload_folder){
            if(file_exists($upload_path.DIRECTORY_SEPARATOR.$upload_folder)){
                if(!in_array($upload_folder, $uploads_exclude)){
                    $uploads_exclude[] = $upload_folder;
                    $need_push_array = true;
                    if(!empty($custom_backup_history['uploads_option']['exclude_uploads_list'])){
                        foreach ($custom_backup_history['uploads_option']['exclude_uploads_list'] as $key => $value){
                            if($key === $upload_folder){
                                $need_push_array = false;
                            }
                        }
                    }
                    if($need_push_array){
                        $temp_array = array();
                        $temp_array['name'] = $upload_folder;
                        $temp_array['type'] = 'wpvivid-custom-li-folder-icon';
                        $custom_backup_history['uploads_option']['exclude_uploads_list'][$upload_folder] = $temp_array;
                    }
                }
            }
        }

        update_option('wpvivid_custom_backup_history', $custom_backup_history);
        update_option('wpvivid_custom_backup_default_exclude_uploads', $uploads_exclude);
    }

    public function ajax_check_security($role='administrator')
    {
        check_ajax_referer( 'wpvivid_ajax', 'nonce' );
        $check=is_admin()&&current_user_can($role);
        $check=apply_filters('wpvivid_ajax_check_security',$check);
        if(!$check)
        {
            die();
        }
    }
}