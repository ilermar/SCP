<?php
require_once( APPPATH . '/models/scp_model.php');

class Citologycaltemplates_model extends Scp_model {

    public function __construct() 
    {
       parent::__construct('citologycal_template');
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

    function get_all($spc)
    {
        $this->db->order_by("key", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 
        
        $q = parent::_get_all(array(), $spc);

        $dataArray = array();

        foreach($q as $row) {
            $data  = new stdClass();
            $data->id = $row->id;
            $data->key = $row->key;
            array_push($dataArray, $data);
        }

 		return $dataArray;
    }
}