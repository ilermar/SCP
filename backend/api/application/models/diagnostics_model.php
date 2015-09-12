<?php
require_once( APPPATH . '/models/scp_model.php');

class Diagnostics_model extends Scp_model {

    public function __construct() 
    {
       parent::__construct('diagnostic');
    }

    function get($id, $spc)
    {
    	return parent::_get($id, $spc);
    }

    function insert($data, $spc)
    {
    	return parent::_insert($data, $spc);
    }

    function update($data, $spc)
    {
    	return parent::_update($data, $spc);
    }

    function delete($id, $spc)
    {
    	return parent::_delete($id, $spc);
    }

    function get_all($data, $spc)
    {
    	return parent::_get_all($data, $spc);
    }
}    