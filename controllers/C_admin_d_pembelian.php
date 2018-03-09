<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_d_pembelian extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_d_pembelian','M_satuan','M_produk','m_d_penerimaan'));
		
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
				$cek_data_pembelian = $this->M_d_pembelian->cek_h_pembelian($this->uri->segment(2,0));
				if(!empty($cek_data_pembelian))
				{
					if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
					{
						$cari = "AND (nama_produk LIKE '%".str_replace("'","",$_GET['cari'])."%')";
					}
					else
					{
						$cari = "";
					}
					
					$list_produk = $this->M_produk->list_produk_limit_d_pembelian($this->uri->segment(2,0),'',10,0);
					$list_satuan = $this->M_satuan->list_satuan('');
					$list_d_pembelian = $this->M_d_pembelian->list_d_pembelian($this->uri->segment(2,0),$cari);
					$data = array('page_content'=>'king_admin_d_pembelian','list_d_pembelian'=>$list_d_pembelian,'cek_data_pembelian' => $cek_data_pembelian,'list_satuan'=>$list_satuan,'list_produk'=>$list_produk);
					$this->load->view('admin/container',$data);
				}
				else
				{
					header('Location: '.base_url().'admin-transaksi-h-pembelian');
				}
			}
			else
			{
				header('Location: '.base_url().'admin-login');
			}
		}
	}
	
	public function simpan()
	{
		$satuan_produk = explode("-",$_POST['satuan_produk']);
		
		if (!empty($_POST['stat_edit']))
		{
			$this->M_d_pembelian->edit
			(
				$_POST['stat_edit']
				,$_POST['id_h_pembelian']
				,$_POST['id_produk']
				,$_POST['jumlah']
				,str_replace(".","",$_POST['harga'])
				,str_replace(".","",$_POST['diskon'])
				,$_POST['optr_diskon']
				,$satuan_produk[0]
				,$satuan_produk[1]
				,$this->session->userdata('ses_id_karyawan')
			);
			
			$cek_d_penerimaan = $this->m_d_penerimaan->get_d_penerimaan('id_d_pembelian',$_POST['stat_edit']);
			if(!empty($cek_d_penerimaan))
			{
				$cek_d_pembelian = $this->M_d_pembelian->get_d_pembelian('id_d_pembelian',$_POST['stat_edit'])->row();
				$cek_d_penerimaan = $cek_d_penerimaan->row();
				$harga_beli = $cek_d_pembelian->harga;
				$harga_konversi = $cek_d_pembelian->harga;
				$this->m_d_penerimaan->edit_perubahan_d_pembelian($cek_d_penerimaan->id_d_penerimaan,$harga_beli,$harga_konversi);
			}
			
			header('Location: '.base_url().'admin-transaksi-d-pembelian/'.$_POST['id_h_pembelian']);
		}
		else
		{
			$this->M_d_pembelian->simpan
			(
				$_POST['id_h_pembelian']
				,$_POST['id_produk']
				,$_POST['jumlah']
				,str_replace(".","",$_POST['harga'])
				,str_replace(".","",$_POST['diskon'])
				,$_POST['optr_diskon']
				,$satuan_produk[0]
				,$satuan_produk[1]
				,$this->session->userdata('ses_id_karyawan')
				,$this->session->userdata('ses_kode_kantor')
			);
			header('Location: '.base_url().'admin-transaksi-d-pembelian/'.$_POST['id_h_pembelian']);
		}
	}
	
	public function hapus()
	{
		$id_h_pembelian = $this->uri->segment(2,0);
		$id_d_pembelian = $this->uri->segment(3,0);
		$this->M_d_pembelian->hapus($id_d_pembelian);
		header('Location: '.base_url().'admin-transaksi-d-pembelian/'.$id_h_pembelian);
	}
	
	function cek_kode_d_pembelian_produk()
	{
		$hasil_cek = $this->M_d_pembelian->get_d_pembelian_num_rows('id_produk',$_POST['id_produk']);
		echo $hasil_cek;
	}
	
	function cek_tb_produk()
	{
		if((!empty($_POST['cari'])) && ($_POST['cari']!= "")  )
		{
			$cari = ' AND (A.nama_produk LIKE "%'.$_POST['cari'].'%" OR A.kode_produk LIKE "%'.$_POST['cari'].'%")';
		}
		else
		{
			$cari='';
		}
		
		//$list_produk = $this->M_produk->list_produk_limit_d_diskon($_POST['id_h_diskon'],$cari,10,0);
		$list_produk = $this->M_produk->list_produk_limit_d_pembelian($_POST['id_h_diskon'],$cari,10,0);
		// if(!empty($list_produk))
		// {
			// echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
				// echo '<thead>
				// <tr>';
							// echo '<th width="5%">No</th>';
							// echo '<th width="25%">Kode Produk</th>';
							// echo '<th width="30%">Nama Produk</th>';
							// echo '<th width="20%">Harga</th>';
							// echo '<th width="20%">Aksi</th>';
				// echo '</tr>
				// </thead>';
				// $list_result = $list_produk->result();
				// $no =1;
				// echo '<tbody>';
				// foreach($list_result as $row)
				// {
					// echo'<tr>';
						// echo'<td><input type="hidden" id="no_'.$row->id_produk.'" value="'.$row->id_produk.'" />'.$no.'</td>';
						
						// echo'<td><input type="hidden" id="kode_produk_'.$row->id_produk.'" value="'.$row->kode_produk.'" />'.$row->kode_produk.'</td>';
						// echo'<td><input type="hidden" id="nama_produk_'.$row->id_produk.'" value="'.$row->nama_produk.'" />'.$row->nama_produk.'</td>';
						
						
						// echo'<td><input type="hidden" id="harga_'.$row->id_produk.'" value="'.number_format($row->harga,0,',','.').'" />'.number_format($row->harga,0,',','.').'</td>';
						
						// echo'<td>
// <button type="button" onclick="insert('.$row->id_produk.')" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Pilih</button>
// </td>';
						
					// echo'</tr>';
					// $no++;
				// }
				
				// echo '</tbody>';
			// echo'</table>';
		// }
		// // else
		// // {
			// // echo'<center>';
			// // echo'Tidak Ada Data Yang Ditampilkan !';
			// // echo'</center>';
		// // }
		
		if(!empty($list_produk))
		{
			echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
				echo '<thead>
<tr>';
							echo '<th width="5%">No</th>';
							echo '<th width="25%">Kode Produk</th>';
							echo '<th width="30%">Nama Produk</th>';
							echo '<th width="20%">Harga</th>';
							echo '<th width="20%">Aksi</th>';
				echo '</tr>
</thead>';
				$list_result = $list_produk->result();
				$no =1;
				echo '<tbody>';
				foreach($list_result as $row)
				{
					echo'<tr>';
						echo'<td><input type="hidden" id="no2_'.$row->id_produk.'" value="'.$row->id_produk.'" />'.$no.'</td>';
						
						echo'<td><input type="hidden" id="kode_produk2_'.$row->id_produk.'" value="'.$row->kode_produk.'" />'.$row->kode_produk.'</td>';
						echo'<td><input type="hidden" id="nama_produk2_'.$row->id_produk.'" value="'.$row->nama_produk.'" />'.$row->nama_produk.'</td>';
						
						
						echo'<td><input type="hidden" id="harga2_'.$row->id_produk.'" value="'.number_format($row->harga,0,',','.').'" />'.number_format($row->harga,0,',','.').'</td>';
						
						echo'<td>
<button type="button" onclick="insert('.$row->id_produk.')" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">Pilih</button>
</td>';
						
					echo'</tr>';
					$no++;
				}
				
				echo '</tbody>';
			echo'</table>';
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin_d_pembelian.php */