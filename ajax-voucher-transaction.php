<?php 
 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

includeClass(array('VoucherTransaction.class.php'));

$voucherTransaction = new VoucherTransaction(); 

$obj = $voucherTransaction;    

include 'ajax-general.php';
if (isset($_GET) && !empty($_GET['action'])) {
		switch ( $_GET['action']){
                
        //case 'calculateVoucherValue' :   
        //    if (!isset($_GET) || empty($_GET['voucherkey']))  die;
        //    if (!isset($_GET) || empty($_GET['vouchertype']))  die; 
        //    
        //    if (isset($_GET) && empty($_GET['totalsales']))  $_GET['totalsales'] = 0;
        //    if (isset($_GET) && empty($_GET['totalshipment']))  $_GET['totalshipment'] = 0;
//
        //    $arrVoucher = $obj->calculateVoucherValue(array('voucherkey' => $_GET['voucherkey'], 
        //                                                      'vouchertype' => $_GET['vouchertype'], 
        //                                                     ),
        //                                                array('totalsales' =>  $_GET['totalsales'],
        //                                                      'totalshipment' =>  $_GET['totalshipment']
        //                                                     )
        //                                                );
//
//
        //    echo json_encode($arrVoucher);    
        //    break;

        case 'calculateVoucherValue':
                
            // sementara baru bisa multiple antara voucher sales dan shipment    
                
            if (!isset($_GET) || empty($_GET['voucherkey'])) die;
            if (!isset($_GET) || empty($_GET['vouchertype'])) die;
            
            if (isset($_GET) && empty($_GET['totalsales'])) $_GET['totalsales'] = 0;
            if (isset($_GET) && empty($_GET['totalshipment'])) $_GET['totalshipment'] = 0;
            
            $voucherKeys = explode(',', $_GET['voucherkey']);
            $voucherTypes = explode(',', $_GET['vouchertype']);
            
            $results = array();
            $totalSales = floatval($_GET['totalsales']);
            $totalShipment = floatval($_GET['totalshipment']);
                
            for ($i=0; $i<count($voucherKeys); $i++) {
                $voucherKey = trim($voucherKeys[$i]);
                $voucherType = trim($voucherTypes[$i]);
                //$categoryKey = trim($categoryKeys[$i]);
                
                if (empty($voucherKey)) continue;
                
                $arrVoucher = $obj->calculateVoucherValue(
                    array(
                        'voucherkey' => $voucherKey,
                        'vouchertype' => $voucherType
                    ),
                    array(
                        'totalsales' => $totalSales,
                        'totalshipment' => $totalShipment
                    )
                );
                $results[] = $arrVoucher;
                
                // bukan fungsinya
                //if ($arrVoucher) {
                //    if ($categoryKey == 2) { // penjualan
                //        $voucherAmount = floatval($arrVoucher['amount']);
                //        $totalSales -= $voucherAmount; 
                //        if ($totalSales < 0) $totalSales = 0;
                //    } else if ($categoryKey == 3) { // ongkir
                //        $voucherAmount = floatval($arrVoucher['amount']);
                //        $totalShipment -= $voucherAmount;
                //        if ($totalShipment < 0) $totalShipment = 0;
                //    }
                //    
                //    $results[] = $arrVoucher;
                //}
            }
            
            echo json_encode($results);
            break;
        }
}
die;
  
?>