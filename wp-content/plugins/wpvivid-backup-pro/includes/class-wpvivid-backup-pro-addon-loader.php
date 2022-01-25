<?php
if (!defined('WPVIVID_BACKUP_PRO_PLUGIN_DIR'))
{
    die;
}
class WPvivid_backup_pro_addon_loader
{
    private $addons_data;

    public function __construct()
    {
        $this->addons_data=array();
    }

    public function get_default_package()
    {
        $default_package=array();
        $default_package['wpvivid-backup-pro-all-in-one']['Name']='wpvivid-backup-pro-all-in-one';
        return $default_package;
    }

    public function has_default_package()
    {
        $b_find=true;
        $packages=$this->get_default_package();
        foreach ($packages as $key=>$package)
        {
            if(!array_key_exists($key,$this->addons_data))
            {
                $b_find=false;
            }
        }
        return $b_find;
    }

    public function load_local_addon()
    {
        if (is_dir(WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'addons') && $dir_handle = opendir(WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'addons')) {
            while (false !== ($file = readdir($dir_handle))) {
                if (is_file(WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'addons/' . $file) && preg_match('/\.php$/', $file)) {
                    $addon_data = $this->get_addon_data(WPVIVID_BACKUP_PRO_PLUGIN_DIR . 'addons/' . $file);
                    if (!empty($addon_data['WPvivid_addon'])) {
                        if (!isset($this->addons_data[$addon_data['Name']])) {
                            $this->addons_data[$addon_data['Name']]['Name'] = $addon_data['Name'];
                            $this->addons_data[$addon_data['Name']]['Version'] = $addon_data['Version'];
                            $this->addons_data[$addon_data['Name']]['Description'] = $addon_data['Description'];
                        } else if (version_compare($addon_data['Version'], $this->addons_data[$addon_data['Name']]['Version'], '>')) {
                            $this->addons_data[$addon_data['Name']]['Version'] = $addon_data['Version'];
                        }
                        $file_data['name'] = $file;
                        $file_data['require'] = $addon_data['Require'];
                        $file_data['need_init'] = $addon_data['Need_init'];
                        $file_data['no_need_load'] = $addon_data['No_need_load'];
                        $file_data['interface'] = $addon_data['Interface_Name'];
                        $this->addons_data[$addon_data['Name']]['files'][$file] = $file_data;
                    }
                }
            }
            @closedir($dir_handle);
        }

        foreach ($this->addons_data as $data)
        {
            if(!isset($data['files']))
                continue;
            foreach ($data['files'] as $file)
            {
                if(empty($file['no_need_load']))
                {
                    include_once WPVIVID_BACKUP_PRO_PLUGIN_DIR.'addons/'.$file['name'];
                }

                if(!empty($file['need_init']))
                {
                    $init=new $file['interface']();
                }
            }
        }
    }

    public function get_local_addon_data($slug)
    {
        if(isset($this->addons_data[$slug]))
        {
            return $this->addons_data[$slug];
        }
        else
        {
            return false;
        }
    }

    public function get_addon_data($file)
    {
        $default_headers = array(
            'Name' => 'Addon Name',
            'Version' => 'Version',
            'Description' => 'Description',
            'WPvivid_addon'=>'WPvivid addon',
            'Require'=>'Require',
            'Need_init'=>'Need_init',
            'No_need_load'=>'No_need_load',
            'Interface_Name'=>'Interface Name'
        );

        return  get_file_data( $file, $default_headers);
    }

    public function get_addons_data()
    {
        return $this->addons_data;
    }
}