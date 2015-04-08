<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
                
        $this->load->library('form_validation');   
        
        $this->load->helper(array('qt'));             
    }

    public function index() {                
        $this->load->view('home');     
    }            
}