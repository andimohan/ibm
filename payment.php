<?php 

// sementara xendit dulu

if(!isset($_GET['code']) || empty($_GET['code'])) {
	header('location: /'); 	
	die;
}

require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

$url = '';

switch($_GET['module']){
		
		case 'subscription' :
				includeClass(array('MembershipSubscription.class.php'));
				$membershipSubscription = new MembershipSubscription(); 

				if(!$membershipSubscription->checksumMD5($_GET['code'],$_GET['checksum'])){ 
					header('location: /'); 	
					die;
				}

				$rs = $membershipSubscription->searchDataRow(array( $membershipSubscription->tableName.'.paymentgatewayinvoiceurl'),
															 ' and '.$membershipSubscription->tableName.'.code = ' . $membershipSubscription->oDbCon->paramString($_GET['code'])
															);
		
				$url = $rs[0]['paymentgatewayinvoiceurl'];

				break;
		
		
		case 'sales-order' :
				includeClass(array('SalesOrder.class.php'));
				$salesOrder = new SalesOrder(); 

				if(!$salesOrder->checksumMD5($_GET['code'],$_GET['checksum'])){ 
					header('location: /'); 	
					die;
				}

				$rs = $salesOrder->searchDataRow(array( $salesOrder->tableName.'.paymentgatewayinvoiceurl'),
															 ' and '.$salesOrder->tableName.'.code = ' . $salesOrder->oDbCon->paramString($_GET['code'])
															);
		
				$url = $rs[0]['paymentgatewayinvoiceurl'];

				break;
		
		default :
				break;
}



// ambil informasi nilai yg mau dicharge

// compile jd invoice
 
$arrTwigVar['paymentURL'] = $url; 
echo $twig->render('payment.html', $arrTwigVar);

?>