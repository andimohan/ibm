<?php

class InvestorReport extends BaseClass
{

   function __construct()
   {

      parent::__construct();

        $this->tableName      = 'investor_report';
        $this->tableCategory      = 'investor_report_category';
        $this->securityObject = 'InvestorReport';
        $this->tableLangValue = 'investor_report_lang';
        $this->tableStatus    = 'master_status';
        $this->uploadFolder = 'investor-report/'; 
        $this->uploadFileFolder = 'investor-report-file/'; 

          $arrDetails = array();
          array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code']        = array('code');
        $this->arrData['trdate']      = array('trDate', 'date');
        $this->arrData['yearperiod']  = array('selYearPeriod');
        $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
        $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
        $this->arrData['title']       = array('title');
        $this->arrData['shortdesc']   = array('txtShortDescription');
        $this->arrData['categorykey'] = array('selCategory');
        $this->arrData['description'] = array('txtDescription');
        $this->arrData['widget'] = array('txtWidget','raw');
        $this->arrData['statuskey']   = array('selStatus');
        $this->arrData['alwaysshow']   = array('chkAlwaysShow');
        $this->arrData['tableformat']   = array('chkTableFormat');
       

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Judul', $this->tableName . '.title')); 
      array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory . '.name'));
      array_push($this->arrSearchColumn, array('Deskripsi', $this->tableName . '.shortdesc'));


      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100,'align'=>'center','format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'period', 'title' => 'period', 'dbfield' => 'yearperiod', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'title', 'title' => 'title', 'dbfield' => 'title', 'default' => true, 'width' => 250));
      array_push($this->arrDataListAvailableColumn, array('code' => 'shortdesc', 'title' => 'shortDescription', 'dbfield' => 'shortdesc', 'default' => true, 'width' => 300));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


      $this->newLoad = true;

      $this->includeClassDependencies(
         array(
     
         )
      );


   }

   function getQuery()
   {

      return '
				select
					' . $this->tableName . '.*,
                    '.$this->tableCategory.'.name as categoryname,
					' . $this->tableStatus . '.status as statusname 
				from 
					' . $this->tableName .'
                        left join  '.$this->tableCategory.' on ' . $this->tableName . '.categorykey = '.$this->tableCategory.'.pkey, 
               ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

   }

   function validateForm($arr, $pkey = '')
   {

      $arrayToJs = parent::validateForm($arr, $pkey);
      $name      = $arr['title'];
      

      $rs = $this->isValueExisted($pkey, 'title', $name);
      if (empty($name)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][1]);
      } else if (count($rs) <> 0) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['title'][2]);
      }


      return $arrayToJs;
   }
 
    function generateTable($id){
     
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx'); 
        $reader->setReadDataOnly(true); 
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.file'),
                                   ' and '.$this->tableName.'.pkey = '. $this->oDbCon->paramString($id)
                                   );
        
        if(empty($rs)) return '';
        
        $inputFileName = DEFAULT_DOC_UPLOAD_PATH.$this->uploadFileFolder.$rs[0]['pkey'].'/'.$rs[0]['file']; 
        
        if (!is_file($inputFileName)) return '';
        
	    $spreadsheet = $reader->load($inputFileName); 

	    $totalSheet = $spreadsheet->getSheetCount();
        
        $returnVal = '';
        $arrSheetName = array();
        
        for($i=0;$i<$totalSheet;$i++){ 
            
            $worksheet = $spreadsheet->getSheet($i);
             
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); // e.g. 5
   
            array_push($arrSheetName,  $spreadsheet->getSheetNames()[$i]);
            
            $returnVal .= '{{ TABLE_TITLE_'.$i.' }}';
            $returnVal .= '<table >';
            $returnVal .= '{{ TABLE_HEADER_'.$i.' }}';
             
            for ($row = 1; $row <= $highestRow; $row++) { 
                $returnVal .= '<tr>';
                for ($col =1; $col <= $highestColumnIndex; $col++) {  
                    $returnVal .= '<td>'.trim($worksheet->getCellByColumnAndRow($col, $row)->getValue()).'</td>';
                }
                
                $returnVal .= '</tr>';
            }
            
            
            
            $returnVal .= '</table>'; 
            $returnVal .= '<div style="clear:both; height: 2em"></div>';
        } 
         
        return array('sheetTitle' => $arrSheetName, 'content' => $returnVal);
        
    }

   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData);
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

}

?>