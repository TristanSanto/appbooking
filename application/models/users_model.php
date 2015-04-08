<?php

class Users_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();     
    }
    
    function fetch($id) {
        $query = $this->db->get_where('users', array('id' => $id), 1);
        
        return $query->row_array();
    }
    
    function auth($email, $password) {
        $this->db->select('id, email');

        $query = $this->db->get_where('users', array('email' => $email, 'password' => $password), 1);
        
        return $query->row();
    }
    
    function update($data) { 
        $this->db->where('id', $data['id']);
        
        return $this->db->update('users', $data);
    }     
}
