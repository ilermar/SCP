<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Login_drv extends REST_Controller {

	function __construct() 
    {
        parent::__construct();

		$this->load->model('login_model');
		$this->load->model('users_model');
        $this->load->model('supportcode_model');
    }

	public function index()
	{
		$this->log_in();//call log_in just to ensure the use of only log_in access
	}
	/**
	* 
	*/
	public function log_in_post()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        scp_log_message(LOG_MINFO, $spc, 'Login iniciado');
		if( $this->post('email') !== FALSE && $this->post('password') !== FALSE )
		{
			$logUser = $this->login_model->login_user( $this->post('email') , $this->post('password'), $spc );
			if( $logUser )
			{
				$user = $logUser[0];
				scp_log_message(LOG_MINFO, $spc, 'Usuario iniciando sesión '. $user->email );
            					
				$this->session->set_userdata('name', $user->name);
				$this->session->set_userdata('email', $user->email);
				$this->session->set_userdata('profile', $user->profile);

				$this->session->set_userdata('startTime', new DateTime());

				$this->response( array( 'rm' => 'Sesión iniciada.', 
										'supportCode' => $spc,
										'profile' => $user->profile,
										'name' => $user->name ), 200);
			}
			else
			{
				scp_log_message(LOG_ERROR, $spc, 'Usuario no válido');
				$this->response(array('rm' => 'Usuario no válido', 'supportCode' => $spc), 404);
			}
		}
		else
		{
			scp_log_message(LOG_ERROR, $spc, 'Datos incompletos para iniciar sesión');
			$this->response(array('rm' => 'Datos incompletos.', 'supportCode' => $spc), 400);
		}
	}

	public function isLogged()
	{
		$userIsLogged = FALSE;
		if($this->session->userdata('name') !== FALSE && $this->session->userdata('profile') !== FALSE)
		{
			_updateSession();
			$userIsLogged = TRUE;
		}
		return $userIsLogged;
	}

	private function _updateSession()
	{
		$this->session->set_userdata('currDate', new DateTime());
	}

	public function log_out_post()
	{
		$spc = $this->supportcode_model->get_next_support_code();
		$this->session->sess_destroy();
		scp_log_message(LOG_MINFO, $spc, 'Datos incompletos para iniciar sesión');
		$this->response(array('rm' => 'Sesión terminada.', 'supportCode' => $spc), 200);
	}

	public function change_post()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        scp_log_message(LOG_MINFO, $spc, 'Cambio de contraseña iniciado');
        //$arr = array($this->post('email'), $this->post('currentpwd'), $this->post('newpwd'));
        //var_dump($arr);
		if( $this->post('currentpwd') !== FALSE && 
			$this->post('newpwd') !== FALSE && 
			$this->post('email') !== FALSE )
		{
			$logUser = $this->login_model->change_pwd( $this->post('email') , $this->post('currentpwd'), $this->post('newpwd'), $spc );
			if( $logUser )
			{
				$user = $logUser[0];
				scp_log_message(LOG_MINFO, $spc, 'Cambio de contraseña '. $user->email );
            	
				$this->response( array( 'rm' => 'Cambio de contraseña exitoso.', 
										'supportCode' => $spc ), 200);
			}
			else
			{
				scp_log_message(LOG_ERROR, $spc, 'Usuario no válido');
				$this->response(array('rm' => 'Usuario no válido', 'supportCode' => $spc), 404);
			}
		}
		else
		{
			scp_log_message(LOG_ERROR, $spc, 'Datos incompletos para cambiar contraseña');
			$this->response(array('rm' => 'Datos incompletos para cambiar contraseña', 'supportCode' => $spc), 400);
		}
	}

	public function recover_post()
	{
		$spc = $this->supportcode_model->get_next_support_code();
        scp_log_message(LOG_MINFO, $spc, 'Recuperación de contraseña');

		if( $this->post('email') !== FALSE )
		{
			$emailUser = $this->post('email');
			
			$logUser = $this->login_model->getByEmail( $emailUser, $spc );
			if( $logUser )
			{//se encontro el usuario
				$this->load->model('settings_model');
				
				$resSettings = $this->settings_model->getCurrent($spc);
				$settings = $resSettings[0];
        		$config = array(	
        			'protocol' => 'smtp',
        			'mailtype' => 'html',
        			'smtp_host'	=> (intval( $settings->use_ssl ) === 1 ? 'ssl://' : '' ).$settings->smtp_server,
					'smtp_user'	=> $settings->user,
					'smtp_pass'	=> $settings->password,
					'smtp_port'	=> $settings->port,
            		'newline'   => "\r\n",
        		);
        		$this->load->library('email', $config);


        		
        		$subject 	= "Recuperación de contraseña";
        		
        		$this->email->from('no-contestar@drsergioramosreyes.com', 'SCP Soporte');
        		$this->email->to($emailUser); 
        		$this->email->subject($subject);
        		
				$data = array(
					'id' => $logUser->id,
					'email' => $logUser->email,
		            'password' => get_random_string()
		        );
		        $this->users_model->update($data, $spc);
        		$htmlBody = $this->load->view('recover_pass', $data, true);
        
        		$this->email->message($htmlBody);
				scp_log_message(LOG_MINFO, $spc, 'Recuperación de contraseña '. $emailUser );            	
				if ( !$this->email->send() )
		        {
		        	scp_log_message(LOG_MINFO, $spc, " No se pudo enviar el correo ---- " . $this->email->print_debugger() );
		        }
		        else
		        {
		            scp_log_message(LOG_MINFO, $spc, " correo enviado " );
		        }
		        $this->response( array( 'rm' => 'Recuperación de contraseña exitosa.', 
										'supportCode' => $spc ), 200);
			}
			else
			{
				scp_log_message(LOG_ERROR, $spc, 'Usuario no encontrado');
				$this->response(array('rm' => 'Usuario no encontrado', 'supportCode' => $spc), 404);
			}
		}
		else
		{
			scp_log_message(LOG_ERROR, $spc, 'Datos incompletos para recuperar contraseña');
			$this->response(array('rm' => 'Datos incompletos para recuperar contraseña', 'supportCode' => $spc), 400);
		}
	}
}