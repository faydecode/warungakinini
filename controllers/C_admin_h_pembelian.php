<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_h_pembelian extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_h_pembelian','M_supplier'));
		
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
				// $data = array('page_content'=>'king_h_pembelian');
				// $this->load->view('admin/container',$data);
				if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
				{
					$cari = "WHERE (A.nama_h_pembelian LIKE '%".str_replace("'","",$_GET['cari'])."%' OR B.nama_supplier LIKE '%".str_replace("'","",$_GET['cari'])."%')";
				}
				else
				{
					$cari = "";
				}
				
				$this->load->library('pagination');
				//$config['first_url'] = base_url().'admin/h_pembelian?'.http_build_query($_GET);
				//$config['base_url'] = base_url().'admin/h_pembelian/';
				$config['first_url'] = site_url('admin-transaksi-h-pembelian?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-transaksi-h-pembelian/');
				$config['total_rows'] = $this->M_h_pembelian->count_h_pembelian_limit($cari)->JUMLAH;
				$config['uri_segment'] = 2;	
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
				$list_h_pembelian = $this->M_h_pembelian->list_h_pembelian_limit($cari,$config['per_page'],$this->uri->segment(2,0));
				$list_supplier = $this->M_supplier->list_supplier_limit('',10,0);
				$data = array('page_content'=>'king_admin_h_pembelian','halaman'=>$halaman,'list_h_pembelian'=>$list_h_pembelian,'list_supplier'=>$list_supplier);
				$this->load->view('admin/container',$data);
			}
			else
			{
				header('Location: '.base_url().'admin-login');
			}
		}
	}
	
	function cek_tb_supplier()
	{
		if((!empty($_POST['cari'])) && ($_POST['cari']!= "")  )
		{
			$cari = ' WHERE (nama_supplier LIKE "%'.$_POST['cari'].'%" OR kode_supplier LIKE "%'.$_POST['cari'].'%")';
		}
		else
		{
			$cari='';
		}
		
		$list_supplier = $this->M_supplier->list_supplier_limit($cari,10,0);
		// if(!empty($list_supplier))
		// {
			// echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
				// echo '<thead>
// <tr>';
							// echo '<th width="5%">No</th>';
							// echo '<th width="20%">Kode</th>';
							// echo '<th width="35%">Nama Supplier</th>';
							// echo '<th width="40%">Bidang Usaha</th>';
							// echo '<th width="10%">Aksi</th>';
				// echo '</tr>
// </thead>';
				// $list_result = $list_supplier->result();
				// $no =1;
				// echo '<tbody>';
				// foreach($list_result as $row)
				// {
					// echo'<tr>';
						// echo'<td><input type="hidden" id="no_'.$row->id_supplier.'" value="'.$row->id_supplier.'" />'.$no.'</td>';
						
						// echo'<td>'.$row->kode_supplier.'</td>';
						// echo'<td>'.$row->nama_supplier.'</td>';
						// echo'<td>'.$row->bidang.'</td>';
						
						// echo'<input type="hidden" id="nama_supplier_'.$row->id_supplier.'" value="'.$row->nama_supplier.'" />';
						
						// echo'<td>
// <button type="button" onclick="insert('.$row->id_supplier.')" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Pilih</button>
// </td>';
						
					// echo'</tr>';
					// $no++;
				// }
				
				// echo '</tbody>';
			// echo'</table>';
		// }
		
		if(!empty($list_supplier))
		{
			echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
				echo '<thead>
<tr>';
							echo '<th width="5%">No</th>';
							echo '<th width="20%">Kode</th>';
							echo '<th width="35%">Nama Supplier</th>';
							echo '<th width="40%">Bidang Usaha</th>';
							echo '<th width="10%">Aksi</th>';
				echo '</tr>
</thead>';
				$list_result = $list_supplier->result();
				$no =1;
				echo '<tbody>';
				foreach($list_result as $row)
				{
					echo'<tr>';
						echo'<td><input type="hidden" id="no2_'.$row->id_supplier.'" value="'.$row->id_supplier.'" />'.$no.'</td>';
						
						echo'<td>'.$row->kode_supplier.'</td>';
						echo'<td>'.$row->nama_supplier.'</td>';
						echo'<td>'.$row->bidang.'</td>';
						
						echo'<input type="hidden" id="nama_supplier2_'.$row->id_supplier.'" value="'.$row->nama_supplier.'" />';
						
						echo'<td>
<button type="button" onclick="insert('.$row->id_supplier.')" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Pilih</button>
</td>';
						
					echo'</tr>';
					$no++;
				}
				
				echo '</tbody>';
			echo'</table>';
		}
	}
	
	public function simpan()
	{
		if (!empty($_POST['stat_edit']))
		{
			$this->M_h_pembelian->edit
			(
				$_POST['stat_edit']
				,$_POST['id_supplier']
				,$_POST['nama_h_pembelian']
				,$_POST['tgl_h_pembelian']
				,$_POST['ket_h_pembelian']
				,$this->session->userdata('ses_id_karyawan')
			);
			header('Location: '.base_url().'admin-transaksi-h-pembelian');
		}
		else
		{
			$this->M_h_pembelian->simpan
			(
				$_POST['id_supplier']
				,$_POST['nama_h_pembelian']
				,$_POST['tgl_h_pembelian']
				,$_POST['ket_h_pembelian']
				,$this->session->userdata('ses_id_karyawan'),$this->session->userdata('ses_kode_kantor')
			);
			header('Location: '.base_url().'admin-transaksi-h-pembelian');
		}
		
	}
	
	public function hapus()
	{
		$id = $this->uri->segment(2,0);
		$this->M_h_pembelian->hapus($id);
		header('Location: '.base_url().'admin-transaksi-h-pembelian');
	}
	
	function cek_h_pembelian()
	{
		$hasil_cek = $this->M_h_pembelian->get_h_pembelian_num_rows('nama_h_pembelian',$_POST['nama']);
		echo $hasil_cek;
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin_h_pembelian.php */