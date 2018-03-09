<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_history_penjualan extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_history_penjualan'));
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
				
				if((!empty($_GET['tgl_from'])) && ($_GET['tgl_from']!= "")  )
				{					
					$tgl1 = $_GET['tgl_from'];
					$tgl2 = $_GET['tgl_to'];
					$status = $_GET['status'];
					$faktur = $_GET['cari'];	
				}
				else
				{
					$tgl1 = date('Y-m-d'); 
					$tgl2 = date('Y-m-d'); 
					$status = '';
					$faktur = '';
				}
				
				
				
				
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-history-penjualan?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-history-penjualan/');
				$config['total_rows'] = $this->M_history_penjualan->count_penjualan_limit($tgl1,$tgl2,$faktur,$status)->JUMLAH;
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
				
				$list_penjualan = $this->M_history_penjualan->list_penjualan(
					$tgl1,$tgl2,$faktur,$status,$config['per_page'],$this->uri->segment(2,0)
				);
				
				
				$data=array('page_content'=>'admin_history_penjualan','halaman'=>$halaman, 
							'list_penjualan'=>$list_penjualan); //
				
				$this->load->view('admin/container',$data);
			}
			else
			{
				header('Location: '.base_url().'admin-login');
			}
		}

	}
	
	function detail_penjualan()
	{
		$id_penjualan = $_POST['id_penjualan'];
		$id_costumer = $_POST['id_costumer'];
		$biaya = $_POST['biaya'];
		$total = $_POST['total'];
		
		$list_d_pejualan = $this->M_history_penjualan->list_d_penjualan($id_penjualan,$id_costumer);
		
		if(!empty($list_d_pejualan))
		{
			echo '<table width="100%" id="example2" class="table table-bordered table-hover">';
			echo '<thead>';
			echo '<tr>';
					echo '<th width="20%">Nama</th>';
					echo '<th width="20%">Deskripsi</th>';
					echo '<th width="5%">Aksi</th>';
			echo '</tr>';
			echo '</thead>';
			$list_result = $list_d_pejualan->result();
			$noxx =0;

			echo '<tbody>';
			
			foreach($list_result as $row)
			{
				echo '<tr>';
				echo '<td><input type="hidden" id="nama_produk_'.$row->id_h_penjualan.'" value="'.$row->nama_produk.'" />'.$row->nama_produk.'</td>';
				echo '<td><input type="hidden" id="desc_'.$row->id_h_penjualan.'" value="'.$row->ket.'" />'.$row->ket.'</td>';
				echo '<td><input type="hidden" id="desc_'.$row->id_h_penjualan.'" value="'.$row->total.'" />'.$row->total.'</td>';
				
				echo '</tr>';
				$noxx++;
			}
			
			echo '<tr>';
			echo '<td></td>';
			echo '<td>Ongkos Kirim : </td>';
			echo '<td>'.$biaya.'</td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td></td>';
			echo '<td>Total Pembayaran : </td>';
			echo '<td>'.$total.'</td>';
			echo '</tr>';
			
			echo '</tbody>';
			echo'</table>';
			
			/*echo '<div class="row">';
			echo '	<div class="pull-right">Ongkos Kirim : '.$biaya.'</div>';
			echo '</div>';
			echo '<div class="row">';
			echo '	<div class="pull-right">Total Pembayaran : '.$total.'</div>';
			echo '</div>';
			*/
		}
		else
		{
			echo'<center>';
			echo'Tidak Ada Data Yang Ditampilkan !';
			echo'</center>';										
		}
	}
	
	function update_transaksi()
	{
		$id_penjualan = $_POST['id_penjualan'];
		$status = $_POST['status'];
		
		$this->M_history_penjualan->update($id_penjualan,$status);
		
		echo 'sukses';
	}
		
}



