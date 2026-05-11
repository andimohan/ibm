<?php

$obj = $chartOfAccount;
$securityObject = 'reportGeneralLedger'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$userkey = base64_decode($_SESSION[$security->loginAdminSession]['id']);

// test dulu UTK ETI / TEL
$balanceInPositive = $obj->loadSetting('GLAsPositiveBalance');
$balanceInPositive = ($balanceInPositive) ? -1 : 1;

$arrFilterInformation = array();  
$arrSelectedCOA = array();

$criteria = ''; 

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"100px", 'dbfield' => 'code');  
$arrDataStructure['accountCode'] = array('title'=>ucwords($obj->lang['accountCode']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['accountName'] = array('title'=>ucwords($obj->lang['accountName']),  'width'=>"250px", 'dbfield' => 'name'); 
$arrDataStructure['openingBalance'] = array('title'=>ucwords($obj->lang['openingBalance']), 'dbfield' => 'openingBalance', 'width'=>"150px" ,'format'=>'number', "sortable" => false);
$arrDataStructure['endingBalance'] = array('title'=>ucwords($obj->lang['endingBalance']), 'dbfield' => 'endingBalance', 'width'=>"150px" ,'format'=>'number', "sortable" => false, 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['generalLedgerReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
// detail ...
$arrDataDetailStructure = array(); 
$arrDataDetailStructure['trdate'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date'); 
$arrDataDetailStructure['journalCode'] = array('title'=>ucwords($obj->lang['journalCode']),  'dbfield' => 'code', 'width'=>"80px" );  
$arrDataDetailStructure['note'] = array('title'=>ucwords($obj->lang['note']),'dbfield' => 'trdesc', 'width'=>"250px" ); 
$arrDataDetailStructure['debit'] = array('title'=>ucwords($obj->lang['debit']),'dbfield' => 'debit', 'width'=>"100px",'format'=>'number', 'textColor' => '568203','calculateTotal' => true); 
$arrDataDetailStructure['credit'] = array('title'=>ucwords($obj->lang['credit']),'dbfield' => 'credit', 'width'=>"100px",'format'=>'number', 'textColor' => 'C41E3A','calculateTotal' => true); 
$arrDataDetailStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balance', 'width'=>"100px",'format'=>'number', 'textColor' => '568203'); 
$arrDataDetailStructure['reference'] = array('title'=>ucwords($obj->lang['reference']),'dbfield' => 'refcode', 'width'=>"130px" );  
$arrDataDetailStructure['warehouseName'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px" ); 

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){    
	  
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
	  
    $rsCOA = array(); 
    
    if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        $arrCOA = $_POST['selCOA'];
        
        // key intersect sama hak akses COA
        $arrCOAAccess = $employee->getCOAAccess();
        $arrCOA = array_intersect($arrCOA, $arrCOAAccess);
          
        if(!empty($arrCOA)){ 
            $criteria .= ' AND '.$obj->tableName.'.pkey in('.$class->oDbCon->paramString($arrCOA,',').')';   
            $rsCOA = $obj->searchData('','',true,$criteria,$order);
        } 
	}
    
     
	//$tableReport = ''; 
    //$date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d')).' -1 day'));
    $tempreport = '';

    if (empty($rsCOA))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
    
    $arrCOAKey = array_column($rsCOA,'pkey');
    
	$rsCol = $generalJournal->getJournalForGL($arrCOAKey,$_POST['trStartDate'],$_POST['trEndDate']);   
    $rsCol = $obj->reindexDetailCollections($rsCol,'coakey');
    
    //$date = date('d / m / Y',strtotime(str_replace('\'','',$obj->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d'))));
    $arrStartingBalance = $generalJournal->sumAccount($arrCOAKey,'',$_POST['trStartDate']); 
    
	foreach ($rsCOA as $coa) {  
        $detail = '';
        
        $rs = $rsCol[$coa['pkey']]; 
        $startingBalance = $arrStartingBalance[$coa['pkey']][0]['balance'];
            
        //$obj->setLog($coa['debittype'],true);
        if($coa['debittype'] == -1)  $startingBalance *= $balanceInPositive;
          
		$endingBalance = $startingBalance;
        
        $totalDebit = 0;
        $totalCredit = 0;
            
        $arrDetailStyle = array();
        
        // custom description
        //$glDesc = $generalJournal->getReportDescription($rs);
            
		for($i=0;$i<count($rs);$i++) {    
            $movement = $rs[$i]['debit'] -  $rs[$i]['credit']; 
            if($coa['debittype'] == -1)  $movement *= $balanceInPositive;
            
			$endingBalance = $endingBalance + $movement; 
             
            /*if(isset($glDesc[$rs[$i]['pkey']]) && !empty($glDesc[$rs[$i]['pkey']])){
                $desc = $glDesc[$rs[$i]['pkey']]; // kalo ad deskripsi dari detail jurnal bagaimana ?? 
            }else{
                $desc = array();

                if(!empty($rs[$i]['trdesc']))  array_push($desc,$rs[$i]['trdesc']);  
                if(!empty($rs[$i]['headerdesc']))  array_push($desc,(empty($desc)) ? $rs[$i]['headerdesc'] : chr(13).$rs[$i]['headerdesc']  );
                $desc = implode('<br>',$desc);
            }*/
            
            $desc = array();

            if(!empty($rs[$i]['headerdesc']))  array_push($desc,$rs[$i]['headerdesc'] );
            if(!empty($rs[$i]['trdesc']))  array_push($desc,$rs[$i]['trdesc']);  
            $desc = implode('<br>',$desc);
            
            $rs[$i]['balance'] = $endingBalance;
            if(!empty($rs[$i]['detailrefcode']))
                $rs[$i]['refcode'] .= '<br>'.$rs[$i]['detailrefcode'];
           
            $rs[$i]['trdesc'] = $desc;
			    
            if (isset($rs[$i]['balance']) && $rs[$i]['balance'] < 0)  
                $arrDetailStyle[$i]['balance']['textColor'] = 'C41E3A';  
		}
        
        $coa['openingBalance'] = $startingBalance;
        $coa['endingBalance'] = $endingBalance;
        
        $coa['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rs, 'style' => $arrDetailStyle);
        $return = $obj->formatReportRows(array('data' => $coa),$arrTemplate); 
        
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
		 
	}

    //$class->setLog(getPerformanceLog($start_time),true);
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
    
}else if (isset($_GET) && !empty($_GET['coakey'])){ 
    array_push($arrSelectedCOA,$_GET['coakey']);
     
    $_POST['trEndDate'] = $obj->formatDBDate($_GET['endDate']); //date('d / m / Y');  
    $_POST['trStartDate'] = $obj->formatDBDate($_GET['startDate']); // date('01 / 01 / Y');   
   
}else{ 
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');  
}
 
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and ' . $chartOfAccount->tableName .'.isleaf = 1' ),'pkey','coaname');
   
$arrTwigVar['inputCOAName'] =  $class->inputSelect('selCOA[]', $arrCOA, array( 'etc' => 'multiple="multiple"','value' => $arrSelectedCOA, 'class' => 'multi-selectbox')); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc'=>' style="text-align:center"'));  
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc'=>' style="text-align:center"'));    
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;        
echo $twig->render('reportGeneralLedger.html', $arrTwigVar);   
?>