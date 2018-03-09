<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('m_dash','M_akun'));
	}
	
	public function index()
	{
		if(($this->session->userdata('ses_user_admin') == null) or ($this->session->userdata('ses_pass_admin') == null))
		{
			header('Location: '.base_url().'admin-login');
		}
		else
		{
			$cek_ses_login = $this->M_akun->get_cek_login($this->session->userdata('ses_user_admin'),md5(base64_decode($this->session->userdata('ses_pass_admin'))));
			
			if(!empty($cek_ses_login))
			{
				$data_transaksi_jual = 0;//$this->m_dash->getTransaksiJual();
				$data_transaksi_beli = 0;//$this->m_dash->getTransaksiBeli();
				$data_total_jual = 0;//$this->m_dash->getTotalJual();
				$data_total_beli = 0;//$this->m_dash->getTotalBeli();
				$st_penjualan = 0;//$this->m_dash->st_penjualan();
				$st_uang_keluar = 0;//$this->m_dash->st_uang_keluar();
				
				$data = array('page_content'=>'admin_dashboard','data_transaksi_jual'=>$data_transaksi_jual,'data_transaksi_beli'=>$data_transaksi_beli,'data_total_jual'=>$data_total_jual,'data_total_beli'=>$data_total_beli,'st_penjualan'=>$st_penjualan,'st_uang_keluar'=>$st_uang_keluar);
				$this->load->view('admin/container',$data);
				//echo "Hallo World";
			}
			else
			{
				header('Location: '.base_url().'admin-login');
			}
		}
	}
	
	public function profile()
	{
		if(($this->session->userdata('ses_user_admin') == null) or ($this->session->userdata('ses_pass_admin') == null))
		{
			header('Location: '.base_url().'admin-login');
		}
		else
		{
			//$cek_ses_login = $this->M_akun->get_cek_login($this->session->userdata('ses_user_admin'),md5(base64_decode($this->session->userdata('ses_pass_admin'))));
			
			//if(!empty($cek_ses_login))
			//{
				
				$profile = $this->M_akun->get_profile($this->session->userdata('ses_id_karyawan'),$this->session->userdata('ses_kode_kantor'));
				
				
				$data = array('page_content'=>'admin_profile','profile'=>$profile);
				$this->load->view('admin/container',$data);
			//}
		
			//else
			//{
			//	header('Location: '.base_url().'admin-login');
			//}
		}
	}
	
	public function simpan_profile()
	{
		$this->M_akun->update_profile(
				$_POST['id_karyawan']
				,$_POST['nama']
				,$_POST['pendidikan']
				,$_POST['hp']
				,$_POST['email']
				,$_POST['alamat']
				,$this->session->userdata('ses_id_karyawan')
				,$this->session->userdata('ses_kode_kantor')
				);
		
		header('Location: '.base_url().'admin-profile');
		
	}
	
	public function update_password()
	{
		$pass_lama = md5($_POST['password']);
		$pass_baru = md5($_POST['pass_baru']);
		
		$cek_password = $this->M_akun->cek_password($_POST['id_akun'],$pass_lama,$this->session->userdata('ses_kode_kantor'));
		
		if(!empty($cek_password))
		{
			$update = $this->M_akun->update_password($_POST['id_akun'],$pass_baru,$this->session->userdata('ses_kode_kantor'));
			echo 'sukses';	
		} else {
			echo 'gagal';
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin.php */