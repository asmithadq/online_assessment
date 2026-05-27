<style>
body{ margin-top:10px;
}
</style>
<table width="100%" border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td colspan="4" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #333;">
                 				  <tr>
                    <td width="103" align="right" valign="top">
                        <?php
                        if($arr_batch_details[0]['ssc_logo'] !== "" && file_exists('./uploads/ssc_logo/'.$arr_batch_details[0]['ssc_logo'])) {
                            
                        ?>
                        <img src="<?php echo base_url(); ?>uploads/ssc_logo/<?php echo $arr_batch_details[0]['ssc_logo']; ?>" alt="logo" width="100"/>
                        <?php
                        }
                        ?>
                    </td>
                      <td width="319" align="center" style="font-size:12px; font-family:arial;">
                        <h3 align="center"><u><?php echo $arr_batch_details[0]['ag_name']; ?></u></h3>
                        <h4 align="center"><u>Attendance Sheet</u></h4>
                       </td>
                    
                    <td width="160" align="center" style="font-size:14px; font-family:arial; ">   
                        <img src="<?php echo base_url(); ?>assets/admin/images/logo/hemsenlogo.png" alt="logo" width="100" />
                      
                        
                    </td>
                  </tr>
                </table></td>
              </tr>      
              
              
</table>             
              
