<?php defined('BASEPATH') or exit('No direct script access allowed');


class Usermanagement extends MY_Controller
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
        
        $isSessionAlive = $this->session->userdata('is_logged_in');
		if(!$isSessionAlive){
			redirect('admin-login');	
		}
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function list()
    {
        $data['title'] = 'Admin Users';

        $this->render_page('admin/masters/list-admin-users',$data);
    }
	
	public function change_password(){
        $data['title'] = 'Admin - Change Password';
        $this->render_page('admin/masters/change-password',$data);
    }
	
    public function submit_change_password(){
        $user_admin_id          = $this->session->userdata('user_id');
        $user_password          = $this->input->post('new_password');
        $user_confirm_password  = $this->input->post('confirm_password');
        
        if($user_password==$user_confirm_password){
            $data['password'] = password_hash($user_password,PASSWORD_BCRYPT);
            $this->db->where('admin_id', $user_admin_id);
            $this->db->update('tbl_admin_user', $data);
            
            $this->session->set_flashdata('msg', 'Password Updated Successfully');
        }else{
            $this->session->set_flashdata('error', 'Password Mismatch');
        }
        redirect('change-password');
    }
}
