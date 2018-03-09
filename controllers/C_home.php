<?php

	class C_home extends CI_Controller
	{
		public function __construct()
		{
			parent::__construct();
			$this->load->model(array('M_home','M_produk','M_satuan','M_public_get'));
			$this->load->library(array('cart')); 
			
		}
	
		public function index()
		{
			
			//informasi login
			if(($this->session->userdata('ses_public_user') == null) or ($this->session->userdata('ses_public_pass') == null))
			{
				$is_login=false;
				$nama_member = '';
			} else {
				$is_login=true;
				$nama_member = $this->session->userdata('ses_public_user');
			}
			
			/*if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
			{
				$cari = "WHERE id_kat_produk like '%%' AND nama_produk like '%".$_GET['cari']."%'";
			} else {
				$cari = "WHERE id_kat_produk like '%%'";
			}
			
			$this->load->library('pagination');
			$config['first_url'] = site_url('admin-jabatan?'.http_build_query($_GET));
			$config['base_url'] = site_url('admin-jabatan/');
			$config['total_rows'] = $this->M_home->count_produk_limit($cari)->JUMLAH;
			$config['uri_segment'] = 2;	
			$config['per_page'] = 10;
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
			*/
			$list_kategori = $this->M_home->list_kategori();
			$list_produk = $this->M_home->list_produk('',0,20);
			//$count_produk = $this->M_home->count_produk_limit('')->JUMLAH;
			
			$list_cart = $this->cart->contents();
			$total_item = $this->cart->total_items();
			$total_price = $this->cart->total();
			
			$data = array('list_kategori' => $list_kategori, 'page' => 'home','list_produk' => $list_produk,'aktif_item' => 'all',
						  'list_cart' => $list_cart, 'total_item' => $total_item,'total_price'=>$total_price,'isCart' => '0',
						  'is_login'=>$is_login,'nama_member'=>$nama_member
							
						);
			
			$this->load->view('front/home.html',$data);
		}

		
		public function produk()
		{
			$id_kat_produk = $this->uri->segment(2,0);
			$list_cart = $this->cart->contents();
			$total_item = $this->cart->total_items();
			$total_price = $this->cart->total();
			
			if($id_kat_produk == "all") 
			{
				$id_kat_produk = '';
				$aktif_item = 'all';
			} else {
				$aktif_item = $id_kat_produk;
			}
			$list_kategori = $this->M_home->list_kategori();
			$list_produk = $this->M_home->list_produk($id_kat_produk,0,10);
			//$count_produk = $this->M_home->count_produk_limit($id_kat_produk)->JUMLAH;
			
			$data = array('list_kategori' => $list_kategori, 'page' => 'home','list_produk' => $list_produk, 'aktif_item' => $aktif_item,
  						  'list_cart' => $list_cart, 'total_item' => $total_item,'total_price'=>$total_price,'isCart' => '0'
						);
			
			$this->load->view('front/home.html',$data);
		}
		
		public function detail_produk()
		{
			$id_produk = $this->uri->segment(2,0);
			$detail_produk = $this->M_home->detail_produk($id_produk);
			$detail_images = $this->M_home->detail_images_produk($id_produk);
			$list_kategori = $this->M_home->list_kategori();
			$kat_produk = $this->M_produk->get_kat_produk_by($id_produk)->id_kat_produk;
			$list_produk = $this->M_home->list_produk($kat_produk,0,10);
			$list_satuan = $this->M_satuan->list_satuan_by_produk($id_produk);
			
			$list_cart = $this->cart->contents();
			$total_item = $this->cart->total_items();
			$total_price = $this->cart->total();
			
			$data = array('list_kategori' => $list_kategori,'aktif_item' => $kat_produk,'detail_produk'=>$detail_produk,
						  'detail_images'=> $detail_images,'related_produk'=>$list_produk,'list_satuan'=>$list_satuan,
						  'list_cart' => $list_cart, 'total_item' => $total_item,'total_price'=>$total_price,'isCart' => '0'
						  );
			$this->load->view('front/page/detail_produk.html',$data);
			
			//echo $id_produk;
		}
		
		public function get_harga_satuan()
		{
			$hasil_cek = $this->M_public_get->get_satuan_harga($_POST['id_produk'],$_POST['kode_satuan']);
			echo $hasil_cek->HARGA_DEFT;
		}
	}
	
?>