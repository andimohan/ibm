<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('VoucherTransaction.class.php'));
$voucherTransaction = new VoucherTransaction();

$obj = $voucherTransaction;   
$fieldValue = $obj->tableName.'.code'; 

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                    
                case 'getAvailableVoucher' : 
                    
                    if (!isset($_GET) || empty($_GET['customerkey']))  die;
                    
                    $rs = $obj->getAvailableVoucher($_GET['customerkey']);
                    echo json_encode($rs); 
                    break;
                    
   				case 'calculateVoucherValue' :   
                    if (!isset($_GET) || empty($_GET['voucherkey']))  die;
                    if (!isset($_GET) || empty($_GET['vouchertype']))  die;

                    if (isset($_GET) && empty($_GET['totalsales']))  $_GET['totalsales'] = 0;
                    if (isset($_GET) && empty($_GET['totalshipment']))  $_GET['totalshipment'] = 0;
                    
                    $voucherValue = $obj->calculateVoucherValue(array('voucherkey' => $_GET['voucherkey'], 
                                                              'vouchertype' => $_GET['vouchertype'], 
                                                             ),
                                                        array('totalsales' =>  $_GET['totalsales'],
                                                              'totalshipment' =>  $_GET['totalshipment']
                                                             )
                                                        );
            
                    echo $voucherValue; 
                    break;
                     
            }
    
}
 
die;
  
?>