<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Labtestdata_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('labtest_model');
        $this->load->model('labtestdatas_model');
        $this->load->model('supportcode_model');
    }

    function labtestdata_get() 
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtestdata : get]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de los labtestdata....');
        if(!$this->get('id'))
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió id de los labtestdata');
            $this->response(array('rm' => 'no se recibió id de los labtestdata', 'supportCode' => $spc), 400);
        }
        $labtestdata = $this->labtestdatas_model->get($this->get('id'), $spc);
        if($labtestdata)
        {
            $labtestdata = $labtestdata[0];
            $dataArray = array('supportCode' => $spc, 'data' => $labtestdata);
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron los labtestdata con id: ' . $this->get('id'));
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron de los labtestdata id: ' . $this->get('id'));
            $this->response(array('rm' => 'No se encontraron de los labtestdata id: ' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function labtestdata_post()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtestdata : post]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
        scp_log_message(LOG_MINFO, $spc, 'labtestdata_post');
        $data = array(
            'id' => $this->post('id'),
            'json_data' => $this->post('json_data')
        );
        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo labtestdata: ' . json_encode($data));
        $labtestdata = $this->labtestdatas_model->insert($data, $spc);

        if($this->post('id_study')){
            scp_log_message(LOG_MINFO, $spc, 'A0');
            $dataStudy = array(
                'id' => $this->post('id_study'),
                'main_json_data' => $this->post('id')
            );
            scp_log_message(LOG_MINFO, $spc, 'A1');
            $this->labtest_model->update($dataStudy, $spc);
            scp_log_message(LOG_MINFO, $spc, 'A2');
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se recibió id_study');
        }

        $dataArray = array(
            'supportCode' => $spc,
            'id' => ''
        );
        if($labtestdata)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se agregó el labtestdata id: ' . $labtestdata['id']);
            $dataArray['rm'] = 'Se agregó el labtestdata id: ' . $labtestdata['id'];
            $dataArray['id'] = $labtestdata['id'];
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se agregó el labtestdata . ' . $this->post('key') . ' error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se agregó el labtestdata. ' . $this->post('key');
        }
        $this->response($dataArray, 200);
    }

    function labtestdata_put()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtestdata : put]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
        scp_log_message(LOG_MINFO, $spc, 'labtestdata_put...');
        $data = array(
            'id' => $this->put('id'),
            'json_data' => $this->put('json_data')
        );
        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar labtestdata: ' . json_encode($data));
        $labtestdata = $this->labtestdatas_model->update($data, $spc);
        $dataArray = array(
            'supportCode' => $spc,
            'id' => ''
        );
        if($labtestdata)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se actualizó el labtestdata id: ' . $this->put('id'));
            $dataArray['rm'] = 'Se actualizó el labtestdata';
            $dataArray['id'] = $this->put('id');
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se actualizó el labtestdata . ' . $this->put('key') . ' error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se actualizó el labtestdata . ' . $this->put('key');
        }
        $this->response($dataArray, 200);
    }

    function labtestdata_delete($id)
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtestdata : delete]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de labtestdata...');
        if(!$id)
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el id del labtestdata');
            $this->response(array('rm' => 'No se recibió el id de los labtestdata', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'Machote a eliminar id: ' . $id);
        $labtestdata = $this->labtestdatas_model->delete($id, $spc);
        if($labtestdata)
        {
            scp_log_message(LOG_MINFO, $spc, 'Los labtestdata fué eliminado id: ' . $id);
            $dataArray = array('supportCode' => $spc, 'id' => $id);
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'Los labtestdata ' . $id . ' no fué eliminado');
            $this->response(array('rm' => 'Los labtestdata ' . $id . ' no fué eliminado', 'supportCode' => $spc), 404);
        }
    }

    function labtestdatas_get()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtestdata : get all]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
        $filters = array();
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $filters['bottom_row'] = $this->get('bottom_row');
            $filters['top_row'] = $this->get('top_row');
        }
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda conn filtros: ' . json_encode($data));
        $labtestdata = $this->labtestdatas_model->get_all($filters, $spc);
        if($labtestdata)
        {
            $responseData = array('supportCode' => $spc, 'data' => $labtestdata);
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($labtestdata) . ' registros.'); 
            $this->responseData($responseData, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron registros, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron registros', 'supportCode' => $spc), 404);
        }
    }
}