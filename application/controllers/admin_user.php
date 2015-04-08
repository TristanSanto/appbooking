<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_user extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->library('session');  
        $this->load->library('encrypt');            
               
        $this->load->helper(array('qt', 'url')); 
        
        $this->load->model('Users_model');           
          
        if (!$this->session->userdata('user')) {
            redirect('/login');
        }        
    }     
    
    public function fetch() {
        $id = filter_input(INPUT_GET, 'id');  
                
        $data = $this->Users_model->fetch($id);              
        
        $this->_flush($data);    
    }       
        
    public function update() {
        $data = json_decode(file_get_contents("php://input"), true);    
                   
        $data['user']['password'] = $this->encrypt->sha1($data['user']['password']);
        
        $this->Users_model->update($data['user']);  
                         
        $this->_flush('ok');        
    }      
            
    private function _flush($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));             
    }
}