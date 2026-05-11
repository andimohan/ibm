<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('AR.class.php', 'Customer.class.php', 'NotificationLetter.class.php'));

$notificationLetter = new NotificationLetter();
$ar  = new AR();
$customer = new Customer();

$obj = $notificationLetter;

$monthLimit = 1;
$rsData = $ar->getOverdueOutstanding($monthLimit);
$rsDataCols = $obj->reindexDetailCollections($rsData, 'customerkey');


$rsCustomer = $customer->searchDataRow(array(
    $customer->tableName.'.pkey',
    $customer->tableName.'.code',
    $customer->tableName.'.name',
), ' and ' . $customer->tableName.'.statuskey = 2 and  ' . $customer->tableName.'.islinked = 1', 'order by pkey asc');


// $obj->setLog($rsDataCols, true);
if(!$obj->oDbCon->startTrans())
        throw new Exception($obj->errorMsg[100]);

$testLimit = 1;

$date = date('d / m / Y');
$dateLabel = $obj->toLocalDate(date('d F Y'));
$endOfMonth = $obj->toLocalDate(date('t F Y'));

foreach($rsCustomer as $row) {
    
    $customerkey = $row['pkey'];

    if(!isset($rsDataCols[$customerkey])) continue;

    $rsDataCol = $rsDataCols[$customerkey];
    
    try { 

        $arr = array();
        $arr['code'] = 'xxxxx';
        $arr['selStatus'] = 1;
        $arr['trDate'] = $date;
        $arr['selWarehouseKey'] = $rsDataCol[0]['warehousekey'];
        $arr['hidCustomerKey'] = $customerkey;
         

        $arrDetail['invoiceCode'] = array();
        $arrDetail['invoiceDate'] = array();
        $arrDetail['amount'] = array();
        
        $grandTotal = 0;
        foreach($rsDataCol as $data) { 
            array_push($arrDetail['invoiceCode'], $data['refcode']);
            array_push($arrDetail['invoiceDate'], $data['trdate']);
            array_push($arrDetail['amount'], $data['outstanding']);
            $grandTotal += $data['outstanding'];
        }
         

        $content = $obj->generateOverdueOutstandingLetter(array('amount' => $grandTotal,
                                                                         'trdate' => $dateLabel,
                                                                         'duedate' => $endOfMonth,
                                                                         'toName' => $row['name'],
                                                                         'ARDetail' => $arrDetail
                                                                        ));
        
//        $obj->setlog($content,true);
        
        $arr['txtDetail'] = trim(preg_replace('/\s\s+/', ' ', $content[0])); // harus dihapus enter agar gk muncul br otomatis
        
        
        $arr['hidDetailKey'] = array();
        $arr['txtDetailAttachment'] = array();
        array_push($arr['hidDetailKey'], 0);
        array_push($arr['txtDetailAttachment'], trim(preg_replace('/\s\s+/', ' ', $content[1])));
        
        $rs = $obj->addData($arr);
         
        $testLimit++;
        if($testLimit > 3) break;
        
    } catch (Exception $e) {
        $obj->oDbCon->rollback();
    }

}


$obj->oDbCon->endTrans();


// try {

// } catch (Exception $e) {
//     echo $e->getMessage() . '<br>';
// }
echo 'Done';

?>