<?php
require_once( APPPATH . '/models/scp_model.php');

class Medicalrecords_model extends Scp_model {

    public function __construct() 
    {
       parent::__construct('medical_record');
    }

    function get($id, $spc)
    {
    	return parent::_get($id, $spc);
    }

    function update_or_insert($data, $spc)
    {
        $q = parent::_get($data['id'], $spc);
        if ( $q) 
        {
            $q = parent::_update($data, $spc);
        }
        else
        {
            $q = parent::_insert($data, $spc);
        }

    	return $q;
    }
}