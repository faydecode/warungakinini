<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_admin_images extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_images','M_produk'));
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
				$id_produk = $this->uri->segment(2,0);
				
				if((!empty($_GET['cari'])) && ($_GET['cari']!= "")  )
				{
					$cari = "AND kode_kantor = '".$this->session->userdata('ses_kode_kantor')."' AND img_nama LIKE '%".str_replace("'","",$_GET['cari'])."%'";
				}
				else
				{
					$cari = "AND kode_kantor = '".$this->session->userdata('ses_kode_kantor')."'";
				}
				
				$this->load->library('pagination');
				$config['first_url'] = site_url('admin-produk?'.http_build_query($_GET));
				$config['base_url'] = site_url('admin-produk/');
				$config['total_rows'] = $this->M_images->count_images_limit($id_produk,'produks',$cari)->JUMLAH;
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
				$list_image = $this->M_images->list_images_limit($id_produk,'produks',$cari,$config['per_page'],$this->uri->segment(3,0));
				$data = array('page_content'=> 'admin_produk_gambar','id_produk' => $id_produk,'list_image'=>$list_image
						      ,'halaman' => $halaman 
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
		$id_produk = $_POST['id_produk'];
		if (!empty($_POST['stat_edit']))
		{
			
			$this->do_upload($_FILES['foto']['name'],$_POST['stat_edit']);
			$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
			$foto = $_POST['stat_edit'].".".$ext;
			
			$this->M_images->edit_with_image(
				$_POST['stat_edit']
				,$_POST['id_produk']
				,'produks'
				,$_POST['nama']
				,$foto
				,base_url().'assets/global/produk/'.$foto
				,$_POST['keterangan']
				,$this->session->userdata('ses_id_karyawan')
				,$this->session->userdata('ses_kode_kantor')
			);
		}
		else
		{
			$get_no = $this->M_images->get_id_images($this->session->userdata('ses_kode_kantor'));
			$this->do_upload($_FILES['foto']['name'],$get_no->id_images);
			$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
			$foto = $get_no->id_images.".".$ext;
			
			$this->M_images->simpan(
				$_POST['id_produk']
				,'produks'
				,$_POST['nama']
				,$foto
				,base_url().'assets/global/produk/'.$foto
				,$_POST['keterangan']
				,$this->session->userdata('ses_id_karyawan')
				,$this->session->userdata('ses_kode_kantor')
			);
		}
		
		header('Location: '.base_url().'admin-produk-gambar/'.$id_produk.'?'.http_build_query($_GET));
	}
	
	public function hapus()
	{
		$id_produk = $this->uri->segment(2,0);
		$id = $this->uri->segment(3,0);
		$hasil_cek = $this->M_images->get_images_id($id);
		$avatar = $hasil_cek->img_file;
		if(!empty($hasil_cek))
		{
			$this->do_upload('',$avatar);
			$this->M_images->hapus($id);
		}
		
		header('Location: '.base_url().'admin-produk-gambar/'.$id_produk.'?'.http_build_query($_GET));
	}
	
	public function get_list_gambar()
	{
		$id = $_POST['id_produk'];
		$cari = "AND kode_kantor = '".$this->session->userdata('ses_kode_kantor')."'";
		
		
		$list_gambar = $this->M_images->list_images_limit($id,'produks',$cari,1000,0);
		
		if(!empty($list_gambar))
		{
			echo'<table width="100%" id="example2" class="table table-bordered table-hover">';
				echo '<thead>
							<tr>';
							echo '<th width="5%">No</th>';
							echo '<th width="10%">Gambar</th>';
							echo '<th width="20%">Nama Gambar</th>';
							echo '<th width="20%">Aksi</th>';
						echo '</tr>
					</thead>';
				$list_result = $list_gambar->result();
				$no=1;
				echo '<tbody>';
				foreach($list_result as $row)
				{
					echo'<tr>';
						echo'<td><input type="hidden" id="no_'.$row->id_images.'" value="'.$row->id_images.'" />'.$no.'</td>';
						if ($row->img_file == "")
						{
							$src = base_url().'assets/global/produk/loading.gif';
							echo '<td><img id="img_'.$row->id_images.'"  width="100px" height="100px" style="border:1px solid #C8C8C8; padding:5px; float:left; margin-right:20px;" src="'.$src.'" /></td>';
							
							echo'<input type="hidden" id="avatar_url_'.$row->id_images.'" value="'.$src.'" />';
						}
						else
						{
							$src = base_url().'assets/global/produk/'.$row->img_file;
							echo '<td><img id="img_'.$row->id_images.'"  width="100px" height="100px" style="border:1px solid #C8C8C8; padding:5px; float:left; margin-right:20px;" src="'.$src.'" /></td>';
							
							echo'<input type="hidden" id="avatar_url_'.$row->id_images.'" value="'.$src.'" />';
						}
						
						echo'<td><input type="hidden" id="nama_gambar_'.$no.'" value="'.$row->img_nama.'" />'.$row->img_nama.'</td>';
						
						echo'<td>
						
						<a href="javascript:void(0)" class="btn" onclick="editGambar('.$no.')"><i class="fa fa-trash-o"></i></a>

						<a href="javascript:void(0)" class="btn" onclick="hapusGambar('.$no.')"><i class="fa fa-trash-o"></i></a>

						</td>';


					echo'</tr>';
					$no++;
				
				}
				
				echo '</tbody>';
			echo'</table>';
			
			return true;
		}
		else
		{
			echo'<center>';
			echo'Tidak Ada Data Yang Ditampilkan !!';
			echo'</center>';
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
			$config['file_name']	= $cek_bfr;
			$config['overwrite']	= true;
			

			$this->upload->initialize($config);

			//Upload file 1
			if ($this->upload->do_upload('foto'))
			{
				$hasil = $this->upload->data();
			}
		}
	}
	
	
}