<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Diagnostics_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('diagnostics_model');
        $this->load->model('supportcode_model');
    }

    function diagnostic_get()
    {
    	$scp = $this->supportcode_model->get_next_support_code();
    	/***************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [diagnostic : get]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/***************************************/

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de diagnostico...');
    	if(!$this->get('id'))
    	{
    		scp_log_message(LOG_ERROR, $spc, 'No se recibio id del diagnostico');
    		$this->response(array('rm' => 'No se recibio id del diagnostico.'), 400);
    	}

    	$diagnostic = $this->diagnostic_model->get($this->get('id'), $spc);

    	if($diagnostic)
    	{
    		$diagnostic = $diagnostic[0];
    		$dataArray = array('supportCode' => $spc, 'data' = $diagnostic);
    		scp_log_message(LOG_MINFO, $spc, 'Se encontro el diagnostico con id: ' . $this->get('id'));
    		$this->response($dataArray, 200);
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se encontro el diagnostico id: ' . $this->get('id'));
    		$this->response(array('rm' => 'No se encotro el diagnostico ' . $this->get('id'), 'supportCode' => $spc), 404);
    	}
    }

    function diagnostic_post()
    {
    	$scp = $this->supportcode_model->get_next_support_code();
    	/***************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [diagnostic : post]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/***************************************/

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando insercion de diagnostico...');
    	$data = array('sub_labtest_id' => $this->post('sub_labtest_id'),
    		'diagnostico' => $this->post('diagnostico'));

    	scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo diagnostico: ' . json_encode($data));
    	$diagnostic = $this->diagnostic_model->insert($data, $spc);
    	$dataArray = array('supportCode' => $spc);
    	$data = array('id' => '');
    	if($diagnostic)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Se agrego el diagnostico id: ' . $diagnostic['id']);
    		$dataArray['rm'] = 'Se agrego el diagnostico id: ' . $diagnostic['id'];
    		$data = $diagnostic;
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se agrego el diagnostico. ' . $this->post('diagnostico') . ' error: ' . $this->db->_error_message());
    		$dataArray['rm'] = 'No se agrego el diagnostico. ' . $this->post('diagnostico') . ' error: ' . $this->db->_error_message();
    	}
    	$dataArray['data'] = $data;
    	$this->response($dataArray, 200);
    }

    function diagnostic_put()
    {
    	$scp = $this->supportcode_model->get_next_support_code();
    	/***************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [diagnostic : put]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/***************************************/

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando actualizacion de diagnostico...');
    	$data = array('id' => $this->put('id'),
    		'sub_labtest_id' => $this->put('sub_labtest_id'),
    		'diagnostico' => $this->put('diagnostico'));
    	scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar diagnostico: ' . json_encode($data));
    	$diagnostic = $this->diagnostic_model->update($data, $spc);

    	$dataArray = array('supportCode' => $spc);
    	$data = array('id' => '');
    	if($diagnostic)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Se actualizo el diagnostico id: ' . $this->put('id'));
    		$dataArray['rm'] = 'Se actualizo el diagnostico';
    		$data = $this->put('id');
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se actualizo el diagnostico . ' . $this->put('diagnostico') . ' error: ' . $this->db->_error_message());
    		$dataArray['rm'] = 'No se actualizo el diagnostico . ' . $this->put('diagnostico') . ' error: ' . $this->db->_error_message();
    	}
    	$dataArray['data'] = $data;
    	$this->response($dataArray, 200);
    }

    function diagnostic_delete($id)
    {
    	$scp = $this->supportcode_model->get_next_support_code();
    	/***************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [diagnostic : delete]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/***************************************/	

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de diagnóstico...');
    	if(!$id)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se recibió el id del diagnóstico.');
    		$this->response(array('rm' => 'No se recibió el id del diagnóstico.', 'supportCode' => $spc), 400);
    	}
    	scp_log_message(LOG_MINFO, $spc, 'Diagnóstico a eliminar id: ' . $id);
    	$diagnostic = $this->diagnostic_model->delete($id, $spc);
    	if($diagnostic)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'El diagnóstico fué eliminado id: ' . $id);
    		$dataArray = array('supportCode' => $spc, 'data' => $id);
    		$this->response($dataArray, 200);
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'El diagnóstico ' .  $id . ' no fué eliminado');
    		$this->response(array('rm' => 'El diagnóstico ' .  $id . ' no fué eliminado', 'supportCode' => $spc), 404);
    	}
    }

    function diagnostics_get()
    {
    	$scp = $this->supportcode_model->get_next_support_code();
    	/***************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [diagnostic : delete]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/***************************************/

    	$data = array('sub_labtest_id' => $this->get('sub_labtest_id'),
    		'diagnostico' => $this->get('diagnostico'));

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de diagnosticos con filtros: ' . json_encode($data));
    	$diagnostics = $this->diagnostic_model->get_all($data, $spc);

    	if($diagnostics)
    	{
    		$responseData = array('supportCode' => $spc, 'data' => $diagnostics);
    		scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($diagnostics) . 'registros de diagnosticos.');
    		$this->response($responseData, 200);	
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se encontraron diagnosticos, se regresa 404 HTTP code');
    		$this->response(array('rm' => 'No se encontraron diagnosticos', 'supportCode' => $spc), 404);
    	}
    }
}