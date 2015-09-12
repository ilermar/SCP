<?php
require_once( APPPATH . '/models/scp_model.php');

class Labtest_model extends Scp_model {


    public function __construct() 
    {
        parent::__construct('labtest');

        $this->tableViewName = 'full_labtest';
    }

	public function get($id, $spc)
	{
        $decryptedId = scp_decrypt($id,$this->scpencrypt);
		$this->db->where('id', $decryptedId);
        $query = $this->db->get($this->tableViewName);
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        if($queryResult)
        {
            $queryResult[0]->id = scp_encrypt($queryResult[0]->id,$this->scpencrypt);
        }
        
        return $queryResult;
	}

    public function nextKeyNumber($keyPrefix, $scp)
    {
        $nextKeyNumber = 1;

        $this->db->select_max('key_number');
        $this->db->where('key_prefix', $keyPrefix);
        $res1 = $this->db->get('labtest');

        if ($res1->num_rows() > 0)
        {
            $res2 = $res1->result_array();
            $nextKeyNumber = $res2[0]['key_number'] + 1;
        }
        
        return $nextKeyNumber;
    }

    public function insert($data, $spc)
    {
    	return parent::_insert($data, $spc);
    }

    public function update($data, $spc)
    {
    	return parent::_update($data, $spc);
    }

    public function delete($id, $spc)
    {
    	return parent::_delete($data, $spc);
    }

    public function get_all($filters, $spc)
    {/*
        Realizar los filtros en base a los filtros recibidos
        */
        $qFilters = array( );
        $auxArray = array( );
        $br = 0;
        $tr = 0;
        foreach($filters as $key => $filter ) 
        {
            if( $key != 'bottom_row' && $key != 'top_row' )
            {
                $auxArray[$key] = $filter;
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
            scp_log_message(LOG_MINFO, $spc, 'Se estableció LIMIT START: '. $start . ' COUNT: ' . $count);
        }
        $this->db->order_by("id", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 
        if( $filters['register_date_start'] != FALSE)
        {
            $qFilters['register_date >'] = $auxArray['register_date_start'];
        }
        if( $filters['register_date_end'] != FALSE)
        {
            $qFilters['register_date <'] = $auxArray['register_date_end'];
        }
        if( $filters['patient_name'] )
        {
            $this->db->like('patient_name', $auxArray['patient_name']);    
        }
        if( $filters['doctor_name'] )
        {
            $this->db->like('doctor_name', $auxArray['doctor_name']);    
        }
        if( $filters['type'] )
        {
            $this->db->where('type &' . $auxArray['type'] . ' <> 0');    
        }
        if( $filters['main_doctor'] )
        {
            $qFilters['main_doctor'] = $auxArray['main_doctor'];
        }
        if( $filters['key_prefix'] )
        {
            $qFilters['key_prefix'] = $auxArray['key_prefix'];
        } 
        if( $filters['key_number'] )
        {
            $qFilters['key_number'] = $auxArray['key_number'];
        } 
        if( $filters['status'] )
        {
            $qFilters['status'] = $auxArray['status'];
        } 

    	$query = $this->db->get_where($this->tableViewName,$qFilters);
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        $dataArray = array();
        foreach($queryResult as $data) {
            $data->id = scp_encrypt($data->id,$this->scpencrypt);
            array_push($dataArray, $data);
        }
        return $dataArray;
    }
}