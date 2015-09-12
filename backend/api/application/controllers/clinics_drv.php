<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Clinics_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('clinics_model');
        $this->load->model('supportcode_model');
    }

    function clinic_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [clinic : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de clínica...');
        if(!$this->get('id')) 
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el ID de la clínica');
            $this->response( array('rm' => 'No se recibió el ID de la clínica.', 'supportCode' => $spc), 400);
        }

        $clinic = $this->clinics_model->get($this->get('id'), $spc);

        if($clinic) 
        {
            $clinic = $clinic[0];            
            $responseData = array( 
                                'supportCode' => $spc,
                                'data' => $clinic
                            );
            scp_log_message(LOG_MINFO, $spc, 'Se encontro la clínica con ID: ' . $this->get('id'));
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el registro de la clínica ' . $this->get('id') );
            $this->response(array('rm' => 'No se encontró el registro de la clínica' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function clinics_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [clinics : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $filters = array();
        if($this->get('name') !== FALSE )
        {
            $filters['name'] = $this->get('name');
        }
        if($this->get('state') !== FALSE )
        {
            $filters['state'] = $this->get('state');
        }
        if($this->get('phone_number') !== FALSE )
        {
            $filters['phone_number'] = $this->get('phone_number');
        }
        if($this->get('email') !== FALSE )
        {
            $filters['email'] = $this->get('email');
        }
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $filters['bottom_row'] = $this->get('bottom_row');
            $filters['top_row'] = $this->get('top_row');
        }
        /*
        Obtiene todos los clínicas en base a los filtros recibidos
        Los filtros de la BD se deben manejar en el modelo
        */
        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de clínicas con filtros: ' . json_encode($filters));
        $clinics = $this->clinics_model->get_all($filters, $spc);

        if($clinics) 
        {// mandar solo los datos solicitados
            $auxArray = $clinics;
            $clinics = array();
            foreach($auxArray as $data) 
            {
                $newElement = array(
                        'id' => $data->id,
                        'name' => $data->name,
                        'notes' => $data->notes
                    );
                array_push($clinics, $newElement);
            }
            $responseData = array(
                                'rm' => 'Listado de clínicas encontradas',
                                'supportCode' => $spc, 
                                'data' => $clinics
                            );
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($clinics) . ' registros de clínicas.');
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron clínicas, se regresa 404 HTTP code');
            $this->response( array( 'rm' => 'No se encontraron clínicas.', 'supportCode' => $spc), 404 );
        }
    }

    function clinic_post() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [clinic : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $data = array();

        scp_log_message(LOG_MINFO, $spc, 'Iniciando insercion de clínica...');
        if( $this->post('name') != '' )
        {
            $data['name'] = $this->post('name');
        }
        else
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos de la clínica, name = ""');
            $this->response(array( 'rm' => 'No se encontró el registro de la clínica', 'supportCode' => $spc), 400);
        }
        if( $this->post('address') != '' )
        {
            $data['address'] = $this->post('address');
        }
        if( $this->post('city') != '' )
        {
            $data['city'] = $this->post('city');
        }
        if( $this->post('state') != '' )
        {
            $data['state'] = $this->post('state');
        }
        if( $this->post('phone_number') != '' )
        {
            $data['phone_number'] = $this->post('phone_number');
        }
        if( $this->post('fax') != '' )
        {
            $data['fax'] = $this->post('fax');
        }
        if( $this->post('email') != '' )
        {
            $data['email'] = $this->post('email');
        }
        if( $this->post('notes') != '' )
        {
            $data['notes'] = $this->post('notes');
        }

        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para la nueva clínica: ' . json_encode($data));
        $clinic = $this->clinics_model->insert($data, $spc);
                
        $responseData = array( 
                            'supportCode' => $spc
                        );
        $data = array('id' => '');
        if($clinic)
        {
            scp_log_message(LOG_MINFO, $spc, 'clínica agregada ID: ' . $clinic['id']);
            $responseData['rm'] = 'Clínica agregada ID: ' . $clinic['id'];
            $data = $clinic;
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se pudo agregar la clínica. ' . $this->post('name')  . ' error :' .  $this->db->_error_message() );
            $responseData['rm'] = 'No se pudo agregar la clínica. ' . $this->post('name')  ;//. ' error :' .  $this->db->_error_message();
        }
        $responseData['data'] = $data;
        $this->response($responseData, 200); // 200 being the HTTP response code
    }

    function clinic_delete($id)
    {
        $spc = $this->supportcode_model->get_next_support_code();
        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [clinic : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de clínica...');
        if(!$id) 
        {
            log_message(LOG_ERROR, $spc, 'No se recibió el ID de la clínica.');
            $this->response( array('rm' => 'No se recibió el ID de la clínica.', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'clínica a eliminar ID: ' . $id);
        $clinic = $this->clinics_model->delete($id, $spc);

        if($clinic) 
        {
            scp_log_message(LOG_MINFO, $spc, 'La clínica fué eliminada ID: ' . $id);
            $responseData = array( 
                                'supportCode' => $spc,
                                'data' => $id
                            );
            $this->response($responseData, 200); // 200 being the HTTP response code
        }
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el registro de la clínica ' . $id );
            $this->response(array('rm' => 'No se encontró el registro de la clínica' . $id, 'supportCode' => $spc), 404);
        }
    }

    function clinic_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code(); 

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [clinic : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
        
        $data = array();

        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualizacion de clínica...');
        if( $this->put('id') != '' )
        {
            $data['id'] = $this->put('id');
        }
        else
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos de la clínica, ID = ""');
            $this->response(array( 'rm' => 'No se encontró el registro de la clínica', 'supportCode' => $spc), 400);
        }
        if( $this->put('name') != '' )
        {
            $data['name'] = $this->put('name');
        }
        else
        {
            spc_log_message(LOG_ERROR, $spc, 'Datos incompletos de la clínica, name = ""' );
            $this->response(array( 'rm' => 'No se encontró el registro de la clínica', 'supportCode' => $spc), 400);
        }
        
        $data['address'] = $this->put('address');
        $data['city'] = $this->put('city');
        $data['state'] = $this->put('state');
        $data['phone_number'] = $this->put('phone_number');
        $data['fax'] = $this->put('fax');
        $data['email'] = $this->put('email');
        $data['notes'] = $this->put('notes');

        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar clínica: ' . json_encode($data));
        $clinic = $this->clinics_model->update($data, $spc);
                
        $responseData = array( 
                            'supportCode' => $spc
                        );
        $data = array('id' => '');
        if($clinic)
        {
            scp_log_message(LOG_MINFO, $spc, 'clínica actualizada ID: ' . $this->put('id'));
            $responseData['rm'] = 'Clínica actualizada';
            $data = $this->put('id');//$clinic;
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se pudo actualizar la clínica. ' . $this->put('name')  . ' error :' .  $this->db->_error_message() );
            $responseData['rm'] = 'No se pudo actualizar la clínica. ' . $this->put('name');//  . ' error :' .  $this->db->_error_message();
        }
        $responseData['data'] = $data;
        $this->response($responseData, 200); // 200 being the HTTP response code
    }


}
