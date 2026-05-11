<?php

// sementara satu sja dulu 

if(empty($_GET['id'])) die;

require_once '../../_config.php';  
require_once '../../_include-v2.php'; 
//require_once($DOC_ROOT.'assets/PDFMerger.php');
 
//ob_start();

includeClass('SalesOrder.class.php');   
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$marketplace = createObjAndAddToCol( new Marketplace()); 

$obj = $salesOrder;
 
$securityObject = $obj->securityObject;
if(!$security->isAdminLogin($securityObject,10,true)); 
 
$arrID = (isset( $_GET['id']) && !empty( $_GET['id'])) ? explode(',',$_GET['id']) : array();
    
$rs = $obj->searchDataRow(array($obj->tableName.'.refcode', $obj->tableName.'.marketplacekey'),
                          ' and '.$obj->tableName.'.pkey in('.$obj->oDbCon->paramString($arrID,',').')',
                          ' limit 1'
                         );
if(empty($rs)) die;

$arrRefCode = array_column($rs,'refcode');
    
if($rs[0]['marketplacekey'] <> 0){

    $marketplaceObj = $marketplace->getMarketplaceObj($rs[0]['marketplacekey']);   
    if(empty($marketplaceObj)) return;
    
    $responses = $marketplaceObj[0]['obj']->getInvoice($arrRefCode);   
    if(empty($responses)){ 
        echo 'Halaman faktur tidak tersedia'; 
        die;
    }     
    
    
    switch($rs[0]['marketplacekey']){
             case MARKETPLACE['lazada']:   
                   /* header('Content-type: text/html;');  
                    $html =  base64_decode($responses['file']);   
                    $html = '<style>.awb.lex { width: 96% !important;}</style>' . $html;
                    echo $html;
            
                    break;*/
             case MARKETPLACE['shopee']:
                     /*header('Content-type: application/pdf;'); 
                     header('Content-Disposition: inline;'); 
            
                    // biar cepet aj 
                    if(count($responses) == 1){
                        $response = $responses[0];
                        
                        $url = $response['airway_bill']; 
                        
                        $curl = curl_init(); 
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => 1
                        ));
        
                        $resp = curl_exec($curl);
                        curl_close($curl);
 
                        echo $resp; 
                        
                    } else{
                        $pdf = new PDFMerger;
                        $ctr = 0;
                        foreach($responses as $response){   
                            if (empty($response)) continue;
                            
                            $url = $response['airway_bill']; 

                            $curl = curl_init(); 
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => 1
                            ));

                            $resp = curl_exec($curl);
                            curl_close($curl);

                            // save dulu
                            $fileName = time().rand().$ctr++;
                            $filePath = DOC_ROOT.'_temp/'.$fileName.'.pdf';
 
                            file_put_contents($filePath, $resp); 
                            fclose($docFile);

                            $pdf->addPDF($filePath);  

                        }

                       $pdf->merge('browser');
                    }       
             
            
                    break; 
                    */
             case MARKETPLACE['tokopedia']:  
                    $ctr = 1;
                    foreach($responses as $response) { 
                       
                      /* $fileName = time().rand().$ctr++;
                       $docFile = fopen(DOC_ROOT.'_temp/'.$fileName.'.html', "w"); 
                       $urlFile =  HTTP_HOST.'_temp/'.$fileName.'.html';
                       fwrite($docFile, $response);
                       fclose($docFile);
                        */  
                        header("location: ".$response['url']); 
                    }
            
                    break; 
            
    } 
} 
    
?>