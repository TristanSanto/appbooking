<?php

class Subscriptions_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_subscriptions($crenel_id, $status = 1) {   
        $this->db->order_by('status', 'desc');  

        $query = $this->db->get_where('subscriptions', array('crenel_id' => $crenel_id, 'status' => $status));
        
        return $query->result_array();
    }   
   
    function fetch_subscription_by_crenel_email($data) {
        $query = $this->db->get_where('subscriptions', array('crenel_id' => $data['crenel_id'], 'email' => $data['email']), 1);
        
        return $query->row_array();
    } 
    
    function fetch_subscription_by_token($token) {
        $query = $this->db->get_where('subscriptions', array('token' => $token), 1);
        
        return $query->row_array();
    }     

    function fetch($id) {
        $query = $this->db->get_where('subscriptions', array('id' => $id), 1);
        
        return $query->row_array();
    }            

    function insert($data) {
        return $this->db->insert('subscriptions', $data);
    }
    
    function update($data) {    
        $this->db->where('id', $data['id']);
        
        return $this->db->update('subscriptions', $data);
    }  
    
    function delete($id) {
        $this->db->where('id', $id);
        
        return $this->db->delete('subscriptions');
    } 
                
    function get_status() {
        return array(
            0 => 'Pending',
            1 => 'Attending',
            2 => 'Unsubscribing',
            3 => 'Unsubscribing by Manager',          
        );
    }    
}
