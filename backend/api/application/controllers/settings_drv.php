<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Settings_drv extends REST_Controller {

    const CURR_SETTING = 1;

    function __construct() 
    {
        parent::__construct();

        $this->load->model('settings_model');
        $this->load->model('supportcode_model');
    }

    function settings_get() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesion no iniciada [settings : get]');
            $this->response(array('rm' => 'Sesion no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/

        scp_log_message(LOG_MINFO, $spc, 'Iniciando busqueda de configuracion del sistema...');
        $settings = $this->settings_model->getCurrent($spc);

        if($settings) 
        {
            scp_log_message(LOG_MINFO, $spc,'Consulta de la configuración del sistema');
            $settings = $settings[0];
            $responseData = array( 
                                'supportCode' => $spc,
                                'data'=> array( 
                                            'smtp_server' => $settings->smtp_server, 
                                            'port' => intval($settings->port),
                                            'use_ssl' => intval($settings->use_ssl),
                                            'user' => $settings->user,
                                            'password' => $settings->password
                                        )
                            );
            $this->response($responseData, 200); // 200 being the HTTP response code
        } 
        else 
        {
            scp_log_message(LOG_MINFO, $spc,'No se encontró el registro de la configuración del sistema');
            $this->response(array('rm' => 'No se encontró el registro de la configuración del sistema', 'supportCode' => $spc), 404);
        }
    }
    
    function settings_put() 
    {
        $spc = $this->supportcode_model->get_next_support_code();

        /*****************/
        if(!$this->isLogged())
        {
            scp_log_message(LOG_MINFO, $spc, 'Sesión no iniciada [settings : put]');
            $this->response(array('rm' => 'Sesión no iniciada', 'supportCode' => $spc), 403);
        }
        /*****************/
        
        $data = array();
        
        scp_log_message(LOG_MINFO, $spc, 'Iniciando actualización de la configuración del sistema...');
        
        if($this->put('smtp_server') == '' OR $this->put('port') == '' OR $this->put('use_ssl') == '' 
            OR $this->put('user') == '' OR $this->put('password') == '')
        {
           scp_log_message(LOG_ERROR, $spc, 'Datos incompletos para actualizar...');
           $this->response(array('rm' => 'Datos incompletos para actualizar...', 'supportCode' => $spc), 401);
        }

        $data['smtp_server']    = $this->put('smtp_server');
        $data['port']           = $this->put('port');
        $data['use_ssl']        = $this->put('use_ssl') === "true" ? 1 :0;
        $data['user']           = $this->put('user');
        $data['password']       = $this->put('password');

        scp_log_message(LOG_MINFO, $spc, 'Datos recibidos para actualizar configuración: ' . json_encode($data));
        $setting = $this->settings_model->update($data, $spc);
        
        $dataArray = array('supportCode' => $spc);
        $dataArray['id'] = '';
        if($setting)
        {
            scp_log_message(LOG_MINFO, $spc, 'Se actualizó la configuración ' . scp_encrypt(self::CURR_SETTING, $this->scpencrypt ) );
            $dataArray['rm'] = 'Se actualizó la configuración';
            $dataArray['id'] = scp_encrypt(self::CURR_SETTING, $this->scpencrypt);
        }
        else
        {
            scp_log_message(LOG_MINFO, $spc, 'No se actualizó la configuración . error: ' . $this->db->_error_message());
            $dataArray['rm'] = 'No se actualizó la configuración';
        }
        $this->response($dataArray, 200); // 200 being the HTTP response code
    }

}