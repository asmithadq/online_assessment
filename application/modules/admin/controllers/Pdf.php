<?php defined('BASEPATH') or exit('No direct script access allowed');
require_once FCPATH . '/vendor/autoload.php';

class Pdf extends MY_Controller
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
    /*public function generate()
    {
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML('<h1>Hello world!</h1>');
        $mpdf->Output();
    }*/
    
    public function generate() {
        // Input PDF path (replace with your actual path)
        $inputPdfPath = $_SERVER['DOCUMENT_ROOT'].'/online_assessment/uploads/sample.pdf';
        //echo $inputPdfPath;exit;

        // Output PDF path (replace with your desired path)
        $outputPdfPath = $_SERVER['DOCUMENT_ROOT'].'/online_assessment/uploads/qp_sample_watermark.pdf';

        // Watermark text (replace with your desired watermark)
        $watermarkText = 'Confidential';

        // Generate PDF with watermark
        $this->generatePdfWithWatermark($inputPdfPath, $outputPdfPath, $watermarkText);
    }
	
	public function generatePdfWithWatermark($inputPdfPath, $outputPdfPath, $watermarkText)
    {
        $mpdf = new \Mpdf\Mpdf();
        
        $mpdf->SetDisplayMode('fullpage');
        $pagecount = $mpdf->setSourceFile($inputPdfPath);
        for ($i = 1; $i <= $pagecount; $i++) {
            $tplId = $mpdf->importPage($i);
            $mpdf->UseTemplate($tplId);
            $mpdf->SetWatermarkText('CONFIDENTIAL');
            $mpdf->showWatermarkText = true;
            $mpdf->AddPage();
        }
        $mpdf->Output($outputPdfPath, \Mpdf\Output\Destination::FILE); 
        
        // Output the PDF
        //$mpdf->Output($outputPdfPath, 'F');
    }
    
}
