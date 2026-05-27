<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model{
    
    public function __construct() {
        parent::__construct();
    }    
    
	public function login($data){

		$this->db->from('tbl_admin_user');
		$this->db->join('tbl_roles', 'tbl_roles.id = tbl_admin_user.role_id');
		$this->db->where('tbl_admin_user.email', $data['email']);
		
		$query = $this->db->get();
		if ($query->num_rows() == 0){
			return false;
		}
		else{
			//Compare the password attempt with the password we have stored.
			$result = $query->row_array();
		    $validPassword = password_verify($data['password'], $result['password']);
		    if($validPassword){
		        return $result = $query->row_array();
		    }
		}
	}

}