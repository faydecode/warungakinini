<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_produk extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		// Your own constructor code
		$this->load->model(array('M_produk','M_kat_produk','M_satuan'));
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
				}
				else
				{
					$cari = "WHERE A.kode_kantor = '".$this->session->userdata('ses_kode_kantor')."'";
				}
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-produk?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-produk/');
				$config['total_rows'] = $this->M_produk->count_produk_limit($cari)->JUMLAH;
				$config['uri_segment'] = 2;	
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
				//$kode_produk = $this->M_akun->get_no_karyawan()->no_karyawan;
				$list_kat_produk = $this->M_kat_produk->list_kat_produk();
				$list_satuan = $this->M_satuan->list_satuan();
				$list_produk = $this->M_produk->list_produk_limit($cari,$config['per_page'],$this->uri->segment(2,0));
				$data = array('page_content'=>'admin_produk','halaman'=>$halaman,'list_produk'=>$list_produk,'list_kat_produk'=>$list_kat_produk,'list_satuan'=>$list_satuan);
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
		$kode_produk = $_POST['kode'];
		
		if (!empty($_POST['stat_edit']))
		{
			//if (empty($_FILES['foto']['name']))
			//{
				$this->M_produk->edit_no_image
				(
					$_POST['stat_edit']
					,$_POST['kat_produk']
					,$_POST['satuan']
					,$kode_produk
					,$_POST['nama']
					,$_POST['charge']
					,$_POST['op_charge']
					,$_POST['charge_beli']
					,$_POST['op_beli']
					,$_POST['min_stok']
					,$_POST['max_stok']
					,$_POST['spek']
					,$_POST['keterangan']
					,$this->session->userdata('ses_id_karyawan')
				);
			//}
			/* else
			{
				$data_produk = $this->M_produk->get_produk_id($_POST['stat_edit']);
				$this->do_upload($_FILES['foto']['name'],$data_produk->avatar);
				$foto = $_FILES['foto']['name'];
				$this->M_produk->edit_with_image
				(
					$_POST['stat_edit']
					,$_POST['kat_produk']
					,$_POST['satuan']
					,$kode_produk
					,$_POST['nama']
					,$_POST['spek']
					,$foto
					,base_url().'assets/global/produk/'.$foto
					,$_POST['keterangan']
					,$this->session->userdata('ses_id_karyawan')
				);
			} */
			
			header('Location: '.base_url().'admin-produk?'.http_build_query($_GET));
		}
		else
		{
			/* if (!empty($_FILES['foto']['name']))
			{
				$this->do_upload($_FILES['foto']['name'],'');
				$foto = $_FILES['foto']['name'];
			}
			else
			{
				$foto = 'noimage.gif';
			} */
			$this->M_produk->simpan
			(
				$_POST['kat_produk']
				,$_POST['satuan']
				,$kode_produk
				,$_POST['nama']
				,$_POST['charge']
				,$_POST['op_charge']
				,$_POST['charge_beli']
				,$_POST['op_beli']
				,$_POST['min_stok']
				,$_POST['max_stok']
				,$_POST['spek']
				,$_POST['keterangan']
				,$this->session->userdata('ses_kode_kantor')
				,$this->session->userdata('ses_id_karyawan')
			);
			header('Location: '.base_url().'admin-produk?'.http_build_query($_GET));
		}
		
	}
	
	function do_upload($id,$cek_bfr)
	{
		$this->load->library('upload');

		if($cek_bfr != '')
		{
			@unlink('./assets/global/produk/'.$cek_bfr);
		}
		
		if (!empty($_FILES['foto']['name']))
		{
			$config['upload_path'] = 'assets/global/produk/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '2024';
			//$config['max_widtd']  = '300';
			//$config['max_height']  = '300';
			$config['file_name']	= $id;
			$config['overwrite']	= true;
			

			$this->upload->initialize($config);

			//Upload file 1
			if ($this->upload->do_upload('foto'))
			{
				$hasil = $this->upload->data();
			}
		}
	}
	
	function cek_produk()
	{
		$hasil_cek = $this->M_produk->get_produk('kode_produk',$_POST['kode']);
		echo $hasil_cek;
	}
	
	function cek_table_produk()
	{
		if((!empty($_POST['cari'])) && ($_POST['cari']!= "")  )
		{
			$cari = ' WHERE nama_produk LIKE "%'.$_POST['cari'].'%"';
		}
		else
		{
			$cari='';
		}
		
		$list_produk = $this->M_produk->list_produk_harga_limit($cari,10,0);
		if(!empty($list_produk))
		{
			echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
			echo '<thead>
	<tr>';
			echo '<th width="5%">No</th>';
			//echo '<th width="15%">Avatar</th>';
			echo '<th width="20%">Kode</th>';
			echo '<th width="35%">Nama</th>';
			echo '<th width="5%">Aksi</th>';
			echo '</tr>
	</thead>';
			$list_result = $list_produk->result();
			$no =1;
			echo '<tbody>';
			foreach($list_result as $row)
			{
				echo'<tr>';
				echo'<td><input type="hidden" id="no_'.$row->id_produk.'" value="'.$row->id_produk.'" />'.$no.'</td>';
				/*if ($row->avatar == "")
				{
					$src = base_url().'assets/global/karyawan/loading.gif';
					echo '<td><img id="img_'.$row->id_produk.'"  width="75px" height="75px" style="border:1px solid #C8C8C8; padding:5px; float:left; margin-right:20px;" src="'.$src.'" /></td>';
					
					echo'<input type="hidden" id="avatar_url_'.$row->id_produk.'" value="'.$src.'" />';
				}
				else
				{
					$src = base_url().'assets/global/produk/'.$row->avatar;
					echo '<td><img id="img_'.$row->id_produk.'"  width="75px" height="75px" style="border:1px solid #C8C8C8; padding:5px; float:left; margin-right:20px;" src="'.$src.'" /></td>';
					
					echo'<input type="hidden" id="avatar_url_'.$row->id_produk.'" value="'.$src.'" />';
				}
				*/
				echo'<td><input type="hidden" id="kode_'.$row->id_produk.'" value="'.$row->kode_produk.'" />'.$row->kode_produk.'</td>';
				echo'<td><input type="hidden" id="nama_'.$row->id_produk.'" value="'.$row->nama_produk.'" />'.$row->nama_produk.'</td>';
				echo'<input type="hidden" id="harga_'.$row->id_produk.'" value="'.$row->harga.'" />';
				
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
	
	public function hapus()
	{
		$id = $this->uri->segment(2,0);
		$hasil_cek = $this->M_produk->get_produk_id($id);
		$avatar = $hasil_cek->avatar;
		if(!empty($hasil_cek))
		{
			$this->do_upload('',$avatar);
			$this->M_produk->hapus($id);
		}
		header('Location: '.base_url().'admin-produk?'.http_build_query($_GET));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/c_admin_kat_karyawan.php */