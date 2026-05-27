<?php defined('BASEPATH') or exit('No direct script access allowed');
class Cms_modules extends MY_Controller 
{
    public $CI;
    protected $data = array();
    public function __construct(){
        parent::__construct();

        $this->load->model('Mdmaster');
        
    }
    
    public function list(){ 
        $data['title'] = 'Modules & Permissions';

        //Get CRM Modules
        $condition = "status = 1";
        $data['arr_cms_modules'] = $this->Mdmaster->getAllRecords('tbl_modules',$condition,'sort_order','ASC'); 
        /*echo "<pre>"; 
        print_r($data);
        echo "</pre>";
        exit;*/
        $this->render_page('admin/cms_modules/list-modules',$data); 
    }
    
    public function save(){
        /*echo "<pre>"; 
        print_r($_POST);
        echo "</pre>";
        exit;*/
        $row_id         = $this->input->post('row_id');
        
        $arrData   = array(
            'name'   => $this->input->post('name'),
            'description'   => $this->input->post('description'),
            'sort_order'    => $this->input->post('sort_order'),
            'slug'          => $this->input->post('slug'),
            'status'        => 1,
        );
        
        if($row_id == 0) {
            $role_id = $this->Mdmaster->addRecord($arrData,'tbl_modules');
            
            redirect('list-module-permissions/'.$role_id);
        }
        else {
            $this->db->where('id', $row_id);
            $this->db->update('tbl_modules', $arrData);
            
            redirect('list-modules');
        }
    }
    
    public function delete_module($row_id){
        $data_arr   = array(
            'status'       => 0,
        );
        $this->db->where('id', $row_id);
        $this->db->update('tbl_modules', $data_arr);
        $this->session->set_flashdata('msg', 'Module deleted successfully');
        redirect('list-modules');
    }

    public function CheckDuplicateModule() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        
        $condition = "status = 1";
        $condition .= ($id > 0) ? " AND id != ".$id : "";
        $validate = $this->Mdmaster->checkDuplicate('name',$name,'tbl_modules',$condition);
        
        $data['validate'] = $validate;
        
        echo json_encode($data);
        
    }

    public function modulePermissionsList($module_id){ 
        $condition = "status = 1 AND id = ".$module_id;
        $arr_module = $this->Mdmaster->getAllRecords('tbl_modules',$condition,'sort_order','ASC'); 
        $module_name = $arr_module[0]['name'];

        $condition = "status = 1 AND module_id = ".$module_id;
        $arr_module_permissions = $this->Mdmaster->getAllRecords('tbl_permissions',$condition,'id','ASC'); 
        
        $data['module_id'] = $module_id;
        $data['module_name'] = $module_name;
        $data['arr_module_permissions'] = $arr_module_permissions;
        
        $this->render_page('admin/cms_modules/list-module-permissions',$data); 
    }

    public function saveModulePermission(){
        $row_id         = $this->input->post('row_id');
        $module_id         = $this->input->post('module_id');
        
        if($row_id == 0) {
            $arr_permissions = explode("|",$this->input->post('name'));
            $arr_text = explode("|",$this->input->post('text'));

            /*echo "<pre>"; 
            print_r($_POST);
            print_r($arr_permissions);
            print_r($arr_text);
            echo "</pre>";
            exit;*/

            foreach($arr_permissions as $key => $name) {
                $arrData   = array(
                    'module_id'   => $module_id,
                    'name'   => $name,
                    'description'   => $this->input->post('description'),
                    'text'   => $arr_text[$key],
                    'status'      => 1,
                );
                $this->Mdmaster->addRecord($arrData,'tbl_permissions');
            }
        }
        else {
            $arrData   = array(
                'module_id'   => $module_id,
                'name'   => $this->input->post('name'),
                'description'   => $this->input->post('description'),
                'text'   => $this->input->post('text'),
                'status'      => 1,
            );

            $this->db->where('id', $row_id);
            $this->db->update('tbl_permissions', $arrData);
        }
        redirect('list-module-permissions/'.$module_id);
    }
    
    public function CheckDuplicatePermission() {
        $id = $this->input->post('id');
        $module_id = $this->input->post('module_id');
        $name = $this->input->post('name');
        
        $condition = "status = 1 AND module_id = ".$module_id;
        $condition .= ($id > 0) ? " AND id != ".$id : "";
        $validate = $this->Mdmaster->checkDuplicate('name',$name,'tbl_permissions',$condition);
        
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }
    
    public function delete_permission($row_id){
        $data_arr   = array(
            'status'       => 0,
        );
        $this->db->where('id', $row_id);
        $this->db->update('tbl_permissions', $data_arr);

        $module_id = $this->uri->segment($this->uri->total_segments());

        redirect('list-module-permissions/'.$module_id);
    }
}
?>