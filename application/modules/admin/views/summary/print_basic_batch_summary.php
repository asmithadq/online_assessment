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
                            <h4 class="card-title">Result Summary</h4>
                        </div>
                        <div class="card-body">
                            <?php
                            ?>
                            <div class="row">
                                <div class="col-md-6" style="float:left;">
                                    <h4>Student Information</h4> <!-- Change h2 to h4 -->
                                    <ul>
                                        <li>SDMS Enrollment No: TS0001C1-0000001</li>
                                        <li>Student Name: Dakshajakumari Pangerwad</li>
                                        <li>Father Name: </li>
                                        <li>Date of Birth: dd-mm-yyyy format</li>
                                        <li>Student Photo: <img src="path_to_images" alt="Student Photo"></li>
                                        <li>Aadhar number: 123456798912</li>
                                        <li>Aadhar Front: <img src="path_to_images" alt="Aadhar Front"></li>
                                        <li>Aadhar Back: <img src="path_to_images" alt="Aadhar Back"></li>
                                        <li>Address: </li>
                                        <li>Geo Location Details: </li>
                                        <li>Assessment Snapshots: <img src="path_to_images" alt="Assessment Snapshots"></li>
                                    </ul>
                                </div>
                                <div class="col-md-6" style="float:right;">
                                    <h4>Assessment Details</h4> <!-- Change h2 to h4 -->
                                    <ul>
                                        <li>Training Partner Name: Telangana Jagruthi</li>
                                        <li>Training Center Name: 449 - TJ Skills - Nirmal</li>
                                        <li>Batch Name: 2829 / 1702TS0001C1JLSC/Q1120-00000B63-</li>
                                        <li>Assessment Date: 03-05-2017</li>
                                        <li>Sector Skill Council: Logistics Sector Skill Council</li>
                                        <li>Trade/QP Name: Consignment Booking Assistant</li>
                                    </ul>
                                  <h4>Result Information</h4> 
                                    <ul>
                                        <li>Passing Percentage: 50% </li>
                                        <li>User Percentage: 82.38%</li>
                                        <li>Result: <span class="badge badge-rounded badge-success">Pass</span> 
                                    <span class="badge badge-rounded badge-danger">Fail</span> </li>
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
                                        <tr>
                                            <td align="left">N1117 - Prepare for Booking</td>
                                            <td>10</td>
                                            <td>2</td>
                                            <td>8</td>
                                            <td>88</td>
                                            <td>88</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1118 - Perform Consignment Booking</td>
                                            <td>10</td>
                                            <td>3</td>
                                            <td>7</td>
                                            <td>88</td>
                                            <td>71</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1119 - Perform Post Booking Activities</td>
                                            <td>10</td>
                                            <td>2</td>
                                            <td>8</td>
                                            <td>88</td>
                                            <td>76</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1128 - Maintain Health Safety and Security Measures While Booking Consignments</td>
                                            <td>10</td>
                                            <td>3</td>
                                            <td>7</td>
                                            <td>88</td>
                                            <td>58.5</td>
                                        </tr>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong>40</strong></td>
                                            <td><strong>10</strong></td>
                                            <td><strong>30</strong></td>
                                            <td><strong>352</strong></td>
                                            <td>293.5</td>
                                        </tr>
                                        <tr>
                                            <th align="left" colspan="10"><strong>Practical Activity</strong></th>
                                        </tr>
                                        <tr>
                                            <td align="left">N1117 - Prepare for Booking</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>9</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1118 - Perform Consignment Booking</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>8</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1119 - Perform Post Booking Activities</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>9</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1128 - Maintain Health Safety and Security Measures While Booking Consignments</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>10</td>
                                        </tr>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong>4</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>48</strong></td>
                                            <td><strong>36</strong></td>
                                        </tr>
                                        <tr>
                                            <th align="left" colspan="10"><strong>Viva</strong></th>
                                        </tr>
                                        <tr>
                                            <td align="left">N1117 - Prepare for Booking</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>9</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1118 - Perform Consignment Booking</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>8</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1119 - Perform Post Booking Activities</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>9</td>
                                        </tr>
                                        <tr>
                                            <td align="left">N1128 - Maintain Health Safety and Security Measures While Booking Consignments</td>
                                            <td>1</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>12</td>
                                            <td>10</td>
                                        </tr>
                                        <tr>
                                            <td align="left">&nbsp;</td>
                                            <td><strong>4</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>-</strong></td>
                                            <td><strong>48</strong></td>
                                            <td><strong>36</strong></td>
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
                                        <tr>
                                            <td>Theory</td>
                                            <td>40</td>
                                            <td>352</td>
                                            <td>293.5</td>
                                            <td>03-05-2017</td>
                                        </tr>
                                        <tr>
                                            <td>Practical Activity</td>
                                            <td>4</td>
                                            <td>48</td>
                                            <td>36</td>
                                            <td>03-05-2017</td>
                                        </tr>
                                        <tr>
                                            <td>Viva</td>
                                            <td>4</td>
                                            <td>48</td>
                                            <td>36</td>
                                            <td>03-05-2017</td>
                                        </tr>
                                        <tr>
                                            <td>Total</td>
                                            <td>44</td>
                                            <td>400</td>
                                            <td>329.5</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
	
		

