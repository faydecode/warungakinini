<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_public_history_order extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_public_history_order','M_public_profile'));
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
			$id_costumer = $this->session->userdata('ses_public_id_user');
			$list_status_pembelian = $this->M_public_history_order->get_header_pembelian($id_costumer);
			$data_profile = $this->M_public_profile->get_data_costumer($this->session->userdata('ses_public_id_user'));

			
			$list_d_pembelian = $this->M_public_history_order->get_detail_pembelian($id_costumer);
			
			
			if(!empty($list_status_pembelian))
			{
				$data = array(
					'username'=>$data_profile->username,
					'tgl_pengajuan'=>$data_profile->tgl_pengajuan,
					'avatar'=>$data_profile->avatar2,
					'h_status_pembelian'=>$list_status_pembelian,
					'list_d_pembelian'=>$list_d_pembelian
				);
			} else {
				$data = array(
					'username'=>$data_profile->username,
					'tgl_pengajuan'=>$data_profile->tgl_pengajuan,
					'avatar'=>$data_profile->avatar2,
					'h_status_pembelian'=>'',
					'list_d_pembelian'=>''
				);
			}
			$this->load->view('front/page/page_history_order.html',$data);
		}
	}
	
	public function history_transaksi()
	{
		if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
		{
			$data = array('is_login'=> false );
			
			header('Location: '.base_url());
		}
		else 
		{
			
			$status = $_POST['status'];
			$fromdate = $_POST['fromdate'];
			$todate = $_POST['todate'];
			$cari = $_POST['cari'];
			
			$id_costumer = $this->session->userdata('ses_public_id_user');
			$history_header = $this->M_public_history_order->history_header_pembelian($status,$fromdate,$todate,$id_costumer,$cari);
			$history_detail = $this->M_public_history_order->history_detail_pembelian($status,$fromdate,$todate,$id_costumer,$cari);
			
			
				if(!empty($history_header))
				{
					/*$list_h = $history_header->result();
					foreach($list_h as $rowx)
					{
						echo '	<p>Tanggal Transaksi : '.$rowx->tgl_h_penjualan.' | Rp. '.$rowx->bayar.'</p>';
					}*/
					
					$no=1;
					echo '<div class="toggle toggle-transparent toggle-bordered-simple">';
			
			
					$list_h = $history_header->result();
					foreach($list_h as $rowx)
					{
						if($no==1)
						{
							echo '<div class="toggle active">';
						} else {
							echo '<div class="toggle">';
						}
						
			
						echo '<label>No. Faktur : '.$rowx->no_faktur.'</label>';
						echo '<div class="toggle-content">';
						echo '	<p>Tanggal Transaksi : '.$rowx->tgl_h_penjualan.' | Rp. '.$rowx->bayar.'</p>';
						echo '	<div class="table-responsive">';
						echo '	<table class="table table-hover">';
						echo '		<thead>';
						echo '			<tr>';
						echo '				<th>Nama Produk</th>';
						echo '				<th>Deskripsi</th>';
						echo '				<th>Total</th>';
						echo '			</tr>';
						echo '		</thead>';
						echo '		<tbody>';
	
						if(!empty($history_detail))
						{
							$list_d = $history_detail->result();
							foreach($list_d as $rowxd)
							{
								if($rowx->id_h_penjualan==$rowxd->id_h_penjualan)
								{
									echo '	<tr>';
									echo '		<td>'.$rowxd->nama_produk.'</td>';
									echo '		<td>'.$rowxd->ket.'</td>';
									echo '		<td>'.$rowxd->total.'</td>';
									echo '	</tr>';
		
								}
							}
						}
						
						echo '		</tbody>';			
						echo '	</table>';
						echo '</div>';
						echo '<div class="row">';
						echo '	<div class="col-md-6">';
						echo '		Alamat Tujuan : <br>';
						echo '		<strong>'.$rowx->nama_penerima.'</strong><br>';
						echo $rowx->detail_alamat;
								
						echo '	</div>';

						echo '	<div class="col-md-6">';
						echo '		<p class="pull-right">Ongkos Kirim : Rp. '.$rowx->biaya.'</p>';
						echo '	</div>';
						echo '</div>';
						echo '<div class="row">';
						echo '	<p class="pull-right"><strong>Total Pembayaran : Rp. '.$rowx->bayar.'</strong></p>';
						echo '</div>';
						echo '<div class="row">';
						echo '	<p>Status : '.$rowx->sts_penjualan.'</p>';
						echo '</div>';
					echo '</div>';
					echo '</div>';
				 
						$no++;
					} 
										
				echo '</div>';
			} else {
				echo '<p>Tidak ada transaksi</p>';
			}
		}
	}
}










