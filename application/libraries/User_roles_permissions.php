<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

class User_roles_permissions {

    protected $CI;
    
    function __construct() {
        $this->CI = & get_instance();
        // Load necessary models or libraries
        $this->CI->load->model('Mdmaster');
        $this->CI->load->model('User_model');
    }

    public function assignModulesToUser($role_id) {
        $arr_module_permissions = $this->CI->User_model->get_superadmin_module_permissions();
        //echo "<br> str ".$this->CI->db->last_query();exit;

        //Delete from tbl_role_permissions
        $this->CI->db->where('role_id', $role_id);
        $this->CI->db->delete('tbl_role_permissions');

        if($arr_module_permissions != false) {
            foreach($arr_module_permissions as $rowData) {
                // Create a record in tbl_role_permissions
                $arr_role_permissions = array(
                    'role_id'        => $role_id,
                    'permission_id'  => $rowData['id'],  
                );
                $this->CI->Mdmaster->addRecord($arr_role_permissions, 'tbl_role_permissions');
            }
        }
    }
}
