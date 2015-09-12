<?php
require_once( APPPATH . '/models/scp_model.php');

class Doctors_model extends Scp_model {

    public function __construct() 
    {
        parent::__construct('doctor');       
    }

    function delete($id, $spc) 
    {
        return parent::_delete($id, $spc);
    }

    function get($id, $spc) 
    {
        return parent::_get($id, $spc);
    }

    function insert($data, $spc) 
    {
        if($data['birth_date'] == '')
        {
            $data['birth_date'] = NULL;
        }
        return parent::_insert($data, $spc);
    }

    function update($data, $spc) 
    {
        return parent::_update($data, $spc);
    }

    function get_all($data, $spc) 
    {
        $qFilters = array();
        $br = 0;
        $tr = 0;

        if(isset($data['top_row']) && isset($data['bottom_row']))
        {
            $tr = $data['top_row'];
            $br = $data['bottom_row'];
        }

        if( $br != 0 || $tr != 0 )
        {

            $start = $br - 1;//para incluir el indice bottom tmbn
            $count = ($tr - $br) + 1;
            $this->db->limit($count,$start);
            scp_log_message(LOG_MINFO, $spc, 'Se establecio LIMIT START: ' . $start . ' COUNT: ' . $count);
        }
        $this->db->order_by("name", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 

        if(!isset($data['autocomplete']))
        {
            if(isset($data['state']) && $data['state'] != '')
            {
                $qFilters['state'] = $data['state'];
            }
            if(isset($data['phone_number']) && $data['phone_number'] != '')
            {
                $where = "phone_number_1 = '" . $data['phone_number'] . "' OR phone_number_2 = '" . $data['phone_number'] . "' OR phone_number_3 = '" . $data['phone_number'] . "'";
                $this->db->where($where);
            }

            if(array_key_exists('name', $data))
                $this->db->like('name', $data['name']);
            if(array_key_exists('email', $data))
                $this->db->like('email', $data['email']);
            if(array_key_exists('specialty', $data))
                $this->db->like('specialty', $data['specialty']);
        }
        else
        {
            $this->db->like('name', $data['name']);
        }

        return parent::_get_all($qFilters, $spc);
    }

}
