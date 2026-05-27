<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter-HMVC
 *
 * @package    CodeIgniter-HMVC
 * @author     N3Cr0N (N3Cr0N@list.ru)
 * @copyright  2019 N3Cr0N
 * @license    https://opensource.org/licenses/MIT  MIT License
 * @link       <URI> (description)
 * @version    GIT: $Id$
 * @since      Version 0.0.1
 * @filesource
 *
 */

class MY_Controller extends MX_Controller
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

        // This function returns the main CodeIgniter object.
        // Normally, to call any of the available CodeIgniter object or pre defined library classes then you need to declare.
        $CI =& get_instance();

        // Copyright year calculation for the footer
        $begin = 2019;
        $end =  date("Y");
        $date = "$begin - $end";

        // Copyright
        $data['copyright'] = $date;

        $this->load->model('admin/User_model');
    }
    
    // --------------------------------------------------------------------

    /**
     * 	Method to load the "view" and add private template variables to the 
     * 	public template variables.
     *
     * 	@author	Asmitha
     *
     * 	@param	string	Path to the template file.
     * 	@param	array	Array of private template variables.
     * 	@param	bool	True or False to enable this template to be cached.
     * 	@param	int	The number of minutes to cache this template.
     *      @tpl for        Front end
     */
    protected function render_page($view, $data)
    {
        $this->load->view('admin/template/header');
        $this->load->view('admin/template/sidebar');
        $this->load->view($view, $data);
        $this->load->view('admin/template/footer');
    }

    //Check whether the user has permission
    protected function require_permission($permission) {
        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
            if($this->session->userdata('role_name') != 'superadmin') {
                redirect('cms-login');	
            }
			else {
                redirect('admin-login');	
            }
		}

        $role_id = $this->session->userdata('role_id');
        $permissions = $this->User_model->get_permissions($role_id);
        //echo "<br> str ".$this->db->last_query();exit;
        /*echo "<pre>";
        print_r($permissions);
        echo "</pre>";
        echo "<br> permission ".$permission;*/

        $has_permission = false;
        foreach ($permissions as $perm) {
            if ($perm['permission_name'] == $permission) {
                $has_permission = true;
                break;
            }
        }

        //echo "<br> has_permission ".$has_permission;exit;

        if (!$has_permission) {
            redirect('permission-denied');
        }
    }

    protected function require_module_permission($permissions) {
        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			if($this->session->userdata('role_name') != 'superadmin') {
                redirect('cms-login');	
            }
			else {
                redirect('admin-login');	
            }
		}
    
        $role_id = $this->session->userdata('role_id');
        $user_permissions = $this->User_model->get_permissions($role_id);
        //echo "<br> str ".$this->db->last_query();exit;
    
        $has_permission = false;
    
        // Normalize $permissions to an array if it's not already
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }
    
        // Check if the user has at least one of the required permissions
        foreach ($user_permissions as $perm) {
            //echo "<br> module_name_slug ".$perm['module_name_slug'];
            if (in_array($perm['module_name_slug'], $permissions)) {
                $has_permission = true;
                break;
            }
        }
        //echo $has_permission;
        // Redirect if the user does not have any of the required permissions
        if (!$has_permission) {
            redirect('permission-denied');
        }
    }
    
    
}

