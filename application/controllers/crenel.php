<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Crenel extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->helper(array('qt'));

        $this->load->model('Crenels_model');
    }

    public function fetch_crenel() {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $data = $this->Crenels_model->fetch($id);
        $data['status'] = (int) $data['status'];
        $data['places'] = (int) $data['places'];

        $this->_flush($data);
    }

    public function fetch_months() {
        $now = new DateTime();
        $start = new DateTime($now->format('Y-m-1'));
        $end = new DateTime(filter_input(INPUT_GET, 'date'));
        $interval = date_diff($start, $end);
        $diff = $interval->format('%m');
        $months_max = ($diff <= 3 ? 3 : $diff);

        $months = array($this->_get_crenels($now->format('Y'), $now->format('m')));

        for ($i = 1; $i < $months_max; $i++) {
            $start->add(new DateInterval('P1M'));
            array_push($months, $this->_get_crenels($start->format('Y'), $start->format('m')));
        }

        $this->_flush($months);
    }

    public function fetch_less() {
        $date = new DateTime(filter_input(INPUT_GET, 'date'));
        $date->sub(new DateInterval('P1M'));

        $this->_flush($this->_get_crenels($date->format('Y'), $date->format('m'), true));
    }

    public function fetch_more() {
        $date = new DateTime(filter_input(INPUT_GET, 'date'));
        $date->add(new DateInterval('P1M'));

        $this->_flush($this->_get_crenels($date->format('Y'), $date->format('m')));
    }

    private function _get_crenels($year, $month, $previous_month = false) {
        $data = array(
            'previous_month' => $previous_month,
            'date' => $year . '-' . $month . '-01',
            'days' =>  array()
        );

        $crenels = $this->Crenels_model->get_crenels($year, $month);

        if (0 === count($crenels)) {
            return $data;
        }

        $days = array(
            'date' => null,
            'crenels' => array()
        );

        foreach ($crenels as $crenel) {
            if (null === $days['date']) {
                $days['date'] = $crenel['date'];
            }
            else if ($days['date'] !== $crenel['date']) {
                array_push($data['days'], $days);

                $days = array(
                    'date' => $crenel['date'],
                    'crenels' => array()
                );
            }

            $crenel['status'] = (int) $crenel['status'];

            array_push($days['crenels'], $crenel);
        }

        array_push($data['days'], $days);

        return $data;
    }

    private function _flush($data) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}