<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Users_drv extends REST_Controller {

    function __construct() 
    {
        parent::__construct();

        $this->load->model('users_model');
        $this->load->model('supportcode_model');
    }

    function user_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [user : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de usuario...');

        if (!$this->get('id')) 
        {
            log_message(LOG_ERROR, $spc, 'No se recibió el ID del usuario.');
            $this->response(array('rm' => 'No se recibió el ID del usuario.', 'supportCode' => $spc), 400);
        }

        $user = $this->users_model->get($this->get('id'), $spc);

        if ($user) 
        {
            $user = $user[0];
            $dataArray = array('supportCode' => $spc, 'data' => $user);
            scp_log_message(LOG_MINFO, $spc, 'Se encontro el usuario con ID: ' . $this->get('id'));
            $this->response($dataArray, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontró el registro del usuario ' . $this->get('id'));
            $this->response(array('rm' => 'No se encontró el registro del usuario ' . $this->get('id'), 'supportCode' => $spc), 404);
        }
    }

    function user_post() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

       /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [user : post]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
            
        scp_log_message(LOG_MINFO, $spc, 'Iniciando inserción de usuario...');
        if($this->post('name') == '')
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del usuario, name = ""');
            $this->response(array('rm' => 'Datos incompletos del usuario.', 'supportCode' => $spc), 400);
        }
        $users = $this->users_model->get_all_by_email( $this->post('email'), $spc );
        if( count( $users ) === 0 )
        {
            $data = array('profile' => $this->post('profile'),
                'name' => $this->post('name'),
                'email' => $this->post('email'),
                'phone_number' => $this->post('phone_number'),
                'password' => get_random_string(),
                'last_login' => NULL,
                'last_change_pwd' => NULL);
            $pa55 = $data['password'];
            $em4il = $data['email'];
            scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para el nuevo usuario: ' . json_encode($data));        
            $user = $this->users_model->insert($data, $spc);
            $dataArray = array('supportCode' => $spc);
            $data = array('id' => '');
            if($user)
            {
                scp_log_message(LOG_MINFO, $spc, 'Se agregó el usuario ID: '. $user['id']);
                $dataArray['rm'] = 'Se agregó el usuario Id: '. $user['id'];
                $data = $user;
                $this->sendEmail($em4il, 'Envío de contraseña SCP', $pa55, $spc);
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc, 'No se agregó el usuario. ' . $this->post('name') . ' error: ' . $this->db->_error_message());
                $dataArray['rm'] = 'No se agregó el usuario. ' . $this->post('name');// . ' error: ' . $this->db->_error_message();
            }
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se agregó el usuario. ' . $this->post('name') . ' error: Email repetido');
            $dataArray['rm'] = 'Ya existe un usuario asociado al correo proporcionado';
        }
        $dataArray['data'] = $data;
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

    function user_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

       /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [user : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $data = array();

        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualización de usuario...');
        if(!$this->put('id'))
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del usuario, ID = ""');
            $this->response(array('rm' => 'Datos incompletos.', 'supportCode' => $spc), 400);
        }
        $data['id'] = $this->put('id');
        if($this->put('name') != '')
        {
            $data['name'] = $this->put('name');
        }
        else
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del usuario, name = ""');
            $this->response(array('rm' => 'Datos incompletos.', 'supportCode' => $spc), 400);
        }
        if($this->put('profile') != '')
        {
            $data['profile'] = $this->put('profile');
        }
        else
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del usuario, profile = ""');
            $this->response(array('rm' => 'Datos incompletos.', 'supportCode' => $spc), 400);
        }   
        if($this->put('email'))
        {
            $data['email'] = $this->put('email');
            $currUser = $this->users_model->get( $this->put('id'), $spc );
            $users = array();
            if( $data['email'] != $this->put('email') ) //validar si al cambiar de email no existe ya 
            {
                $users = $this->users_model->get_all_by_email( $data['email'], $spc );    
            }            
            if( count( $users ) === 0 )
            {
                $data['phone_number'] = $this->put('phone_number');
                if($this->put('password') != '')
                {
                    $newItem = array('password' => $this->put('password'));
                    array_push($data, $newItem);
                }
                scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar usuario: ' . json_encode($data));
                $user = $this->users_model->update($data, $spc);                
                $dataArray = array('supportCode' => $spc);
                $data = array('id' => '');
                if($user)
                {
                    scp_log_message(LOG_MINFO, $spc, 'Se actualizó el ususario ID: ' . $this->put('id'));
                    $dataArray['rm'] = 'Se actualizó el ususario';
                    $data = $this->put('id');//$user;
                }
                else
                {
                    scp_log_message(LOG_MINFO, $spc, 'No se actualizó el usuario . ' . $this->put('name') . ' error: ' . $this->db->_error_message());
                    $dataArray['rm'] = 'No se actualizó el usuario . ' . $this->put('name');// . ' error: ' . $this->db->_error_message();
                }
            }
            else
            {
                scp_log_message(LOG_MINFO, $spc, 'No se actualizó el usuario . ' . $this->put('name') . ' error: Email repetido');
                $dataArray['rm'] = 'No se actualizó el usuario . ' . $this->put('name'). ' Email repetido.';
            }
        }
        else
        {
            scp_log_message(LOG_ERROR, $spc, 'Datos incompletos del usuario, email = ""');
            $this->response(array('rm' => 'Datos incompletos.', 'supportCode' => $spc), 400);
        }
        
        $dataArray['data'] = $data;
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

    function user_delete($id) 
    {
        $spc = $this->supportcode_model->get_next_support_code();
       /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [user : delete]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando eliminación de usuario...');
        if(!$id)
        {
            scp_log_message(LOG_ERROR, $spc, 'No se recibió el ID del ususario.');
            $this->response(array('rm' => 'No se recibió el ID del ususario.', 'supportCode' => $spc), 400);
        }
        scp_log_message(LOG_MINFO, $spc, 'Usuario a eliminar ID: ' . $id);
        $user = $this->users_model->delete($id, $spc);
        if($user)
        {
            scp_log_message(LOG_MINFO, $spc, 'El usuario fué eliminado ID: ' . $id);
            $dataArray = array('supportCode' => $spc, 'data' => $id);//$user);
            $this->response($dataArray, 200);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'El usuario '. $id .' no fué eliminado');
            $this->response(array('rm' => 'El usuario '. $id .' no fué eliminado', 'supportCode' => $spc), 404);
        }
    }

    function users_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();
        
        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [users : get]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        $data = array();

        if($this->get('profile')){
            $data['profile']=$this->get('profile');
        }

        $data['name']=$this->get('name');
        $data['phone_number']=$this->get('phone_number');
        $data['email']=$this->get('email');

        if($this->get('bottom_row') !== FALSE && $this->get('top_row') !== FALSE )
        {
            $data['bottom_row'] = $this->get('bottom_row');
            $data['top_row'] = $this->get('top_row');
        }
        scp_log_message(LOG_MINFO, $spc, 'Iniciando búsqueda de usuarios con filtros: ' . json_encode($data));
        $users = $this->users_model->get_all($data, $spc);

        if ($users) 
        {
            $auxArray = $users;
            $users = array();
            foreach ($auxArray as $data) 
            {
                $newItem = array('id' => $data->id, 'name' => $data->name, 'profile' => $data->profile, 'email' => $data->email);
                array_push($users, $newItem);
            }
            $responseData = array('supportCode' => $spc, 'data' => $users);
            scp_log_message(LOG_MINFO, $spc, 'Se encontraron ' . count($users) . ' registros de usuarios.');
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc, 'No se encontraron usuarios, se regresa 404 HTTP code');
            $this->response(array('rm' => 'No se encontraron usuarios.', 'supportCode' => $spc), 404);
        }
    }

    private function sendEmail($to, $subject, $message, $spc)
    {
        $this->load->model('settings_model');
        
        $resSettings = $this->settings_model->getCurrent($spc);
        $settings = $resSettings[0];
        $config = array(    
            'protocol' => 'smtp',
            'mailtype' => 'html',
            'smtp_host' => (intval( $settings->use_ssl ) === 1 ? 'ssl://' : '' ).$settings->smtp_server,
            'smtp_user' => $settings->user,
            'smtp_pass' => $settings->password,
            'smtp_port' => $settings->port,
            'newline'   => "\r\n",
        );
        $this->load->library('email', $config);
        //$this->email->initialize($config);
        $this->email->from('no-contestar@drsergioramosreyes.com', 'SCP Soporte');
        $this->email->to($to); 
        //$this->email->bcc('no-contestar@drsergioramosreyes.com');
        $this->email->subject($subject);
        $data = array('password' => $message);
        $htmlemail = $this->load->view('signon_email', $data, true);
        $this->email->message($htmlemail);
        if ( !$this->email->send() )
        {
            scp_log_message(LOG_MINFO, $spc, " No se pudo enviar el correo ---- " . $this->email->print_debugger() );
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, " correo enviado " );
        }
    }

}
