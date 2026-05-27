<?php defined('BASEPATH') or exit('No direct script access allowed');
class User extends MY_Controller
{
    public $CI;
    protected $data = array();
    public function __construct(){
        parent::__construct();

        //$this->require_module_permission('user_management');

        $this->load->model('Mdmaster');
        $this->load->model('user_model');
    }
    
    public function userList(){
        //$this->require_permission('view_users');

        //Get Users
        $data['arr_users'] = $this->user_model->get_users();
        $data['arr_roles'] = $this->user_model->get_roles();
        $data['title'] = 'Users';

        $this->render_page('admin/user/list-users',$data);
        
    }

    public function save(){
        //$this->require_permission('add_users');
        /*echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        exit;*/

        $row_id     = $this->input->post('row_id');
        $role_id =  $this->input->post('role_id');
        $email = $this->input->post('email');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $mobile_no = $this->input->post('mobile_no');
        $status = $this->input->post('status');

        $full_name = preg_replace('/[^A-Za-z0-9 ]/', '', $firstname.$lastname);

        // Use the first three letters of the contact name
        $prefix = strtoupper(substr($full_name, 0, 3));
        // Extract the last four digits of the contact number
        $last_four_digits = substr($mobile_no, -4);
        // Combine the prefix and the last four digits
        $password = $prefix.$last_four_digits;
        $login_link = base_url()."cms-login";

        // Begin transaction
        $this->db->trans_begin();

        try {
            if($row_id == 0) {
                //Create user in tbl_admin_user
                $arr_user = array(
                    'firstname'        => $firstname,
                    'lastname'         => $lastname,
                    'mobile_no'        => $mobile_no,
                    'email'            => $email,
                    'username'         => $email,
                    'password'         => password_hash($password, PASSWORD_DEFAULT),
                    'role_id'          => $role_id,
                    'status'           => $status,
                    'created_datetime'      => date('Y-m-d H:i:s'),
                );
                /*echo "<pre>";
                print_r($arr_user);
                echo "</pre>";
                exit;*/

                $user_id = $this->Mdmaster->addRecord($arr_user, 'tbl_admin_user');

                if($user_id > 0) {
                    //Send Login Details via Email
                    $getMailTemplateDetails = get_email_template_details(2);
                    foreach($getMailTemplateDetails as $details) {
                        $subject = $details['email_subject'];
                        $email_content = $details['email_content'];

                        $message = str_replace(array('$first_name','$email','$password','$login_link'),array($firstname,$email,$password,$login_link),$email_content);

                        //echo "<br> Message <br>".$message;
                        //exit;
                        send_email($subject,$message,$firstname,$email);
                    }  
                }
            }
            else {
                $user_id = $row_id;

                //update user in tbl_crm_users
                $arr_user = array(
                    'firstname'        => $firstname,
                    'lastname'         => $lastname,
                    'mobile_no'        => $mobile_no,
                    'email'            => $email,
                    'username'         => $email,
                    'role_id'          => $role_id,
                    'status'           => $status,
                );
                $this->db->where('admin_id', $user_id);
                $this->db->update('tbl_admin_user', $arr_user);
                //echo "<br> str ".$this->db->last_query();exit;
            }

            // Check for any errors in the transaction
            if ($this->db->trans_status() === FALSE) {
                // Rollback the transaction if something went wrong
                $this->db->trans_rollback();
                throw new Exception('Transaction failed');
            } else {
                // Commit the transaction if everything is good
                $this->db->trans_commit();
            }
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->db->trans_rollback();
            // Log the error or handle it as required
            log_message('error', $e->getMessage());
            echo "An error occurred: " . $e->getMessage();
        }    

        redirect('list-users');
    }

    public function edit_user_details(){
        //$this->require_permission('edit_users');

        $row_id     = $this->input->post('keys');
        $details    = $this->user_model->get_company_users($this->company_id,$row_id);
        
        $result = array(
            'details'       => $details[0],
        );
        echo json_encode($result);
    }
    
    public function delete_user_details(){
        //$this->require_permission('delete_users');

        $row_id     = $this->input->post('keys');
        
        $arr_user   = array(
            'status'       => '0',
        );
        $this->db->where('id', $row_id);
        $this->db->update('tbl_crm_users', $arr_user);

        $output = array(
            'status' => 'Success',
            'info'   => 'User Deleted Successfully !!!',
        );
        echo json_encode($output);
    }

    public function CheckDuplicateUserEmail() {
        $email = $this->input->post('email');
        $user_id = $this->input->post('user_id');
        
        $condition = ($user_id > 0) ? " id != ".$user_id : "";
        $validate = $this->Mdmaster->checkDuplicate('email',$email,'tbl_companies',$condition);
        
        $data['validate'] = $validate;
        
        echo json_encode($data);
    }

    public function changePassword(){
        //$this->require_permission('view_users');

        $data['title'] = 'Change Password';

        $this->render_page('admin/user/change-password',$data);
        
    }
}
?>