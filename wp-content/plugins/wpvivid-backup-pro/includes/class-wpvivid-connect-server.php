<?php

if (!defined('WPVIVID_BACKUP_PRO_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Connect_server
{
    private $url='https://wpvivid.com/wc-api/wpvivid_api';
    private $update_url='https://download.wpvivid.com';
    private $public_key;

    public function __construct()
    {
        /*
        if(empty($instance))
        {
            $permalink = get_permalink();
            $find = array( 'http://', 'https://' );
            $replace = '';
            $this->instance = md5(str_replace( $find, $replace, $permalink ));
            update_option('wpvivid_instance',$this->instance);
        }
        else
        {
            $this->instance=$instance;
        }*/
    }

    public function login($email=false,$user_info,$encrypt_user_info,$use_token=false,$get_key=false)
    {
        if($get_key)
            $public_key='';
        else
        $public_key=get_option('wpvivid_connect_key','');

        if(empty($public_key))
        {
            $public_key=$this->get_key();
            if($public_key===false)
            {
                $ret['result']='failed';
                $ret['error']='An error occurred when connecting to WPvivid Backup Pro server. Please try again later or contact us.';
                return $ret;
            }
            WPvivid_Setting::update_option('wpvivid_connect_key',$public_key);
        }

        $crypt=new WPvivid_crypt_addon($public_key);

        if($encrypt_user_info)
        {
            if($use_token)
            {
                $encrypt_user_info=$crypt->encrypt_user_token($user_info);
                $encrypt_user_info=base64_encode($encrypt_user_info);
            }
            else
            {
                $encrypt_user_info=$crypt->encrypt_user_info($user_info);
                $encrypt_user_info=base64_encode($encrypt_user_info);
            }

        }
        else
        {
            $encrypt_user_info=$user_info;
        }

        $crypt->generate_key();

        $json['user_info']=$encrypt_user_info;
        $json['domain'] = strtolower(home_url());
        $json=json_encode($json);
        $data=$crypt->encrypt_message($json);

        $action='get_status_v2';
        $url=$this->url;
        $url.='?request='.$action;
        $url.='&data='.rawurlencode(base64_encode($data));
        $options=array();
        $options['timeout']=30;
        $request=wp_remote_request($url,$options);

        if(!is_wp_error($request) && ($request['response']['code'] == 200))
        {
            $json= wp_remote_retrieve_body($request);
            $body=json_decode($json,true);
            if(!isset($body['data']) && isset($body['result']) && $body['result'] == 'failed' && isset($body['error']) && $body['error'] == 'not allowed'){
                $ret['result'] = 'failed';
                $ret['error'] = 'need_reactive';
                return $ret;
            }
            if(is_null($body))
            {
                $ret['result']='failed';
                $ret['error']='Decoding json failed. Please try again later.';
                return $ret;
            }
            if(isset($body['token']))
            {
                $encrypt_user_info=$crypt->encrypt_user_token($body['token']);
                $encrypt_user_info=base64_encode($encrypt_user_info);
                if($email !== false){
                    $info['email']=$email;
                }
                $info['token']=$encrypt_user_info;
                WPvivid_Setting::update_option('wpvivid_pro_user',$info);
            }
            else if($use_token)
            {
                if($email !== false) {
                    $info['email'] = $email;
                }
                $info['token']=$encrypt_user_info;
                WPvivid_Setting::update_option('wpvivid_pro_user',$info);
            }
            return $body;
        }
        else
        {
            $ret['result']='failed';
            if ( is_wp_error( $request ) )
            {
                $error_message = $request->get_error_message();
                $ret['error']="Sorry, something went wrong: $error_message. Please try again later or contact us.";
            }
            else if($request['response']['code'] != 200)
            {
                $ret['error']=$request['response']['message'];
            }
            else {
                $ret['error']=$request;
            }
            return $ret;
        }
    }

    public function active_site($email=false,$user_info)
    {
        $public_key=get_option('wpvivid_connect_key','');

        if(empty($public_key))
        {
            $public_key=$this->get_key();
            if($public_key===false)
            {
                $ret['result']='failed';
                $ret['error']='An error occurred when connecting to WPvivid Backup Pro server. Please try again later or contact us.';
                return $ret;
            }
            WPvivid_Setting::update_option('wpvivid_connect_key',$public_key);
        }

        $crypt=new WPvivid_crypt_addon($public_key);

        $encrypt_user_info=$user_info;

        $crypt->generate_key();

        $json['user_info']=$encrypt_user_info;
        $json['domain'] = strtolower(home_url());
        $json=json_encode($json);
        $data=$crypt->encrypt_message($json);

        $action='active_site';
        $url=$this->url;
        $url.='?request='.$action;
        $url.='&data='.rawurlencode(base64_encode($data));
        $options=array();
        $options['timeout']=30;
        $request=wp_remote_request($url,$options);

        if(!is_wp_error($request) && ($request['response']['code'] == 200))
        {
            $json= wp_remote_retrieve_body($request);
            $body=json_decode($json,true);
            if(is_null($body))
            {
                $ret['result']='failed';
                $ret['error']=$json;
                return $ret;
            }

            if(isset($body['token']))
            {
                $encrypt_user_info=$crypt->encrypt_user_token($body['token']);
                $encrypt_user_info=base64_encode($encrypt_user_info);
                if($email !== false) {
                    $info['email'] = $email;
                }
                $info['token']=$encrypt_user_info;
                WPvivid_Setting::update_option('wpvivid_pro_user',$info);
            }

            return $body;
        }
        else
        {
            $ret['result']='failed';
            if ( is_wp_error( $request ) )
            {
                $error_message = $request->get_error_message();
                $ret['error']="Sorry, something went wrong: $error_message. Please try again later or contact us.";
            }
            else if($request['response']['code'] != 200)
            {
                $ret['error']=$request['response']['message'];
            }
            else {
                $ret['error']=$request;
            }
            return $ret;
        }
    }

    public function install_addon($email=false,$user_info,$slug)
    {
        $public_key=get_option('wpvivid_connect_key','');
        if(empty($public_key))
        {
            $public_key=$this->get_key();
            if($public_key===false)
            {
                $ret['result']='failed';
                $ret['error']='An error occurred when connecting to WPvivid Backup Pro server. Please try again later or contact us.';
                return $ret;
            }
            WPvivid_Setting::update_option('wpvivid_connect_key',$public_key);
        }

        $crypt=new WPvivid_crypt_addon($public_key);

        $crypt->generate_key();

        $json['user_info']=$user_info;
        $json['domain'] = strtolower(home_url());
        $json['slug']=$slug;
        $json['v2']=1;
        $json=json_encode($json);
        $data=$crypt->encrypt_message($json);

        $url=$this->update_url;
        $data=base64_encode($data);
        $options=array();
        $options['body']['data']=$data;
        $options['timeout']=30;
        $request=wp_remote_post($url,$options);
        if(!is_wp_error($request) && ($request['response']['code'] == 200))
        {
            $json= wp_remote_retrieve_body($request);
            $body=json_decode($json,true);
            if(!isset($body['data']) && isset($body['result']) && $body['result'] == 'failed' && isset($body['error']) && $body['error'] == 'not allowed'){
                $ret['result'] = 'failed';
                $ret['error'] = 'need_reactive';
                return $ret;
            }

            if(is_null($body))
            {
                $ret['result']='failed';
                $ret['error']=$json;
                return $ret;
            }

            if(isset($body['token']))
            {
                $encrypt_user_info=$crypt->encrypt_user_token($body['token']);
                $encrypt_user_info=base64_encode($encrypt_user_info);
                if($email !== false) {
                    $info['email'] = $email;
                }
                $info['token']=$encrypt_user_info;
                WPvivid_Setting::update_option('wpvivid_pro_user',$info);
            }

            $data=base64_decode($body['data']);
            $data=$crypt->decrypt_message($data);

            $params=json_decode($data,1);
            if(is_null($params))
            {
                $ret['result']='failed';
                $ret['error']='Dectypting data failed. Please try again later.';
                $ret['data']=$body['data'];
                return $ret;
            }
            else
            {
                $path=WPVIVID_BACKUP_PRO_PLUGIN_DIR.'/addons/'.$params['file_name'];
                @unlink($path);

                file_put_contents($path,base64_decode($params['content']));

                if (!class_exists('PclZip'))
                    include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
                $archive = new PclZip($path);

                global $wpvivid_backup_pro;

                $addon_data=$wpvivid_backup_pro->addons->get_local_addon_data($slug);

                if($addon_data!==false)
                {
                    foreach ($addon_data['files'] as $file)
                    {
                        @unlink(WPVIVID_BACKUP_PRO_PLUGIN_DIR . '/addons/' . $file['name']);
                    }
                }

                $zip_ret = $archive->extract(PCLZIP_OPT_PATH,dirname($path),PCLZIP_OPT_REPLACE_NEWER);

                if(!$zip_ret)
                {
                    $ret['result']='failed';
                    $ret['error'] = $archive->errorInfo(true);
                }
                else
                {
                    @unlink($path);
                    $ret['result']='success';
                }

                return $ret;
            }
        }
        else
        {
            $ret['result']='failed';
            if ( is_wp_error( $request ) )
            {
                $error_message = $request->get_error_message();
                $ret['error']="Sorry, something went wrong: $error_message. Please try again later or contact us.";
            }
            else if($request['response']['code'] != 200)
            {
                $ret['error']=$request['response']['message'];
            }
            else {
                $ret['error']=$request;
            }
            return $ret;
        }
    }

    public function update_pro($email=false,$user_info)
    {
        $public_key=get_option('wpvivid_connect_key','');
        if(empty($public_key))
        {
            $public_key=$this->get_key();
            if($public_key===false)
            {
                $ret['result']='failed';
                $ret['error']='An error occurred when connecting to WPvivid Backup Pro server. Please try again later or contact us.';
                return $ret;
            }
            WPvivid_Setting::update_option('wpvivid_connect_key',$public_key);
        }

        $crypt=new WPvivid_crypt_addon($public_key);

        $crypt->generate_key();

        $json['user_info']=$user_info;
        $json['domain'] = strtolower(home_url());
        $json['slug']='wpvivid-backup-pro';
        $json['v2']=1;
        $json=json_encode($json);
        $data=$crypt->encrypt_message($json);

        $url=$this->update_url;
        $data=base64_encode($data);
        $options=array();
        $options['body']['data']=$data;
        $options['timeout']=30;
        $request=wp_remote_post($url,$options);

        if(!is_wp_error($request) && ($request['response']['code'] == 200))
        {
            $json= wp_remote_retrieve_body($request);
            $body=json_decode($json,true);
            if(!isset($body['data']) && isset($body['result']) && $body['result'] == 'failed' && isset($body['error']) && $body['error'] == 'not allowed'){
                $ret['result'] = 'failed';
                $ret['error'] = 'need_reactive';
                return $ret;
            }

            if(is_null($body))
            {
                $ret['result']='failed';
                $ret['error']=$json;
                return $ret;
            }

            if(isset($body['token']))
            {
                $encrypt_user_info=$crypt->encrypt_user_token($body['token']);
                $encrypt_user_info=base64_encode($encrypt_user_info);
                if($email !== false) {
                    $info['email'] = $email;
                }
                $info['token']=$encrypt_user_info;
                WPvivid_Setting::update_option('wpvivid_pro_user',$info);
            }

            $data=base64_decode($body['data']);
            $data=$crypt->decrypt_message($data);

            $params=json_decode($data,1);
            if(is_null($params))
            {
                $ret['result']='failed';
                $ret['error']='Decrypting data failed. Please try again later.';
                $ret['data']=$body['data'];
                return $ret;
            }
            else
            {
                $path=WPVIVID_BACKUP_PRO_PLUGIN_DIR.DIRECTORY_SEPARATOR.$params['file_name'];
                @unlink($path);

                file_put_contents($path,base64_decode($params['content']));

                if (!class_exists('PclZip'))
                    include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
                $archive = new PclZip($path);

                $zip_ret = $archive->extract(PCLZIP_OPT_PATH,dirname($path),PCLZIP_OPT_REPLACE_NEWER,PCLZIP_OPT_REMOVE_PATH,'wpvivid-backup-pro');

                if(!$zip_ret)
                {
                    $ret['result']='failed';
                    $ret['error'] = $archive->errorInfo(true);
                }
                else
                {
                    @unlink($path);
                    $ret['result']='success';
                }

                return $ret;
            }
        }
        else
        {
            $ret['result']='failed';
            if ( is_wp_error( $request ) )
            {
                $error_message = $request->get_error_message();
                $ret['error']="Sorry, something went wrong: $error_message. Please try again later or contact us.";
            }
            else if($request['response']['code'] != 200)
            {
                $ret['error']=$request['response']['message'];
            }
            else {
                $ret['error']=$request;
            }
            return $ret;
        }
    }

    public function set_key($public_key)
    {
        $this->public_key=$public_key;
    }

    public function get_key()
    {
        $options=array();
        $options['timeout']=30;
        $request=wp_remote_request($this->url.'?request=get_key',$options);

        if(!is_wp_error($request) && ($request['response']['code'] == 200))
        {
            $json= wp_remote_retrieve_body($request);
            $body=json_decode($json,true);
            if(is_null($body))
            {
               return false;
            }

            if($body['result']=='success')
            {
                $public_key=base64_decode($body['public_key']);
                if($public_key==null)
                {
                    return false;
                }
                else
                {
                    return $public_key;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}