<?php defined('BASEPATH') or exit('No direct script access allowed');


class Moderation extends MY_Controller
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
		$this->load->model('Results_model');
        
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
    public function moderate_results($student_id_encode)
    {
        $data['title'] = 'Results Moderation';
		$data['theory_moderation'] = $data['practical_activity_moderation'] = $data['viva_moderation'] = array();
		$data['ta_ids'] =array();
		
		$student_id = id_decode($student_id_encode);
		$data['student_id'] = $student_id;
		
		$data['student'] = $this->Results_model->getStudentByID($student_id);
		//echo "<br> str ".$this->db->last_query();exit;
		
		$res_theory_questions = $this->Results_model->getTheoryQuestionsByStudentID($student_id);		
		if($res_theory_questions != false)
		{
			$arr_theory_questions = explode(',', $res_theory_questions['theory_questions']);
			$data['theory_moderation'] = $this->Results_model->getTheoryModeration($student_id, $arr_theory_questions);	
			foreach($data['theory_moderation'] as $row)
			{
				$data['ta_ids'][] = $row['ta_id'];
			}
		}
		
		$res_practical_activity_questions = $this->Results_model->getPracticalActivityQuestionsByStudentID($student_id);	
		if($res_practical_activity_questions != false)
		{
			$arr_practical_activity_questions = explode(',', $res_practical_activity_questions['practical_activity_questions']);
			$data['practical_activity_moderation'] = $this->Results_model->getPracticalActivityModeration($student_id, $arr_practical_activity_questions);			
		}
		
		$res_viva_questions = $this->Results_model->getVivaQuestionsByStudentID($student_id);	
		
		if($res_viva_questions != false)
		{
			$arr_viva_questions = explode(',', $res_viva_questions['viva_questions']);
			$data['viva_moderation'] = $this->Results_model->getVivaModeration($student_id, $arr_viva_questions);			
		}
        $this->render_page('admin/results/student-result-moderation',$data);
    }
    
    public function save_theory_moderation()
    {
		$student_id = $this->input->post("student_id");
		$tb_id = $this->input->post("tb_id");
		
		foreach($this->input->post() as $key => $value)
		{
			if( strpos($key, "text_theory") !== false && $value !="") {
				$arr = explode("text_theory_",$key);	
				$data = array(
					'ans' => strtolower($value)
				);				
				$this->db->where('ta_id', $arr[1]);
				$query = $this->db->update('tbl_theory_answers', $data);
			}
		}	
		$this->session->set_flashdata('msg', 'Data updated successfully');
		redirect('search-results/'.id_encode($tb_id));
	}
	
	public function save_pa_moderation()
    {
		$student_id = $this->input->post("student_id");
		$tb_id = $this->input->post("tb_id");
		
		foreach($this->input->post() as $key => $value)
		{
			if( strpos($key, "text_pa") !== false && $value !="") {
				$arr = explode("text_pa_",$key);	
				$data = array(
					'marks' => $value
				);				
				$this->db->where('pa_id', $arr[1]);
				$query = $this->db->update('tbl_practical_activity_answers', $data);
			}
		}	
		$this->session->set_flashdata('msg', 'Data updated successfully');
		redirect('search-results/'.id_encode($tb_id));
	}
	
	public function save_viva_moderation()
    {
		$student_id = $this->input->post("student_id");
		$tb_id = $this->input->post("tb_id");
		
		foreach($this->input->post() as $key => $value)
		{
			if( strpos($key, "text_viva_") !== false && $value !="") {
				$arr = explode("text_viva_",$key);	
				$data = array(
					'marks' => $value
				);				
				$this->db->where('va_id', $arr[1]);
				$query = $this->db->update('tbl_viva_answers', $data);
			}
		}	
		$this->session->set_flashdata('msg', 'Data updated successfully');
		redirect('search-results/'.id_encode($tb_id));
	}
}
