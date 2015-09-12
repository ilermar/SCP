<?php

class Scp_model extends CI_Model {
    private $tableName;
    public function __construct($table) 
    {
       $this->tableName = $table;
    }

    function log_scp_error($query_result, $spc) 
    {       
        if(!$query_result)
        {
            scp_log_message(LOG_ERROR, $spc, $this->db->_error_number() . ' : ' . $this->db->_error_message());
        } 
    }

    function _get($id, $spc)
    {
        return $this->_simple_get(scp_decrypt($id,$this->scpencrypt), $spc);
    }

    function _simple_get($id, $spc)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->tableName);
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        if($queryResult)
        {
            $queryResult[0]->id = scp_encrypt($queryResult[0]->id,$this->scpencrypt);
        }
        
        return $queryResult;
    }

    function _insert($data, $spc) 
    {
        if(array_key_exists('id', $data))
        {
            $data['id'] = scp_decrypt($data['id'], $this->scpencrypt);
        }
        $queryResult = $this->db->insert($this->tableName, $data);        
        $this->log_scp_error($queryResult, $spc);
        if($queryResult)
        {
            $queryResult = array('id' => scp_encrypt($this->db->insert_id(),$this->scpencrypt));
        }
        
        return $queryResult;
    }

    function _simple_insert($data, $spc) 
    {
        $queryResult = $this->db->insert($this->tableName, $data);        
        $this->log_scp_error($queryResult, $spc);
        return $queryResult;
    }

    function _update($data, $spc) 
    {   
        $data['id'] = scp_decrypt($data['id'],$this->scpencrypt);
        
        return $this->_simple_update($data, $spc);
    }

    function _simple_update($data, $spc) 
    {   
        $this->db->where('id',$data['id'] );
        $queryResult = $this->db->update($this->tableName, $data);
        $this->log_scp_error($queryResult, $spc);
        return $queryResult;
    }

    function _get_all($data, $spc) 
    {
        $query = $this->db->get_where($this->tableName,$data);
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);

        $dataArray = array();
        foreach($queryResult as $data) {
            $data->id = scp_encrypt($data->id,$this->scpencrypt);
            array_push($dataArray, $data);
        }

        return $dataArray;
    }

    function _delete($id, $spc)
    {
        $queryResult = $this->db->delete($this->tableName,array('id' => scp_decrypt($id,$this->scpencrypt)));
        $this->log_scp_error($queryResult, $spc);
        return $queryResult;
    }

    function buildRequestData($notEmpty, $allowEmpty, $emptyIsNull, $inputData, &$data, $spc){
        
        //Si viene cadena vacía debe recharzarse
        foreach ($notEmpty as $field)
        {
            if(array_key_exists($field, $inputData))
            {
                if($inputData[$field] != '')
                {
                    $data[$field] = $inputData[$field];
                }
                else
                {
                    return 'Datos incompletos, falta ('. $field .')';
                }
            }                    
        }

        //Cadena vacía debe guardarse
        //$patientFields = array("name", "address", "city", "state", "phone_number_1", "phone_number_2", "phone_number_3", "email");
        foreach ($allowEmpty as $field)
        {
            if(array_key_exists($field, $inputData))
                $data[$field] = $inputData[$field];
        }
        //Cadena vacía = null
        //$patientFields = array("birth_date");
        foreach ($emptyIsNull as $field)
        {
            if(array_key_exists($field, $inputData) && $inputData[$field] != '')
                $data[$field] = $inputData[$field];
            else
                $data[$field] = NULL;
        }

        return NULL;
    }
}
