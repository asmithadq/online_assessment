<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $category; ?></title>
    <style>
        body {
            font-size: 10px;
            font-family: Cambria, "Hoefler Text", "Liberation Serif", Times, "Times New Roman", "serif", "DejaVu Sans", "sans-serif";
        }
        .container {
            width: 100%;
			margin-left: 1px;
            margin-right: 1px;
        }
        .row::after {
            content: "";
            display: table;
            clear: both;
        }
        .column {
            float: left;
        }
        .column-left {
            width: 33%;
            text-align: left;
        }
        .column-center {
            width: 34%;
            text-align: center;
        }
        .column-right {
            width: 33%;
            text-align: right;
        }
        .bold-text {
            font-weight: bold;
        }
        .small-text {
            font-size: 10px;
        }
        .candidate-card {
            border: 1px solid #ccc;
            padding: 5px;
            margin: 2px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
           
            flex-direction: column;
        }
        .candidate-photo {
            max-width: 100%;
            height: auto;
            display: block;
			padding: 22px;
			
        }
        .candidate-info {
            margin-bottom: 2px;
        }
        .info-label {
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .candidate-card {
                width: 80%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="column column-left">
                <img src="<?php echo base_url().$this->config->item('ssc_logo_path').$arr_batch_details['ssc_logo']; ?>" alt="SSC Logo" width="120" />
            </div>
            <div class="column column-center">
                <p class="bold-text small-text" style="font-size: 12px;"><u><?php echo $category; ?></u></p>
            </div>
            <div class="column column-right">
                <img src="<?php echo base_url(); ?>assets/admin/images/logo/hemsenlogo.png" alt="Hemsen Logo" width="120" />
            </div>
        </div>
        <hr>
        <div class="col-xl-12 col-lg-12">
            <div class="bold-text small-text"><p style="font-size: 12px;">Batch ID: <?php echo $arr_batch_details['batch_id'] ?></p></div>
            <?php 
            $row_count = 0;
            foreach ($arrGroupDocDetails as $acdm_id => $documentFileUploaded) { ?>
			<div class="column">
                    <div class="candidate-card">
                        <div class="candidate-info">
                            <span class="info-label">Name:</span> <?php echo $arrDocumentMaster[$acdm_id]; ?>
                        </div>
                        <div class="candidate-photo" style="text-align:center;"> 
                            <img style="width: 160mm; height: 200mm;" src="<?php echo base_url().$this->config->item('assessors_checklist_documents_path').$documentFileUploaded; ?>" alt="<?php echo $arrDocumentMaster[$acdm_id]; ?> Photo">
                        </div>
                    </div>
            </div>
			<?php 
			    $row_count++;
                if($row_count < count($arrGroupDocDetails)) {
                    echo "<pagebreak />"; //Page break
                } 
			} ?>
        </div>
    </div>
</body>
</html>
