<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Admin_crenel extends CI_Controller {

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

    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        $data['postpone_date'] = $data['date'];
        $data['postpone_time_begin'] = $data['time_begin'];
        $data['postpone_time_end'] = $data['time_end'];

        $this->Crenels_model->insert($data);

        $this->_flush($data);
    }

    public function update() {
        $data = json_decode(file_get_contents("php://input"), true);

        $this->Crenels_model->update($data);

        // send mail to participants
        if ('3' === $data['status']) {
            $subscriptions = $this->Subscriptions_model->get_subscriptions($data['id']);

            foreach ($subscriptions as $subscription) {
                $subject = '[AppBooking] Event ' . $data['name'] . ' cancelled';

                $message = 'Dear ' . ucwords($subscription['firstname']) . ',<br><br>';
                $message .= 'The event <b>' . $data['name'] . '</b> has been cancelled.<br><br>';
                $message .= $data['reason'] . '<br><br>';
                $message .= 'Best regards,<br>';
                $message .= 'Your webmaster<br><br>';
                $message .= "NB: Please don't reply to this automatic email.";

                sendmail($this->email, $subscription['email'], $subject, $message, $this->config->item('email_administrator'));
            }
        }

        $this->_flush($data);
    }

    public function duplicate() {
        $data = json_decode(file_get_contents("php://input"), true);

        $data['id'] = null;
        $data['places_subscribed'] = 0;

        $this->Crenels_model->insert($data);

        $this->_flush($data);
    }

    public function delete() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $reason = filter_input(INPUT_GET, 'reason');

        // send mail to participants
        if (!empty($reason)) {
            $subscriptions = $this->Subscriptions_model->get_subscriptions($id);

            foreach ($subscriptions as $subscription) {
                $subject = '[AppBooking] Event ' . $data['name'] . ' has been deleted';

                $message = 'Dear ' . ucwords($subscription['firstname']) . ',<br><br>';
                $message .= 'The event <b>' . $data['name'] . '</b> has been deleted.<br><br>';
                $message .= $reason . '<br><br>';
                $message .= 'Best regards,<br>';
                $message .= 'Your webmaster<br><br>';
                $message .= "NB: Please don't reply to this automatic email.";

                sendmail($this->email, $subscription['email'], $subject, $message, $this->config->item('email_administrator'));
            }
        }

        $this->Crenels_model->delete_by_postpone_id($id);
        $this->Crenels_model->delete($id);

        $this->_flush($id);
    }

    public function postpone() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (null !== $data['postpone_id']) {
            $this->Crenels_model->update($data);
            return;
        }

        // send mail to participants
        $subscriptions = $this->Subscriptions_model->get_subscriptions($data['id']);

        foreach ($subscriptions as $subscription) {
            $subject = '[AppBooking] Event ' . $data['name'] . ' postponed to ' . $data['date'];

            $message = 'Dear ' . ucwords($subscription['firstname']) . ',<br><br>';
            $message .= 'The event <b>' . $data['name'] . '</b> has been postponed to ' . $data['date'] . ' at ' . $data['time_begin'] . '<br><br>';
            $message .= $data['reason'] . '<br><br>';
            $message .= 'Best regards,<br>';
            $message .= 'Your webmaster<br><br>';
            $message .= "NB: Please don't reply to this automatic email.";

            sendmail($this->email, $subscription['email'], $subject, $message, $this->config->item('email_administrator'));
        }

        $this->Crenels_model->delete_by_postpone_id($data['id']);

        $data['postpone_id'] = $data['id'];
        $data['date'] = $data['postpone_date'];
        $data['time_begin'] = $data['postpone_time_begin'];
        $data['time_end'] = $data['postpone_time_end'];

        $data['postpone_date'] = null;
        $data['postpone_time_begin'] = null;
        $data['postpone_time_end'] = null;

        unset($data['id']);

        $this->Crenels_model->insert($data);
    }

    public function export($crenel_id, $delimiter = 'semicolon') {
        if (!is_numeric($crenel_id)) {
            $this->output->set_header("HTTP/1.1 406 Not Acceptable");
        }

        $crenel = $this->Crenels_model->fetch($crenel_id);

        if (!$crenel) {
            $this->output->set_header("HTTP/1.1 404 Not Found");
        }

        $this->output->set_header("Content-Type: application/csv; charset=utf-8");
        $this->output->set_header("Content-Disposition: attachment;filename=subscriptions_crenel_{$crenel_id}.csv");

        switch ($delimiter) {
            case 'coma':
                $delimiter = ',';
                break;
            default:
                $delimiter = ';';
        }

        ob_start();

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'Nom',
            utf8_decode('Prénom'),
            'Email',
            'Anniversaire'
        ), $delimiter);

        $subscriptions = $this->Subscriptions_model->get_subscriptions($crenel_id);

        foreach ($subscriptions as $subscription) {
            $subscriptions_family = $this->Subscriptions_family_model->fetch_by_subscription_id($subscription['id']);

            $lastname = utf8_decode($subscription['lastname']);
            $firstname = utf8_decode($subscription['firstname']);

            fputcsv($output, array(
                $lastname,
                $firstname,
                $subscription['email']
            ), $delimiter);

            if ($subscriptions_family && $subscriptions_family['with_someone']) {
                fputcsv($output, array(
                    $lastname,
                    $firstname,
                    'Conjoint(e)',
                    ''
                ), $delimiter);
            }

            if (!$subscriptions_family) {
                continue;
            }

            if (!empty($subscriptions_family['child_firstname1'])) {
                fputcsv($output, array(
                    $lastname,
                    utf8_decode($subscriptions_family['child_firstname1']),
                    '',
                    utf8_decode($subscriptions_family['child_birthday1'])
                ), $delimiter);
            }

            if ($subscriptions_family['child_firstname2']) {
                fputcsv($output, array(
                    $lastname,
                    utf8_decode($subscriptions_family['child_firstname2']),
                    '',
                    utf8_decode($subscriptions_family['child_birthday2'])
                ), $delimiter);
            }

            if ($subscriptions_family['child_firstname3']) {
                fputcsv($output, array(
                    $lastname,
                    utf8_decode($subscriptions_family['child_firstname3']),
                    '',
                    utf8_decode($subscriptions_family['child_birthday3'])
                ), $delimiter);
            }

            if ($subscriptions_family['child_firstname4']) {
                fputcsv($output, array(
                    $lastname,
                    utf8_decode($subscriptions_family['child_firstname4']),
                    '',
                    utf8_decode($subscriptions_family['child_birthday4'])
                ), $delimiter);
            }
        }

        fclose($output);
    }

    private function _flush($data) {
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($data));
    }
}