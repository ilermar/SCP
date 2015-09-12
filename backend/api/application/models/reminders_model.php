<?php
require_once( APPPATH . '/models/scp_model.php');
class Reminders_model extends Scp_model {

    public function __construct() 
    {
        parent::__construct('reminder');
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
        $this->db->order_by("id", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 

        $auxArray = array();
        if($qFilters['register_date_start'] != FALSE)
        {
            $auxArray['register_date >'] = $qFilters['register_date_start'];
        }
        if($qFilters['register_date_end'] != FALSE)
        {
            $auxArray['register_date <'] = $qFilters['register_date_end'];
        }
        if($qFilters['reminder_date_start'] != FALSE)
        {
            $auxArray['reminder_date >'] = $qFilters['reminder_date_start'];
        }
        if($qFilters['reminder_date_end'] != FALSE)
        {
            $auxArray['reminder_date <'] = $qFilters['reminder_date_end'];
        }
        if( $qFilters['notes'] )
        {
            $this->db->like('notes', $qFilters['notes']);    
        }
        return parent::_get_all($auxArray, $spc);// en este caso losfiltros que se reciban van filter = value AND filter1 = value1
    }

    function insert($data ,$spc) 
    {
        if($data['reminder_date'] == '')
        {
            $data['reminder_date'] = NULL;
        }
        if($data['register_date'] == '')
        {
            $data['register_date'] = NULL;
        }
        
        $data['user_id'] = scp_decrypt($data['user_id'], $this->scpencrypt);
        
        return parent::_insert($data, $spc);
    }

    function delete($id, $spc) 
    {       
        return parent::_delete($id, $spc);
    }
    
    function update($data, $spc) 
    {   
        return parent::_update($data, $spc);
    }
}
