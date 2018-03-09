<?php

	class C_public_login extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->helper(array('form','url'));
			$this->load->model(array('M_public_get','M_member','M_email'));
			$this->load->library(array('form_validation','email'));
			
		}
	
		public function index()
		{
			$this->load->view('front/page_login.html');
		}
		
		public function register()
		{
			$data = array('is_login'=>false);
			$this->load->view('front/page_register.html',$data);
		}
		
		function cek_login()
		{
			$user = $_POST['user'];
            $pass = $_POST['password'];
            $data_login = $this->M_public_get->get_login_costumer($user,md5($pass));
    		if(!empty($data_login))
    		{
                if ($data_login->avatar <> "")
                {
                    $src = $data_login->avatar_url;
                }
                else
                {
                	$src = base_url().'assets/global/users/loading.gif';
                }
				
				$member = array(
					'ses_public_id_user'  => $data_login->id_costumer,
					'ses_public_user'  => $user,
					'ses_public_pass'  => base64_encode($pass),
					'ses_public_nama_member' => $data_login->nama_lengkap,
					'ses_public_no_costumer' => $data_login->no_costumer,
					'ses_public_avatar_url' => $src,
					'ses_public_tgl_lahir' => $data_login->tgl_lahir,
					'ses_public_alamat' => $data_login->alamat_rumah_sekarang,
					'ses_public_hp' => $data_login->hp,
					'ses_public_tgl_lahir' => $data_login->tgl_lahir,
					'ses_public_kat_costumer' => $data_login->nama_kat_costumer,
					'ses_public_email' => $data_login->email_costumer,
				);
 
    			$this->session->set_userdata($member);
    			//redirect('index.php/admin','location');
				header('Location: '.base_url());
    		}
    		else
    		{
    			//redirect('index.php/login','location');
				header('Location: '.base_url().'login-akun');
    		}
		}
		
		function do_register()
		{
			$kode_member = $this->M_akun->get_no_costumer()->no_costumer;
			$foto = '';
			
			//validasi form dlu
			$this->form_validation->set_rules('username','Username','required|is_unique[tb_costumer.username]'); 
			$this->form_validation->set_rules('email','Email','required|valid_email|is_unique[tb_costumer.email_costumer]'); 
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|max_length[15]');  
			$this->form_validation->set_rules('password2', 'Password Confirmation', 'required|matches[password]');  
			
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('msg',validation_errors());  
				header('Location: '.base_url().'daftar-akun');
			} else {
				$email = $_POST['email'];
				
				//generate id uniq
				$saltid = md5($email);
				$status = 0;
				
				if($this->M_member->simpan
				(
					'20170100002'  //sementara di patok retail
					,''
					,$kode_member
					,$_POST['nama']
					,$_POST['panggilan']
					,$_POST['tgl_lahir']
					,$_POST['jkel']
					,''
					,$_POST['hp']
					,$_POST['username']
					,md5($_POST['password'])
					,$foto
					,base_url().'assets/global/member/'.$foto
					,$_POST['email']
					,''
					,''
					,''
				)) {
					if($this->sendemail($email,$saltid))
					{
						//sukses kirim email
						//$this->session->set_flashdata('msg','<div class="alert alert-success text-center">Please confirm the mail sent to your email id to complete the registration.</div>');  
						header('Location: '.base_url().'daftar-sukses');
						
					} else {
						$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Please try again ...</div>');  
						header('Location: '.base_url().'daftar-akun');
					}
				} else {
					//gagal simpan
					$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Something Wrong. Please try again ...</div>');  
					header('Location: '.base_url().'daftar-akun');
				}
				
			}
			
			
			
			//header('Location: '.base_url().'daftar-sukses');
			
		}
		
		function register_sukses()
		{
			$data = array('is_login'=>false);
			$this->load->view('front/page/register_sukses.html',$data);
		}
		
		function logout()
		{
			$this->session->unset_userdata('ses_public_user');
			$this->session->unset_userdata('ses_public_pass');
			$this->session->unset_userdata('ses_public_nama_member');
            $this->session->unset_userdata('ses_public_no_costumer');
			$this->session->unset_userdata('ses_public_avatar_url');
			$this->session->unset_userdata('ses_public_tgl_lahir');
			$this->session->unset_userdata('ses_public_alamat');
			$this->session->unset_userdata('ses_public_hp');
			$this->session->unset_userdata('ses_public_tgl_lahir');
			$this->session->unset_userdata('ses_public_kat_costumer');
			$this->session->unset_userdata('ses_public_email');
			
			//redirect('index.php/login','location');
			header('Location: '.base_url());
		}
		
		function sendemail($email,$saltid)
		{  
			// configure the email setting  
			$config['protocol'] = 'smtp';  
			$config['smtp_host'] = 'ssl://smtp.googlemail.com'; //smtp host name  
			$config['smtp_port'] = '465'; //smtp port number  
			$config['smtp_user'] = 'ryuur3i@gmail.com';
			$config['smtp_pass'] = 'dejavumemorian'; //$from_email password  
			$config['mailtype'] = 'html';  
			$config['charset'] = 'iso-8859-1';  
			$config['wordwrap'] = TRUE;  
			$config['newline'] = "\r\n"; //use double quotes  
			$this->email->initialize($config);  
			$url = base_url()."user/confirmation/".$saltid;  
			$this->email->from('ryuur3i@gmail.com', 'CodesQuery');  
			$this->email->to($email);   
			$this->email->subject('Please Verify Your Email Address');  
			$message = "<html><head><head></head><body><p>Hi,</p><p>Thanks for registration with CodesQuery.</p><p>Please click below link to verify your email.</p>".$url."<br/><p>Sincerely,</p><p>CodesQuery Team</p></body></html>";  
			$this->email->message($message);  
			$this->email->send(FALSE);
			$this->email->print_debugger(array('headers'));
			
		}  
		
		public function confirmation($key)  
		{  
		  if($this->M_email->verifyemail($key))  
		  {  
			$this->session->set_flashdata('msg','<div class="alert alert-success text-center">Your Email Address is successfully verified!</div>');  
			redirect(base_url());
		  }  
		  else  
		  {  
			$this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Your Email Address Verification Failed. Please try again later...</div>');  
			redirect(base_url());
		  }  
	    }  
		
	}

?>