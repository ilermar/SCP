<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Doctors_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('doctors_model');
        $this->load->model('supportcode_model');

        $this->notEmpty = array("name");
        $this->allowEmpty = array("address", "city", "state", "phone_number_1", "phone_number_2", "phone_number_3", "fax",  "email", "specialty");
        $this->emptyIsNull = array("birth_date");
    }

    function doctor_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [doctor : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de doctor...');
        if (!$this->get('id')) 
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el ID del doctor.');
            $this->response(array('rm' => 'No se recibió el ID del doctor.', 'supportCode' => $spc), 400);
        }

        $doctor = $this->doctors_model->get($this->get('id'), $spc);

        if ($doctor) 
        {
            $doctor = $doctor[0];
            $dataArray = array('supportCode' => $spc, 'data' => $doctor);
            scp_log_message(LOG_MINFO, $spc, 'Se encontró el doctor con ID: ' . $this->get('id'));
            $this->response($dataArray, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el registro del doctor ID: ' . $this->get('id'));            
            $this->response(array('rm' => 'No se encontró el registro del doctor ' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function doctor_post() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [doctor : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
            return;
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando inserción de doctor...');
        if($this->post('name') == '')
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del doctor, name = ""');
            $this->response(array('rm' => 'Datos incompletos del doctor.', 'supportCode' => $spc), 400);
            return;
        }

        $data = array();
        $message = $this->doctors_model->buildRequestData($this->notEmpty, $this->allowEmpty, $this->emptyIsNull, $this->post(), $data,  $spc);
        if($message)
        {
            $this->response(array('rm' => $message, 'supportCode' => $spc), 400);
            return;
        }

        scp_log_message(LOG_MINFO, $spc, 'Datos recibido para el nuevo doctor: ' . json_encode($data));
        $doctor = $this->doctors_model->insert($data, $spc);
        $dataArray = array('supportCode' => $spc);
        $data =array('id' => '');
        if($doctor)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se agregó el doctor Id: '. $doctor['id']);
            $dataArray['rm'] = 'Se agregó el doctor Id: '. $doctor['id'];
            $data = $doctor;
            $data['id'] = $doctor['id'];
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se agregó el doctor. ' . $this->post('name') . ' error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se agregó el doctor. ' . $this->post('name');// . ' error: ' . $this->db->_error_message();
        }
        $dataArray['data'] = $data;
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

    function doctor_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [doctor : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $data = array();

        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualizacion de doctor...');
        if(!$this->put('id'))
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del doctor, ID = ""');
            $this->response(array('rm' => 'Datos incompletos del doctor.', 'supportCode' => $spc), 400);
        }
        $data['id'] = $this->put('id');
        
        $message = $this->doctors_model->buildRequestData($this->notEmpty, $this->allowEmpty, $this->emptyIsNull, $this->post(), $data,  $spc);
        if($message)
        {
            $this->response(array('rm' => $message, 'supportCode' => $spc), 400);
            return;
        }

        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar doctor: ' . json_encode($data));
        $doctor = $this->doctors_model->update($data, $spc);

        $dataArray = array('supportCode' => $spc);
        $data = array('id' => '');
        if($doctor)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se actualizo el doctor ID: ' . $this->put('id'));
            $dataArray['rm'] = 'Se actualizó el doctor';
            $data = $this->put('id');//$doctor;
        }
        else
        {
            scp_log_message(LOG_MINFO, $scp, 'No se actualizo el doctor . ' . $this->put('name') . ' error :' . $this->db->_error_message());
            $dataArray['rm'] = 'No se actualizó el doctor . ' . $this->put('name');// . ' error :' . $this->db->_error_message();
        }
        $dataArray['data'] = $data;
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

    function doctor_delete($id) 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [doctor : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminacion de doctor...');
        if(!$id)
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibio el ID del doctor.');
            $this->response(array('rm' => 'No se recibió el ID del doctor.', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'Doctor a elimnar ID: ' . $id);
        $doctor = $this->doctors_model->delete($id, $spc);
        if($doctor)
        {
            scp_log_message(LOG_MINFO, $spc, 'El doctor fue eliminado ID: ' . $id);
            $dataArray = array('supportCode' => $spc, 'id' => $id);//$doctor);
            $this->response($dataArray,200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'El doctor ' . $id . 'no fue eliminado');
            $this->response(array('rm' =>'El doctor ' . $id . 'no fue eliminado', 'supportCode' => $spc), 404);
        }
    }

    function doctors_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [doctors : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
        
        $data = array();

        $this->doctors_model->buildRequestData($this->notEmpty, $this->allowEmpty, array(), $this->get(), $data,  $spc);
              
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $data['bottom_row'] = $this->get('bottom_row');
            $data['top_row'] = $this->get('top_row');
        }
        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de doctores con filtros: ' . json_encode($data));
        $doctors = $this->doctors_model->get_all($data, $spc);

        if ($doctors) 
        {
            $auxArray = $doctors;
            $doctors = array();
            $showSpecialty = TRUE;
            if($this->get('autocomplete') !== FALSE)
            {
                $showSpecialty = FALSE;
            }
            foreach ($auxArray as $data) 
            {
                if(!$showSpecialty)
                {
                    $newItem = array('id' => $data->id, 'name' => $data->name);   
                }
                else
                {
                    $newItem = array('id' => $data->id, 'name' => $data->name, 'specialty' => $data->specialty);
                }
                array_push($doctors, $newItem);
            }
            $responseData = array('supportCode' => '00', 'data' => $doctors);
            scp_log_message('LOG_MINFO', $spc, 'Se encontraron ' . count($doctors) . ' registros de doctores.');
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron doctores, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron doctores!', 'supportCode' => $spc), 404);
        }
    }

}
