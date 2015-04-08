<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_subscription extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->library('session');  
        $this->load->library('encrypt');            
        $this->load->library('form_validation');  
        $this->load->library('email');           
        
        $this->load->helper(array('qt', 'url')); 
        
        $this->load->model('Crenels_model');                                  
        $this->load->model('Subscriptions_model');          
        $this->load->model('Subscriptions_family_model'); 
      
        if (!$this->session->userdata('user')) {
            redirect('/login');
        }        
    }
    
    public function fetch_subscriptions() {
        $id = filter_input(INPUT_GET, 'id');  
                
        $data = $this->Subscriptions_model->get_subscriptions($id);           
        $data = array_merge($data, $this->Subscriptions_model->get_subscriptions($id, 2));  
        $data = array_merge($data, $this->Subscriptions_model->get_subscriptions($id, 0));         
        $data = array_merge($data, $this->Subscriptions_model->get_subscriptions($id, 4));       
        $data = array_merge($data, $this->Subscriptions_model->get_subscriptions($id, 3));      
        
        foreach ($data as $key => $value) {
            $data[$key]['family'] = $this->Subscriptions_family_model->fetch_by_subscription_id($value['id']);             
        }
        
        $this->_flush($data);    
    }    
        
    public function subscribe() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $data['status'] = 1;  
        unset($data['family']);
        
        $this->Subscriptions_model->update($data);  
        
        // mail notification  
        $crenel = $this->Crenels_model->fetch($data['crenel_id']);                   
      
        $crenel['places_subscribed'] = $crenel['places_subscribed'] + 1;
        $this->Crenels_model->update($crenel);
            
        $subject = '[AppBooking] Subscription to event ' . $crenel['name'];             

        $message = 'Dear ' . ucwords($data['firstname']) . ',<br><br>';
        $message .= 'You have been subscribed to the event <b>' . $crenel['name'] . '</b> by the webmaster.<br><br>';
        $message .= 'Please save the following date : ' . $crenel['date'] . ' at ' . $crenel['time_begin'] . '<br><br>';
        $message .= $data['reason'] . '<br><br>';
        $message .= 'Best regards,<br>';
        $message .= 'Your webmaster<br><br>';
        $message .= "NB: Please don't reply to this automatic email.";
             
        sendmail($this->email, $data['email'], $subject, $message, $this->config->item('email_administrator'));  
                              
        $this->_flush($data);        
    }   
    
    public function unsubscribe() {
        $data = json_decode(file_get_contents("php://input"), true);            
        
        $data['status'] = 3;   
        unset($data['family']);
        
        $this->Subscriptions_model->update($data);              

        // mail notification  
        $crenel = $this->Crenels_model->fetch($data['crenel_id']);    
        
        // Update place available
        $subscription_places = 1;             
        
        // Family event            
        if ('1' === $crenel['type']) {     
           $subscription_family = $this->Subscriptions_family_model->fetch_by_subscription_id($data['id']);               

           if ($subscription_family['with_someone']) {
               $subscription_places++;
           }

            if ($subscription_family['child_firstname1']) {
                $subscription_places++;
            }   

            if ($subscription_family['child_firstname2']) {
                $subscription_places++;
            }     

            if ($subscription_family['child_firstname3']) {
                $subscription_places++;
            }   

            if ($subscription_family['child_firstname4']) {
                $subscription_places++;
            } 
            
            $this->Subscriptions_family_model->delete($subscription_family['id']);
        }        
        
        $crenel['places_subscribed'] = max(0, $crenel['places_subscribed'] - $subscription_places);
        $this->Crenels_model->update($crenel);  
        
        $subject = '[AppBooking] Unsubscription from event ' . $crenel['name'];             

        $message = 'Dear ' . ucwords($data['firstname']) . ',<br><br>';
        $message .= 'You have been unsubscribed from the event <b>' . $crenel['name'] . '</b> by the webmaster.<br><br>';
        $message .= 'Note that you couldn\'t sign up to this event again.<br><br>';
        $message .= $data['reason'] . '<br><br>';        
        $message .= 'Best regards,<br>';
        $message .= 'Your webmaster<br><br>';
        $message .= "NB: Please don't reply to this automatic email.";
             
        sendmail($this->email, $data['email'], $subject, $message, $this->config->item('email_administrator'));  
                              
        $this->_flush($data);        
    } 
    
    public function absent() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        $data['status'] = 4;  
        unset($data['family']);
        
        $this->Subscriptions_model->update($data);  
                              
        $this->_flush($data);        
    } 
            
    private function _flush($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));             
    }
}