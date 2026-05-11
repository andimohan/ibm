<?php
include '../../_config.php';
include '../../_include-v2.php';

includeClass('ChartOfAccount.class.php'); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$generalJournal = createObjAndAddToCol(new GeneralJournal());

include '_global.php';
 
$obj= $chartOfAccount;
$securityObject = 'reportTrialBalance';
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrFilterInformation = array();  
    
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array(); 

$arrDataStructure = array();
	
if(!isset($_POST['isNormalBalance'])) $_POST['isNormalBalance'] = true;

$hideEmptyAmount =  (isset($_POST['isHideEmptyAmount']) && !empty($_POST['isHideEmptyAmount'])) ? true : false;
$normalBalance =  (isset($_POST['isNormalBalance']) && !empty($_POST['isNormalBalance'])) ? true : false;

$arrSelType = array();
$arrSelType[1] = 'Normal';
$arrSelType[2] = 'Mutasi';
$arrSelType[3] = 'Mutasi (D/C)';
$arrSelType[4] = 'Saldo Akhir';
$arrSelType[5] = 'Saldo Akhir (D/C)';
 
// ==================================== ADD PERIOD COLUMN
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
    $_POST['trStartDate'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['trEndDate'] = date('F Y');  
}

$typeKey = $_POST['selType'];

$monthPeriod = $obj->getMonthPeriod($_POST['trStartDate'], $_POST['trEndDate']);

$arrKeyPeriod = array(); 
foreach ($monthPeriod as $dt) { 
    $keyIndex = $dt->format('n-Y');  
    $arrKeyPeriod[$keyIndex] = array('label' => $dt->format('M Y')); 
}

$arrDataStructure['coacode'] = array('title'=>ucwords($obj->lang['code']), 'width'=>"80px", 'dbfield' => 'code', 'format' => 'string', "sortable" => false);  
$arrDataStructure['coaname'] = array('title'=>ucwords($obj->lang['account']),'dbfield' => 'name', 'width'=>"280px","sortable" => false);  

foreach($arrKeyPeriod as $keyPeriod => $period){ 
    
	$arrSet = array();
	$arrSet['beginningBalance'] = array('title'=>ucwords($obj->lang['beginningBalance']),'group' => $period['label'], 'dbfield' => 'beginningbalance'.$keyPeriod, 'width'=>"90px" ,"format" => 'accounting',  "sortable" => false);
	$arrSet['debit'] = array('title'=>ucwords($obj->lang['debit']),'group' => $period['label'],'dbfield' => 'debit'.$keyPeriod, 'width'=>"90px","format" => 'accounting', "sortable" => false);
	$arrSet['credit'] = array('title'=>ucwords($obj->lang['credit']),'group' => $period['label'],'dbfield' => 'credit'.$keyPeriod,'style' => 'test', 'width'=>"90px","format" => 'accounting', "sortable" => false);
	$arrSet['endingBalance'] = array('title'=>ucwords($obj->lang['endingBalance']),'group' => $period['label'],'dbfield' => 'endingbalance'.$keyPeriod, 'width'=>"90px","format" => 'accounting', "sortable" => false, "textColor" => '568203');
	$arrSet['endingBalanceDebit'] =   array('title'=>ucwords($obj->lang['debit']),'group' => $period['label'],'dbfield' => 'endingBalanceDebit'.$keyPeriod, 'width'=>"90px","format" => 'accounting', "sortable" => false);
	$arrSet['endingBalanceCredit'] =array('title'=>ucwords($obj->lang['credit']),'group' => $period['label'],'dbfield' => 'endingBalanceCredit'.$keyPeriod,'style' => 'test', 'width'=>"90px","format" => 'accounting', "sortable" => false); 
	$arrSet['mutation'] = array('title'=>ucwords($obj->lang['mutation']),'group' => $period['label'],'dbfield' => 'mutation'.$keyPeriod, 'width'=>"90px","format" => 'accounting', "sortable" => false);
		
    switch($typeKey){
            
        case 1: 
            $arrDataStructure['beginningBalance'.$keyPeriod] = $arrSet['beginningBalance'];
            $arrDataStructure['debit'.$keyPeriod] = $arrSet['debit'];
            $arrDataStructure['credit'.$keyPeriod] = $arrSet['credit'];
            $arrDataStructure['endingBalance'.$keyPeriod] =  $arrSet['endingBalance'];
            break; 
		case 2: 
            $arrDataStructure['mutation'.$keyPeriod] = $arrSet['mutation']; 
            break; 
		case 3: 
            $arrDataStructure['debit'.$keyPeriod] = $arrSet['debit'];
            $arrDataStructure['credit'.$keyPeriod] = $arrSet['credit'];
            break; 
        case 4: 
           	$arrDataStructure['endingBalance'.$keyPeriod] =  $arrSet['endingBalance'];
            break; 
        case 5: 
            $arrDataStructure['endingBalanceDebit'.$keyPeriod] =	$arrSet['endingBalanceDebit']; 
            $arrDataStructure['endingBalanceCredit'.$keyPeriod] = 	$arrSet['endingBalanceCredit']; 
			break;
       
    }
}

/*
$arrDataStructure['totalTrip'] = array('title'=>ucwords($obj->lang['trip']),'group' => $obj->lang['total'], 'dbfield' => 'totaltrip', 'width'=>"20px" ,"align" => 'center', "sortable" => false,"format" => 'number', "style" => 'font-weight:bold');
$arrDataStructure['totalRevenue'] = array('title'=>ucwords($obj->lang['revenue']),'group' => $obj->lang['total'], 'dbfield' => 'totalrevenue', 'width'=>"60px" ,"format" => 'number', "sortable" => false, "textColor" => '568203', "style" => 'font-weight:bold');
*/

// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['trialBalanceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
    
$criteria = '';
    
$arrCOA = $class->convertForCombobox($chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey',1,true,' and ' . $chartOfAccount->tableName .'.isleaf = 1' ),'pkey','coaname');

$arrTwigVar['inputIsHideEmptyAmount'] =  $class->inputCheckBox('isHideEmptyAmount');
$arrTwigVar['inputNormalBalance'] =  $class->inputCheckBox('isNormalBalance');
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));    
$arrTwigVar['inputCOAName'] =  $class->inputSelect('selCOA[]', $arrCOA, array( 'etc' => 'multiple="multiple"',  'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelType'] =  $class->inputSelect('selType', $arrSelType); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
//$arrTwigVar['exportExcel'] = false;
 

if (isset($_POST) && !empty($_POST['hidAction'])){    
     
    $order = 'order by orderlist asc';
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
    }

    if(isset($_POST) && !empty($_POST['selCOA'])) { 
        
        $arrCOA = $_POST['selCOA'];
        
        // key intersect sama hak akses COA
        $arrCOAAccess = $employee->getCOAAccess();
        $arrCOA = array_intersect($arrCOA, $arrCOAAccess);
          
        if(!empty($arrCOA)){ 
            $criteria .= ' AND '.$obj->tableName.'.pkey in('.$class->oDbCon->paramString($arrCOA,',').')';   
            $rsCOA = $obj->searchData('','',true,$criteria,$order);
        } 
        
	}else{ 
          $rsCOA = $obj->searchData('','',true,$criteria,$order);
    }
    

    //$rsCOA =  $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in(1,2)','order by orderlist asc' );   
    $arrCOAKey = array_column($rsCOA,'pkey');
    $arrCOA = array_column($rsCOA,null,'pkey');
  
    // sebenrnya sum account sebelum tgl X / saldo awal per tgl X
    $arrStartingBalance = $generalJournal->sumAccount($arrCOAKey,'',date('01 / m / Y',strtotime($_POST['trStartDate'])) );  
    $arrDebitCredit = $generalJournal->sumAccount($arrCOAKey, date('01 / m / Y',strtotime($_POST['trStartDate'])),date('01 / m / Y',strtotime($_POST['trEndDate'] . ' + 1 month')),true );  
   
	$tempreport = ''; 
    
    $totalRs = count($rs);
    
    foreach($rsCOA as $key=>$row){   
        if($row['isleaf'] == 0) continue;
        
        $arrHeaderStyle = array();  
         
         //saldo awwal 
        $firstPeriod = true;
		$coaHasAmount = false;
		
        foreach($arrKeyPeriod as $keyPeriod => $period){ 
 
            if($row['isleaf'] == 0){
                $arrHeaderStyle['code']['fontWeight'] = 'bold'; 
                $arrHeaderStyle['name']['fontWeight'] = 'bold'; 
                
                $row['beginningbalance'.$keyPeriod] = '';
                $row['debit'.$keyPeriod] = '';
                $row['credit'.$keyPeriod] = '';
                $row['endingbalance'.$keyPeriod] = '';  
            }else{
                
                $debitCredit = isset($arrDebitCredit[$row['pkey']]) ? $arrDebitCredit[$row['pkey']] : array();
                $arrDC =  array_column($debitCredit,null,'dateindex');

                if($firstPeriod)  
                    $beginningBalance =  (isset($arrStartingBalance[$row['pkey']][0]['balance'])) ?  $arrStartingBalance[$row['pkey']][0]['balance'] : 0;

                $debit = (isset($arrDC[$keyPeriod]['debit'])) ? $arrDC[$keyPeriod]['debit'] : 0;
                $credit =  (isset($arrDC[$keyPeriod]['credit'])) ? $arrDC[$keyPeriod]['credit'] : 0;
                $endingBalance = $beginningBalance + $debit - $credit;
                      
                if($endingBalance > 0){
                    $row['endingBalanceDebit'.$keyPeriod] =  $endingBalance;
                    $row['endingBalanceCredit'.$keyPeriod] = 0;
                }else{
                    $row['endingBalanceDebit'.$keyPeriod] = 0;                    
                    $row['endingBalanceCredit'.$keyPeriod] = $endingBalance * -1; 
                }

                // nanti diupdate lg, pake metode lain, kalo kaya gini, kalo di bulan awal gk ad transaksi, gk muncul sama sekali
                // kalo gk ad transaksi sama sekali, lewatin saja 
                //if($beginningBalance == 0 && $debit == 0 && $credit == 0) continue 2;
                    
                $row['beginningbalance'.$keyPeriod] = ($arrCOA[$row['pkey']]['debittype'] == -1 && !$normalBalance) ?  ($beginningBalance * -1) : $beginningBalance;
                $row['debit'.$keyPeriod] = $debit;
                $row['credit'.$keyPeriod] = $credit;
                $row['endingbalance'.$keyPeriod] = ($arrCOA[$row['pkey']]['debittype'] == -1 && !$normalBalance) ? ($endingBalance * -1) : $endingBalance;
				 
				$mutation = $debit - $credit;
				$row['mutation'.$keyPeriod] =  ($arrCOA[$row['pkey']]['debittype'] == -1 && !$normalBalance) ? ($mutation*-1) : $mutation; 

                $beginningBalance = $endingBalance;
                $firstPeriod = false;
				 
				if($hideEmptyAmount){
					switch($typeKey){

						case 1:   
							if($debit != 0 || $credit != 0 || $beginningBalance != 0 || $endingBalance != 0 ) $coaHasAmount = true; 
							break; 
						case 2:  
							if($mutation != 0 ) $coaHasAmount = true; 
							break; 
						case 3: 

							if($debit != 0 || $credit != 0 ) $coaHasAmount = true;
							break; 
						case 4:  
							if($endingBalance != 0 ) $coaHasAmount = true;  
							break; 
						case 5:  
							if($row['endingBalanceDebit'.$keyPeriod] != 0 || $row['endingBalanceCredit'.$keyPeriod]) $coaHasAmount = true;
							break;

					}
				}

            } 
        } 
        
        if($hideEmptyAmount && !$coaHasAmount) continue;
		
        $return = $obj->formatReportRows(array('data' => $row, 'style' => $arrHeaderStyle),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
             
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar); 
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportTrialBalance.html', $arrTwigVar);   
?>