<?php defined('BASEPATH') or exit('No direct script access allowed');

class Cron_generate_permissions extends MY_Controller 
{
    //
    public $CI;

    /**
     * An array of variables to be passed through to the
     * view, layout,....
     */
    protected $data = array();

    /**
     * [__construct description]
     *
     * @method __construct
     */
    public function __construct()
    {
        // To inherit directly the attributes of the parent class.
        parent::__construct();

        $this->load->model('Mdmaster');
        $this->load->model('mainModel');
    }
	
	public function index()
    {
		$this->db->select('*'); 
        $this->db->where('id > 2');
        $query=$this->db->get('tbl_modules');
    	$arr_modules = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit; 

        $arr_operations = array('view','add','edit','delete');

    	if(count($arr_modules) > 0){ 
            foreach($arr_modules as $row) { 
                $module_id = $row['id'];
                $slug = $row['slug'];
                $description = $row['description'];

                foreach($arr_operations as $name) {
                    $arrInsert['module_id'] = $module_id;
                    $arrInsert['name'] = $name."_".$slug;
                    $arrInsert['description'] = ucwords($name)." ".$description;
                    $arrInsert['text'] = ucwords($name)." ".$description;
                    $arrInsert['status'] = 1;

                    $this->db->insert('tbl_permissions', $arrInsert);  
                }
            }
        }
    }

    public function assignPermissionToSuperAdmin()
    {
		$this->db->select('*'); 
        $this->db->where('id > 6');
        $query=$this->db->get('tbl_permissions');
    	$arr_permissions = $query->result_array();
        //echo "<br> str ".$this->db->last_query();exit; 

        if(count($arr_permissions) > 0){ 
            foreach($arr_permissions as $row) { 
                $permission_id = $row['id'];

                $arrInsert['role_id'] = 1;
                $arrInsert['permission_id'] = $permission_id;
                $this->db->insert('tbl_role_permissions', $arrInsert);  
            }
        }
    }

}
