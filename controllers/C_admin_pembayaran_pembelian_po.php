<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_pembayaran_pembelian_po extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('m_bayar_pembelian','m_h_pembelian'));
		
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
				// $data = array('page_content'=>'king_jabatan');
				// $this->load->view('admin/container',$data);
				if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
				{
					$cari = " WHERE (NO_PO LIKE '%".str_replace("'","",$_GET['cari'])."%' OR nama_h_pembelian LIKE '%".str_replace("'","",$_GET['cari'])."%')";
				}
				else
				{
					$cari = "";
				}
				
				$this->load->library('pagination');
				//$config['first_url'] = base_url().'admin/jabatan?'.http_build_query($_GET);
				//$config['base_url'] = base_url().'admin/jabatan/';
				$config['first_url'] = site_url('admin-pembelian-bayar/'.$this->uri->segment(2,0).'?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-pembelian-bayar/'.$this->uri->segment(2,0).'/');
				$config['total_rows'] = $this->m_bayar_pembelian->count_pembayaran_pembelian_limit(" WHERE A.id_h_pembelian = '".$this->uri->segment(2,0)."'",$cari)->JUMLAH;
				$config['uri_segment'] = 3;	
				$config['per_page'] = 30;
				$config['num_links'] = 2;
				$config['suffix'] = '?' . http_build_query($_GET, '', "&");
				//$config['use_page_numbers'] = TRUE;
				//$config['page_query_string'] = false;
				//$config['query_string_segment'] = '';
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
				$data_h_pembelian = $this->m_h_pembelian->list_h_pembelian_limit(" WHERE A.id_h_pembelian = '".$this->uri->segment(2,0)."'",1,0);
				$list_supplier_bayar = $this->m_bayar_pembelian->list_pembayaran_pembelian_limit(" WHERE A.id_h_pembelian = '".$this->uri->segment(2,0)."'",$cari,$config['per_page'],$this->uri->segment(3,0));
				$data = array('page_content'=>'king_admin_supplier_bayar_po','halaman'=>$halaman,'list_supplier_bayar'=>$list_supplier_bayar,'data_h_pembelian'=>$data_h_pembelian->row());
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
		$nominal = str_replace(".","",$_POST['nominal']);
		if (!empty($_POST['stat_edit']))
		{
			$this->m_bayar_pembelian->edit($_POST['stat_edit'],$_POST['id_supplier'],$_POST['id_h_pembelian'],$_POST['cara'],$nominal,$_POST['ket'],$_POST['tgl_bayar'],$this->session->userdata('ses_id_karyawan'));
			header('Location: '.base_url().'admin-pembelian-bayar/'.$this->uri->segment(2,0));
		}
		else
		{
			
			$this->m_bayar_pembelian->simpan($_POST['id_h_pembelian'],$_POST['id_supplier'],$_POST['cara'],$nominal,$_POST['ket'],$_POST['tgl_bayar'],$this->session->userdata('ses_id_karyawan'),$this->session->userdata('ses_kode_kantor'));
			header('Location: '.base_url().'admin-pembelian-bayar/'.$this->uri->segment(2,0));
		}
	}
	
	public function hapus()
	{
		$id = $this->uri->segment(3,0);
		$this->m_bayar_pembelian->hapus($id);
		header('Location: '.base_url().'admin-pembelian-bayar/'.$this->uri->segment(2,0));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin_jabatan.php */