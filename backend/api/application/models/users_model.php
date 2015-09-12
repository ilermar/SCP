<?php
require_once( APPPATH . '/models/scp_model.php');
class Users_model extends Scp_model {

    public function __construct() 
    {
        parent::__construct('user');
    }

    function delete($id, $spc) 
    {
        return parent::_delete($id, $spc);
    }

    function get($id, $spc) 
    {
        $this->db->select('id,name,profile,phone_number,email');
        return parent::_get($id, $spc);
    }

    function get_by_name($data)
    {
        $query = $this->db->get_where('user', $data);
        $queryResult = $query->result();
        $user_id = 0;
        if($queryResult)
        {
            $user_id = scp_encrypt( $queryResult[0]->id, $this->scpencrypt );
        }
        return $user_id;
    }

    function insert($data, $spc)
    {
        if(isset($data['password']) && $data['password'] != '')
        {
            $pa55 = hash('sha256', $data['email'] . P_SEPARATOR . $data['password']);
            $data['password'] = $pa55;
        }
        return parent::_insert($data, $spc);
    }

    function update($data, $spc) 
    {
        if(isset($data['password']) && $data['password'] != '')
        {
            $pa55 = hash('sha256', $data['email'] . P_SEPARATOR . $data['password']);
            $data['password'] = $pa55;
        }
        return parent::_update($data, $spc);
    }

    function get_all_by_email( $email, $spc )
    {
        $qFilters = array(
            'email' => $email
        );
        return parent::_get_all($qFilters, $spc);
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
            scp_log_message(LOG_MINFO, $spc, 'Se estableció LIMIT START: ' . $start . ' COUNT: ' . $count);
        }
        $this->db->order_by("name", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 

        if(isset($data['profile']) && $data['profile'] != '')
        {
            $qFilters['profile'] = $data['profile'];
        }
        
        $this->db->like('name', $data['name']);
        $this->db->like('phone_number', $data['phone_number']);
        $this->db->like('email', $data['email']);

        return parent::_get_all($qFilters, $spc);
    }

}
