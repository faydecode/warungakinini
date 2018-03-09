<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_get_produk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_get_produk','M_produk','M_satuan'));
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
					$cari = "WHERE A.kode_kantor = '".$this->session->userdata('ses_kode_kantor')."' AND A.nama_d_diskon LIKE '%".str_replace("'","",$_GET['cari'])."%'";
				}
				else
				{
					$cari = "WHERE A.kode_kantor = '".$this->session->userdata('ses_kode_kantor')."'";
				}
				
				$id_h_diskon = $this->uri->segment(2,0);
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-get-produk?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-get-produk/');
				$config['total_rows'] = $this->M_get_produk->count_detail_diskon_limit($cari)->JUMLAH;
				$config['uri_segment'] = 3;	
				$config['per_page'] = 30;
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
				$list_detail_diskon = $this->M_get_produk->list_detail_diskon_limit($cari,$config['per_page'],$this->uri->segment(3,0));
				$list_produk = $this->M_produk->list_produk_limit('',10,0);
				//$list_satuan = $this->M_satuan->list_satuan();
		
				$data = array('page_content'=>'admin_get_produk','halaman'=>$halaman,'id_h_diskon'=>$id_h_diskon,//'list_satuan'=>$list_satuan,
							  'list_produk' => $list_produk,'list_detail_diskon'=>$list_detail_diskon
							);
				
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
			$this->M_get_produk->edit(
				$_POST['stat_edit'],
				$_POST['id_h_diskon'],
				$_POST['id_produk'],
				$_POST['satuan'],
				$_POST['nama'],
				$_POST['nominal'],
				$_POST['keterangan'],
				$this->session->userdata('ses_id_karyawan'),
				$this->session->userdata('ses_kode_kantor')
			);
			header('Location: '.base_url().'admin-get-produk/'.$id_h_diskon.'?'.http_build_query($_GET));
		}
		else
		{
			$this->M_get_produk->simpan(
				$_POST['id_h_diskon'],
				$_POST['id_produk'],
				$_POST['satuan'],
				$_POST['nama'],
				$_POST['nominal'],
				$_POST['keterangan'],
				$this->session->userdata('ses_id_karyawan'),
				$this->session->userdata('ses_kode_kantor')
			);
			header('Location: '.base_url().'admin-get-produk/'.$id_h_diskon.'?'.http_build_query($_GET));
		}
		
		//echo 'ade';
	}
	
	public function hapus()
	{
		$id_h_diskon = $this->uri->segment(2,0);
		$id = $this->uri->segment(3,0);
		$this->M_get_produk->hapus($id);
		header('Location: '.base_url().'admin-get-produk/'.$id_h_diskon.'?'.http_build_query($_GET));
	}
	
	function get_satuan_produk()
	{
		$id = $_POST['id'];
		
		$list_satuan = $this->M_satuan->list_satuan_by_produk($id);
		
		if(!empty($list_satuan))
		{
			echo '<option value="">--Pilih Satuan--</option>';

			if (!empty($list_satuan))
			{
				$list_result = $list_satuan->result();
				foreach($list_result as $row)
				{
					echo '<option value="'.$row->kode_satuan.'">'.$row->kode_satuan.'</option>';
				}
			 }

		}
		
	}
	
	function cek_detail_diskon()
	{
		$hasil_cek = $this->M_get_produk->get_detail_diskon_num_rows('nama_d_diskon',$_POST['nama']);
		echo $hasil_cek;
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin_jabatan.php */