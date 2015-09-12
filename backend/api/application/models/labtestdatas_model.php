<?php
require_once( APPPATH . '/models/scp_model.php');

class Labtestdatas_model extends Scp_model {

    public function __construct() 
    {
        parent::__construct('labtest_data');
    }

	public function get($id, $spc)
	{
		/*$this->db->where('id', $id);
        $query = $this->db->get($this->tableName);
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        if($queryResult)
        {
            $queryResult[0]->id = scp_encrypt($queryResult[0]->id,$this->encrypt);
        }
        
        return $queryResult;*/
        return parent::_simple_get($id, $spc);
	}

    public function insert($data, $spc)
    {
        $queryResult = parent::_simple_insert($data, $spc);

        return $queryResult;
    }

    public function update($data, $spc)
    {
        /*$this->db->where('id',$data['id'] );
        $queryResult = $this->db->update($this->tableName, $data);
        $this->log_scp_error($queryResult, $spc);
        return $queryResult;*/
        return parent::_simple_update($data, $spc);
    }

    public function delete($id, $spc)
    {
    	$queryResult = $this->db->delete($this->tableName,array('id' => $id));
        $this->log_scp_error($queryResult, $spc);
        return $queryResult;
    }

    public function get_all($data, $spc)
    {
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
            scp_log_message(LOG_MINFO, $spc, 'Se establecio LIMIT START: '. $start . ' COUNT: ' . $count);
        }
        $this->db->order_by("id", "asc"); //para garantizar el mismo resultado cuando se utiliza LIMIT 
    	$query = $this->db->get_where($this->tableName,array());
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);

        $dataArray = array();
        foreach($queryResult as $data) {
            array_push($dataArray, $data);
        }
        return $dataArray;
    }
}