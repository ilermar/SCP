<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Citologycaltemplates_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('citologycaltemplates_model');
        $this->load->model('supportcode_model');
    }

    function citologycaltemplate_get()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
        /************************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [citologycaltemplate : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /************************************************************************************/
     	scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de machote....');
     	if(!$this->get('id'))
     	{
     		scp_log_message(LOG_ERROR, $spc, 'No se recibió id del machote');
     		$this->response(array('rm' => 'no se recibió id del machote', 'supportCode' => $spc), 400);
     	}
     	$cittemplate = $this->citologycaltemplates_model->get($this->get('id'), $spc);
     	if($cittemplate)
     	{
     		$cittemplate = $cittemplate[0];
     		$dataArray = array('supportCode' => $spc, 'data' => $cittemplate);
     		scp_log_message(LOG_MINFO, $spc, 'Se encontró el machote con id: ' . $this->get('id'));
     		$this->response($dataArray, 200);
     	}
     	else
     	{
     		scp_log_message(LOG_MINFO, $spc, 'No se encontró el machote id: ' . $this->get('id'));
     		$this->response(array('rm' => 'No se encontró el machote id: ' . $this->get('id'), 'supportCode' => $spc), 404);
     	}
    }

	function citologycaltemplate_post()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        /************************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [citologycaltemplate : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /************************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando inserción de machote');
        $data = array(
            'key' => $this->post('key'),
        	'json_data' => $this->post('json_data')
        );
        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo machote: ' . json_encode($data));
        $cittemplate = $this->citologycaltemplates_model->insert($data, $spc);
        $dataArray = array(
            'supportCode' => $spc,
            'id' => ''
        );
        if($cittemplate)
        {
        	scp_log_message(LOG_MINFO, $spc, 'Se agregó el machote id: ' . $cittemplate['id']);
        	$dataArray['rm'] = 'Se agregó el machote id: ' . $cittemplate['id'];
        	$dataArray['id'] = $cittemplate['id'];
        }
        else
        {
        	scp_log_message(LOG_MINFO, $spc, 'No se agregó el machote . ' . $this->post('key') . ' error: ' . $this->db->_error_message());
        	$dataArray['rm'] = 'No se agregó el machote. ' . $this->post('key');
        }
        $this->response($dataArray, 200);
	}

	function citologycaltemplate_put()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        /************************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [citologycaltemplate : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /************************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualización de machote...');
        $data = array(
            'id' => $this->put('id'),
        	'key' => $this->put('key'),
        	'json_data' => $this->put('json_data')
        );
     	scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar machote: ' . json_encode($data));
     	$cittemplate = $this->citologycaltemplates_model->update($data, $spc);
     	$dataArray = array(
            'supportCode' => $spc,
            'id' => ''
        );
     	if($cittemplate)
     	{
     		scp_log_message(LOG_MINFO, $spc, 'Se actualizó el machote id: ' . $this->put('id'));
     		$dataArray['rm'] = 'Se actualizó el machote';
     		$dataArray['id'] = $this->put('id');
     	}
     	else
     	{
     		scp_log_message(LOG_MINFO, $spc, 'No se actualizó el machote . ' . $this->put('key') . ' error: ' . $this->db->_error_message());
     		$dataArray['rm'] = 'No se actualizó el machote . ' . $this->put('key');
     	}
     	$this->response($dataArray, 200);
	}

	function citologycaltemplate_delete($id)
	{
		$spc = $this->supportcode_model->get_next_support_code();
        /************************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [citologycaltemplate : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /************************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de machote...');
        if(!$id)
        {
        	scp_log_message(LOG_ERROR, $spc, 'No se recibió el id del machote');
        	$this->response(array('rm' => 'No se recibió el id del machote', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'Machote a eliminar id: ' . $id);
        $cittemplate = $this->citologycaltemplates_model->delete($id, $spc);
        if($cittemplate)
        {
        	scp_log_message(LOG_MINFO, $spc, 'El machote fué eliminado id: ' . $id);
        	$dataArray = array('supportCode' => $spc, 'id' => $id);
        	$this->response($dataArray, 200);
        }
        else
        {
        	scp_log_message(LOG_MINFO, $spc, 'El machote ' . $id . ' no fué eliminado');
        	$this->response(array('rm' => 'El machote ' . $id . ' no fué eliminado', 'supportCode' => $spc), 404);
        }
    }

    function citologycaltemplates_get()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        /************************************************************************************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [citologycaltemplates : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /************************************************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de templates citologicos');
        $cittemplates = $this->citologycaltemplates_model->get_all($spc);
        if($cittemplates)
        {
            $responseData = array('supportCode' => $spc, 'data' => $cittemplates);
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($cittemplates) . ' registros.');	
            $this->response($responseData, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron registros, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron registros', 'supportCode' => $spc), 404);
        }
    }
}