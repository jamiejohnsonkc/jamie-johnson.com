<?php
if (!defined('WPVIVID_BACKUP_PRO_PLUGIN_DIR'))
{
    die;
}
class WPvivid_pro_page
{
    public $main_tab;

    public function __construct()
    {
        include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR.'includes/class-wpvivid-connect-server.php';

        add_filter('wpvivid_get_screen_ids',array($this,'get_screen_ids'),11);
        add_filter('wpvivid_get_admin_menus',array($this,'get_admin_menus'),99);
        add_filter('wpvivid_get_toolbar_menus',array($this,'get_toolbar_menus'),20);

        //actions
        add_action('wpvivid_pro_update_event',array( $this,'check_pro_update_event'));

        //filters
        add_filter('wpvivid_history_addon', array($this, 'wpvivid_history_addon'), 11);
        add_filter('wpvivid_get_wpvivid_pro_url', array($this, 'wpvivid_get_wpvivid_pro_url'), 11, 2);
        add_filter('wpvivid_check_is_pro_mainwp', array($this, 'wpvivid_check_is_pro_mainwp'), 11);
        add_filter('wpvivid_get_wpvivid_info_addon_mainwp', array($this, 'wpvivid_get_wpvivid_info_addon_mainwp'), 11);
        add_filter('wpvivid_upgrade_plugin_addon_mainwp_v2', array($this, 'wpvivid_upgrade_plugin_addon_mainwp_v2'), 11);
        add_filter('wpvivid_get_upgrade_progress_addon_mainwp_v2', array($this, 'wpvivid_get_upgrade_progress_addon_mainwp_v2'), 11);
        add_filter('wpvivid_login_account_addon_mainwp', array($this, 'wpvivid_login_account_addon_mainwp'));

        //ajax
        add_action('wp_ajax_wpvivid_connect_account',array( $this,'connect_account'));
        add_action('wp_ajax_wpvivid_check_pro_update',array($this,'check_pro_update'));
        add_action('wp_ajax_wpvivid_active_site',array( $this,'active_site'));
        add_action('wp_ajax_wpvivid_update_addon',array( $this,'update_addon'));
        add_action('wp_ajax_wpvivid_update_all',array( $this,'update_all'));
        add_action('wp_ajax_wpvivid_auto_update_setting', array( $this, 'auto_update_setting' ));
        add_action('wp_ajax_wpvivid_add_active_info',array( $this,'add_active_info'));

        //dashboard
        $this->check_update_schedule();
    }

    public function get_screen_ids($screen_ids)
    {
        $screen_ids[]=apply_filters('wpvivid_white_label_screen_id', 'wpvivid-backup_page_wpvivid-pro');
        return $screen_ids;
    }

    public function get_dashboard_menu($submenus,$parent_slug)
    {
        $display = apply_filters('wpvivid_get_menu_capability_addon', 'menu_pro_page');
        if($display)
        {
            $submenu['parent_slug'] = $parent_slug;
            $submenu['page_title'] = apply_filters('wpvivid_white_label_display', 'License');
            $submenu['menu_title'] = 'License';
            $submenu['capability'] = 'administrator';
            $submenu['menu_slug'] = strtolower(sprintf('%s-pro', apply_filters('wpvivid_white_label_slug', 'wpvivid')));
            $submenu['index'] = 99;
            $submenu['function'] = array($this, 'init_page');
            $submenus[$submenu['menu_slug']] = $submenu;
        }
        return $submenus;
    }

    public function get_admin_menus($submenus)
    {
        $display = apply_filters('wpvivid_get_menu_capability_addon', 'menu_pro_page');
        if($display) {
            $submenu['parent_slug'] = apply_filters('wpvivid_white_label_slug', WPVIVID_PRO_PLUGIN_SLUG);
            $submenu['page_title'] = apply_filters('wpvivid_white_label_display', 'License');
            $submenu['menu_title'] = 'License';
            $submenu['capability'] = 'administrator';
            $submenu['menu_slug'] = strtolower(sprintf('%s-pro', apply_filters('wpvivid_white_label_slug', 'wpvivid')));
            $submenu['index'] = 16;
            $submenu['function'] = array($this, 'init_page');
            $submenus[$submenu['menu_slug']] = $submenu;
        }
        return $submenus;
    }

    public function get_toolbar_menus($toolbar_menus)
    {
        $admin_url = apply_filters('wpvivid_get_admin_url', '');
        $display = apply_filters('wpvivid_get_menu_capability_addon', 'menu_pro_page');
        if($display) {
            $menu['id'] = 'wpvivid_admin_menu_pro';
            $menu['parent'] = 'wpvivid_admin_menu';
            $menu['title'] = 'License';
            $menu['tab'] = 'admin.php?page=' . apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro');
            $menu['href'] = $admin_url . 'admin.php?page=' . apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro');
            $menu['capability'] = 'administrator';
            $menu['index'] = 16;
            $toolbar_menus[$menu['parent']]['child'][$menu['id']] = $menu;
        }
        return $toolbar_menus;
    }

    /***** license actions begin *****/
    public function check_pro_update_event(){
        try {
            set_time_limit(120);
            $default = false;
            $auto_update = WPvivid_Setting::get_option('wpvivid_auto_update_addon', $default);
            if(isset($auto_update) && $auto_update !== false)
            {
                if($auto_update == '1')
                {
                    $auto_update = true;
                }
                else {
                    $auto_update = false;
                }
            }
            else{
                $auto_update = true;
            }

            if($auto_update)
            {
                $info= get_option('wpvivid_pro_user',false);
                if($info===false)
                {
                    die();
                }

                $server=new WPvivid_Connect_server();
                if(!isset($info['email'])){
                    $info['email'] = false;
                }

                if(isset($info['token']))
                {
                    $user_info=$info['token'];
                }
                else
                {
                    $user_info=$info['password'];
                }
                $ret=$server->login($info['email'],$user_info,false);

                if($ret['result']=='success')
                {
                    if(isset($ret['status']['check_active']) && $ret['status']['check_active'] == 0){
                        WPvivid_Setting::update_option('wpvivid_pro_addons_cache',array());
                        delete_option('wpvivid_pro_user');
                        WPvivid_Setting::update_option('wpvivid_connect_server_last_error', 'Please enter your email and password/token to activate WPvivid Backup Pro.');
                    }
                    else{
                        WPvivid_Setting::update_option('wpvivid_pro_addons_cache',$ret['status']);
                        WPvivid_Setting::update_option('wpvivid_last_update_time',time());

                        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
                        if(version_compare(WPVIVID_BACKUP_PRO_VERSION,$addons_cache['pro']['version'],'<'))
                        {
                            $ret=$server->update_pro($info['email'],$user_info);

                            if($ret['result']=='failed')
                            {
                                die();
                            }
                        }

                        global $wpvivid_backup_pro;
                        foreach ($addons_cache['addons'] as $addon)
                        {
                            if ($addon['active'])
                            {
                                $data=$wpvivid_backup_pro->addons->get_local_addon_data($addon['slug']);
                                if($data===false||version_compare($data['Version'],$addon['version'],'<'))
                                {
                                    $ret=$server->install_addon($info['email'],$user_info,$addon['slug']);
                                    if($ret['result']=='failed')
                                    {
                                        die();
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    WPvivid_Setting::update_option('wpvivid_pro_addons_cache',array());
                    WPvivid_Setting::update_option('wpvivid_last_update_time',time());
                }

                $this->update_wpvivid_free();
            }
        }
        catch (Exception $e)
        {
        }
        die();
    }
    /***** license actions end *****/

    /***** license filters begin *****/
    public function wpvivid_history_addon($json){
        $default = false;
        $pro_addon_cache = get_option('wpvivid_pro_addons_cache', $default);
        $json['data']['wpvivid_pro_addons_cache'] = $pro_addon_cache;

        $pro_user = get_option('wpvivid_pro_user', $default);
        $json['data']['wpvivid_pro_user'] = $pro_user;

        $connect_key = get_option('wpvivid_connect_key', '');
        $json['data']['wpvivid_connect_key'] = $connect_key;
        return $json;
    }

    public function wpvivid_get_wpvivid_pro_url($url, $type){
        if($type === 'wasabi' || $type === 'pCloud') {
            $url = WPVIVID_BACKUP_PRO_PLUGIN_URL;
        }
        return $url;
    }

    public function wpvivid_check_is_pro_mainwp(){
        $ret['check_pro'] = false;
        $ret['check_install'] = true;
        $ret['check_login'] = false;
        $ret['latest_version'] = false;
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        if($addons_cache!==false)
        {
            if(isset($addons_cache['pro_user']) && isset($addons_cache['check_active']))
            {
                if ($addons_cache['pro_user'] && $addons_cache['check_active'])
                {
                    $ret['check_pro'] = true;
                    $ret['latest_version'] = $addons_cache['pro']['version'];
                }
            }
        }
        $user_info=get_option('wpvivid_pro_user',false);
        if($user_info===false)
        {
            $ret['check_login'] = false;
        }
        else {
            $ret['check_login'] = true;
        }
        if(!$addons_cache['check_active']){
            $ret['check_login'] = false;
        }
        return $ret;
    }

    public function wpvivid_get_wpvivid_info_addon_mainwp($data){
        global $wpvivid_backup_pro;
        $ret['need_update'] = $wpvivid_backup_pro->check_need_update_pro();
        $ret['current_version'] = WPVIVID_BACKUP_PRO_VERSION;
        return $ret;
    }

    public function wpvivid_upgrade_plugin_addon_mainwp_v2($data){
        try {
            $site_id = $data['site_id'];
            $upgrade_task = array();
            $upgrade_task['start_time'] = time();
            $upgrade_task['site_id'] = $site_id;
            $upgrade_task['status'] = 'running';
            self::wpvivid_update_upgrade_task($site_id, $upgrade_task);

            set_time_limit(120);
            $info= get_option('wpvivid_pro_user',false);
            if($info===false)
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                $upgrade_task['status'] = 'error';
                $upgrade_task['error'] = $ret['error'];
                self::wpvivid_update_upgrade_task($site_id, $upgrade_task);
                return $ret;
            }
            $server=new WPvivid_Connect_server();

            if(!isset($info['email'])){
                $info['email'] = false;
            }
            $addons_cache=get_option('wpvivid_pro_addons_cache',false);
            if(version_compare(WPVIVID_BACKUP_PRO_VERSION,$addons_cache['pro']['version'],'<'))
            {
                if(isset($info['token']))
                {
                    $user_info=$info['token'];
                }
                else
                {
                    $user_info=$info['password'];
                }
                $ret=$server->update_pro($info['email'],$user_info);

                if($ret['result']=='failed')
                {
                    $upgrade_task['status'] = 'error';
                    $upgrade_task['error'] = $ret['error'];
                    self::wpvivid_update_upgrade_task($site_id, $upgrade_task);
                    return $ret;
                }
            }

            global $wpvivid_backup_pro;
            foreach ($addons_cache['addons'] as $addon)
            {
                if ($addon['active'])
                {
                    $data=$wpvivid_backup_pro->addons->get_local_addon_data($addon['slug']);
                    if($data===false||version_compare($data['Version'],$addon['version'],'<'))
                    {
                        if(isset($info['token']))
                        {
                            $user_info=$info['token'];
                        }
                        else
                        {
                            $user_info=$info['password'];
                        }
                        $ret=$server->install_addon($info['email'],$user_info,$addon['slug']);
                        if($ret['result']=='failed')
                        {
                            $upgrade_task['status'] = 'error';
                            $upgrade_task['error'] = $ret['error'];
                            self::wpvivid_update_upgrade_task($site_id, $upgrade_task);
                            return $ret;
                        }
                    }
                }
            }
            $ret['result']='success';
            $upgrade_task['status'] = 'completed';
            self::wpvivid_update_upgrade_task($site_id, $upgrade_task);
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']=$e->getMessage();
            $upgrade_task['status'] = 'error';
            $upgrade_task['error'] = $ret['error'];
            self::wpvivid_update_upgrade_task($site_id, $upgrade_task);
        }
        return $ret;
    }

    public function wpvivid_get_upgrade_progress_addon_mainwp_v2($data){
        try{
            $need_delete_opt = false;
            $options = self::wpvivid_get_upgrade_tasks();
            $site_id = $data['site_id'];
            $ret['result']='success';
            if(isset($options[$site_id]) && !empty($options[$site_id])){
                $time_spend=time()-$options[$site_id]['start_time'];
                if($time_spend > 180){
                    $options[$site_id]['status'] = 'no_responds';
                    $options[$site_id]['error'] = 'Not responding for a long time.';
                    $need_delete_opt = true;
                }
                if($options[$site_id]['status'] === 'completed'){
                    $file_path = WPVIVID_BACKUP_PRO_PLUGIN_DIR.'/wpvivid-backup-pro.php';
                    $default_headers = array(
                        'Version' => 'Version'
                    );
                    $addon_data = get_file_data( $file_path, $default_headers);
                    if(!empty($addon_data['Version'])){
                        $ret['current_version'] = $addon_data['Version'];
                    }
                    else{
                        $ret['current_version'] = WPVIVID_BACKUP_PRO_VERSION;
                    }
                    $addons_cache=get_option('wpvivid_pro_addons_cache',false);
                    if(isset($addons_cache['addons'])&&is_array($addons_cache['addons'])) {
                        if (version_compare($ret['current_version'], $addons_cache['pro']['version'], '<')) {
                            $ret['need_update'] = true;
                        }
                    }
                    else {
                        $ret['need_update'] = false;
                    }
                    $need_delete_opt = true;
                }
                if($options[$site_id]['status'] === 'error'){
                    $need_delete_opt = true;
                }
                $ret['upgrade_task'] = $options[$site_id];
                if($need_delete_opt){
                    unset($options[$site_id]);
                    WPvivid_Setting::update_option('wpvivid_upgrade_plugin_task_mainwp', $options);
                }
            }
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']=$e->getMessage();
        }
        return $ret;
    }

    public function wpvivid_login_account_addon_mainwp($data){
        try
        {
            if(isset($data['login_info']['wpvivid_connect_key']))
            {
                WPvivid_Setting::update_option('wpvivid_connect_key', $data['login_info']['wpvivid_connect_key']);
            }
            if(isset($data['login_info']['wpvivid_pro_user']))
            {
                WPvivid_Setting::update_option('wpvivid_pro_user', $data['login_info']['wpvivid_pro_user']);
            }
            if(isset($data['login_info']['wpvivid_pro_addons_cache']))
            {
                WPvivid_Setting::update_option('wpvivid_pro_addons_cache', $data['login_info']['wpvivid_pro_addons_cache']);
            }
            WPvivid_Setting::update_option('wpvivid_last_update_time', time());
            global $wpvivid_backup_pro;
            $ret['need_update'] = $wpvivid_backup_pro->check_need_update_pro();
            $ret['current_version'] = WPVIVID_BACKUP_PRO_VERSION;
            $ret['result']='success';
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']=$e->getMessage();
        }
        return $ret;
    }
    /***** license filters end *****/

    /***** license useful function begin *****/
    public function is_logged(){
        $user_info=get_option('wpvivid_pro_user',false);
        if($user_info===false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public static function wpvivid_update_upgrade_task($site_id, $options){
        $upgrade_tasks = self::wpvivid_get_upgrade_tasks();
        $upgrade_tasks[$site_id]=$options;
        WPvivid_Setting::update_option('wpvivid_upgrade_plugin_task_mainwp', $upgrade_tasks);
    }

    public static function wpvivid_get_upgrade_tasks(){
        $default = array();
        $options = WPvivid_Setting::get_option('wpvivid_upgrade_plugin_task_mainwp', $default);
        return $options;
    }

    public function check_update_schedule(){
        if(!defined( 'DOING_CRON' ))
        {
            if(wp_get_schedule('wpvivid_pro_update_event')===false)
            {
                if(wp_schedule_event(time()+30, 'daily', 'wpvivid_pro_update_event')===false)
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function update_wpvivid_free(){
        $current = get_site_transient( 'update_plugins' );
        $plugin_file='wpvivid-backuprestore/wpvivid-backuprestore.php';
        if ( isset( $current->response[ $plugin_file ] ) )
        {
            include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

            $upgrader = new Plugin_Upgrader();
            $upgrader->upgrade( $plugin_file );

            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $plugin_list=array();
            $plugin_list[]='wpvivid-backuprestore/wpvivid-backuprestore.php';

            if(is_multisite())
                activate_plugins($plugin_list,'',true,true);
            else
                activate_plugins($plugin_list,'',false,true);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function handle_server_error($error){
        if(isset($error['error_code']))
        {
            if($error['error_code']==109||$error['error_code']==108||$error['error_code']==107)
            {
                delete_option('wpvivid_pro_user');
                delete_option('wpvivid_pro_addons_cache');
            }
        }

        WPvivid_Setting::update_option('wpvivid_connect_server_last_error',$error['error']);
    }
    /***** license useful function end *****/

    /***** license ajax begin *****/
    public function connect_account(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try
        {
            if(isset($_POST['password']))
            {
                if(empty($_POST['password']))
                {
                    $ret['result']='failed';
                    $ret['error']='A license is required.';
                    echo json_encode($ret);
                    die();
                }

                $user_info=$_POST['password'];
            }
            else
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                echo json_encode($ret);
                die();
            }

            if(isset($_POST['token']) && $_POST['token'] == '1'){
                $use_token = true;
            }
            else{
                $use_token = false;
            }

            $server=new WPvivid_Connect_server();
            $ret=$server->login(false,$user_info,true,$use_token);
            if(isset($ret['result']) && $ret['result']=='success')
            {
                if($ret['status']['pro_user'])
                {
                    $ret['pro_user']=1;
                    if($ret['status']['check_active'])
                    {
                        $ret['check_active']=1;
                    }
                    else
                    {
                        $ret['check_active']=0;
                    }

                    if(isset($ret['status']['pro']['version']) && version_compare(WPVIVID_BACKUP_PRO_VERSION,$ret['status']['pro']['version'],'<'))
                    {
                        $ret['need_update']=1;
                    }
                    else
                    {
                        $ret['need_update']=0;
                    }

                    if(!$wpvivid_backup_pro->addons->has_default_package())
                    {
                        $ret['need_install']=1;
                    }
                    else
                    {
                        $ret['need_install']=0;
                    }
                }
                else
                {
                    $ret['pro_user']=0;
                    $ret['check_active']=0;
                }
                WPvivid_Setting::update_option('wpvivid_pro_addons_cache',$ret['status']);
                WPvivid_Setting::update_option('wpvivid_last_update_time',time());
            }

            echo json_encode($ret);
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']= $e->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function check_pro_update(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try
        {
            $info= get_option('wpvivid_pro_user',false);
            if($info===false)
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                echo json_encode($ret);
                die();
            }

            $server=new WPvivid_Connect_server();
            if(!isset($info['email'])){
                $info['email'] = false;
            }
            if(isset($info['token']))
            {
                $user_info=$info['token'];
            }
            else
            {
                $user_info=$info['password'];
            }
            $ret=$server->login($info['email'],$user_info,false);

            if($ret['result']=='success')
            {
                WPvivid_Setting::update_option('wpvivid_pro_addons_cache',$ret['status']);
                WPvivid_Setting::update_option('wpvivid_last_update_time',time());
            }
            else
            {
                $this->handle_server_error($ret);
            }
            echo json_encode($ret);
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']= $e->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function active_site(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try
        {
            global $wpvivid_backup_pro;
            $info= get_option('wpvivid_pro_user',false);
            if($info===false)
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                echo json_encode($ret);
                die();
            }

            $server=new WPvivid_Connect_server();
            if(!isset($info['email'])){
                $info['email'] = false;
            }
            if(isset($info['token']))
            {
                $user_info=$info['token'];
            }
            else
            {
                $user_info=$info['password'];
            }
            $ret=$server->active_site($info['email'],$user_info);

            if($ret['result']=='success')
            {
                WPvivid_Setting::update_option('wpvivid_pro_addons_cache',$ret['status']);

                WPvivid_Setting::update_option('wpvivid_last_update_time',time());

                if(isset($ret['status']['pro']) && $ret['status']['pro']!==false)
                {
                    if(isset($ret['status']['pro']['version']) && version_compare(WPVIVID_BACKUP_PRO_VERSION,$ret['status']['pro']['version'],'<'))
                    {
                        if(isset($info['token']))
                        {
                            $user_info=$info['token'];
                        }
                        else
                        {
                            $user_info=$info['password'];
                        }
                        $ret=$server->update_pro($info['email'],$user_info);
                        if($ret['result']=='failed')
                        {
                            echo json_encode($ret);
                            die();
                        }
                    }
                }

                global $wpvivid_backup_pro;
                if(isset($ret['status']['addons']))
                {
                    foreach ($ret['status']['addons'] as $addon)
                    {
                        if ($addon['active'])
                        {
                            $data=$wpvivid_backup_pro->addons->get_local_addon_data($addon['slug']);
                            if($data===false||version_compare($data['Version'],$addon['version'],'<'))
                            {
                                if(isset($info['token']))
                                {
                                    $user_info=$info['token'];
                                }
                                else
                                {
                                    $user_info=$info['password'];
                                }
                                $ret=$server->install_addon($info['email'],$user_info,$addon['slug']);
                                if($ret['result']=='failed')
                                {
                                    echo json_encode($ret);
                                    die();
                                }
                            }
                        }
                    }
                }
            }

            echo json_encode($ret);
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']= $e->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function update_addon(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try
        {
            $info= get_option('wpvivid_pro_user',false);
            if($info===false)
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                echo json_encode($ret);
                die();
            }
            $server=new WPvivid_Connect_server();

            if(!isset($info['email'])){
                $info['email'] = false;
            }
            if(isset($info['token']))
            {
                $user_info=$info['token'];
            }
            else
            {
                $user_info=$info['password'];
            }
            $ret=$server->install_addon($info['email'],$user_info,$_POST['slug']);

            echo json_encode($ret);
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']=$e->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function update_all(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try
        {
            set_time_limit(120);
            $info= get_option('wpvivid_pro_user',false);
            if($info===false)
            {
                $ret['result']='failed';
                $ret['error']='Retrieving user information failed. Please try again later.';
                echo json_encode($ret);
                die();
            }
            $server=new WPvivid_Connect_server();

            if(!isset($info['email'])){
                $info['email'] = false;
            }
            $addons_cache=get_option('wpvivid_pro_addons_cache',false);
            if(version_compare(WPVIVID_BACKUP_PRO_VERSION,$addons_cache['pro']['version'],'<'))
            {
                if(isset($info['token']))
                {
                    $user_info=$info['token'];
                }
                else
                {
                    $user_info=$info['password'];
                }
                $ret=$server->update_pro($info['email'],$user_info);

                if($ret['result']=='failed')
                {
                    echo json_encode($ret);
                    die();
                }
            }

            global $wpvivid_backup_pro;
            foreach ($addons_cache['addons'] as $addon)
            {
                if ($addon['active'])
                {
                    $data=$wpvivid_backup_pro->addons->get_local_addon_data($addon['slug']);
                    if($data===false||version_compare($data['Version'],$addon['version'],'<'))
                    {
                        if(isset($info['token']))
                        {
                            $user_info=$info['token'];
                        }
                        else
                        {
                            $user_info=$info['password'];
                        }
                        $ret=$server->install_addon($info['email'],$user_info,$addon['slug']);
                        if($ret['result']=='failed')
                        {
                            echo json_encode($ret);
                            die();
                        }
                    }
                }
            }

            $ret['result']='success';
            echo json_encode($ret);
            die();
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']=$e->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function auto_update_setting(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try{
            if(isset($_POST['auto_update']) && is_string($_POST['auto_update'])){
                $auto_update = sanitize_text_field($_POST['auto_update']);
                WPvivid_Setting::update_option('wpvivid_auto_update_addon', $auto_update);
                $ret['result']='success';
                echo json_encode($ret);
            }
        }
        catch (Exception $e)
        {
            $ret['result']='failed';
            $ret['error']= $e->getMessage();
            echo json_encode($ret);
        }
        die();
    }

    public function add_active_info(){
        global $wpvivid_backup_pro;
        $wpvivid_backup_pro->ajax_check_security();
        try {
            if (isset($_POST['active-action'])) {
                if ($_POST['active-action'] == 'active-later') {
                    WPvivid_Setting::update_option('wpvivid_active_info', time() + 604800);
                } else {
                    WPvivid_Setting::update_option('wpvivid_active_info', time());
                }

                $options = array();
                $options['timeout'] = 30;
                wp_remote_request('http://access.wpvivid.com?action=' . $_POST['active-action'], $options);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }
    /***** license ajax end *****/

    /***** license ui display function begin *****/
    public function output_login_ex()
    {
        ?>
        <form action="">
            <!--<div style="margin-top: 10px; margin-bottom: 15px;"><input type="text" class="regular-text" id="wpvivid_account_user" placeholder="Email" autocomplete="off" required=""></div>-->
            <div style="margin-bottom: 15px;"><input type="password" class="regular-text" id="wpvivid_account_pw" placeholder="License" autocomplete="new-password" required=""></div>
            <!--<div style="margin-bottom: 15px;">
                <label>
                    <input type="checkbox" id="wpvivid_account_use_token">Use <code>license</code> instead of password
                </label>
            </div>-->
            <div style="margin-bottom: 10px; float: left; margin-left: 0; margin-right: 10px;"><input class="button-primary" id="wpvivid_active_btn" type="button" value="Activate"></div>
            <div class="spinner" id="wpvivid_login_box_progress" style="float: left; margin-left: 0; margin-right: 10px;"></div>
            <div style="float: left; margin-top: 4px;"><span id="wpvivid_log_progress_text"></span></div>
            <div style="clear: both;"></div>
        </form>
        <script>
            jQuery('#wpvivid_active_btn').click(function() {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_connect_account_and_active();
            });

            function wpvivid_connect_account_and_active() {
                //var value1 = jQuery('#wpvivid_account_user').val();
                var value2 = jQuery('#wpvivid_account_pw').val();
                var value3 = '1';

                var ajax_data={
                    'action':'wpvivid_connect_account',
                    //'user':value1,
                    'password':value2,
                    'token':value3,
                    'auto_login':false
                };

                var login_msg = '<?php echo sprintf(__('Logging in to your %s Pro account', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>';
                wpvivid_lock_login(true);
                wpvivid_login_progress(login_msg);

                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        if(jsonarray.pro_user===1)
                        {
                            if(jsonarray.check_active===1)
                            {
                                if(jsonarray.need_update===1||jsonarray.need_install===1)
                                {
                                    wpvivid_connect_update_all();
                                }
                                else
                                {
                                    wpvivid_login_progress('You have successfully logged in');
                                    location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&login_success'?>';
                                }
                            }
                            else
                            {
                                wpvivid_connect_active_site();
                            }
                        }
                        else
                        {
                            wpvivid_login_progress('You have successfully logged in');
                            location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&login_success'?>';
                        }
                    }
                    else
                    {
                        wpvivid_lock_login(false,jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('connect server and active', textStatus, errorThrown);
                    wpvivid_lock_login(false,error_message);
                });
            }

            function wpvivid_connect_update_all() {
                var ajax_data={
                    'action':'wpvivid_update_all'
                };

                var update_msg = '<?php echo sprintf(__('Updating %s Pro...', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>';
                wpvivid_login_progress(update_msg);

                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        wpvivid_login_progress('Update completed successfully!');
                        location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&update_success'?>';
                    }
                    else {
                        if(jsonarray.error === 'need_reactive'){
                            wpvivid_connect_active_site();
                        }
                        else {
                            wpvivid_lock_login(false, jsonarray.error);
                        }
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('updating', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_lock_login(false,error_message);
                });
            }

            function wpvivid_connect_active_site() {
                var ajax_data={
                    'action':'wpvivid_active_site',
                };
                wpvivid_login_progress('Activating your license on the current site');
                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        wpvivid_login_progress('Your license has been activated successfully');
                        location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&active_success'?>';
                    }
                    else {
                        wpvivid_lock_login(false,jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('active site', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_lock_login(false,error_message);
                });
            }

            function wpvivid_lock_login(lock,error='') {
                if(lock)
                {
                    jQuery('#wpvivid_active_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_login_box_progress').show();
                    jQuery('#wpvivid_login_box_progress').addClass('is-active');
                    jQuery('#wpvivid_connect_result').hide();
                    jQuery('#wpvivid_connect_result').html('');
                }
                else
                {
                    jQuery('#wpvivid_log_progress_text').html('');
                    jQuery('#wpvivid_login_box_progress').hide();
                    jQuery('#wpvivid_login_box_progress').removeClass('is-active');
                    jQuery('#wpvivid_active_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    if(error!=='')
                    {
                        wpvivid_display_pro_notice('Error', error);
                    }
                }
            }

            function wpvivid_login_progress(log) {
                jQuery('#wpvivid_log_progress_text').html(log);
            }
        </script>
        <?php
    }

    public function output_user_info_ex()
    {
        global $wpvivid_backup_pro;
        $b_has_update = $wpvivid_backup_pro->check_need_update_pro();
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);

        $white_label_setting=WPvivid_Setting::get_option('white_label_setting');
        $white_label_website_protocol = empty($white_label_setting['white_label_website_protocol']) ? 'https' : $white_label_setting['white_label_website_protocol'];
        $white_label_website = empty($white_label_setting['white_label_website']) ? 'wpvivid.com/my-account' : $white_label_setting['white_label_website'];

        if(isset($addons_cache['pro_user']) && $addons_cache['pro_user']==1){
            ?>
            <span class="dashicons dashicons-businessman wpvivid-dashicons-green"></span><span><a href="<?php echo esc_html($white_label_website_protocol); ?>://<?php echo esc_html($white_label_website); ?>" target="_blank">My Account</a></span>
            <span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span><span><a href="#" id="wpvivid_signout_btn">Sign Out</a></span>
            <span class="dashicons dashicons-editor-help wpvivid-dashicons-editor-help wpvivid-tooltip">
                <div class="wpvivid-bottom">
                    <!-- The content you need -->
                    <p>Sign out or switch to another account. Once signed out, you will need to re-enter a license to get WPvivid Pro authorization.</p>
                    <i></i> <!-- do not delete this line -->
                </div>
            </span>
            <?php
            if(!$addons_cache['check_active'])
            {
                $this->output_active_box();
            }
            else{
                ?>
                <p><span><code><?php echo home_url()?></code></span> has been activated</p>
                <?php
                if ($b_has_update) {
                    ?>
                    <p><input id="wpvivid_update_all_btn" type="button" class="button-primary ud_connectsubmit" value="update pro & install all addons with one click"></p>
                    <?php
                }
                else {
                    ?>
                    <p><input id="wpvivid_check_pro_update_btn" type="button" class="button-primary ud_connectsubmit" value="Check Update"></p>
                    <?php
                }
                ?>

                <?php
            }
        }
        else{
            ?>
            <p><span>You are not a Pro user yet, <a href="https://pro.wpvivid.com/pricing" target="_blank">get it now!</a></span></p>
            <p><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span><span><a href="#" id="wpvivid_signout_btn">Sign Out</a></span></p>
            <p><a id="wpvivid_refresh_user_accounts" style="cursor: pointer;">If you got a Pro account, click here to reload.</a></p>
            <?php
        }
        ?>
        <div class="spinner" id="wpvivid_user_info_box_progress" style="float: left;"></div>
        <div style="float: left; margin-top: 4px;"><span id="wpvivid_user_info_log_progress_text"></span></div>
        <div style="clear: both;"></div>
        <div id="wpvivid_action_result" style="display: none; margin-bottom: 10px;"></div>
        <?php
    }

    public function output_active_box(){
        ?>
        <p><span>Your website has not been activated. You can install and update addon after activation site.</span></p>
        <p><input id="wpvivid_active_site_btn" type="button" class="button-primary ud_connectsubmit" value="Active your site" onclick="wpvivid_active_site();"></p>
        <script>
            function wpvivid_active_site() {
                jQuery('#wpvivid_pro_notice').hide();
                var ajax_data={
                    'action':'wpvivid_active_site',
                };
                wpvivid_is_checking(true);
                wpvivid_user_info_lock_login(true);
                wpvivid_user_info_progress('Activating your license on the current site');
                jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    wpvivid_is_checking(true);
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&active_success'?>';
                    }
                    else {
                        wpvivid_user_info_lock_login(false, jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_is_checking(true);
                    var error_message = wpvivid_output_ajaxerror('active site', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false, error_message);
                });
            }
        </script>
        <?php
    }
    /***** license ui display function end *****/

    public function init_page()
    {
        global $wpvivid_backup_pro;

        $membership = 'N/A';
        $active_status = 'Inactive';
        $expire = 'N/A';

        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        if(isset($addons_cache['pro_user']) && isset($addons_cache['check_active'])) {
            if (!$addons_cache['pro_user'] || !$addons_cache['check_active']) {
                $active_status = 'Inactive';
            } else {
                $active_status = 'Active';
            }
        }
        else{
            $active_status = 'Inactive';
        }

        if(isset($addons_cache['memberships'])) {
            foreach ($addons_cache['memberships'] as $member => $value) {
                $membership = $value['name'];
                if($membership === 'WPvivid Backup Pro - Beta') {
                    $membership = 'WPvivid Backup Pro (Beta)';
                    $expire = $value['end_date'];
                    if($expire === 0){
                        $expire = '12-31-2019';
                    }
                }
                else {
                    $expire = $value['end_date'];
                    if($expire === 0){
                        $expire = 'Never';
                    }
                }
            }
        }

        $membership = apply_filters('wpvivid_white_label_display_ex', $membership);

        $version_compare = ' (Latest Version)';
        if(isset($addons_cache['pro'])&&is_array($addons_cache['pro'])) {
            if(version_compare(WPVIVID_BACKUP_PRO_VERSION, $addons_cache['pro']['version'], '<')) {
                $version_compare = '(Latest Version Available: '.$addons_cache['pro']['version'].')';
            }
        }

        $default = false;
        $auto_update = WPvivid_Setting::get_option('wpvivid_auto_update_addon', $default);
        if(isset($auto_update) && $auto_update !== false){
            if($auto_update == '1'){
                $auto_update_class = 'wpvivid-grey';
                $auto_update_text = 'Turn Off';
                $auto_update_status = 'Enabled';
            }
            else{
                $auto_update_class = 'wpvivid-green';
                $auto_update_text = 'Turn On';
                $auto_update_status = 'Disabled';
            }
        }
        else{
            $auto_update_class = 'wpvivid-grey';
            $auto_update_text = 'Turn Off';
            $auto_update_status = 'Enabled';
        }

        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h1><?php esc_attr_e( apply_filters('wpvivid_white_label_display', 'WPvivid').' Plugins - License', 'WpvividPlugins' ); ?></h1>

            <div id="wpvivid_pro_notice">
                <?php
                if(isset($_REQUEST['login_success']))
                {
                    _e('<div class="notice notice-success is-dismissible inline"><p>You have successfully logged in.</p></div>');
                }
                else if(isset($_REQUEST['update_success']))
                {
                    _e('<div class="notice notice-success is-dismissible inline"><p>Update completed successfully.</p></div>');
                }
                else if(isset($_REQUEST['active_success']))
                {
                    _e('<div class="notice notice-success is-dismissible inline"><p>Your license has been activated successfully.</p></div>');
                }
                else if(isset($_REQUEST['error']))
                {
                    if($_REQUEST['error'] === 'need_reactive'){
                        _e('<div class="notice notice-error inline is-dismissible"><p>Please enter your email and password/token to activate WPvivid Backup Pro.</p></div>');
                    }
                    else{
                        _e('<div class="notice notice-error inline is-dismissible"><p>'.$_REQUEST['error'].'</p></div>');
                    }
                    $last_error=get_option('wpvivid_connect_server_last_error',false);
                    if($last_error!==false)
                    {
                        delete_option('wpvivid_connect_server_last_error');
                    }
                }
                else
                {
                    $last_error=get_option('wpvivid_connect_server_last_error',false);
                    if($last_error!==false)
                    {
                        if(is_string($last_error))
                            _e('<div class="notice notice-error is-dismissible"><p>'.$last_error.'</p></div>');
                        delete_option('wpvivid_connect_server_last_error');
                    }
                }
                ?>
            </div>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <!-- main content -->
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <div class="wpvivid-backup">
                                <div class="wpvivid-welcome-bar wpvivid-clear-float">
                                    <div class="wpvivid-welcome-bar-left">
                                        <p><span class="dashicons dashicons-admin-network wpvivid-dashicons-large wpvivid-dashicons-green"></span><span class="wpvivid-page-title">License</span></p>
                                        <span class="about-description">Enter your license to activate WPvivid plugins and get plugin updates and support.</span>
                                    </div>
                                    <div class="wpvivid-welcome-bar-right">
                                        <p></p>
                                        <div style="float:right;">
                                            <span>Local Time:</span>
                                            <span>
                                                <a href="<?php esc_attr_e(apply_filters('wpvivid_get_admin_url', '').'options-general.php'); ?>">
                                                    <?php
                                                    $offset=get_option('gmt_offset');
                                                    echo date("l, F d, Y H:i",time()+$offset*60*60);
                                                    ?>
                                                </a>
                                            </span>
                                            <span class="dashicons dashicons-editor-help wpvivid-dashicons-editor-help wpvivid-tooltip">
                                                <div class="wpvivid-left">
                                                    <!-- The content you need -->
                                                    <p>Clicking the date and time will redirect you to the WordPress General Settings page where you can change your timezone settings.</p>
                                                    <i></i> <!-- do not delete this line -->
                                                </div>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="wpvivid-nav-bar wpvivid-clear-float">
                                        <span class="dashicons dashicons-lightbulb wpvivid-dashicons-orange"></span>
                                        <span> Tips: You can use either a father license or a child license to activate WPvivid plugins.</span>
                                    </div>
                                </div>

                                <div class="wpvivid-canvas wpvivid-clear-float">
                                    <div class="wpvivid-one-coloum">
                                        <div class="wpvivid-one-coloum wpvivid-workflow wpvivid-clear-float">
                                            <div class="wpvivid-two-col">
                                                <p><span class="dashicons dashicons-awards wpvivid-dashicons-blue"></span><span>Current Version: </span><span><?php echo WPVIVID_BACKUP_PRO_VERSION; ?></span><span><?php echo $version_compare; ?></span></p>
                                                <p><span class="dashicons dashicons-update-alt wpvivid-dashicons-blue"></span><span>Automatic Updates: </span><span id="auto_update_status"><?php _e($auto_update_status); ?></span><span class="wpvivid-rectangle <?php esc_attr_e($auto_update_class); ?>" id="wpvivid-auto-update-switch" title="Click here to disable automatic updates of WPvivid Plugin Pro" style="cursor:pointer;"><?php _e($auto_update_text); ?></span></p>
                                                <p><span class="dashicons dashicons-yes-alt wpvivid-dashicons-blue"></span><span>Status: </span><span><?php echo $active_status; ?></span></p>
                                                <p><span class="dashicons dashicons-calendar wpvivid-dashicons-blue"></span><span>Expiration Date: </span><span><?php echo $expire; ?></span></p>
                                            </div>
                                            <div class="wpvivid-two-col" style="padding-right:1em;">
                                                <?php
                                                if(isset($_REQUEST['sign_out'])) {
                                                    WPvivid_Setting::update_option('wpvivid_pro_addons_cache',array());
                                                    delete_option('wpvivid_pro_user');
                                                }
                                                if(isset($_REQUEST['switch'])||isset($_REQUEST['sign_out'])) {
                                                    $this->output_login_ex();
                                                }
                                                else{
                                                    if(!$this->is_logged()) {
                                                        $this->output_login_ex();
                                                    }
                                                    else{
                                                        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
                                                        if(!isset($addons_cache['pro_user']) || $addons_cache['pro_user']!=1 || !$addons_cache['check_active']){
                                                            WPvivid_Setting::update_option('wpvivid_pro_addons_cache',array());
                                                            delete_option('wpvivid_pro_user');
                                                            $this->output_login_ex();
                                                        }
                                                        else{
                                                            $this->output_user_info_ex();
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    if(isset($addons_cache['addons'])&&is_array($addons_cache['addons'])){
                                        if ($wpvivid_backup_pro->addons->has_default_package()){
                                            ?>
                                            <div class="wpvivid-one-coloum">
                                                <table class="widefat updates-table">
                                                    <thead>
                                                    <tr>
                                                        <td class="manage-column">
                                                            <span>PRO</span>
                                                        </td>
                                                        <td class="manage-column">
                                                            <span>Status</span>
                                                        </td>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="plugins">
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Custom database tables,Include/exclude folders
                                                                    backup </strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Create a staging or development enviroment</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Sending a staging or development enviroment to a remote
                                                                    storage</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Backup plugins, themes or the WordPress core files before
                                                                    updating</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Google Drive: Custom folder for each wordpress site</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Dropbox: Custom folder for each wordpress site</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Microsoft Onedrive: Custom folder for each wordpress
                                                                    site</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Amazon S3: Custom folder for each wordpress site</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>DigitalOcean Space: Custom folder for each wordpress
                                                                    site</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Custom content for each schedule</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Custom start time of schedule</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Restore what you want from a backup</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Migrate Wordpress site via uploading a backup to your remote
                                                                    storage</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>Custom email template.Sending email report to many email
                                                                    address</strong>
                                                            </p>
                                                        </td>
                                                        <td class="plugin-title">
                                                            <p>
                                                                <strong>
                                                                    active
                                                                </strong>
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- sidebar -->
                    <?php
                    do_action( 'wpvivid_add_sidebar' );
                    ?>

                </div>
            </div>
        </div>

        <script>
            function wpvivid_user_info_lock_login(lock,error='') {
                if(lock) {
                    jQuery('#wpvivid_change_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_signout_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_update_all_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_check_pro_update_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_refresh_user_accounts').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_user_info_box_progress').show();
                    jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                    jQuery('#wpvivid_action_result').hide();
                    jQuery('#wpvivid_action_result').html('');
                }
                else
                {
                    jQuery('#wpvivid_change_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_signout_btn').css({'pointer-events': 'none', 'opacity': '1'});
                    jQuery('#wpvivid_update_all_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_check_pro_update_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_refresh_user_accounts').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_user_info_box_progress').hide();
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    jQuery('#wpvivid_user_info_log_progress_text').html('');
                    if(error!=='')
                    {
                        wpvivid_display_pro_notice('Error', error);
                    }
                }
            }

            function wpvivid_user_info_progress(log) {
                jQuery('#wpvivid_user_info_log_progress_text').html(log);
            }

            jQuery('#wpvivid_change_btn').click(function() {
                wpvivid_change_user();
            });

            jQuery('#wpvivid_signout_btn').click(function() {
                wpvivid_sign_out();
            });

            jQuery('#wpvivid_update_all_btn').click(function() {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_click_update_all();
            });

            jQuery('#wpvivid_check_pro_update_btn').click(function() {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_check_pro_update();
            });

            jQuery('#wpvivid_refresh_user_accounts').click(function() {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_check_pro_update();
            });

            jQuery('#wpvivid-auto-update-switch').click(function() {
                if(jQuery(this).hasClass('wpvivid-green')){
                    var auto_update = '1';
                }
                else{
                    var auto_update = '0';
                }

                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                var ajax_data = {
                    'action': 'wpvivid_auto_update_setting',
                    'auto_update': auto_update
                };
                wpvivid_post_request_addon(ajax_data, function(data){
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                            location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro') ?>';
                        }
                        else if (jsonarray.result === 'failed') {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown) {
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('updating', textStatus, errorThrown);
                    alert(error_message);
                });
            });

            function wpvivid_change_user() {
                var descript = 'Are you sure switch accounts?';
                var ret = confirm(descript);
                if(ret === true)
                {
                    location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&switch=1'?>';
                }
            }

            function wpvivid_sign_out() {
                var descript = 'Are you sure you want to sign out?';
                var ret = confirm(descript);
                if(ret === true)
                {
                    location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&sign_out=1'?>';
                }
            }

            function wpvivid_click_update_all() {
                var ajax_data={
                    'action':'wpvivid_update_all'
                };
                wpvivid_user_info_lock_login(true);
                var update_txt = '<?php echo sprintf(__('Updating %s Pro...', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>';
                wpvivid_user_info_progress(update_txt);
                wpvivid_is_checking(true);
                jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    wpvivid_is_checking(false);
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&update_success'?>';
                    }
                    else {
                        if(jsonarray.error === 'need_reactive'){
                            //wpvivid_user_info_connect_active_site();
                            var admin_url = '<?php echo apply_filters('wpvivid_get_admin_url', ''); ?>';
                            location.href=admin_url+'admin.php?page=<?php echo apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro'); ?>&error='+jsonarray.error;
                        }
                        else {
                            wpvivid_user_info_lock_login(false, jsonarray.error);
                        }
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_is_checking(false);
                    var error_message = wpvivid_output_ajaxerror('updating', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false, error_message);
                });

            }

            function wpvivid_check_pro_update() {
                var ajax_data={
                    'action':'wpvivid_check_pro_update',
                };
                wpvivid_user_info_lock_login(true);
                wpvivid_user_info_progress('Checking Update...');
                wpvivid_is_checking(true);
                jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    wpvivid_is_checking(false);
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success') {
                        if(jsonarray.status.check_active){
                            location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro') ?>';
                        }
                        else{
                            jsonarray.error = 'need_reactive';
                            var admin_url = '<?php echo apply_filters('wpvivid_get_admin_url', ''); ?>';
                            location.href=admin_url+'admin.php?page=<?php echo apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro'); ?>&error='+jsonarray.error;
                        }
                    }
                    else {
                        if(jsonarray.error === 'need_reactive'){
                            //wpvivid_user_info_connect_active_site();
                            var admin_url = '<?php echo apply_filters('wpvivid_get_admin_url', ''); ?>';
                            location.href=admin_url+'admin.php?page=<?php echo apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro'); ?>&error='+jsonarray.error;
                        }
                        else {
                            var admin_url = '<?php echo apply_filters('wpvivid_get_admin_url', ''); ?>';
                            location.href=admin_url+'admin.php?page=<?php echo apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro'); ?>&error='+jsonarray.error;
                        }
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_is_checking(false);
                    var error_message = wpvivid_output_ajaxerror('check update', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false, error_message);
                });
            }

            function wpvivid_user_info_connect_active_site() {
                var ajax_data={
                    'action':'wpvivid_active_site',
                };
                wpvivid_user_info_progress('Activating your license on the current site');
                wpvivid_post_request_addon(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        wpvivid_user_info_progress('Your license has been activated successfully');
                        location.href='<?php echo apply_filters('wpvivid_get_admin_url', '') . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-pro').'&active_success'?>';
                    }
                    else {
                        wpvivid_user_info_lock_login(false,jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('active site', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false,error_message);
                });
            }

            function wpvivid_is_checking(is_checking){
                if(is_checking){
                    jQuery('#wpvivid_refresh_user_info').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_check_pro_update_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_change_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_signout_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_active_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_update_all_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                    jQuery('#wpvivid_active_site_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                }
                else{
                    jQuery('#wpvivid_refresh_user_info').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_check_pro_update_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_change_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_signout_btn').css({'pointer-events': 'none', 'opacity': '1'});
                    jQuery('#wpvivid_active_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_update_all_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_active_site_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }

            function wpvivid_add_active_info(name) {
                if(name=='active-now')
                {
                    wpvivid_click_switch_page('wrap', 'wpvivid_tab_pro', false);
                }
                var ajax_data={
                    'action': 'wpvivid_add_active_info',
                    'active-action': name
                };
                jQuery('#wpvivid_notice_rate').hide();
                wpvivid_post_request_addon(ajax_data, function(res)
                {
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                });
            }

            function wpvivid_display_pro_notice(notice_type, notice_message){
                if(notice_type === 'Success'){
                    var div = "<div class='notice notice-success is-dismissible inline'><p>" + notice_message + "</p>" +
                        "<button type='button' class='notice-dismiss' onclick='click_dismiss_pro_notice(this);'>" +
                        "<span class='screen-reader-text'>Dismiss this notice.</span>" +
                        "</button>" +
                        "</div>";
                }
                else{
                    if(typeof notice_message === 'object'){
                        if(typeof notice_message.response.message !== 'undefined'){
                            if(typeof notice_message.response.code !== 'undefined'){
                                var div = "<div class=\"notice notice-error inline\"><p>Error: " + notice_message.response.message + "(" + notice_message.response.code + ")" + "</p></div>";
                            }
                            else{
                                var div = "<div class=\"notice notice-error inline\"><p>Error: " + notice_message.response.message + "</p></div>";
                            }
                        }
                        else{
                            var div = "<div class=\"notice notice-error inline\"><p>Error: This error may be request not reaching or server not responding. Please try again later.</p></div>";
                        }
                    }
                    else{
                        var div = "<div class=\"notice notice-error inline\"><p>Error: " + notice_message + "</p></div>";
                    }
                }
                jQuery('#wpvivid_pro_notice').show();
                jQuery('#wpvivid_pro_notice').html(div);
            }

            function click_dismiss_pro_notice(obj){
                jQuery(obj).parent().remove();
            }

            jQuery(document).ready(function () {
                jQuery('input[option=wpvivid-active]').click(function()
                {
                    var name = jQuery(this).prop('name');
                    wpvivid_add_active_info(name);
                });
            });
        </script>
        <?php
    }
}