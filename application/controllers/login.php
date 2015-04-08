<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->library('session');
        $this->load->library('encrypt');            
        $this->load->library('form_validation');   
        
        $this->load->helper(array('qt', 'form', 'url'));                        
    }

    public function index() {
        if ($this->session->userdata('user')) {
            redirect('/admin');
        }
        
        $this->load->view('login');
    }
    
    public function submit() {                
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
                
        if (!(empty($email) || empty($password))) {                
            $this->load->model('Users_model');    
            
            $password = $this->encrypt->sha1($password);
            $user = $this->Users_model->auth($email, $password);
            
            if (!empty($user)) {
                $this->session->set_userdata('user', $user);
                redirect('/admin', 'refresh'); 
            }
        }
        
        redirect('/login'); 
    }
    
    public function logout() {
        $this->session->sess_destroy();
        
        redirect('/home'); 
    }
}