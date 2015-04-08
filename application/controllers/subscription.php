<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Subscription extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->helper(array('qt'));    
        $this->load->library('email');        
        $this->load->model('Subscriptions_model');          
        $this->load->model('Subscriptions_family_model');   
        $this->load->model('Crenels_model');           
        $this->load->model('Users_model');           
    }

    public function fetch_subscriptions() {
        $id = filter_input(INPUT_GET, 'id');  
        $status = filter_input(INPUT_GET, 'status');
                
        $data = $this->Subscriptions_model->get_subscriptions($id);
        
        if (null !== $status) {
            $data = array_merge($data, $this->Subscriptions_model->get_subscriptions($id, $status));
        }        

        $this->_flush($data);    
    }
    
    public function signup() {          
        $data = json_decode(file_get_contents("php://input"), true);  
        $data['lastname'] = strtoupper($data['lastname']);
        $data['firstname'] = ucwords($data['firstname']);
        $data['status'] = 1; 
        
        $data_family = $data['family'];
        unset($data['family']);
                       
        $crenel = $this->Crenels_model->fetch($data['crenel_id']); 
        $subscription = $this->Subscriptions_model->fetch_subscription_by_crenel_email($data);
        $user = $this->Users_model->fetch(1);             
                                
        if ($crenel['places_subscribed'] >= $crenel['places']) {
            $data['error'] = "Sorry but there is no place left for this event.";
        }        
        else if (empty($subscription) || ('1' !== $subscription['status'] && '3' !== $subscription['status'] && '4' !== $subscription['status'])) {                 
            if (!empty($subscription)) {
                $this->Subscriptions_model->delete($subscription['id']);
            }  

            $this->Subscriptions_model->insert($data);                 
            $subscription_id = $this->db->insert_id();  
                               
            // Update place available
            $subscription_places = 1;    
            
            // Family event            
            if ('1' === $crenel['type']) {     
                if ($data_family['with_someone']) {
                    $subscription_places++;
                }
                
                if ($data_family['child_firstname1']) {
                    $subscription_places++;
                }   
                
                if ($data_family['child_firstname2']) {
                    $subscription_places++;
                }     
                
                if ($data_family['child_firstname3']) {
                    $subscription_places++;
                }   
                
                if ($data_family['child_firstname4']) {
                    $subscription_places++;
                }                
                
                $data_family['subscription_id'] = $subscription_id;                                        
                $this->Subscriptions_family_model->insert($data_family);
            }

            $crenel['places_subscribed'] = $crenel['places_subscribed'] + $subscription_places;                
            $this->Crenels_model->update($crenel);            
            
            // Send notification
            $subject = '[AppBooking] Subscription notification for ' . $crenel['name'] . ' at ' . $crenel['date'];  
            $message = 'Dear Webmaster,<br><br>';
            $message .= $data['firstname'] . ' ' . $data['lastname'] . ' has validated his/her subscription for :<br>';
            $message .= $crenel['name'] . ' at ' . $crenel['date'] . '<br><br>';
            $message .= 'Best regards,<br>';
            $message .= 'Your AppBooking System Agent<br><br>';            
            $message .= "NB: Please don't reply to this automatic email.";

            sendmail($this->email, $user['email'], $subject, $message, $this->config->item('email_administrator'));      
            
            $data['error'] = "Your demand of signup has been registered successfully.";
        } 
        else if ('1' === $subscription['status']) {
            $data['error'] = "Oups, you are already subscribed to this event.";            
        }
        else {
            $data['error'] = "Sorry but you can't subscribe to this event anymore.\nPlease contact the webmaster to know the reasons.";
        }
        
        $this->_flush($data);              
    }
    
    public function withdraw() {
        $data = json_decode(file_get_contents("php://input"), true);  
        $subscription = $this->Subscriptions_model->fetch_subscription_by_crenel_email($data);
                               
        if (!empty($subscription)) {           
            $crenel = $this->Crenels_model->fetch($data['crenel_id']);
            
            $data['id'] = $subscription['id'];
            $data['token'] = $this->_generate_token('withdraw', $data);                                  
            $this->Subscriptions_model->update($data);  
                        
            // send mail for validation
            $url = $this->config->item('subscription_url') . 'withdraw_validation?token=' . $data['token'];
            $subject = '[AppBooking] Validate your unsubscription from ' . $crenel['name'] . ' at ' . $crenel['date'];            
            
            $message = 'Dear ' . ucwords($subscription['firstname']) . ',<br><br>';
            $message .= 'Your unsubscription will be definitely validated by clicking on the following link :<br>';
            $message .= '<a href="' . $url . '">' . $url . '</a><br><br>';
            $message .= 'Best regards,<br>';
            $message .= 'Your webmaster<br><br>';
            $message .= "NB: Please don't reply to this automatic email.";
            
            sendmail($this->email, $data['email'], $subject, $message, $this->config->item('email_administrator'));         
        }
          
        $this->_flush($data);        
    } 
    
    public function withdraw_validation() {
        $token = filter_input(INPUT_GET, 'token');                     
        $subscription = $this->Subscriptions_model->fetch_subscription_by_token($token);       
        $user = $this->Users_model->fetch(1);        
        
        if (!empty($subscription) && '1' === $subscription['status']) {
            $crenel = $this->Crenels_model->fetch($subscription['crenel_id']);
            
            // Update place available
            $subscription_places = 1;
            
            // Family event            
            if ('1' === $crenel['type']) {     
               $subscription_family = $this->Subscriptions_family_model->fetch_by_subscription_id($subscription['id']);
                                           
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
            
            $subscription['token'] = null;
            $subscription['status'] = 2;
            
            $this->Subscriptions_model->update($subscription);     
            
            $subject = '[AppBooking] Unsubscription notification for ' . $crenel['name'] . ' at ' . $crenel['date'];  
            $message = 'Dear Webmaster,<br><br>';
            $message .= $subscription['firstname'] . ' ' . $subscription['lastname'] . ' has unsubscribed himsef/herself from :<br>';
            $message .= $crenel['name'] . ' at ' . $crenel['date'] . '<br><br>';
            $message .= 'Given reason: ' . $subscription['reason'] . '<br><br>';
            $message .= 'Best regards,<br>';
            $message .= 'Your AppBooking System Agent<br><br>';            
            $message .= "NB: Please don't reply to this automatic email.";
            
            sendmail($this->email, $user['email'], $subject, $message, $this->config->item('email_administrator'));            
            
            $this->load->view('emails/withdraw_validation_success');  
        } else {
            $this->load->view('emails/withdraw_validation_error');          
        }       
    }
    
    private function _generate_token($type, $data) {
        return sha1($type . $this->config->item('encryption_key') . $data['email'] . $data['crenel_id']);
    }
      
    private function _flush($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));             
    }
}