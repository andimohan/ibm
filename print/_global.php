<?php
//ob_end_clean(); 


// Include the main TCPDF library (search for installation path). 
require_once($DOC_ROOT.'assets/tcpdf/tcpdf_include.php');
   
function addNewPDFPage($pdf,$obj, $defaultGenerateContent = '',$opt = array()){
	
    global $class;
    global $customCode;
    global $DISABLE_FILE_CUSTOM;

    
    //cek ad template custom gk   

    // OVERWRITE VARIABLE
    $PDF_MARGIN_TOP = 38;

    $PDF_MARGIN_HEADER = PDF_MARGIN_HEADER; 
    
    // ================================= OVERWRITE FILE IF EXIST 
    $fileName = $_GET['filename']; 
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileName = (empty($ext)) ? $fileName .'.php' : $fileName;
        
    //cek FILE di custom code
     
    if (!isset($DISABLE_FILE_CUSTOM) || !$DISABLE_FILE_CUSTOM) { 
        $rsKey = $class->getTableKeyAndObj($obj->tableName);
        $tablekey = $rsKey['key']; 
        $rsCustomCode  = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'], true,'','order by pkey asc');  

        // kalo ada custom print file langsung
        if (isset($opt['printFile']) && !empty($opt['printFile'])){ 
            $fileName = $opt['printFile'];
        }else if (!empty($rsCustomCode)){
            $rs = $pdf->dataset['rs'];
            $rsTemp = array_column($rsCustomCode, 'printfile','pkey'); 
            $temp = (isset($rs[0]['customcodekey'])) ? $rsTemp[$rs[0]['customcodekey']] : $rsCustomCode[0]['printfile'];

            $fileName = (!empty($temp)) ? $temp : $fileName;    
        }
         
    }
    
    //$filePath = DOC_ROOT.'admin/print/'.DOMAIN_NAME.'/'. $fileName;  
    $filePath = PERSONALIZED_DOC_PATH.'print/'. $fileName;
	
    if (is_file($filePath))
        include $filePath;
    // ================================= OVERWRITE PARAMETER
     
    $CUSTOM_SETTINGS = $pdf->customSettings;
     
    $SHOW_PRINT_HEADER = isset($CUSTOM_SETTINGS['showPrintHeader']) ? $CUSTOM_SETTINGS['showPrintHeader']  : true; 
    $SHOW_PRINT_FOOTER = isset($CUSTOM_SETTINGS['showPrintFooter']) ? $CUSTOM_SETTINGS['showPrintFooter']  : true; 
          
    //$class->setLog( ($SHOW_PRINT_HEADER) ? "header" : 'none');
    //$class->setLog( ($SHOW_PRINT_FOOTER) ? "footer" : 'none');
        
    if(isset($CUSTOM_SETTINGS['paperSetting'])) 
     $PAPER_SETTING = $CUSTOM_SETTINGS['paperSetting'];  

// deprecated
//    if(isset($CUSTOM_SETTINGS['pdfMarginTop'])) 
//     $PDF_MARGIN_TOP = $CUSTOM_SETTINGS['pdfMarginTop'];  

    
    if(isset($CUSTOM_SETTINGS['pdfMarginHeader'])){ 
     $PDF_MARGIN_HEADER = $CUSTOM_SETTINGS['pdfMarginHeader']; 
     //$PDF_MARGIN_TOP = $PDF_MARGIN_HEADER; // yg ini gk yakin
    }
 
    $PDF_MARGIN_FOOTER = (isset($CUSTOM_SETTINGS['marginFooter'])) ? $CUSTOM_SETTINGS['marginFooter'] : PDF_MARGIN_FOOTER;    
       
	// hilangin px
	$PDF_MARGIN_FOOTER = intval(str_replace('px','',$PDF_MARGIN_FOOTER));
	
    $FONT_NAME = (isset($CUSTOM_SETTINGS['fontName'])) ? $CUSTOM_SETTINGS['fontName'] : PDF_FONT_NAME_MAIN;    
    
    // ================================= OVERWRITE PARAMETER
  
     
    // set margins  
    $pdf->SetHeaderMargin($PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin($PDF_MARGIN_FOOTER); // tanpa PX harusnya

    if ($SHOW_PRINT_HEADER){  
        $pdf->SetMargins(PDF_MARGIN_LEFT, $PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->setPrintHeader(true);
    }else{ 
        $pdf->SetMargins(PDF_MARGIN_LEFT, $PDF_MARGIN_HEADER, PDF_MARGIN_RIGHT);
        $pdf->setPrintHeader(false);
    }

    
    if ($SHOW_PRINT_FOOTER)  
        $pdf->setPrintFooter(true);
    else
        $pdf->setPrintFooter(false);
          

    // set auto page breaks
    // PDF_MARGIN_BOTTOM
    //$pdf->SetAutoPageBreak(TRUE, 16);
      
    $pdf->SetAutoPageBreak(TRUE, ($PDF_MARGIN_FOOTER + 6));
 

    // Set font
    // dejavusans is a UTF-8 Unicode font, if you only need to
    // print standard ASCII chars, you can use core fonts like
    // helvetica or times to reduce file size. 
     
    $PDF_FONT_SIZE_MAIN = 9;
    $pdf->SetFont($FONT_NAME, '', $PDF_FONT_SIZE_MAIN, '', true);
 
      
    // ================================= create new PDF document
    
    $pdf->startPageGroup();  
    
    $paperSize = PDF_PAGE_FORMAT;
    $paperOrientation = PDF_PAGE_ORIENTATION;

    // load default paper size settings
    if (!isset($PAPER_SETTING))
        $PAPER_SETTING = $class->loadSetting('pageSize');

    if(!empty($PAPER_SETTING)){
        $arrTemp = explode(',',$PAPER_SETTING);
        $paperSize = trim($arrTemp[0]);

        if (isset($arrTemp[1]) && !empty(trim($arrTemp[1]))) $paperOrientation = trim($arrTemp[1]);
    }
      
    $pdf->AddPage($paperOrientation,$paperSize);  
 
    
    if(isset($generateReportContent)) 
        $pdf->printPDFHTML($generateReportContent); 
    else  
        $pdf->printPDFHTML($defaultGenerateContent);  
   
}
  
class MYPDF extends TCPDF {


   public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false){ 
        parent::__construct($orientation, $unit , $format , $unicode , $encoding , $diskcache , $pdfa );
        $this->contentWidth = '680'; 
        $this->customSettings = array();
        $this->reCacheSettings = true;
        $this->settingsCache = null;  
        $this->dataset = array(); 
    } 
    
    //Page header
    public function Header() {
        global $class; 
        global $PDF_MARGIN_HEADER; 
         
        if ($this->reCacheSettings){ 
            //$class->setLog("start caching ... ");
            $this->settingsCache = $this->customSettings;
            $this->reCacheSettings = false;
        }
        
/*        if (!$this->startCacheSettings){  
            $this->startCacheSettings = true;
        }*/
            
        //$class->setLog("header " . $this->customSettings['headerAlign']) ;
        
        // Logo  
        $profileImg = $class->loadSetting('companyLogo'); 
        $headerWidth = $this->contentWidth.'px';
        $logo = '';
        
        if (!empty($profileImg)){ 
             
            $img =  HTTP_HOST.'phpthumb/phpThumb.php?src='.$class->phpThumbURLSrc.'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg);
           
            $x = 10;
            $y = $PDF_MARGIN_HEADER;
            
            $w = 30;
            $h = 18;
            
            if(isset($this->customSettings['logoSize'])){
                $size = explode(',',$this->customSettings['logoSize']);
                $w = $size[0];
                $h = $size[1]; 
            } 
        
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $img);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $file = curl_exec($ch);
            curl_close($ch);
            
            //$this->Rect($x, $y, $w, $h, 'F', array(), array(128,255,255));
            $this->Image('@'.$file,$x,$y,$w,$h,'','','',false,300,'',false,false,0,'CM',false,false); 
             
            $logo = '  <td style="width:110px;"></td>
                       <td style="width:20px;"></td>
                    '; 
            $headerWidth = '550px';
        }
       
        $headerAlign = isset($this->customSettings['headerAlign']) ? $this->customSettings['headerAlign'] : 'left' ;

        $header = '<table style="border-bottom:1px solid #333; padding-bottom: 10px;"> 
                        <tr>
                            '.$logo.'
                            <td style="height:80px; width:'.$headerWidth.'; text-align:'.$headerAlign.';"><span style="font-weight:bold">'.$class->loadSetting('companyName').'</span><br><span>'.str_replace(chr(13),'<br>',$class->loadSetting('companyAddress')).'</span></td>
                        </tr>
                  </table>';
         
        
        $this->writeHTML($header);   
 
    }
    
    public function Footer() { 
        global $class;  
        global $employee;    
        
         
        $rs = $this->dataset['rs'];
        
        $page = $this->getGroupPageNo().'/'.$this->getPageGroupAlias();        
         
        $settingCache = $this->settingsCache;
        $this->reCacheSettings = true;
        if(isset($this->settingsCache['footer'])){   
            $html = $this->settingsCache['footer'];
            $html = str_replace('{{ GROUP_PAGE_NO }}', $page,$html);
            $this->writeHTML($html);   
            return;
        } 
        
 
        if(isset($this->customSettings['footer'])){
            $html = $this->customSettings['footer']; 
        }else{
            $employeeLogName = array();

            $rsEmployee = $employee->getDataRowById($rs[0]['createdby']);
            array_push($employeeLogName, 'Buat: ' .$rsEmployee[0]['name']); 

            if (!empty($rs[0]['confirmedby'])){ 
                $rsEmployee = $employee->getDataRowById($rs[0]['confirmedby']);
                array_push($employeeLogName, 'Konfirmasi: ' .$rsEmployee[0]['name']);
            } 
            
            $rsEmployee = $employee->getDataRowById(base64_decode($_SESSION[$employee->loginAdminSession]['id']));
            array_push($employeeLogName, 'Cetak: ' .$rsEmployee[0]['name']);

            $html = '<table style="width:'.$this->contentWidth.'px">
                <tr><td style="width:70%">'.implode('. ' ,$employeeLogName).'.</td><td style="width:30%; text-align:right">Status: '.ucwords($rs[0]['statusname']).'</td></tr>
                <tr><td>Halaman '.$page.'</td><td style="text-align:right;">'.date('d / m / Y H:i').'</td></tr>
            </table>'; 
        }
         
        $html = str_replace('{{ GROUP_PAGE_NO }}', $page,$html);
         
        $this->writeHTML($html);   
     }

    public function setCustomSettings($customSettings){  
            $this->customSettings = $customSettings;
    }
    

   public function printPDFHTML($generateReportContent){
        global $class;
       
        $firstPage = true;
        $newGroup = false;
     
            if(!is_array($generateReportContent))  $generateReportContent = array($generateReportContent);

            foreach($generateReportContent as $content){  
                 
                // cek ad index content gk, kalo ad dipisah arraynya
                if ( is_array($content) && isset($content['content']) ){ 
                    $newGroup = (isset($content['newGroup'])) ? $content['newGroup'] : false;    
                    $content = $content['content'];
                }


                $contentHTML = $content($this->dataset);
                
                 if (!is_array($contentHTML)) $contentHTML = array($contentHTML);

                 foreach($contentHTML as $html){   
                     if (empty($html)) continue;
                     
                     if(!$firstPage){ 
                         if ($newGroup)  $this->startPageGroup();  
                         $this->AddPage();
                     }
                     
                     $this->writeHTML($html); 
                     $firstPage = false;
                 }
             
            }
    }
    
 
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false); 

$pdf->setFooterData(array(0,0,0), array(0,0,0));

// set header and footer fonts 
 
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA)); 

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
// By default TCPDF enables font subsetting to reduce the size of embedded Unicode TTF fonts, 
// this process, that is very slow and requires a lot of memory, can be turned off using setFontSubsetting(false) method

$pdf->setFontSubsetting(false); 

?>