<?php   
include_once '../_config.php';  
include_once '../_include.php';
include '_global.php';

$obj = $truckingServiceWorkOrder; 
$rsWorkOrder = $obj->searchData('','',true, '',' and '.$obj->tableName.'.statuskey in(1,2) order by '.$obj->tableName.'.code desc');

$rsCarCheklist = $carChecklist->searchData($carChecklist->tableName.'.statuskey',1);
foreach($rsCarCheklist as $key=>$row){ 
    $options = array();
    array_push($options,array('value' => '1' ));
    array_push($options,array('value' => '2' ));  

    $rsCarCheklist[$key]['inputRdbCondition'] = $obj->inputRadio('rdbCondition'.$row['pkey'], array('optionItems' => $options));  
    $rsCarCheklist[$key]['inputHiddenPkey'] = $obj->inputHidden('hidConditionCarKey'.$row['pkey'],array('value' => $row['pkey']));  
    $rsCarCheklist[$key]['inputDescription'] = $obj->inputText('description'.$row['pkey'], array('etc' => 'style="height: 5em"'));  
} 

//$rsAllProgressStep = $workProgressStep->searchData($workProgressStep->tableName.'.statuskey',1,true,'order by orderlist asc');  
 //$carRegistrationNumber = (isset($_GET['carRegistrationNumber']) || !empty($_GET['carRegistrationNumber'])) ? $car->normalizePoliceNumber($_GET['carRegistrationNumber']) : '';
foreach($rsWorkOrder as $key=>$row){ 
    $_POST['carRegistrationNumber[]'] = $row['policenumber']; 
    $rsWorkOrder[$key]['inputCarRegistrationNumber'] = $obj->inputText('carRegistrationNumber[]',array('etc' => 'placeholder="'.$obj->lang['carRegistrationNumber'].'"'));
}

$arrTwigVar['rsWorkOrder'] = $rsWorkOrder;  
$arrTwigVar['rsCarChecklist'] = $rsCarCheklist;   
$arrTwigVar['inputDesc'] = $obj->inputTextArea('trConditionDesc',array('etc' => 'style="height:10em;"')); 
$arrTwigVar['btnSave'] = $obj->inputButton('btnSave',$obj->lang['submit']);
$arrTwigVar['btnProceed'] = $obj->inputButton('btnProceed',$obj->lang['proceed']);

echo $twig->render('jobList.html', $arrTwigVar); 
?>
