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

class Login extends MX_Controller
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
        $this->load->model('Login_model');
        $this->load->model('Mdmaster');
    }

    /**
     * [index description]
     *
     * @method index
     *
     * @return [type] [description]
     */
    public function index()
    {
        $data = array();

        $isSessionAlive = $this->session->userdata('is_logged_in');
		if($isSessionAlive){
			redirect('admin-dashboard');	
		}
	
		if($this->input->post('submit'))
		{
			$data = array(
				'email' => $this->input->post('email'),
				'password' => $this->input->post('password')
			);
			
			$result = $this->Login_model->login($data);
			
			if($result)
			{
				$this->session->set_userdata('is_logged_in','1');
    			
    			$this->session->set_userdata('user_id',$result['admin_id']);
    			$this->session->set_userdata('role_id',$result['role_id']); 
                $this->session->set_userdata('role_name',$result['role_name']);
    			$this->session->set_userdata('user_name',$result['email']); 
    			$this->session->set_userdata('name',$result['firstname']);
                
                redirect('admin-dashboard');	
											
			}
			else{
				$this->session->set_flashdata('errors', 'Invalid Username or Password!');
				redirect('admin-login');
			}			
		}
        
        $this->load->view('admin/admin-login', $data);
    }
	
	public function logout()
	{
		$this->session->sess_destroy();
        if($this->session->userdata('role_name') != 'superadmin') {
            redirect('cms-login');	
        }
        else {
            redirect('admin-login');	
        }
	}
}
