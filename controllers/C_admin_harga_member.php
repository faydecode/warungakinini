<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_harga_member extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_harga_member','M_kat_member','M_satuan','M_produk'));
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
				
				if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
				{
					$cari = "WHERE A.kode_kantor = '".$this->session->userdata('ses_kode_kantor')."' AND nama_produk LIKE '%".str_replace("'","",$_GET['cari'])."%'";
					$cari2 = $_GET['cari'];
				}
				else
				{
					$cari = "WHERE A.kode_kantor = '".$this->session->userdata('ses_kode_kantor')."'";
					$cari2 = "";
				}
				
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-harga_member?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-harga_member/');
				$config['total_rows'] = 100;//$this->M_harga_member->count_konversi_limit($cari)->JUMLAH;
				$config['uri_segment'] = 2;	
				$config['per_page'] = 20;
				$config['num_links'] = 2;
				$config['suffix'] = '?' . http_build_query($_GET, '', "&");
				$config['first_page'] = 'Awal';
				$config['last_page'] = 'Akhir';
				$config['next_page'] = '&laquo;';
				$config['prev_page'] = '&raquo;';
				
				
				$config['full_tag_open'] = '<div><ul class="pagination">';
				$config['full_tag_close'] = '</ul></div>';
				$config['first_link'] = '&laquo; First';
				$config['first_tag_open'] = '<li class="prev page">';
				$config['first_tag_close'] = '</li>';
				$config['last_link'] = 'Last &raquo;';
				$config['last_tag_open'] = '<li class="next page">';
				$config['last_tag_close'] = '</li>';
				$config['next_link'] = 'Next &rarr;';
				$config['next_tag_open'] = '<li class="next page">';
				$config['next_tag_close'] = '</li>';
				$config['prev_link'] = '&larr; Previous';
				$config['prev_tag_open'] = '<li class="prev page">';
				$config['prev_tag_close'] = '</li>';
				$config['cur_tag_open'] = '<li class="active"><a href="">';
				$config['cur_tag_close'] = '</a></li>';
				$config['num_tag_open'] = '<li class="page">';
				$config['num_tag_close'] = '</li>';
				
				
				//inisialisasi config
				$this->pagination->initialize($config);
				$halaman = $this->pagination->create_links(); 
				
				$list_kat_member = $this->M_kat_member->list_kat_member();
				$list_satuan = $this->M_satuan->list_satuan();
				//$list_produk = $this->M_produk->list_produk_harga_limit($cari,10,0);

				$list_harga_member = $this->M_harga_member->list_harga_member_limit($this->session->userdata('ses_kode_kantor'),$cari2,$this->uri->segment(2,0),$config['per_page']);
				$list_field = $list_harga_member->field_data();
				$data=array('page_content'=>'admin_harga_member','list_kat_member' => $list_kat_member,
							'list_satuan' =>$list_satuan,'list_field' =>$list_field,//'list_produk' =>$list_produk,
							'halaman'=>$halaman, 'list_harga_member'=>$list_harga_member); //
				
				$this->load->view('admin/container',$data);
			}
			else
			{
				header('Location: '.base_url().'admin-login');
			}
		}
	}
	
	public function simpan()
	{
		
		if (!empty($_POST['stat_edit']))
		{
			$this->M_harga_member->edit(
					$_POST['stat_edit']
					,$_POST['id_produk']
					,$_POST['satuan']
					,$_POST['kat_member']
					,$_POST['harga']
					,$_POST['besar']
					,$_POST['keterangan']
					,$this->session->userdata('ses_id_karyawan'));
		}
		else
		{
			$this->M_harga_member->simpan(
					$_POST['id_produk']
					,$_POST['satuan']
					,$_POST['kat_member']
					,$_POST['harga']
					,$_POST['besar']
					,$_POST['keterangan']
					,$this->session->userdata('ses_id_karyawan')
					,$this->session->userdata('ses_kode_kantor'));
							
		}
		header('Location: '.base_url().'admin-harga-member?'.http_build_query($_GET));
	}
		
}