<?php
require_once( APPPATH . '/models/scp_model.php');

class Clinics_model extends Scp_model {

    public function __construct() {
        parent::__construct('clinic');
    }

    function get($id, $spc) 
    {
        return parent::_get($id, $spc);
    }

    function get_all($filters, $spc) 
    {
        $qFilters = array( );
        /*
        Realizar los filtros en base a los filtros recibidos
        */
        $br = 0;
        $tr = 0;
        foreach($filters as $key => $filter ) 
        {
            if( $key != 'bottom_row' && $key != 'top_row' )
            {
                $qFilters[$key] = $filter;
            }
            else
            {
                $br = $filters['bottom_row'];
                $tr = $filters['top_row'];
            }
        }
        if( $br != 0 || $tr != 0 )
        {
            $start = $br - 1;//para incluir el indice bottom tmbn
            $count = ($tr - $br) + 1;
            $this->db->limit($count,$start);
            scp_log_message(LOG_MINFO, $spc, 'Se establecio LIMIT START: '. $start . ' COUNT: ' . $count);
        }
        $this->db->order_by("name", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 
        if( isset( $filters['name'] ) )
        {
            $this->db->like('name', $filters['name']);    
        }
        if( isset( $filters['email'] ) )
        {
            $this->db->like('email', $filters['email']);
        }
        if( isset( $filters['phone_number'] ) )
        {
            $this->db->like('phone_number', $filters['phone_number']);
        }
        
        return parent::_get_all($qFilters, $spc);// en este caso losfiltros que se reciban van filter = value AND filter1 = value1
    }

    function insert($data, $spc) 
    {
        return parent::_insert($data, $spc);
    }

    function delete($id, $spc) 
    {       
        return parent::_delete($id, $spc);
    }

    function update($data, $spc) {
        return parent::_update($data, $spc);
    }

}
