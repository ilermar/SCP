<?php
require_once( APPPATH . '/models/scp_model.php');

class Settings_model extends Scp_model {

    const CURR_SETTINGS = 1;

    public function __construct() 
    {
        parent::__construct('settings');
    }

    function get($id, $spc) 
    {
        return parent::_get($id, $spc);
    }

    function update($data, $spc) 
    {   
        $data['id'] = self::CURR_SETTINGS;

        return parent::_simple_update($data, $spc);
    }

    function getCurrent( $spc )
    {
        return parent::_simple_get(self::CURR_SETTINGS, $spc, true);
    }

}
