<?php
class Autentifikasi extends CI_Controller
{
  public function index()
  {
    //jika statusnya sudah login, maka tidak bisa mengakses halaman login alias dikembalikan ke tampilan user
    if ($this->session->userdata('email'))
    {
      redirect('admin');
    }

    $this->form_validation->set_rules('email', 'Alamat Email', 'required|trim|valid_email',
    [
      'required' => 'Email Harus Diisi!',
      'valid_email' => 'Email Anda Salah!!'
    ]);

    $this->form_validation->set_rules('password', 'password', 'required|trim',
    [
      'required' => 'Password Harus Diisi!',
    ]);

    if ($this->form_validation->run() == false) {
        $data['judul'] = 'Login';
        $data['user'] = '';
        //kata 'login' merupakan nilai dari variabel judul dalam array $data dikirimkan ke view aute_header
        $this->load->view('templates/aute_header', $data);
        $this->load->view('autentifikasi/login');
        $this->load->view('templates/aute_footer');
    } else {
        $this->_login();
        }
    }

    private function _login()
    {
      $email = htmlspecialchars($this->input->post('email', true));
      $password = $this->input->post('password', true);
      $user = $this->ModelUser->cekData(['email' => $email])->row_array();
      
      //jika usernya ada
      if ($user) {
        //jika user sudah aktif
        if ($user['is_active'] == 1) {
          //cek password
          if (password_verify($password, $user['password'])) {
            $data = [
              'email' => $user['email'],
              'role_id' => $user['role_id']
            ];
            $this->session->set_userdata($data);
            redirect('admin');
          } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">LOGIN GAGAL!</div>');
            redirect('autentifikasi');
          }
        } else {
          $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">User belum diaktifasi!!</div>');
          redirect('autentifikasi');
        }
      } else {
        $this->session->set_flashdata('pesan', '<div class="alert alert-danger alert-message" role="alert">Email tidak terdaftar!!</div>');
        redirect('autentifikasi');
      }
    }
    
    public function blok()
    {
        $this->load->view('autentifikasi/blok');
    }
    public function gagal()
    {
        $this->load->view('autentifikasi/gagal');    
    }

    

  public function logout()
  {
    $this->session->unset_userdata('email');
    $this->session->unset_userdata('role_id');
    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">You has been logot </div>
    ');
    redirect('autentifikasi');
  }
}
