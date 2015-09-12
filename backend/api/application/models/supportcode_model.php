<?php
//require APPPATH . '/models/scp_model.php';
class Supportcode_model extends CI_Model {

	 public function __construct() 
    {
       parent::__construct();
    }  

    function get() 
    {
        $this->db->where('id','1');
        $query = $this->db->get('supportcode');
        return $query->result();
    }

    function update($data) 
    {   
        $this->db->where('id','1');
        return $this->db->update('supportcode', $data);
    }

    function get_next_support_code($type = 'U')
    {
        $separator = '';

        $code = $this->get();
        $code = $code[0]->sp_code;
        $spcode = $type . $separator . date('Ymdhis') . $separator . sprintf("%04s",   $code);
        
        if($code + 1 == 9999)
        {
            $code = 0;
        }
        $this->supportcode_model->update(array('id' => '1', 'sp_code' => $code + 1));

        return $spcode;
    }
}