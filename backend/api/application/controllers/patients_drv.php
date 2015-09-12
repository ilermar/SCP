<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Patients_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('patients_model');
        $this->load->model('supportcode_model');

        $this->notEmpty = array("name");
        $this->allowEmpty = array("address", "city", "state", "phone_number_1", "phone_number_2", "phone_number_3", "email");
        $this->emptyIsNull = array("birth_date");
    }

    function patient_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [patient : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de paciente...');
        if(!$this->get('id')) 
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibio ID del paciente');
            $this->response( array('rm' => 'No se recibio ID del paciente.', 'supportCode' => $spc), 400);
        }

        $patient = $this->patients_model->get($this->get('id'), $spc);

        if($patient) 
        {
            $patient = $patient[0];            
            $dataArray = array('supportCode' => $spc, 'data' => $patient);
            scp_log_message(LOG_MINFO, $spc, 'Se encontro el paciente con ID: ' . $this->get('id'));
            $this->response($dataArray, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontro paciente ID: ' . $this->get('id'));
            $this->response(array('rm' => 'No se encontró el registro del paciente ' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function patient_post() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [patient : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
            return;
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando insercion de paciente...');
        if($this->post('name') == '')
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del paciente, name = ""');
            $this->response(array('rm' => 'Datos incompletos del paciente.', 'supportCode' => $spc), 400);
            return;
        }

        $data = array();
        $message = $this->patients_model->buildRequestData($this->notEmpty, $this->allowEmpty, $this->emptyIsNull, $this->post(), $data,  $spc);
        if($message)
        {
            $this->response(array('rm' => $message, 'supportCode' => $spc), 400);
            return;
        }

        if($data['birth_date'] != NULL)
        {
            $birthdate = explode('-', $data['birth_date']);
            $birthdate = $birthdate[0] . $birthdate[1] . $birthdate[2];
            $today = date('Ymd');
            $age = explode('.', ($today - $birthdate)/10000);
        }

        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo paciente: ' . json_encode($data));
        $patient = $this->patients_model->insert($data, $spc);
        $dataArray = array('supportCode' => $spc);
        $data = array('id' => '');
        if($patient)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se agregó el paciente ID: ' . $patient['id']);
            $dataArray['rm'] = 'Se agregó el paciente Id: '. $patient['id'];
            $data['age'] = isset($age) ? $age[0] : 0;   
            $data['id'] = $patient['id'];
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se agregó el paciente. ' . $this->post('name') . ' error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se agregó el paciente. ' . $this->post('name');// . ' error: ' . $this->db->_error_message();
            $this->response($dataArray, 500); 
            return;
        }
        $dataArray['data'] = $data;
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

    function patient_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [patient : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $data = array();

        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualizacion de paciente...');
        if(!$this->put('id')) 
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del paciente, ID = ""');
            $this->response(array('rm' => 'Datos incompletos del paciente.', 'supportCode' => $spc), 400);            
        }
        else
        {
            $data['id'] = $this->put('id');

            $message = $this->patients_model->buildRequestData($this->notEmpty, $this->allowEmpty, $this->emptyIsNull, $this->put(), $data,  $spc);

            if($message)
            {
                $this->response(array('rm' => $message, 'supportCode' => $spc), 400);
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar paciente: ' . json_encode($data));
                $patient = $this->patients_model->update($data, $spc);

                $dataArray = array('supportCode' => $spc);
                $data = array('id' => '');
                if($patient)
                {
                    scp_log_message(LOG_MINFO, $spc, 'Se actualizo el paciente ID: ' . $this->put('id'));
                    $dataArray['rm'] = 'Se actualizó el paciente';
                    $data = $this->put('id');//$patient;
                }
                else
                {
                    scp_log_message(LOG_MINFO, $spc, 'No se actualizo el paciente . ' . $this->put('name') . ' error :' . $this->db->_error_message());
                    $dataArray['rm'] = 'No se actualizó el paciente . ' . $this->put('name');// . ' error :' . $this->db->_error_message();
                }
                $dataArray['data'] = $data;
                $this->response($dataArray, 200); // 200 being the HTTP response code
            }
        }
    }

    function patient_delete($id) 
    {
        $spc = $this->supportcode_model->get_next_support_code();
        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [patient : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminacion de paciente...');
        if(!$id) 
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el ID del paciente.');
            $this->response(array('rm' => 'No se recibió el ID del paciente.', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'Paciente a eliminar ID: ' . $id);
        $patient = $this->patients_model->delete($id, $spc);
        if($patient)
        {
            scp_log_message(LOG_MINFO, $spc, 'El paciente fue eliminado ID: ' . $id);
            $dataArray = array('supportCode' => $spc, 'data' => $id);//$patient);
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc,'El paciente '. $id .' no fue eliminado');
            $this->response(array('rm' => 'El paciente '. $id .' no fue eliminado', 'supportCode' => $spc), 404);
        }
    }

    function patients_get() 
    {   
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [patients : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
        
        $data = array();

        $this->patients_model->buildRequestData($this->notEmpty, $this->allowEmpty, array(), $this->get(), $data,  $spc);
 
        if($this->get('autocomplete') !== FALSE)
        {
            $data['autocomplete'] = $this->get('autocomplete');
        }
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $data['bottom_row'] = $this->get('bottom_row');
            $data['top_row'] = $this->get('top_row');
        }
        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de pacientes con filtros: ' . json_encode($data));//implode(';',$data));
        $patients = $this->patients_model->get_all($data, $spc);

        if($patients) 
        {
            $auxArray = $patients;
            $patients = array();
            $doAge = $this->get('autocomplete') !== FALSE ? FALSE : TRUE;
            
            foreach ($auxArray as $data) 
            {
                if($doAge)
                {
                    if($data->birth_date != NULL)
                    {
                        $birthdate = explode('-', $data->birth_date);
                        $birthdate = $birthdate[0] . $birthdate[1] . $birthdate[2];
                        $today = date('Ymd');
                        $age = ($today - $birthdate)/10000;
                        $age = explode('.', $age);
                    }
                    else
                    {
                        $age = array(0 => '0');
                    }
                    $newItem = array('id' => $data->id, 'name' => $data->name, 'age' => $age[0]);
                }
                else
                {
                    $newItem = array('id' => $data->id, 'name' => $data->name);   
                }
                array_push($patients, $newItem);
            }
            $responseData = array('supportCode' => $spc , 'data' => $patients);
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($patients) . ' registros de pacientes.');
            $this->response($responseData, 200); // 200 being the HTTP response code            
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron pacientes, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron pacientes.', 'supportCode' => $spc), 404);            
        }
    }
}