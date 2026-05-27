<?php

class Settings_model extends CI_Model {

    public function __construct() {

        parent::__construct();

        $this->load->database();

    }



    public function get_email_data() {

        $this->db->select('*');

        $this->db->from('tbl_email_templates');

        $this->db->order_by('id','ASC');

        return $this->db->get()->result();

    }

    

   public function insert_data($data) {

        // Insert data into the table

        $this->db->insert('tbl_email_templates', $data);

    }



    public function update_data($id, $data) {

        // Update data in the table based on id

        $this->db->where('id', $id);

        $this->db->update('tbl_email_templates', $data);

    }



    public function delete_data($id) {

        // Delete data from the table based on id

        $this->db->where('id', $id);

        $this->db->delete('tbl_email_templates');

    }
	
	public function getEmailTemplatesByID($template_id)
	{
		$this->db->select('*');		
		$this->db->where('id', $template_id);
		$query=$this->db->get('tbl_email_templates');
		$result=$query->result_array();
    	if(count($result)>0){
    		return $result;
    	}else{
    	    return false;
    	}
	}

}

?>