<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->library('session');  
        $this->load->library('encrypt');            
        $this->load->library('form_validation');   
        
        $this->load->helper(array('qt', 'array_column', 'form', 'url'));             
        
        if (!$this->session->userdata('user')) {
            redirect('/login');
        }
    }

    public function index() {                 
        $this->load->view('admin');     
    }
}