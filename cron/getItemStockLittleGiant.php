<?php

    require_once '../_config.php'; 
    require_once '../_include-v2.php'; 

    if (!in_array(DOMAIN_NAME, array('ligiant.wintera.co.id','littlegiant.co.id'))) die('invalid domain');

    includeClass(array('Item.class.php','ItemMovement.class.php')); 
    $item = new Item();
    $itemMovement = new ItemMovement();

    $date = date('Y-m-d');
    $createdby = 0;// base64_decode($_SESSION[$class->loginAdminSession]['id']);
    $note = 'Data Dari API';

    $url =  'https://fulfillment.gje.co.id/api/stock' ;
    $header = array(
        'Content-Type: application/json',
        'Authorization: Bearer 2x1fBu05PSPIYJjT4dNrKjT3DUyssK3VVXGiVLsIe32be1a4'
    );

    $payload = array();
    $payload = json_encode($payload); 

    $connection = curl_init(); 
    curl_setopt($connection, CURLOPT_URL, $url);
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($connection, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($connection); 
    $response = json_decode($response,true); 
    if (!empty($response)) {
        $sql = 'truncate item_in_warehouse';
        $class->oDbCon->execute($sql);
        $sql = 'truncate item_movement';
        $class->oDbCon->execute($sql);
    }

    $rsItem = $item->searchData('','',true);
    $rsItem = array_column($rsItem,null, 'code'); 

    foreach( $response as $data){
        $code = $data['sku'];
        
        if(!isset($rsItem[$code])) continue;
        if($data['stock'] <= 0) continue;
        
        $arrItem = $rsItem[$code]; 
        $itemkey = $arrItem['pkey'];

            try { 

                if(!$class->oDbCon->startTrans(true))
                    throw new Exception($class->errorMsg[100]);

                $sql = '
                    INSERT INTO		
                    '.$itemMovement->tableName.' (
                        refkey,
                        trdate,
                        itemkey,
                        vendorpartnumberkey,
                        warehousekey, 
                        qtyinbaseunit,
                        costinbaseunit,
                        costinpcs,
                        reftable, 
                        note,
                        qtyinpcs,
                        statuskey,
                        createdon,
                        createdby
                    )
                    VALUES (
                        '.$class->oDbCon->paramString(1).',
                        '.$class->oDbCon->paramString($date).',
                        '.$class->oDbCon->paramString($itemkey).', 
                        '.$class->oDbCon->paramString(0).', 
                        '.$class->oDbCon->paramString(1).',
                        '.$class->oDbCon->paramString($data['stock']).',
                        '.$class->oDbCon->paramString(0).',
                        '.$class->oDbCon->paramString(0).',
                        '.$class->oDbCon->paramString(0).',
                        '.$class->oDbCon->paramString($note).',
                        '.$class->oDbCon->paramString($data['stock']).',
                        1  ,
                        now(),
                        '.$class->oDbCon->paramString($createdby).'
                    )';			 

                $class->oDbCon->execute($sql);

                $sql = '
                    INSERT INTO	'.$itemMovement->tableItemInWarehouse.' (
                        itemkey,
                        warehousekey,
                        qtyinbaseunit,
                        qtyinpcs  
                        )
                    VALUES (
                        '.$class->oDbCon->paramString($itemkey).',
                        '.$class->oDbCon->paramString(1).',
                        '.$class->oDbCon->paramString($data['stock']).', 
                        '.$class->oDbCon->paramString($data['stock']).'  
                        )';			

                $class->oDbCon->execute($sql);
                $class->oDbCon->endTrans();   

            } catch(Exception $e){  
                var_dump($e->getMessage());
                echo '<br>';
                $class->oDbCon->rollback();
            }	
       
    }

echo 'done';
?>