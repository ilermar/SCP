<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Labtest_drv extends REST_Controller {

    const STATUS_SIGNED = 1; 
    const STATUS_OPENED = 0;

    function __construct() 
    {
        parent::__construct();

        $this->load->model('labtest_model');
        $this->load->model('supportcode_model');
    }

    function labtest_get() 
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesion no iniciada [labtest : get]');
    		$this->response(array('rm' => 'Sesion no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/

        if($this->get('key_prefix') && $this->get('next_number')){
            scp_log_message(LOG_MINFO, $spc, 'Siguiente número de estudio para ...' . $this->get('key_prefix'));

            $keyNumber = $this->labtest_model->nextKeyNumber($this->get('key_prefix'), $spc);

            if(!$keyNumber){
                $keyNumber = 1;
            }

            $keyNumber = str_pad('' . $keyNumber, 4, '0', STR_PAD_LEFT);

            $dataArray = array('supportCode' => $spc, 'data' => $keyNumber);
            scp_log_message(LOG_MINFO, $spc, 'Siguiente secuencia: ' . $keyNumber);
            $this->response($dataArray, 200);
        }else{
            scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de estudio...');
            if(!$this->get('id'))
            {
                scp_log_message(LOG_ERROR, $spc, 'No se recibio id del estudio');
                $this->response(array('rm' => 'No se recibio id del estudio.', 'supportCode' => $spc), 400);
            }
            
            $labtest = $this->labtest_model->get($this->get('id'), $spc);

            if($labtest)
            {
                $labtest = $labtest[0];
                $dataArray = array('supportCode' => $spc, 'data' => $labtest);
                scp_log_message(LOG_MINFO, $spc, 'Se encontro el estudio con id: ' . $this->get('id'));
                $this->response($dataArray, 200);
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc, 'No se encontro estudio id: ' . $this->get('id'));
                $this->response(array('rm' => 'No se encontro estudio id: ' . $this->get('id'), 'supportCode' => $spc),404);
            }
        }
    }

    function labtest_post()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtest : post]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
    	scp_log_message(LOG_MINFO, $spc, 'Iniciando inserción de estudio...');
    	$data = array (
            'patient_id' => scp_decrypt($this->post('patient_id'),$this->scpencrypt),
    		'doctor_id' => scp_decrypt($this->post('doctor_id'),$this->scpencrypt),
            'patient_age' => $this->post('patient_age'),
    		'type' => $this->post('type'),
    		'main_doctor_id' => scp_decrypt($this->post('main_doctor_id'),$this->scpencrypt),
    		'notes' => $this->post('notes'),
    		'key_prefix' => $this->post('key_prefix'),
    		'key_number' => $this->post('key_number'),
    		'status' => self::STATUS_OPENED
        );
    	scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo estudio: ' . json_encode($data));
    	$labtest = $this->labtest_model->insert($data, $spc);
    	$dataArray = array('supportCode' => $spc);
    	$data = array('id' => '');
    	if($labtest)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Se agregó el estudio id: '. $labtest['id']);
    		$dataArray['rm'] = 'Se agregó el estudio id: ' . $labtest['id'];
            $dataArray['id'] = $labtest['id'];
    		$data = $labtest;
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se agregó el estudio. ' . $this->post('type') . ' error: '. $this->db->_error_message());
    		$dataArray['rm'] = 'No se agregó el estudio. '. $this->post('type');
    	}
    	$this->response($dataArray, 200);
    }

    function labtest_put()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtest : put]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
    	scp_log_message(LOG_MINFO, $spc, 'Iniciando actualización de estudio...');
    	$data = array ('id' => $this->put('id'),
    		'patient_id' => scp_decrypt($this->put('patient_id'),$this->scpencrypt),
    		'doctor_id' => scp_decrypt($this->put('doctor_id'),$this->scpencrypt),
            'patient_age' => $this->put('patient_age'),
    		'type' => $this->put('type'),
    		'main_doctor_id' => scp_decrypt($this->put('main_doctor_id'),$this->scpencrypt),
    		'notes' => $this->put('notes'),
    		'key_prefix' => $this->put('key_prefix'),
    		'key_number' => $this->put('key_number'),
    		'status' => $this->put('status'),
            'main_json_data' => $this->put('main_json_data')
        );
    	scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar estudio: ' . json_encode($data));
    	$labtest = $this->labtest_model->update($data, $spc);
    	$dataArray = array(
            'supportCode' => $spc,
            'id' => ''
        );
    	if($labtest)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Se actualizó el estudio id: '. $this->put('id'));
    		$dataArray['rm'] = 'Se actualizó el estudio';
    		$dataArray['id'] = $this->put('id');
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se actualizó el estudio . ' . $this->put('type') . ' error: ' . $this->db->_error_message());
    		$dataArray['rm'] = 'No se actualizó el estudio . ' . $this->put('type');
    	}
    	$this->response($dataArray, 200);
    }

    function labtest_delete($id)
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtest : delete]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/

    	scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de estudio...');
    	if(!$id)
    	{
    		scp_log_message(LOG_ERROR, $spc, 'No se recibió id del estudio.');
    		$this->response(array('rm' => 'No se recibió id del estudio.', 'supportCode' => $spc), 400);
    	}
    	scp_log_message(LOG_MINFO, $spc, 'Estudio a eliminar id: ' . $id);
    	$labtest = $this->labtest_model->delete($id, $spc);
    	if($labtest)
    	{
    		scp_log_message(LOG_MINFO, $spc, 'El estudio fué eliminado id: ' . $id);
    		$dataArray = array('supportCode' => $spc, 'data' => $this->delete('id'));
    		$this->response($dataArray, 200);
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'El estudio ' . $this->delete('id') . ' no fué eliminado');
    		$this->response(array('rm' => 'El estudio ' . $this->delete('id') . ' no fué eliminado', 'supportCode' => $spc), 404);
    	}
    }

    function labtests_get()
    {
    	$spc = $this->supportcode_model->get_next_support_code();
    	/******************************************************/
    	if(!$this->isLogged())
    	{
    		scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [labtests : get]');
    		$this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
    	}
    	/*****************************************************/
    	$data = array (
            'key_prefix' => $this->get('key_prefix'),
            'key_number' => $this->get('key_number'),
            'register_date_start' => $this->get('register_date_start'),
            'register_date_end' => $this->get('register_date_end'),
            'patient_name' => $this->get('patient_name'),
    		'doctor_name' => $this->get('doctor_name'),
    		'type' => $this->get('type'),
    		'main_doctor' => $this->get('main_doctor'),
    		'status' => $this->get('status')
        );
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $data['bottom_row'] = $this->get('bottom_row');
            $data['top_row'] = $this->get('top_row');
        }
    	scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de estudios con filtros: ' . json_encode($data));
    	$labtests = $this->labtest_model->get_all($data, $spc);
    	if($labtests)
    	{
    		$responseData = array('supportCode' => $spc, 'data' => $labtests);
    		scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($labtests) . ' registros de estudios.');
    		$this->response($responseData, 200);
    	}
    	else
    	{
    		scp_log_message(LOG_MINFO, $spc, 'No se encontraron estudios, se regresa 404 HTTP code');
    		$this->response(array('rm' => 'No se encontraron estudios.', 'supportCode' => $spc), 404);
    	}
    }
}