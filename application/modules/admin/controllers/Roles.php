<?php defined('BASEPATH') or exit('No direct script access allowed');
class Roles extends MY_Controller
{
    public $CI;
    protected $data = array();
    public function __construct(){
        parent::__construct();

        //$this->require_module_permission('user_management');

        $this->load->model('Mdmaster');
        $this->load->model('User_model');
        
        $this->login_id = $this->session->userdata('id');
    }
    
    public function list(){
        //$this->require_permission('view_roles');

        $data['title'] = 'Roles';

        //Get CRM Modules
        $condition = "status = 1 AND role_name != 'superadmin'";
        $data['arr_roles'] = $this->Mdmaster->getAllRecords('tbl_roles',$condition,'id','ASC'); 
        /*echo "<pre>"; 
        print_r($data);
        echo "</pre>";
        exit;*/
        $this->render_page('admin/roles/list-roles',$data); 
    }
    
    public function save(){
        //$this->require_permission('add_roles');
        /*echo "<pre>"; 
        print_r($_POST);
        echo "</pre>";
        exit;*/
        $row_id         = $this->input->post('row_id');
        
        $arrData   = array(
            'role_name'   => $this->input->post('role_name'),
            'status'      => 1,
        );
        
        if($row_id == 0) {
            $role_id = $this->Mdmaster->addRecord($arrData,'tbl_roles');
            
            redirect('list-roles-permissions/'.$role_id);
        }
        else {
            $this->db->where('id', $row_id);
            $this->db->update('tbl_roles', $arrData);
            
            redirect('list-roles');
        }
    }
    
    public function delete_role($row_id){
        //$this->require_permission('delete_roles');
        $data_arr   = array(
            'status'       => 0,
        );
        $this->db->where('id', $row_id);
        $this->db->update('tbl_roles', $data_arr);

        $this->session->set_flashdata('msg', 'Role deleted successfully');
        
        redirect('list-roles');
    }

    public function CheckDuplicateRole() {
        $role_id = $this->input->post('role_id');
        $role_name = $this->input->post('role_name');
        
        $condition = "status = 1";
        $condition .= ($role_id > 0) ? " AND id != ".$role_id : "";
        $validate = $this->Mdmaster->checkDuplicate('role_name',$role_name,'tbl_roles',$condition);
        
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }

    public function rolesPermissionsList($role_id){
        //$this->require_permission('view_roles');

        $permissions = $this->User_model->get_module_permissions();   
        //echo "<br> str ".$this->db->last_query();exit;

        $arr_role_details = $this->User_model->get_role($role_id);
        $role_name = $arr_role_details['role_name'];

        $arr_assigned_permission = array();
        $arr_assigned_permission_list = $this->User_model->get_permissions($role_id); 
        if($arr_assigned_permission_list != false) {
            foreach($arr_assigned_permission_list as $assignedData) {
                $arr_assigned_permission[$assignedData['permission_id']] = $assignedData['permission_id'];
            }
        }

        $arrModules = array();
        $arrPermissions = array();
        foreach ($permissions as $perm) {
            $arrModules[$perm['module_id']] = $perm['module_name'];
            $arrPermissions[$perm['module_id']][$perm['permission_id']] = $perm['text'];
        }

        /*echo "<pre>"; 
        print_r($arrModules);
        print_r($arrPermissions);
        echo "</pre>";
        exit;*/

        $data['title'] = 'Permission';
        $data['role_id'] = $role_id;
        $data['role_name'] = $role_name;
        $data['arrModules'] = $arrModules;
        $data['arrPermissions'] = $arrPermissions;
        $data['arr_assigned_permission'] = $arr_assigned_permission;

        $this->render_page('admin/roles/list-roles-permissions',$data); 
    }

    public function savePermissions(){
        //$this->require_permission('add_roles');
        /*echo "<pre>"; 
        print_r($_POST);
        echo "</pre>";
        exit;*/
        $arr_permissions  = (array_key_exists('permission_id',$this->input->post())) ? $this->input->post('permission_id') : array();
        $role_id  = $this->input->post('role_id');

        if(count($arr_permissions) > 0) {
            //Delete from tbl_role_permissions
            $this->db->where('role_id', $role_id);
            $this->db->delete('tbl_role_permissions');

            foreach($arr_permissions as $permission_id => $permission_check) {
                // Create a record in tbl_role_permissions
                $arr_role_permissions = array(
                    'role_id'        => $role_id,
                    'permission_id'  => $permission_id,  
                );
                /*echo "<pre>"; 
                print_r($arr_role_permissions);
                echo "</pre>";
                exit;*/
                $this->Mdmaster->addRecord($arr_role_permissions, 'tbl_role_permissions');
            }
        }
        
        redirect('list-roles');
    }

    public function saveSuperadminPermissions($role_id = 1){
        //$this->require_permission('view_roles');

        $this->load->library('user_roles_permissions');
        $this->user_roles_permissions->assignModulesToUser($role_id);
        
    }
}
?>