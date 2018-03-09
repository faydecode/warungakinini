<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_public_profile extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_public_profile'));
	}
	
	public function index()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			if((!empty($_GET['cari_alamat'])) && ($_GET['cari_alamat']!= ""))
			{
				$cari = $_GET['cari_alamat'];
			} else {
				$cari = '';
			}
			
			$data_profile = $this->M_public_profile->get_data_costumer($this->session->userdata('ses_public_id_user'));
			$list_alamat = $this->M_public_profile->list_alamat($this->session->userdata('ses_public_id_user'),$cari);
			
			
			
			$data = array(
				'id_costumer'=>$data_profile->id_costumer,
				'no_costumer'=>$data_profile->no_costumer,
				'tgl_pengajuan'=>$data_profile->tgl_pengajuan,
				'nama_lengkap'=>$data_profile->nama_lengkap,
				'panggilan'=>$data_profile->panggilan,
				'hp'=>$data_profile->hp,
				'alamat_rumah'=>$data_profile->alamat_rumah_sekarang,
				'avatar'=>$data_profile->avatar2,
				'avatar_url'=>$data_profile->avatar_url,
				'email'=>$data_profile->email_costumer,
				'username'=>$data_profile->username,
				'list_alamat'=>$list_alamat
			);
				
			$this->load->view('front/page/page_profile.html',$data);
			
		}
	}
	
	public function update_profile()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_user = $_POST['id_costumer'];
			$nama = $_POST['nama'];
			$panggilan = $_POST['panggilan'];
			$hp = $_POST['hp'];
			$email = $_POST['email'];
			$alamat_rumah = $_POST['alamat_rumah'];
			
			$query = $this->M_public_profile->update_profile(
				$id_user,$nama,$panggilan,$alamat_rumah,$hp,$email
			);
			
			if($query)
			{
				header('Location: '.base_url().'profile');
			} else {
				echo "Data Gagal di Update";
				$data = '';
				$this->load->view('front/page/page_profile.html',$data);
			}
		}
	}
	
	public function update_password()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_costumer = $this->session->userdata('ses_public_id_user');
			$username = $this->session->userdata('ses_public_user');
			$old_pass = md5($_POST['password1']);
			$new_pass = md5($_POST['password2']);
			
			$cek_data = $this->M_public_profile->cek_user($id_costumer,$username,$old_pass);
			if(!empty($cek_data))
			{
				$this->M_public_profile->update_password($id_costumer,$username,$new_pass);
			} else {
				echo 'gagal update';
			}
			
			header('Location: '.base_url().'profile');
		}
	}
	
	
	public function pengaturan()
	{
		$data_profile = $this->M_public_profile->get_data_costumer($this->session->userdata('ses_public_id_user'));
			
		$data = array(
			'id_costumer'=>$data_profile->id_costumer,
			'tgl_pengajuan'=>$data_profile->tgl_pengajuan,
			'avatar'=>$data_profile->avatar2,
			'username'=>$data_profile->username
		);
		$this->load->view('front/page/profile_setting.html',$data);
	}
	
	function cari_alamat()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$list_alamat = $this->M_public_profile->list_alamat($this->session->userdata('ses_public_id_user'),'');
			
		}
	}
	
	function simpan_alamat()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			if (!empty($_POST['stat_edit']))
			{
				$this->M_public_profile->update_alamat(
					$_POST['stat_edit'],
					$_POST['nama_alamat'],
					$_POST['nama_penerima'],
					$_POST['no_hp'],
					$_POST['provinsi'],
					$_POST['kabupaten'],
					$_POST['kecamatan'],
					$_POST['kodepos'],
					$_POST['detail_alamat']
				);
			}
			else
			{
				$this->M_public_profile->simpan_alamat(
					$this->session->userdata('ses_public_id_user'),
					$_POST['nama_alamat'],
					$_POST['nama_penerima'],
					$_POST['no_hp'],
					$_POST['kode_negara'],
					$_POST['provinsi'],
					$_POST['kabupaten'],
					$_POST['kecamatan'],
					'',
					$_POST['kodepos'],
					$_POST['detail_alamat']
				);
			}
			
			//header('Location: '.base_url().'profile');
			
		}
		
	}
	
	function hapus_alamat()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_alamat = $_POST['id_alamat'];
			
			$this->M_public_profile->hapus_alamat($id_alamat);
		}
		
	}
	
	function load_prov()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$data = $this->M_public_profile->load_prov();
			
			if(!empty($data))
			{
				$list_prov = $data->result();
				//echo '<option value="">--- Provinsi ---</option>';
				foreach($list_prov as $row)
				{
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}
	}
	
	function load_kab()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_prov = $_POST['id_prov'];
			
			$data = $this->M_public_profile->load_kab($id_prov);
			
			if(!empty($data))
			{
				$list_kab = $data->result();
				//echo '<option value="">--- Kabupaten/Kota ---</option>';
				foreach($list_kab as $row)
				{
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}
	}
	
	function load_kec()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_kab = $_POST['id_kab'];
			
			$data = $this->M_public_profile->load_kec($id_kab);
			
			if(!empty($data))
			{
				$list_kec = $data->result();
				//echo '<option value="">--- Kecamatan ---</option>';
				foreach($list_kec as $row)
				{
					echo '<option value="'.$row->id.'">'.$row->name.'</option>';
				}
			}
		}
	}
	
	
	function do_upload($id,$cek_bfr)
	{
		$this->load->library('upload');

		if($cek_bfr != '')
		{
			@unlink('./assets/global/member/'.$cek_bfr);
		}
		
		if (!empty($_FILES['foto']['name']))
		{
			$config['upload_path'] = 'assets/global/member/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '2024';
			//$config['max_widtd']  = '300';
			//$config['max_height']  = '300';
			$config['file_name']	= $cek_bfr;
			$config['overwrite']	= true;
			

			$this->upload->initialize($config);

			//Upload file 1
			if ($this->upload->do_upload('foto'))
			{
				$hasil = $this->upload->data();
			}
		}
	}
	
	public function upload_gambar()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			$id_costumer = $this->session->userdata('ses_public_id_user');
			
			$this->do_upload($_FILES['foto']['name'],$id_costumer);
			$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
			$foto = $id_costumer.".".$ext;
			
			$this->M_public_profile->update_avatar($id_costumer,$foto);
		}
		
		header('Location: '.base_url().'profile');
	}
	
	
	
	
}














