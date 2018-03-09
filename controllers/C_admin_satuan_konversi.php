<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_satuan_konversi extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_satuan_konversi'));
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
					$cari = $_GET['cari'];
				}
				else
				{
					$cari = "";
				}
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-satuan-konversi?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-satuan-konversi/');
				
				
				
				$config['total_rows'] = $this->M_satuan_konversi->count_konversi_limit($cari)->JUMLAH;
				$config['uri_segment'] = 2;	
				$config['per_page'] = 5;
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
				
				$tabel_konversi = $this->M_satuan_konversi->list_konversi_limit($this->session->userdata('ses_kode_kantor'),$cari,$this->uri->segment(2,0),$config['per_page']);//
				$list_field = $tabel_konversi->field_data();
				$data=array('page_content'=>'admin_satuan_konversi','tabel_konversi'=>$tabel_konversi,'list_field'=>$list_field,'halaman'=>$halaman);//
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
		$id_satuan = $this->M_satuan_konversi->getIdsatuan( $_POST['id_satuan'])->id_satuan;
		$id_produk = $_POST['id_produk'];
		$nilai = $_POST['nilai'];
		
		$this->M_satuan_konversi->simpan($id_satuan,$id_produk,$nilai,$this->session->userdata('ses_id_karyawan'),$this->session->userdata('ses_kode_kantor'));
		//header('Location: '.base_url().'admin-login');
	}
		
}