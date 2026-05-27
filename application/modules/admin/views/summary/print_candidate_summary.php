    <style>
        .content-body .container-fluid .row .col-md-6 ul li {
            padding: 5px; /* Add padding to list items */
        }

        .content-body .container-fluid .row .col-md-6 h2 {
            font-size: 1.2rem; /* Reduce h2 font size to h4 */
        }
		table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
	
	<div class="content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Candidate Detailed Result Summary</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6" style="float:left;">
                                    <h4>Student Information</h4> <!-- Change h2 to h4 -->  
                                    <ul>
                                        <li>Candidate ID: <?php echo $arr_student_details['enrollment_number']; ?></li>
                                        <li>Student Name: <?php echo $arr_student_details['student_name']; ?></li>
                                        <li>Father Name: <?php echo $arr_student_details['father_name']; ?></li>
                                        <li>Date of Birth: <?php echo date('d-m-Y',strtotime($arr_student_details['dob'])); ?></li>
                                        <li>Student Photo: <img src="<?php echo base_url().$this->config->item('student_photo_path').$arr_student_details['student_photo']; ?>" alt="Student Photo" height="150" width="150"></li>
                                        <li>Aadhar number: <?php echo $arr_student_details['aadhar_number']; ?></li>
                                        <li>Aadhar Front: <img src="<?php echo base_url().$this->config->item('aadhaar_filename_path').$arr_student_details['aadhar_front_filename']; ?>" alt="Aadhar Front" height="150" width="150"></li>
                                        <li>Aadhar Back: <img src="<?php echo base_url().$this->config->item('aadhaar_filename_path').$arr_student_details['aadhar_back_filename']; ?>" alt="Aadhar Back" height="150" width="150"></li>
                                        <li>Address: <?php echo $arr_student_details['address']; ?></li>
                                        <li>Geo Location Details: Latitude - <?php echo $arr_student_details['lat']; ?>,Longitude - <?php echo $arr_student_details['lng']; ?></li>
                                        <li>Assessment Snapshots: <img src="path_to_images" alt="Assessment Snapshots"></li>
                                    </ul>
                                </div>
                                <div class="col-md-6" style="float:right;">
                                    <h4>Assessment Details</h4> <!-- Change h2 to h4 -->
                                    <ul>
                                        <li>Training Partner Name: <?php echo $arr_student_details['tp_name']; ?></li>
                                        <li>Training Center Name: <?php echo $arr_student_details['tc_name']; ?></li>
                                        <li>Batch Name: <?php echo $arr_student_details['batch_id']; ?></li>
                                        <li>Assessment Date: <?php echo date('d-m-Y',strtotime($arr_student_details['tb_assessment_date'])); ?></li>
                                        <li>Sector Skill Council: <?php echo $arr_student_details['ssc_code']."-".$arr_student_details['ssc_title']; ?></li>
                                        <li>Trade/QP Name: <?php echo $arr_student_details['trade_code']."-".$arr_student_details['trade_name']; ?></li>
                                    </ul>
                                  <h4>Result Information</h4> 
                                    <ul>
                                        <li>Passing Percentage: <?php echo $arr_student_details['pass_percentage']; ?>% </li>
                                        <li>User Percentage: <?php echo $arr_student_details['marks_percentage']; ?>%</li>
                                        <li>Result: <?php echo $arr_student_details['result']; ?></li>
                                    </ul>
                                </div>
								 <h4>Assessment Details</h4>
                                 <table>
                                    <thead>
                                        <tr>
                                            <th>Trade/QP Name</th>
                                            <th>Total Questions</th>
                                            <th>Theory</th>
                                            <th>Practical Skill</th>
                                            <th>Full Score</th>
                                            <th>User Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="left" colspan="10"><strong>Theory </strong></td>
                                        </tr>
                                        <?php
                                        $grand_total_qns = 0;
                                        $grand_total_theory_qns = 0;
                                        $grand_total_practical_skill_qns = 0;
                                        $grand_total_full_score = 0;
                                        $grand_total_user_score = 0;

                                        if(count($arr_trade_nos_details) > 0) {
                                            foreach($arr_trade_nos_details as $nos_id => $nos_details) {
                                                $total_theory_qns = (array_key_exists('Theory',$arr_qn_type_details[$nos_id])) ? count($arr_qn_type_details[$nos_id]['Theory']) : 0;
                                                $total_practical_skill_qns = (array_key_exists('PracticalSkill',$arr_qn_type_details[$nos_id])) ? count($arr_qn_type_details[$nos_id]['PracticalSkill']) : 0;
                                                $total_qns = $total_theory_qns + $total_practical_skill_qns;
                                                $full_score = $nos_details['theory_marks'] + $nos_details['practical_skill_marks'];
                                                $user_score = $arr_nos_wise_user_score[$nos_id]['theory'];

                                                $grand_total_qns += $total_qns;
                                                $grand_total_theory_qns += $total_theory_qns;
                                                $grand_total_practical_skill_qns += $total_practical_skill_qns;
                                                $grand_total_full_score += $full_score;
                                                $grand_total_user_score += $user_score;
                                            ?>
                                                <tr>
                                                    <td align="left"><?php echo $nos_details['nos_code']; ?> - <?php echo $nos_details['nos_title']; ?></td>
                                                    <td><?php echo $total_qns; ?></td>
                                                    <td><?php echo $total_theory_qns; ?></td>
                                                    <td><?php echo $total_practical_skill_qns; ?></td>
                                                    <td><?php echo $full_score; ?></td>
                                                    <td><?php echo $user_score; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong><?php echo $grand_total_qns; ?></strong></td>
                                            <td><strong><?php echo $grand_total_theory_qns; ?></strong></td>
                                            <td><strong><?php echo $grand_total_practical_skill_qns; ?></strong></td>
                                            <td><strong><?php echo $grand_total_full_score; ?></strong></td>
                                            <td><?php echo $grand_total_user_score; ?></td>
                                        </tr>
                                        <?php
                                        if($show_practical_activity == 1) {
                                            $grand_total_practical_activity_qns = 0;
                                            $grand_total_practical_activity_full_score = 0;
                                            $grand_total_practical_activity_user_score = 0;
                                        ?> 
                                            <tr>
                                                <th align="left" colspan="10"><strong>Practical Activity</strong></th>
                                            </tr>
                                        <?php   
                                            if(count($arr_trade_nos_details) > 0) {
                                                foreach($arr_trade_nos_details as $nos_id => $nos_details) {
                                                    $total_practical_activity_qns = (array_key_exists('PracticalActivity',$arr_qn_type_details[$nos_id])) ? count($arr_qn_type_details[$nos_id]['PracticalActivity']) : 0;
                                                    $practical_activity_full_score = $nos_details['practical_marks'];
                                                    $practical_activity_user_score = $arr_nos_wise_user_score[$nos_id]['practical_activity'];

                                                    $grand_total_practical_activity_qns += $total_practical_activity_qns;
                                                    $grand_total_practical_activity_full_score += $practical_activity_full_score;
                                                    $grand_total_practical_activity_user_score += $practical_activity_user_score;
                                                ?>
                                                    <tr>
                                                        <td align="left"><?php echo $nos_details['nos_code']; ?> - <?php echo $nos_details['nos_title']; ?></td>
                                                        <td><?php echo $total_practical_activity_qns; ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td><?php echo $practical_activity_full_score; ?></td>
                                                        <td><?php echo $practical_activity_user_score; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong><?php echo $grand_total_practical_activity_qns; ?></strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong><?php echo $grand_total_practical_activity_full_score; ?></strong></td>
                                            <td><?php echo $grand_total_practical_activity_user_score; ?></td>
                                        </tr>
                                        <?php
                                        if($show_viva == 1) {
                                            $grand_total_viva_qns = 0;
                                            $grand_total_viva_full_score = 0;
                                            $grand_total_viva_user_score = 0;
                                        ?> 
                                            <tr>
                                                <th align="left" colspan="10"><strong>Viva</strong></th>
                                            </tr>
                                        <?php   
                                            if(count($arr_trade_nos_details) > 0) {
                                                foreach($arr_trade_nos_details as $nos_id => $nos_details) {
                                                    $total_viva_qns = (array_key_exists('Viva',$arr_qn_type_details[$nos_id])) ? count($arr_qn_type_details[$nos_id]['Viva']) : 0;
                                                    $viva_full_score = $nos_details['viva_marks'];
                                                    $viva_user_score = $arr_nos_wise_user_score[$nos_id]['viva'];

                                                    $grand_total_viva_qns += $total_viva_qns;
                                                    $grand_total_viva_full_score += $viva_full_score;
                                                    $grand_total_viva_user_score += $viva_user_score;
                                                    ?>
                                                    <tr>
                                                        <td align="left"><?php echo $nos_details['nos_code']; ?> - <?php echo $nos_details['nos_title']; ?></td>
                                                        <td><?php echo $total_viva_qns; ?></td>
                                                        <td>-</td>
                                                        <td>-</td>
                                                        <td><?php echo $viva_full_score; ?></td>
                                                        <td><?php echo $viva_user_score; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong><?php echo $grand_total_viva_qns; ?></strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong><?php echo $grand_total_viva_full_score; ?></strong></td>
                                            <td><?php echo $grand_total_viva_user_score; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h4>Overall Result</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Exam Type</th>
                                            <th>Total Questions</th>
                                            <th>Full Score</th>
                                            <th>User Score</th>
                                            <th>Date of Exam</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $arr_optional_exam_type = explode(",",$arr_student_details['optional_exam_type']);
                                        $theoryQns = 0;
                                        $practicalActivityQns = 0;
                                        $vivaQns = 0;

                                        if(in_array('theory',$arr_optional_exam_type)) {
                                            $theoryQns = count(explode(",",$arr_student_details['theory_questions']));
                                        ?>
                                            <tr>
                                                <td>Theory</td>
                                                <td><?php echo $theoryQns; ?></td>
                                                <td><?php echo $grand_total_full_score; ?></td>
                                                <td><?php echo $grand_total_user_score; ?></td>
                                                <td><?php echo ($arr_student_details['theory_submission_dts'] != "") ? date('d-m-Y',strtotime($arr_student_details['theory_submission_dts'])) : ""; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        if($show_practical_activity == 1) {
                                            $practicalActivityQns = count(explode(",",$arr_student_details['practical_activity_questions']));
                                        ?>
                                            <tr>
                                                <td>Practical Activity</td>
                                                <td><?php echo $practicalActivityQns; ?></td>
                                                <td><?php echo $grand_total_practical_activity_full_score; ?></td>
                                                <td><?php echo $grand_total_practical_activity_user_score; ?></td>
                                                <td><?php echo ($arr_student_details['practicalactivity_submission_dts'] != "") ? date('d-m-Y',strtotime($arr_student_details['practicalactivity_submission_dts'])) : ""; ?></td>
                                            </tr>  
                                        <?php      
                                        }
                                        if($show_viva == 1) {
                                            $vivaQns = count(explode(",",$arr_student_details['viva_questions']));
                                        ?>
                                            <tr>
                                                <td>Viva</td>
                                                <td><?php echo $vivaQns; ?></td>
                                                <td><?php echo $grand_total_viva_full_score; ?></td>
                                                <td><?php echo $grand_total_viva_user_score; ?></td>
                                                <td><?php echo ($arr_student_details['viva_submission_dts'] != "") ? date('d-m-Y',strtotime($arr_student_details['viva_submission_dts'])) : ""; ?></td>
                                            </tr>  
                                        <?php      
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php if($type == 'detailed_pdf') { ?>
                                <h4>Theory Information</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>SNo.</th>
                                            <th>Question</th>
                                            <th>Marks</th>
                                            <th>User's Response(s)</th>
                                            <th>Correct Answer</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sl_no = 1;
                                        if(count($arr_theory_answers_list) > 0) {
                                            foreach($arr_theory_answers_list as $qn_id => $theory_answer_data) {
                                                //echo "<br> qn_id ".$qn_id;
                                                $nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
                                                $user_ans = strtoupper($arr_theory_answers_list[$qn_id]['ans']);
                                                $correct_ans = strtoupper($arr_theory_answers_list[$qn_id]['correct_ans']);
                                                $save_type = $arr_theory_answers_list[$qn_id]['save_type'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $sl_no; ?></td>
                                                    <td>
                                                        <h6><?php echo $arr_qns_details[$nos_id][$qn_id]['question']; ?></h6>
                                                    </td>
                                                    <td><?php echo $arr_theory_answers_list[$qn_id]['marks']; ?></td>
                                                    <td><b><?php echo ($user_ans != "") ? $user_ans : $save_type; ?></b></td>
                                                    <td><?php echo $correct_ans; ?></td>
                                                    <td><?php echo ($user_ans == $correct_ans) ? '<i class="fa fa-check-circle text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>'; ?></td>
                                                </tr>
                                                <?php
                                                $sl_no++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php if($show_practical_activity == 1) { ?>
                                    <!-- PracticalActivity Detail Information -->
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h4>Practical Activity Information</h4>
                                        </div>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="7%">SNo.</th>
                                                <th width="70%">Question</th>
                                                <th width="5%">Marks Awarded</th>
                                                <th width="5%">Max Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $sl_no = 1;
                                            if(count($arr_practical_activity_answers_list) > 0) {
                                                foreach($arr_practical_activity_answers_list as $qn_id => $practical_activity_answer_data) {
                                                    //echo "<br> qn_id ".$qn_id;
                                                    $nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $sl_no; ?></td>
                                                        <td>
                                                            <div class="products">
                                                                <div>
                                                                    <h6><?php echo $arr_qns_details[$nos_id][$qn_id]['question']; ?></h6>
                                                                </div>	
                                                            </div>    
                                                        </td>
                                                        <td><?php echo $arr_practical_activity_answers_list[$qn_id]['marks']; ?></td>
                                                        <td><?php echo $arr_practical_activity_answers_list[$qn_id]['max_marks']; ?></td>
                                                    </tr>
                                                    <?php
                                                    $sl_no++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php } ?>    
                                <?php if($show_viva == 1) { ?>                 
                                    <!-- Viva Detail Information -->
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h4>Viva Information</h4>
                                        </div>
                                    </div>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="7%">SNo.</th>
                                                <th width="70%">Question</th>
                                                <th width="5%">Marks Awarded</th>
                                                <th width="5%">Max Marks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Details for Viva questions -->
                                            <?php
                                            $sl_no = 1;
                                            if(count($arr_viva_answers_list) > 0) {
                                                foreach($arr_viva_answers_list as $qn_id => $viva_answer_data) {
                                                    //echo "<br> qn_id ".$qn_id;
                                                    $nos_id = $arr_qn_nos_ids[$qn_id]['nos_id'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $sl_no; ?></td>
                                                        <td>
                                                            <div class="products">
                                                                <div>
                                                                    <h6><?php echo $arr_qns_details[$nos_id][$qn_id]['question']; ?></h6>
                                                                </div>	
                                                            </div>    
                                                        </td>
                                                        <td><?php echo $arr_viva_answers_list[$qn_id]['marks']; ?></td>
                                                        <td><?php echo $arr_viva_answers_list[$qn_id]['max_marks']; ?></td>
                                                    </tr>
                                                    <?php
                                                    $sl_no++;
                                                }
                                            }
                                            ?>
                                            <!-- Add more rows for Viva questions -->
                                        </tbody>
                                    </table>
                                <?php } 
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

