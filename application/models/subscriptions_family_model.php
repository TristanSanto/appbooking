<?php

class Subscriptions_family_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    function fetch_by_subscription_id($id) {
        $query = $this->db->get_where('subscriptions_family', array('subscription_id' => $id), 1);
        
        return $query->row_array();
    }       
   
    function fetch($id) {
        $query = $this->db->get_where('subscriptions_family', array('id' => $id), 1);
        
        return $query->row_array();
    }            

    function insert($data) {
        return $this->db->insert('subscriptions_family', $data);
    }
    
    function update($data) {    
        $this->db->where('id', $data['id']);
        
        return $this->db->update('subscriptions_family', $data);
    }  
    
    function delete($id) {
        $this->db->where('id', $id);
        
        return $this->db->delete('subscriptions_family');
    }  
}
