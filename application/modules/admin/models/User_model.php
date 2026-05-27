<?php
class User_model extends CI_Model {

    public function __construct() {
    }

    public function get_user_with_role($email)
    {
        // Perform a join between 'tbl_admin_user' and 'tbl_roles'
        $this->db->select('tbl_admin_user.*, tbl_roles.role_name'); // Specify the columns you want to retrieve
        $this->db->from('tbl_admin_user');
        $this->db->join('tbl_roles', 'tbl_admin_user.role_id = tbl_roles.id', 'left'); // Join condition
        $this->db->where(array(
            'tbl_admin_user.email' => $email,
            'tbl_admin_user.status' => 1
        ));

        // Execute the query
        $query = $this->db->get();

        // Return the result
        return $query->row_array(); // Use row() to get a single row, or result() for multiple rows
    }

    public function get_user($email) {
        $query = $this->db->get_where('tbl_admin_user', array('email' => $email,'status' => 1));
        return $query->row_array();
    }

    public function get_role($role_id) {
        $query = $this->db->get_where('tbl_roles', array('id' => $role_id));
        return $query->row_array();
    }

    public function get_permissions($role_id) {
        $this->db->select('tbl_modules.name as module_name,tbl_modules.slug as module_name_slug,
                            tbl_permissions.id as permission_id,tbl_permissions.module_id,tbl_permissions.name as permission_name,tbl_permissions.text,tbl_roles.role_name');
        $this->db->from('tbl_permissions');
        $this->db->join('tbl_modules', 'tbl_modules.id = tbl_permissions.module_id');
        $this->db->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id');
        $this->db->join('tbl_roles', 'tbl_roles.id = tbl_role_permissions.role_id');
        $this->db->where('tbl_role_permissions.role_id', $role_id);
        $this->db->where('tbl_roles.status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function check_permission($role_id,$permission_name) {
        $this->db->select('tbl_modules.name as module_name,tbl_modules.slug as module_name_slug,
                            tbl_permissions.id as permission_id,tbl_permissions.module_id,tbl_permissions.name as permission_name,tbl_permissions.text,tbl_roles.role_name');
        $this->db->from('tbl_permissions');
        $this->db->join('tbl_modules', 'tbl_modules.id = tbl_permissions.module_id');
        $this->db->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id');
        $this->db->join('tbl_roles', 'tbl_roles.id = tbl_role_permissions.role_id');
        $this->db->where('tbl_role_permissions.role_id', $role_id);
        $this->db->where('tbl_permissions.name', $permission_name);
        $this->db->where('tbl_roles.status', 1);
        $query = $this->db->get();
        $result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return array();
    	}
    }

    public function get_module_permissions() {
        $this->db->select('tbl_modules.name as module_name,tbl_modules.slug as module_name_slug,
                            tbl_permissions.id as permission_id,tbl_permissions.module_id,tbl_permissions.name as permission_name,tbl_permissions.text,tbl_roles.role_name');
        $this->db->from('tbl_permissions');
        $this->db->join('tbl_modules', 'tbl_modules.id = tbl_permissions.module_id');
        $this->db->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id');
        $this->db->join('tbl_roles', 'tbl_roles.id = tbl_role_permissions.role_id');
        $this->db->where('tbl_roles.role_name', $this->session->userdata('role_name'));
        $this->db->where('tbl_roles.status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_users($user_id = 0)
    {
        // Perform a join between 'tbl_admin_user' and 'tbl_roles'
        $this->db->select('tbl_admin_user.*, tbl_roles.role_name'); // Specify the columns you want to retrieve
        $this->db->from('tbl_admin_user');
        $this->db->join('tbl_roles', 'tbl_admin_user.role_id = tbl_roles.id'); // Join condition
        $this->db->where(array(
            'tbl_admin_user.status' => 1
        ));
        //If logged in user is superadmin then display all data else display non superadmin role users
        if($this->session->userdata('role_name') != 'superadmin') {
            $this->db->where('tbl_roles.role_name != ', 'superadmin');
        }
        if($user_id > 0) {
            $this->db->where('tbl_admin_user.id', $user_id);
        }

        // Execute the query
        $query = $this->db->get();

        // Return the result
        return $query->result_array();
    }

    public function get_roles() {
        $this->db->select('*');
        $this->db->from('tbl_roles');
        $this->db->where('tbl_roles.role_name != ', 'superadmin');
        $this->db->where('tbl_roles.status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_superadmin_module_permissions() {
        // Get module permissions by enabled modules
        $this->db->select('tbl_permissions.*'); 
        $this->db->join('tbl_modules', 'tbl_modules.id = tbl_permissions.module_id');	
        $this->db->where('tbl_modules.status',1);
        $this->db->where('tbl_permissions.status',1);
        $this->db->order_by('module_id','ASC');
        $query=$this->db->get('tbl_permissions');
        //echo "<br> str ".$this->db->last_query();
    	$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
    }

} 
?>
