<?php

class WPvivid_Staging_pro_page
{
    public $main_tab;
    public function __construct()
    {
        include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-staging-connect-server.php';
        //ajax;
        add_action('wp_ajax_wpvivid_connect_account_ex',array( $this,'connect_account'));
        add_action('wp_ajax_wpvivid_check_pro_update_ex',array($this,'check_pro_update'));

        add_action('wp_ajax_wpvivid_update_all_ex',array( $this,'update_all'));

        add_action('wp_ajax_wpvivid_auto_update_staging_setting', array($this, 'auto_update_staging_setting'));
        add_action('wpvivid_staging_update_event',array( $this,'check_staging_update_event'));
        $this->check_update_staging_schedule();
    }

    public function display_plugin_pro_page()
    {
        $this->init_page();
    }

    public function init_page()
    {
       ?>
        <div class="wrap" style="max-width:1720px;">
            <h1>
                <?php
                $plugin_display_name = 'WPvivid Staging';
                _e($plugin_display_name);
                ?>
            </h1>

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
                    _e('<div class="notice notice-error inline is-dismissible"><p>'.$_REQUEST['error'].'</p></div>');
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
            <?php
            if(!class_exists('WPvivid_Tab_Page_Container_Ex'))
                include_once WPVIVID_STAGING_PLUGIN_DIR . '/includes/class-wpvivid-tab-page-container-ex.php';
            $this->main_tab=new WPvivid_Tab_Page_Container_Ex();
            $this->main_tab->add_tab('WPvivid Staging','wpvivid_staging_pro',array($this, 'output_pro_page'));
            $this->main_tab->display();
            ?>
        </div>
        <?php
    }

    public function output_pro_page()
    {
        ?>
        <div id="pro-page" class="wrap-tab-content wpvivid_tab_pro">
            <?php

            if(isset($_REQUEST['sign_out']))
            {
                update_option('wpvivid_pro_addons_cache',array());
                delete_option('wpvivid_pro_user');
            }


            if(isset($_REQUEST['switch'])||isset($_REQUEST['sign_out']))
            {
                $this->output_login_ex();
            }
            else
            {
                if(!$this->is_logged())
                {
                    $this->output_login_ex();
                }
                else
                {
                    $this->output_user_info_ex();
                }
            }

            ?>
        </div>
        <script>
            function wpvivid_is_checking(is_checking)
            {
                if(is_checking)
                {
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

            function wpvivid_display_pro_notice(notice_type, notice_message)
            {
                if(notice_type === 'Success')
                {
                    var div = "<div class='notice notice-success is-dismissible inline'><p>" + notice_message + "</p>" +
                        "<button type='button' class='notice-dismiss' onclick='click_dismiss_pro_notice(this);'>" +
                        "<span class='screen-reader-text'>Dismiss this notice.</span>" +
                        "</button>" +
                        "</div>";
                }
                else{
                    var div = "<div class=\"notice notice-error inline\"><p>Error: " + notice_message + "</p></div>";
                }
                jQuery('#wpvivid_pro_notice').show();
                jQuery('#wpvivid_pro_notice').html(div);
            }

            function click_dismiss_pro_notice(obj)
            {
                jQuery(obj).parent().remove();
            }

        </script>
        <?php
    }

    public function is_logged()
    {
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

    public function output_login_ex()
    {
        $membership = 'N/A';
        $activation_number = 'N/A';
        $expire = 'N/A';
        $active_status = 'InActive';

        $white_label_website_protocol = 'https';
        $white_label_website = 'wpvivid.com';

        ?>
        <div class="postbox quickbackup-addon" id="wpvivid_login_box">
            <table class="wp-list-table widefat plugins" style="width: 100%;">
                <tbody>
                <tr>
                    <td class="column-primary" style="margin: 10px;">
                        <div>
                            <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/pro.png'); ?>" style="width:100px; height:100px;">
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div>
                            <form action="">
                                <div style="margin-top: 10px; margin-bottom: 15px;"><input type="text" class="regular-text" id="wpvivid_account_user" placeholder="Email" autocomplete="off" required /></div>
                                <div style="margin-bottom: 15px;"><input type="password" class="regular-text" id="wpvivid_account_pw" placeholder="Password" autocomplete="new-password" required /></div>
                                <div style="margin-bottom: 10px; float: left; margin-left: 0; margin-right: 10px;"><input class="button-primary" id="wpvivid_active_btn" type="button" value="Activate"/></div>
                                <div class="spinner" id="wpvivid_login_box_progress" style="float: left; margin-left: 0; margin-right: 10px;"></div>
                                <div style="float: left; margin-top: 4px;"><span id="wpvivid_log_progress_text"></span></div>
                                <div style="clear: both;"></div>
                            </form>
                            <div id="wpvivid_connect_result" style="display: none; margin-bottom: 10px;"></div>
                            <div style="background-color:#f5f5f5; padding:5px;">
                                <i>Tips: The email and password are the same as your registration on <a href="<?php echo esc_html($white_label_website_protocol); ?>://<?php echo esc_html($white_label_website); ?>" target="_blank"><?php echo apply_filters('wpvivid_white_label_website', 'wpvivid.com'); ?></a>. You have to activate the installation to get update and support</i>
                            </div>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div style="padding-left: 5px;">
                            <div style="margin-bottom: 10px;"><strong>WPvivid Staging</strong></div>
                            <div style="margin-bottom: 10px;"><i><?php echo sprintf(__('%s works as an individual WordPress plugin that allows you to create a staging site and publish a staging site to a live site.', 'wpvivid'), 'WPvivid Staging'); ?></i></div>
                        </div>
                        <div style="border-left:4px solid #00a0d2;padding-left:10px;">
                            <div>
                                <div style="margin-right: 5px; float: left; margin-bottom: 5px;">Current Version: </div><div style="float: left; margin-bottom: 5px;"><?php echo WPVIVID_STAGING_VERSION; ?></div>
                                <div style="clear: both;"></div>
                            </div>
                            <div>
                                <div style="margin-right: 5px; float: left; margin-bottom: 5px;">Membership Plan: </div><div style="float: left; margin-bottom: 5px;"><?php echo $membership; ?></div>
                                <div style="clear: both;"></div>
                            </div>
                            <div>
                                <div style="margin-right: 5px; float: left; margin-bottom: 5px;">Status: </div><div style="float: left; margin-bottom: 5px;"><?php echo $active_status; ?></div>
                                <div style="clear: both;"></div>
                            </div>
                            <div>
                                <div style="margin-right: 5px; float: left;">Expiration Date: </div><div style="float: left;"><?php echo $expire; ?></div>
                                <div style="clear: both;"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <script>

            jQuery('#wpvivid_active_btn').click(function()
            {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_connect_account_and_active();
            });

            function wpvivid_lock_login(lock,error='')
            {
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
                        /*jQuery('#wpvivid_connect_result').show();
                        jQuery('#wpvivid_connect_result').html(error);*/
                        wpvivid_display_pro_notice('Error', error);
                    }
                }
            }

            function wpvivid_login_progress(log)
            {
                jQuery('#wpvivid_log_progress_text').html(log);
            }

            function wpvivid_connect_account_and_active()
            {
                var value1 = jQuery('#wpvivid_account_user').val();
                var value2 = jQuery('#wpvivid_account_pw').val();
                //var value3=jQuery('#wpvivid_account_use_token').val();
                var ajax_data={
                    'action':'wpvivid_connect_account_ex',
                    'user':value1,
                    'password':value2,
                    //'token':value3,
                    'auto_login':false
                };

                var login_msg = '<?php echo sprintf(__('Logging in to your %s Pro account', 'wpvivid'), 'WPvivid Staging'); ?>';
                wpvivid_lock_login(true);
                wpvivid_login_progress(login_msg);

                wpvivid_post_request(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        if(jsonarray.pro_user===1)
                        {
                            if(jsonarray.need_update===1)
                            {
                                wpvivid_connect_update_all();
                            }
                            else
                            {
                                wpvivid_login_progress('You have successfully logged in');
                                location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&login_success'?>';
                            }
                        }
                        else
                        {
                            wpvivid_login_progress('You have successfully logged in');
                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&login_success'?>';
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

            function wpvivid_connect_update_all()
            {
                var ajax_data={
                    'action':'wpvivid_update_all_ex'
                };

                var update_msg = '<?php echo sprintf(__('Updating %s Pro...', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>';
                wpvivid_login_progress(update_msg);

                wpvivid_post_request(ajax_data, function(data)
                {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        wpvivid_login_progress('Update completed successfully!');
                        location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&update_success'?>';
                    }
                    else {
                        wpvivid_lock_login(false,jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('updating', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_lock_login(false,error_message);
                });
            }
        </script>
        <?php
    }

    public function output_user_info_ex()
    {
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        $user_info=get_option('wpvivid_pro_user',false);
        $membership = 'N/A';
        $activation_number = 'N/A';
        $expire = 'N/A';

        if(isset($addons_cache['memberships']))
        {
            foreach ($addons_cache['memberships'] as $member => $value)
            {
                $membership = $value['name'];
                if($membership === 'WPvivid Backup Pro - Beta')
                {
                    $membership = 'WPvivid Backup Pro (Beta)';
                    $expire = $value['end_date'];
                    if($expire === 0)
                    {
                        $expire = '12-31-2019';
                    }
                }
                else
                {
                    $expire = $value['end_date'];
                    if($expire === 0)
                    {
                        $expire = 'N/A';
                    }
                }

            }
        }

        $version_compare = '<div style="float: left; color: #9e5c07; margin-left: 5px;"> (Latest Version)</div>';
        if(isset($addons_cache['staging'])&&is_array($addons_cache['staging']))
        {
            if(version_compare(WPVIVID_STAGING_VERSION,$addons_cache['staging']['version'],'<'))
            {
                $version_compare = '<div style="float: left; color: #9e5c07; margin-left: 5px;"> (Latest Version Available: '.$addons_cache['staging']['version'].')</div>';
            }
        }

        $white_label_setting=get_option('white_label_setting');
        $white_label_website_protocol = empty($white_label_setting['white_label_website_protocol']) ? 'https' : $white_label_setting['white_label_website_protocol'];
        $white_label_website = empty($white_label_setting['white_label_website']) ? 'wpvivid.com' : $white_label_setting['white_label_website'];

        $b_has_update = $this->check_need_update_pro();
        ?>
        <div class="postbox quickbackup-addon" id="wpvivid_userinfo_box" style="padding-bottom: 0;">
            <?php
            if(isset($addons_cache['staging'])&&is_array($addons_cache['staging']))
            {
                $default = false;
                $auto_update = get_option('wpvivid_auto_update_staging', $default);
                if(isset($auto_update) && $auto_update !== false){
                    if($auto_update == '1'){
                        $auto_update_check = 'checked';
                    }
                    else{
                        $auto_update_check = '';
                    }
                }
                else{
                    $auto_update_check = 'checked';
                }
                ?>
                <div class="postbox schedule-tab-block wpvivid-setting-addon wpvivid-element-space-bottom">
                    <div class="wpvivid-element-space-right" style="margin-top: 6px; float: left;">
                        <label>
                            <input type="checkbox" name="auto_update_staging" <?php esc_attr_e($auto_update_check); ?> style="margin-top:0; margin-bottom:0; margin-right: 4px;" />
                            <span><strong><?php _e('Update WPvivid Staging plugin automatically', 'wpvivid'); ?></strong></span>
                        </label>
                    </div>
                    <div style="float: left;">
                        <input class="button-primary" type="button" id="wpvivid_save_auto_update_staging_btn" value="Save Change" />
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div style="clear: both;"></div>
                <?php
            }
            ?>
            <div class="wpvivid-element-space-bottom">
                <table class="wp-list-table widefat plugins" style="width: 100%;">
                    <tbody>
                    <tr>
                        <td class="column-primary" style="margin: 10px;">
                            <div>
                                <img src="<?php echo esc_url(WPVIVID_STAGING_PLUGIN_URL.'includes/images/pro.png'); ?>" style="width:100px; height:100px;">
                            </div>
                        </td>
                        <td class="column-description desc">
                            <div style="margin-top: 10px;">
                                <?php
                                if(isset($addons_cache['pro_user']) && $addons_cache['pro_user']==1 && isset($addons_cache['staging']) && $addons_cache['staging']['can_use_staging'])
                                {
                                    ?>
                                    <div style="float: left; margin-bottom: 10px; margin-right: 10px;"><?php echo sprintf(__('You are a %s Staging user', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid')); ?><a href="<?php echo esc_html($white_label_website_protocol); ?>://<?php echo esc_html($white_label_website); ?>" target="_blank"> Go to My Account</a></div>
                                    <div style="float: left; margin-bottom: 10px; margin-right: 10px;">
                                        <a href="#" id="wpvivid_change_btn">Switch Account</a>
                                    </div>
                                    <div style="float: left; margin-bottom: 10px;">
                                        <a href="#" id="wpvivid_signout_btn">Sign out</a>
                                    </div>
                                    <div style="clear: both;"></div>
                                    <?php

                                    ?>
                                    <div class="postbox" style="margin-bottom: 10px;">
                                        <div style="padding: 10px;">
                                            <span><?php echo home_url()?> has been activated</span>
                                        </div>
                                    </div>
                                    <?php

                                    if ($b_has_update)
                                    {
                                        ?>
                                        <div style="margin-bottom: 10px; float: left;">
                                            <input id="wpvivid_update_all_btn" type="button"
                                                   class="button-primary ud_connectsubmit"
                                                   value="Update WPvivid Staging with One Click"
                                            >
                                        </div>
                                        <?php
                                    }
                                    else {
                                        ?>
                                        <div style="margin-bottom: 10px; float: left;">
                                            <input id="wpvivid_check_pro_update_btn" type="button"
                                                   class="button-primary ud_connectsubmit" value="Check Update"
                                            >
                                        </div>
                                        <?php
                                    }
                                }
                                else
                                {
                                    if(!isset($addons_cache['pro_user']) || $addons_cache['pro_user']!=1){
                                        ?>
                                        <div style="margin-bottom: 10px; margin-right: 10px; float: left;">You are not a Pro user yet, <a href="https://pro.wpvivid.com/pricing" target="_blank">get it now!</a></div>
                                        <div style="float: left; margin-bottom: 10px; margin-right: 10px;">
                                            <a href="#" id="wpvivid_change_btn">Switch Accounts</a>
                                        </div>
                                        <div style="float: left; margin-bottom: 10px;">
                                            <a href="#" id="wpvivid_signout_btn">Sign out</a>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div style="float: left; margin-bottom: 10px;">
                                            <a id="wpvivid_refresh_user_accounts" style="cursor: pointer;">If you got a Pro account, click here to reload.</a>
                                        </div>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <div style="margin-bottom: 10px; margin-right: 10px; float: left;">You are not a WPvivid Staging user, <a href="https://pro.wpvivid.com/pricing" target="_blank">get it now!</a></div>
                                        <div style="float: left; margin-bottom: 10px; margin-right: 10px;">
                                            <a href="#" id="wpvivid_change_btn">Switch Accounts</a>
                                        </div>
                                        <div style="float: left; margin-bottom: 10px;">
                                            <a href="#" id="wpvivid_signout_btn">Sign out</a>
                                        </div>
                                        <div style="clear: both;"></div>
                                        <div style="float: left; margin-bottom: 10px;">
                                            <a id="wpvivid_refresh_user_accounts" style="cursor: pointer;">If your subscription or plan supports staging, click here to reload.</a>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <div class="spinner" id="wpvivid_user_info_box_progress" style="float: left;"></div>
                                <div style="float: left; margin-top: 4px;"><span id="wpvivid_user_info_log_progress_text"></span></div>
                                <div style="clear: both;"></div>
                                <div id="wpvivid_action_result" style="display: none; margin-bottom: 10px;"></div>
                                <div style="background-color:#f5f5f5; padding:5px;">
                                    <i>Tips: The email and password are the same as your registration on <a href="<?php echo esc_html($white_label_website_protocol); ?>://<?php echo esc_html($white_label_website); ?>" target="_blank"><?php echo apply_filters('wpvivid_white_label_website', 'wpvivid.com'); ?></a>. You have to activate the installation to get update and support</i>
                                </div>
                            </div>
                        </td>
                        <td class="column-description desc">
                            <div style="padding-left: 5px;">
                                <div style="margin-bottom: 10px;"><strong>WPvivid Staging</strong></div>
                                <div style="margin-bottom: 10px;"><i><?php echo sprintf(__('%s works as an individual WordPress plugin that allows you to create a staging site and publish a staging site to a live site.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Staging')); ?></i></div>
                            </div>
                            <div style="border-left:4px solid #00a0d2;padding-left:10px;">
                                <div>
                                    <div style="margin-right: 5px; float: left; margin-bottom: 5px;">Current Version: </div><div style="float: left; margin-bottom: 5px;"><?php echo WPVIVID_STAGING_VERSION; ?></div><?php _e($version_compare); ?>
                                    <div style="clear: both;"></div>
                                </div>
                                <div>
                                    <div style="margin-right: 5px; float: left; margin-bottom: 5px;">Membership Plan: </div><div style="float: left; margin-bottom: 5px;"><?php echo apply_filters('wpvivid_white_label_display_ex', $membership); ?></div>
                                    <div style="clear: both;"></div>
                                </div>
                                <div>
                                    <div style="margin-right: 5px; float: left;">Expiration Date: </div><div style="float: left;"><?php echo $expire; ?></div>
                                    <div style="clear: both;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            function wpvivid_user_info_lock_login(lock,error='')
            {
                if(lock)
                {
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

            function wpvivid_user_info_progress(log)
            {
                jQuery('#wpvivid_user_info_log_progress_text').html(log);
            }

            jQuery('#wpvivid_change_btn').click(function()
            {
                wpvivid_change_user();
            });
            //
            jQuery('#wpvivid_signout_btn').click(function()
            {
                wpvivid_sign_out();
            });

            jQuery('#wpvivid_update_all_btn').click(function()
            {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_click_update_all();
            });
            jQuery('#wpvivid_check_pro_update_btn').click(function()
            {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_check_pro_update();
            });
            jQuery('#wpvivid_refresh_user_accounts').click(function()
            {
                jQuery('#wpvivid_pro_notice').hide();
                wpvivid_check_pro_update();
            });

            jQuery('#wpvivid_save_auto_update_staging_btn').click(function(){
                if(jQuery('input:checkbox[name=auto_update_staging]').prop('checked')){
                    var auto_update = '1';
                }
                else{
                    var auto_update = '0';
                }
                jQuery(this).css({'pointer-events': 'none', 'opacity': '0.4'});
                var ajax_data = {
                    'action': 'wpvivid_auto_update_staging_setting',
                    'auto_update': auto_update
                };
                wpvivid_post_request(ajax_data, function(data){
                    jQuery(this).css({'pointer-events': 'auto', 'opacity': '1'});
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === 'success') {
                            location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro'; ?>';
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

            function wpvivid_change_user()
            {
                var descript = 'Are you sure switch accounts?';
                var ret = confirm(descript);
                if(ret === true)
                {
                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&switch=1'?>';
                }
            }

            function wpvivid_sign_out()
            {
                var descript = 'Are you sure you want to sign out?';
                var ret = confirm(descript);
                if(ret === true)
                {
                    location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&sign_out=1'?>';
                }
            }
            //
            function wpvivid_click_update_all()
            {
                var ajax_data={
                    'action':'wpvivid_update_all_ex'
                };
                wpvivid_user_info_lock_login(true);
                var update_txt = '<?php echo sprintf(__('Updating %s Pro...', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup')); ?>';
                wpvivid_user_info_progress(update_txt);
                wpvivid_is_checking(true);
                jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                wpvivid_post_request(ajax_data, function(data)
                {
                    wpvivid_is_checking(false);
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success')
                    {
                        location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro&update_success'?>';
                    }
                    else {
                        wpvivid_user_info_lock_login(false, jsonarray.error);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_is_checking(false);
                    var error_message = wpvivid_output_ajaxerror('updating', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false, error_message);
                });

            }

            function wpvivid_check_pro_update()
            {
                var ajax_data={
                    'action':'wpvivid_check_pro_update_ex',
                };
                wpvivid_user_info_lock_login(true);
                wpvivid_user_info_progress('Checking Update...');
                wpvivid_is_checking(true);
                jQuery('#wpvivid_user_info_box_progress').addClass('is-active');
                wpvivid_post_request(ajax_data, function(data)
                {
                    wpvivid_is_checking(false);
                    jQuery('#wpvivid_user_info_box_progress').removeClass('is-active');
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success') {
                        location.href='<?php echo apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-pro'; ?>';
                    }
                    else {
                        var admin_url = '<?php echo apply_filters('wpvividstg_get_admin_url', ''); ?>';
                        location.href=admin_url+'admin.php?page=<?php echo 'wpvividstg-pro'; ?>&error='+jsonarray.error;
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown)
                {
                    wpvivid_is_checking(false);
                    var error_message = wpvivid_output_ajaxerror('check update', textStatus, errorThrown);
                    alert(error_message);
                    wpvivid_user_info_lock_login(false, error_message);
                });
            }
        </script>
        <?php
    }

    public function check_need_update_pro()
    {
        $addons_cache=get_option('wpvivid_pro_addons_cache',false);
        if($addons_cache===false)
            return false;
        if(isset($addons_cache['staging']['version'])){
            $staging_version = $addons_cache['staging']['version'];
        }
        else{
            $staging_version = 0;
        }
        if (version_compare(WPVIVID_STAGING_VERSION, $staging_version, '<'))
        {
            return true;
        }
        return false;
    }

    public function handle_server_error($error)
    {
        if(isset($error['error_code']))
        {
            if($error['error_code']==109||$error['error_code']==108||$error['error_code']==107)
            {
                delete_option('wpvivid_pro_user');
                delete_option('wpvivid_pro_addons_cache');
            }
        }

        update_option('wpvivid_connect_server_last_error',$error['error']);
    }

    public function connect_account()
    {
        global $wpvivid_staging;
        $wpvivid_staging->ajax_check_security();
        try
        {
            if(isset($_POST['user'])&&isset($_POST['password']))
            {
                $email=sanitize_email($_POST['user']);
                if(!is_email($email))
                {
                    $ret['result']='failed';
                    $ret['error']='An email address is required.';
                    echo json_encode($ret);
                    die();
                }

                if(empty($_POST['password']))
                {
                    $ret['result']='failed';
                    $ret['error']='A password is required.';
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

            $server=new WPvivid_Staging_Connect_server();
            $ret=$server->login($email,$user_info,true,false);
            if($ret['result']=='success')
            {
                if($ret['status']['pro_user']&&$ret['status']['staging']['can_use_staging'])
                {
                    $ret['pro_user']=1;

                    if(version_compare(WPVIVID_STAGING_VERSION,$ret['status']['staging']['version'],'<'))
                    {
                        $ret['need_update']=1;
                    }
                    else
                    {
                        $ret['need_update']=0;
                    }
                }
                else
                {
                    $ret['pro_user']=0;
                }
                update_option('wpvivid_pro_addons_cache',$ret['status']);
                update_option('wpvivid_last_update_time',time());
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

    public function update_all()
    {
        global $wpvivid_staging;
        $wpvivid_staging->ajax_check_security();
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
            $server=new WPvivid_Staging_Connect_server();
            $addons_cache=get_option('wpvivid_pro_addons_cache',false);
            if(isset($addons_cache['staging']['version'])){
                $staging_version = $addons_cache['staging']['version'];
            }
            else{
                $staging_version = 0;
            }
            if(version_compare(WPVIVID_STAGING_VERSION,$staging_version,'<'))
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
            $ret['result']='success';
            //$ret['error']='Sorry, something went wrong. Please try again later or contact us.';
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

    public function check_pro_update()
    {
        global $wpvivid_staging;
        $wpvivid_staging->ajax_check_security();
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

            $server=new WPvivid_Staging_Connect_server();
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
                update_option('wpvivid_pro_addons_cache',$ret['status']);
                update_option('wpvivid_last_update_time',time());
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

    public function auto_update_staging_setting()
    {
        global $wpvivid_staging;
        $wpvivid_staging->ajax_check_security();
        try{
            if(isset($_POST['auto_update']) && is_string($_POST['auto_update'])){
                $auto_update = sanitize_text_field($_POST['auto_update']);
                update_option('wpvivid_auto_update_staging', $auto_update);
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

    public function check_update_staging_schedule()
    {
        if(!defined( 'DOING_CRON' ))
        {
            if(wp_get_schedule('wpvivid_staging_update_event')===false)
            {
                if(wp_schedule_event(time()+30, 'daily', 'wpvivid_staging_update_event')===false)
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function check_staging_update_event()
    {
        try
        {
            set_time_limit(120);
            $default = false;
            $auto_update = get_option('wpvivid_auto_update_staging', $default);
            if(isset($auto_update) && $auto_update !== false){
                if($auto_update == '1'){
                    $auto_update = true;
                }
                else{
                    $auto_update = false;
                }
            }
            else{
                $auto_update = true;
            }

            if($auto_update) {
                $info= get_option('wpvivid_pro_user',false);
                if($info===false)
                {
                    die();
                }

                $server=new WPvivid_Staging_Connect_server();
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
                    update_option('wpvivid_pro_addons_cache',$ret['status']);
                    update_option('wpvivid_last_update_time',time());

                    $addons_cache=get_option('wpvivid_pro_addons_cache',false);
                    if(isset($addons_cache['staging']['version'])){
                        $staging_version = $addons_cache['staging']['version'];
                    }
                    else{
                        $staging_version = 0;
                    }
                    if(version_compare(WPVIVID_STAGING_VERSION,$staging_version,'<'))
                    {
                        $ret=$server->update_pro($info['email'],$user_info);

                        if($ret['result']=='failed')
                        {
                            die();
                        }
                    }
                }
                else
                {
                    update_option('wpvivid_pro_addons_cache',array());
                    update_option('wpvivid_last_update_time',time());
                }
            }
        }
        catch (Exception $e)
        {
        }
        die();
    }
}