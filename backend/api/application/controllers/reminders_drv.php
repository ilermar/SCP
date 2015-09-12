<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Reminders_drv extends REST_Controller {

    const CURR_SETTING = 1;

    function __construct() 
    {
        parent::__construct();

        $this->load->model('reminders_model');
        $this->load->model('supportcode_model');
        $this->load->model('users_model');
    }

    function reminder_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesion no iniciada [reminder : get]');
            $this->response(array('rm' => 'Sesion no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de recordatorio...');
        if(!$this->get('id')) 
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el ID del recordatorio.');
            $this->response(array('rm' => 'No se recibió el ID del recordatorio.', 'supportCode' => $spc), 400);    
        }

        $reminder = $this->reminders_model->get($this->get('id'), $spc);

        if($reminder) 
        {
            $reminder = $reminder[0];
            $rDate = $reminder->reminder_date;
            $responseData = array( 
                                'supportCode' => $spc,
                                'data' => array( 
                                            'id' => $reminder->id, 
                                            'reminder_date' => $rDate,
                                            'notes' => $reminder->notes
                                        )
                            );
            scp_log_message(LOG_MINFO, $spc, 'Se encontro el recordatorio con ID: ' . $this->get('id'));
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el registro del recordatorio' . $this->get('id'));
            $this->response(array('error' => 'No se encontró el registro del recordatorio' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function reminders_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

         /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [reminders : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $filters = array();       
        $filters['register_date_start'] = $this->get('register_date_start');
        $filters['register_date_end']   = $this->get('register_date_end');
        $filters['reminder_date_start'] = $this->get('reminder_date_start');
        $filters['reminder_date_end']   = $this->get('reminder_date_end');
        $filters['notes']               = $this->get('notes');
        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $filters['bottom_row']      = $this->get('bottom_row');
            $filters['top_row']         = $this->get('top_row');
        }
        /*
        Obtiene todos los recordatorios en base a los filtros recibidos
        Los filtros de la BD se deben manejar en el modelo
        */
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de recordatorios con filtros: ' . json_encode($filters));
        $reminders = $this->reminders_model->get_all($filters, $spc);

        if($reminders) 
        {
            $responseData = array(
                                'supportCode' => $spc, 
                                'data' => $reminders,
                                'rm' => 'Listado de recordatorios'
                            );
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($reminders) . ' registros de recordatorios.');
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron recordatorios, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron recordatorios.', 'supportCode' => $spc), 404);
        }
    }

    function reminder_post() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

         /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesion no iniciada [reminder : post]');
            $this->response(array('rm' => 'Sesion no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando insercion de recordatorio...');
        if(!$this->post('reminder_date') OR !$this->post('notes') )
        {
            scp_log_message(LOG_ERROR, $spc, 'No se encontrarón los valores correctos de reminder_date: '. $this->post('reminder_date') .' notes: ' .$this->post('notes'));
            $this->response(array('rm' => 'No se encontrarón los valores correctos de reminder_date: '. $this->post('reminder_date') .' notes: ' .$this->post('notes'), 
                'supportCode' => $spc),400);
        }
        $uId = $this->users_model->get_by_name(array('name' => $this->session->userdata('name'), 'email' => $this->session->userdata('email')));
        if($uId === 0)
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontro usuario name = ' . $this->session->userdata('name') . ' id: ' . $uId);
            $this->response(array('rm' => 'No se encontró el usuairo logeado para el recordatorio'), 404);
        }
        else
        {
            $data = array(
                    'register_date' => date('Y-m-d'),
                    'reminder_date' => $this->post('reminder_date'),
                    'notes' => $this->post('notes'),
                    'user_id' => $uId //TODO: FALTA IDENTIFICAR EL USER_ID DE LA SESION O QUE LO ENVIE EL FRONT_END
                    );

            scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo recordatorio: ' . json_encode($data));
            $reminder = $this->reminders_model->insert($data, $spc);

            $responseData = array( 
                                'supportCode' => $spc
                            );
            $data = array('id' => '');
            if($reminder)
            {
                scp_log_message(LOG_MINFO, $spc, 'Recordatorio agregado ID: ' . $reminder['id']);
                $responseData['rm'] = 'Recordatorio agregado ID: ' . $reminder['id'];
                $data = $reminder;
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc,'No se pudo agregar el recordatorio. Date: ' . $this->post('reminder_date') );
                $responseData['rm'] = 'No se pudo agregar el recordatorio. Date: ' . $this->post('reminder_date');
            }

            $responseData['data'] = $data;
            $this->response($responseData, 200); // 200 being the HTTP response code
        }
    }

    function reminder_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

         /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [reminder : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualización de recordatorio...');
        if(!$this->put('id') OR !$this->put('reminder_date') OR !$this->put('notes') )
        {
            scp_log_message(LOG_ERROR, $spc, 'No se encontrarón los valores correctos de reminder_date: '. $this->put('reminder_date') .' notes: ' .$this->post('notes'));
            $this->response(array('rm' => 'No se encontrarón los valores correctos de id:'.$this->put('id').' Fecha: '. $this->put('reminder_date') .' Notas: ' .$this->put('notes'), 
                'supportCode' => $spc),400);
        }
        $uId = $this->users_model->get_by_name(array('name' => $this->session->userdata('name'), 'email' => $this->session->userdata('email')));
        if($uId === 0)
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontro usuario name = ' . $this->session->userdata('name') . ' id: ' . $uId);
            $this->response(array('rm' => 'No se encontró el usuairo logeado para el recordatorio'), 404);
        }
        else
        {
            $data = array(
                    'id' => $this->put('id'),
                    'reminder_date' => $this->put('reminder_date'),
                    'notes' => $this->put('notes')
                    );

            scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo recordatorio: ' . json_encode($data));
            $reminder = $this->reminders_model->update($data, $spc);

            $responseData = array( 
                                'supportCode' => $spc
                            );
            $data = array('id' => '');
            if($reminder)
            {
                scp_log_message(LOG_MINFO, $spc, 'Recordatorio actualizado ID: ' . $reminder['id']);
                $responseData['rm'] = 'Recordatorio actualizado ID: ' . $reminder['id'];
                $data = $reminder;
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc,'No se pudo actualizar el recordatorio. Id: ' . $this->put('id') );
                $responseData['rm'] = 'No se pudo actualizar el recordatorio. Id: ' . $this->put('id');
            }
            $responseData['data'] = $data;
            $this->response($responseData, 200); // 200 being the HTTP response code
        }
    }

    function reminder_delete($id) 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [reminder : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
        scp_log_message( LOG_MINFO, $spc, 'Iniciando eliminación de recordatorio, id: '.$id );
        if(!$id) 
        {
            scp_log_message(LOG_ERROR, $spc, 'Falta id para obtener el recordatorio.');
            $this->response(array('rm' => 'Falta id para obtener el recordatorio', 'supportCode' => $spc), 400);            
        }
        
        scp_log_message(LOG_MINFO, $spc, 'Recordatorio a eliminar ID: ' . $id );
        $queryResult = $this->reminders_model->delete($id, $spc);
        if($queryResult)
        {
            scp_log_message(LOG_MINFO, $spc, 'El recordatorio fué eliminado ID: ' . $id);
            $dataArray = array('supportCode' => $spc, 'id' => $id);
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc,'El recordatorio '. $id .' no fué eliminado');
            $this->response(array('rm' => 'El recordatorio '. $id .' no fué eliminado', 'supportCode' => $spc), 404);
        }
    }
     
}
