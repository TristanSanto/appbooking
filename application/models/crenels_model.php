<?php

class Crenels_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_crenels($year, $month) {
        if (!is_numeric($year) || $year < 0) {
           $year = date('Y');
        }
        
        if (!is_numeric($month) || $month < 0) {
            $month = date('m'); 
        }
        
        $begin = $year . '-' . $month . '-01';
        $end = $year . '-' . $month . '-31';
        
        $this->db->order_by('date', 'asc');     
        $this->db->order_by('time_begin', 'asc');         

        $query = $this->db->get_where('crenels', array('date >=' => $begin, 'date <=' => $end));
        
        return $query->result_array();
    }    

    function fetch($id) {
        $query = $this->db->get_where('crenels', array('id' => $id), 1);
        
        return $query->row_array();
    }    

    function insert($data) {
        return $this->db->insert('crenels', $data);
    }
    
    function update($data) {    
        $this->db->where('id', $data['id']);
        
        return $this->db->update('crenels', $data);
    }  
    
    function delete($id) {
        $this->db->where('id', $id);
        
        return $this->db->delete('crenels');
    } 
    
    function delete_by_postpone_id($id) {
        $this->db->where('postpone_id', $id);
        
        return $this->db->delete('crenels');
    }     
                
    function get_status() {
        return array(
            0 => 'Forthcoming',
            1 => 'Ongoing',
            2 => 'Past',
            3 => 'Postponed',          
            4 => 'Canceled',             
        );
    }    
}