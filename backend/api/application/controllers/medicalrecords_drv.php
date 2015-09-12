<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Medicalrecords_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('medicalrecords_model');
        $this->load->model('supportcode_model');
    }

    function medicalrecord_get()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
        /***********************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [medicalrecord : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /***********************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de historial médico...');
        if(!$this->get('id'))
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió id del historial médico');
            $this->response(array('rm' => 'no se recibió id del historial médico', 'supportCode' => $spc), 400);
        }
        $medical = $this->medicalrecords_model->get($this->get('id'), $spc);
        if($medical)
        {
            $medical = $medical[0];
            $dataArray = array('supportCode' => $spc, 'data' => $medical);
            scp_log_message(LOG_MINFO, $spc, 'Se encontró el historial médico con id: ' . $this->get('id'));
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el historial médico id: ' . $this->get('id'));
            $this->response(array('rm' => 'No se encontró el historial médico id: ' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function medicalrecord_post()
    {
    	$spc = $this->supportcode_model->get_next_support_code();

        /***********************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [medicalrecord : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /***********************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando inserción de historial médico');
        $data = array(
            'id' => $this->post('id'),
            'json_data' => $this->post('json_data')
        );
        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo historial médico: ' . json_encode($data));
        $medical = $this->medicalrecords_model->update_or_insert($data, $spc);
        $dataArray = array(
            'supportCode' => $spc,
            'id' => $this->post('id')
        );
        if($medical)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se agregó el historial médico id: ' . $medical['id']);
            $dataArray['rm'] = 'Se agregó el historial médic id: ' . $medical['id'];
            $dataArray['id'] = $medical['id'];
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se agregó el historial médico . ' . $this->post('id') . ' error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se agregó el historial médico. ' . $this->post('id');
        }
        $this->response($dataArray, 200);
    }
}