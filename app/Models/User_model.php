<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function register($data) {
        return $this->db->insert('users', $data);
    }

    public function check_login($username, $password) {
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query = $this->db->get('users');
        return $query->row_array();
    }
}
