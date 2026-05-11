<?php

// sementara hanya support satu jenis marketplace setiap print
// dan hanya diambil transaksi pertma kalo print beberapa

if(empty($_GET['id'])) die;

require_once '../../_config.php';  
require_once '../../_include-v2.php'; 
require_once($DOC_ROOT.'assets/PDFMerger.php');
 
ob_start();

includeClass('SalesOrder.class.php');   
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$marketplace = createObjAndAddToCol( new Marketplace()); 

$obj = $salesOrder;
 
$securityObject = $obj->securityObject;
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();

$rs = $obj->searchDataRow(array($obj->tableName.'.refcode', $obj->tableName.'.marketplacekey'),
                          ' and '.$obj->tableName.'.pkey in('.$obj->oDbCon->paramString($arrID,',').')');
if(empty($rs)) die;

$arrRefCode = array_column($rs,'refcode');
     

if($rs[0]['marketplacekey'] <> 0){

    $marketplaceObj = $marketplace->getMarketplaceObj($rs[0]['marketplacekey']);   
    if(empty($marketplaceObj)) return;
    
    $responses = $marketplaceObj[0]['obj']->getAirwayBill($arrRefCode);   
         
    switch($rs[0]['marketplacekey']){
             case MARKETPLACE['lazada']:   
                    header('Content-type: text/html;');  
                    $html =  base64_decode($responses['file']);   
                    $html = '<style>.awb.lex { width: 96% !important;}</style>' . $html;
                    echo $html;
            
                    break;
             case MARKETPLACE['shopee']:
                     header('Content-type: application/pdf;'); 
                     header('Content-Disposition: inline;'); 
                     echo $responses;
                    // // biar cepet aj 
                    // if(count($responses) == 1){
                    //     $response = $responses[0];
                        
                    //     $url = $response['airway_bill']; 
                        
                    //     $curl = curl_init(); 
                    //     curl_setopt_array($curl, array(
                    //         CURLOPT_URL => $url,
                    //         CURLOPT_RETURNTRANSFER => 1
                    //     ));
        
                    //     $resp = curl_exec($curl);
                    //     curl_close($curl);
 
                    //     echo $resp; 
                        
                    // } else{
                    //     $pdf = new PDFMerger;
                    //     $ctr = 0;
                    //     foreach($responses as $response){   
                    //         if (empty($response)) continue;
                            
                    //         $url = $response['airway_bill']; 

                    //         $curl = curl_init(); 
                    //         curl_setopt_array($curl, array(
                    //             CURLOPT_URL => $url,
                    //             CURLOPT_RETURNTRANSFER => 1
                    //         ));

                    //         $resp = curl_exec($curl);
                    //         curl_close($curl);

                    //         // save dulu
                    //         $fileName = time().rand().$ctr++;
                    //         $filePath = DOC_ROOT.'_temp/'.$fileName.'.pdf';
 
                    //         file_put_contents($filePath, $resp); 
                    //         fclose($docFile);

                    //         $pdf->addPDF($filePath);  

                    //     }

                    //   $pdf->merge('browser');
                    // }       
             
            
                    break; 
                    
             case MARKETPLACE['tokopedia']:  
                     $ctr = 1;
                    foreach($responses as $response) { 
                       
                       $fileName = time().rand().$ctr++;
                       $docFile = fopen(DOC_ROOT.'_temp/'.$fileName.'.html', "w"); 
                       $urlFile =  HTTP_HOST.'_temp/'.$fileName.'.html';
                       fwrite($docFile, $response);
                       fclose($docFile);
                        
                       echo '<iframe src="'.$urlFile.'" style="width:95%; height: 20em; border:0; border-bottom:1px solid #ccc; padding:0.5em 0"></iframe>'; 
                    }
            
                    break; 
            
    } 
} 
    
?>