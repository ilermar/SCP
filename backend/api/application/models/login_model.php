<?php
require_once( APPPATH . '/models/scp_model.php' );

class Login_model extends Scp_model {

    public function __construct() 
    {
        parent::__construct('user');
    }

	public function login_user($us3r, $p455, $spc)
	{
		$this->db->where('email',$us3r);
        $this->db->where('password', hash( 'sha256', $us3r . P_SEPARATOR . $p455 ) );
        $query = $this->db->get('user');
    	$queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        scp_log_message(LOG_ERROR, $spc,"query nr " .  $query->num_rows());
        if($query->num_rows() == 1)
        {
	        if($queryResult)
	        {
	            $queryResult[0]->id = scp_encrypt($queryResult[0]->id,$this->scpencrypt);

	            $id = $queryResult[0]->id; //actualizar last_login del usuario
		        $this->db->where('id',$id );
		        $date = new DateTime();
		        $data = array('last_login' => $date->getTimestamp() );
		        $queryResupd = $this->db->update('user', $data);
		        $this->log_scp_error($queryResupd, $spc);
	        }
	        return $queryResult;
        }
        else
        {
        	scp_log_message(LOG_ERROR,$spc,'Más de un usuario registrados');
        	return FALSE;
        }
	}

	public function change_pwd($us3r, $p455, $newP455, $spc)
	{
		$this->db->where('email',$us3r);
        $this->db->where('password', hash( 'sha256', $us3r . P_SEPARATOR . $p455 ) );
        $query = $this->db->get('user');
    	$queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        if($query->num_rows() == 1)
        {
	        if($queryResult)
	        {
	            $id = $queryResult[0]->id; //actualizar last_login del usuario
		        $this->db->where('id',$id );
		        $data = array('password' => hash('sha256',$us3r . P_SEPARATOR . $newP455) );
		        $queryResult = $this->db->update('user', $data);
		        $this->log_scp_error($queryResult, $spc);
	        }
	        return $queryResult;
        }
        else
        {
        	scp_log_message(LOG_ERROR,$spc,'Más de un usuario registrados');
        	return FALSE;
        }
	}

    public function getByEmail($email, $spc)
    {
        $this->db->where('email',$email);
        $query = $this->db->get('user');
        $queryResult = $query->result();
        $this->log_scp_error($queryResult, $spc);
        scp_log_message(LOG_ERROR, $spc,"query nr " .  $query->num_rows());
        if($query->num_rows() == 1)
        {
            if($queryResult)
            {
                $queryResult[0]->id = scp_encrypt($queryResult[0]->id,$this->scpencrypt);
                $this->log_scp_error($queryResult, $spc);
            }
            return $queryResult[0];
        }
        else
        {
            scp_log_message(LOG_ERROR,$spc,'Más de un usuario registrados');
            return FALSE;
        }
    }
}
