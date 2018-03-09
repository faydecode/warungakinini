<?php
	class C_public_cart extends CI_Controller {
		function __construct() {
			parent::__construct();

			$this->load->library('form_validation'); // digunakan untuk proses validasi yg di input
			$this->load->model(array('M_public_cart','M_produk','M_home')); // Load our cart model for our entire class
			$this->load->library(array('cart')); // Load our cart model for our entire class
			$this->load->helper(array('url','form')); // Load our cart model for our entire class
		}
		
		function index() {
			
			$list_produk_lain = $this->M_home->list_produk('',0,10);
			
			$data = array('list_produk_lain' =>$list_produk_lain);
			$this->load->view('front/page/shop_cart.html',$data);
		}
		
		
		function tambah_barang()
		{
			$id = $_POST['id_produk2']; 
			$cty = $_POST['jumlah']; 
			$satuan = $_POST['satuan2']; 
			$harga = $_POST['harga2']; 
			$nama = $_POST['nama_produk2'];
			$gambar = $_POST['gambar2'];
			
			$arrChar = array("/","-","'","%");
			
			$query = $this->M_public_cart->get_produk_id($id);
			
			//echo $query->result();
			
			// Check if a row has been found
			if($query->num_rows > 0){
			
				foreach ($query->result() as $row)
				{
					$nama = str_replace($arrChar,"",$nama);
					
					$data = array(
						'id'      => $id,
						'qty'     => $cty,
						'price'   => $harga,
						'name'    => substr($nama,0,20),
						'satuan'  => $satuan,
						'gambar'  => $gambar
					);

					$this->cart->insert($data);
				}
			} else{
				$this->load->view('front/home.html');
				return;
			}
			
			redirect('keranjang-belanja');
			
		}
		
		function update_cart(){
			$total = $this->cart->total_items();
			$item = $this->input->post('rowid');
			$qty = $this->input->post('qty');

			for($i=0;$i < $total;$i++)
			{
				$data = array(
				   'rowid' => $item[$i],
				   'qty'   => $qty[$i]
				);
				
				$this->cart->update($data);
			}
			redirect('keranjang-belanja');
		}
		
		function hitung_total()
		{
			$total = $_POST['total'];
			$ongkir = $_POST['ongkir'];
			$diskon = $_POST['diskon'];
			
			$total_akhir  = $total+$ongkir-$diskon;
			
			echo number_format($total_akhir);
			
		}
		
		function show_cart() {
			$this->load->view('list_cart');
		}
		
		function empty_cart() {
			$this->cart->destroy();
			redirect('keranjang-belanja');
		}
		
		function hapus_item()
		{
			$item = $this->input->post('rowid');
			$this->cart->remove($item);
			
			redirect('keranjang-belanja');
		}
		
		function total_cart() {
			$data['total'] = $this->cart->total_items();
			$this->load->view('total',$data);
		}
		
		
		function cekout()
		{
			if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
			{
				$data = array('is_login'=> false );
				
				$this->load->view('front/page/shop_cekout.html',$data);
			}
			else 
			{
				//echo "SUKSES";
				$total = $this->input->post('total_out');
				$diskon = $this->input->post('diskon_out');
				$ongkir = 0;
				$total_akhir = $total+$ongkir-$diskon;
				$id_costumer = $this->session->userdata('ses_public_id_user');
				$list_alamat = $this->M_public_cart->list_alamat($id_costumer);
				$data = array('is_login'=> true,'total'=>$total,'diskon'=>$diskon,'ongkir'=>$ongkir,
							  'total_akhir'=>$total_akhir,'list_alamat'=>$list_alamat);
				$this->load->view('front/page/shop_cekout.html',$data);
			}
		}
		
		function cekout_selesai()
		{
			if($this->cart->contents())
			{
				$no_faktur = $this->M_public_cart->get_no_faktur()->no_faktur;
				$id_costumer = $this->session->userdata('ses_public_id_user');
				$biaya = $_POST['ongkir'];
				$diskon = $_POST['diskon'];
				$bayar = $_POST['total_akhir'];
				$metode = $_POST['metode'];
				$id_alamat = $_POST['alamat'];
				$ket = '';//$_POST['ket'];
				
				$this->M_public_cart->simpan_h_pesanan(
					$id_costumer,$no_faktur,$biaya,$diskon,$bayar,$ket,$metode,$id_alamat
				);
				
				$id_h_penjualan = $this->M_public_cart->get_no_h_penjualan($id_costumer,$no_faktur)->id_h_penjualan;
				
				
				
				foreach($this->cart->contents() as $items)
				{
					$satuan_jual = $items['satuan'];
					$id_produk = $items['id'];
					
					$d_produk = $this->M_public_cart->get_harga_produk($id_produk,$satuan_jual);
					$status = $d_produk->status_konversi;
					$besar_konversi = $d_produk->besar_konversi;
					$harga = $items['price'];
					$jumlah_konversi = $items['qty'] * $besar_konversi;
					$diskon = 0;
					$harga_konversi = $harga * $besar_konversi;
					$ket = '';
					
					$this->M_public_cart->simpan_d_pesanan(
						$id_h_penjualan,$id_produk,$items['qty'],$status,$besar_konversi,$satuan_jual,
						$jumlah_konversi,$diskon,$harga,$harga_konversi,$harga,$ket
					);
					
				}
				
				
				$this->cart->destroy();
			} 
			
			$data = array('nama'=>$this->session->userdata('ses_public_nama_member'));
			$this->load->view('front/page/shop_cekout_final.html',$data);
		
			
		}
		
		
		//Sintak Untuk Menimpan ke database
		function pesanSekarang() {
			$this->form_validation->set_rules('IDpesanan[]', 'kode_pesanan', 'required|trim|xss_clean');
			$this->form_validation->set_rules('qty[]', 'qty', 'required|trim|xss_clean');
			$this->form_validation->set_rules('produk[]', 'produk', 'required|trim|xss_clean');
			$this->form_validation->set_rules('harga_satuan[]', 'hrg_satuan', 'required|trim|xss_clean');
			
			if ($this->form_validation->run() == FALSE){
				echo validation_errors(); // tampilkan apabila ada error
			}else{
				
				$kp = $this->input->post('IDpesanan');
				$tg = date('Y-m-d H-i-s');
				$result = array();
				foreach($kp AS $key => $val){
					$result[] = array(
						"kode_pesanan" 	=> $_POST['IDpesanan'][$key],
						"qty"          	=> $_POST['qty'][$key],
						"produk"       	=> $_POST['produk'][$key],
						"hrg_satuan"    => $_POST['harga_satuan'][$key],
						"tgl" 			=> $tg,
						"status" 		=> 'Baru'
					);
				}            
				
				$res = $this->db->insert_batch('pesanan', $result); // fungsi dari codeigniter untuk menyimpan multi array
				
				if($res){
					echo "Barang Sudah Dipesan";
					redirect('cart');
				}else{
					echo "gagal di input";
				}
			}
		}
		
	}

?>