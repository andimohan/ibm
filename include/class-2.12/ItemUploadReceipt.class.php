<?php

class ItemUploadReceipt extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'item_upload_receipt_header';
    $this->tableNameDetail = 'item_upload_receipt_detail';
    $this->tableWarehouse = 'warehouse';   
    $this->tableItem = 'item';   
    $this->tableCity= 'city';   
    $this->tableCustomer = 'customer';   
    $this->tableStatus = 'item_upload_receipt_status';
    $this->uploadFolder = 'upload-receipt/';
    $this->securityObject = 'ReceiptValidation'; 

    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
    $this->arrDataDetail['point'] = array('point','number');
    $this->arrDataDetail['qty'] = array('qty','number');
        
    $arrDetails = array();
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));   
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['name'] = array('name');
    $this->arrData['citykey'] = array('hidCityKey');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['storename'] = array('storeName');
    $this->arrData['invoicenumber'] = array('invoiceNumber');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['receiptdate'] = array('receiptDate','date');
    $this->arrData['description'] = array('trDesc');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['filename'] = array('fileName'); 
    $this->arrData['cancelreasonkey'] = array('selCancelReasonKey'); 
    $this->arrData['cancelreason'] = array('cancelReason'); 

    $this->noCancelReason = '-----';
        
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'datetime','title' => 'uploadDate','dbfield' => 'trdate','default'=>true, 'width' => 110, 'align'=>'center', 'format' => 'datetime'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'store','title' => 'store','dbfield' => 'storename','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'receipt','title' => 'invoiceNumber','dbfield' => 'invoicenumber','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'totalpoint','title' => 'point','dbfield' => 'totalpoint','default'=>true, 'width' => 50, 'align'=>'right', 'format' => 'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'cancelreason','title' => 'cancelReason','dbfield' => 'cancelreason','default'=>true, 'width' =>200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
     
    $this->includeClassDependencies(array(
        'Warehouse.class.php',  
        'City.class.php', 
        'Customer.class.php', 
        'Item.class.php',  
        'CancelReason.class.php',  
        'Voucher.class.php',  
    ));   
        
    $this->newLoad = true;
    $this->overwriteConfig();
        
    }

    function getQuery(){
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableCity.'.name as cityname,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableCustomer.',
                 '.$this->tableName.' 
                    left join   '.$this->tableCity.' on  '.$this->tableName.'.citykey = '.$this->tableCity.'.pkey
                    
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
 
        return $sql;
    }

    function validateForm($arr,$pkey = ''){ 
  
        $arrayToJs = parent::validateForm($arr,$pkey); 
        
        $arrItem = $arr['hidItemKey'];
        $arrQtyItem = $arr['qty'];
        $storeName = trim($arr['storeName']);
        $invoiceNumber = trim($arr['invoiceNumber']);
        $image =  $arr['item-image-uploader'];
        $receiptDate = trim($arr['receiptDate']);
        
        if(empty($storeName))
            $this->addErrorList($arrayToJs,false, 'Nama toko harus diisi.'); 
        
        if(empty($invoiceNumber))
            $this->addErrorList($arrayToJs,false, 'Nomor struk harus diisi.'); 
        
        // storename = '.$this->oDbCon->paramString($storeName).' and 
        // validasi pkey
        $sql = 'select pkey from '.$this->tableName.' 
                where 
                    statuskey in (1,2) and 
                    invoicenumber = '.$this->oDbCon->paramString($invoiceNumber).' and 
                    pkey <> '.$this->oDbCon->paramString($pkey).'     
                ';
        
        $rs = $this->oDbCon->doQuery($sql);
        if(!empty($rs))  
            $this->addErrorList($arrayToJs,false, 'Nomor struk sudah terdaftar.'); 
         
          
        // validasi cuma dr FE
        if(isset($arr['fromFE']) && $arr['fromFE'] == 1){
            $hasQty = false;
            foreach($arrQtyItem as $qty){    
                if($this->formatNumber($qty) > 0) {
                    $hasQty = true;
                    break;
                } 
            }
            
            // cek tgl periode
            
             
            $dateDiff1 = $this->dateDiff('01 / 09 / 2021',$receiptDate);   
            $dateDiff2 = $this->dateDiff($receiptDate, '15 / 10 / 2021');   
                    
            if(empty($receiptDate))
                $this->addErrorList($arrayToJs,false, 'Tgl. struk harus diisi.'); 
            else if ($dateDiff1 < 0 || $dateDiff2 < 0)
                $this->addErrorList($arrayToJs,false, 'Tgl. struk tidak valid.'); 

            if(!$hasQty)   
                $this->addErrorList($arrayToJs,false, 'Jumlah pembelian harus diisi.'); 

            if(empty($image))
                $this->addErrorList($arrayToJs,false, 'Foto struk harus diupload.'); 

            if(empty($arr['chkAgree'])) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['registration'][1]);
        }
        
        // kalo ubah status ke cancel, harus ad alasan
        if($arr['selStatus'] == 3 && $arr['selCancelReasonKey'] == 0){
             $this->addErrorList($arrayToJs,false,$this->errorMsg[903],true);  
        }
        
        return $arrayToJs;
    }

    function afterUpdateData($arrParam, $action){   
         if(isset($arrParam['item-image-uploader'])){ 
          $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']); 
          $sql = 'update '.$this->tableName.' set filename = ' . $this->oDbCon->paramString($arrParam['fileName']) . ' where pkey = '. $this->oDbCon->paramString($arrParam['pkey']);
          $this->oDbCon->execute($sql);
        }
        
        $this->updatePoint($arrParam['pkey']);
         
        if ($action == INSERT_DATA){ 
            $this->sendReceiptUploadedEmail($arrParam['hidCustomerKey'],$arrParam['code']);
        }else {
            $rsHeader = $this->getDataRowById($arrParam['pkey']);
            if ($rsHeader[0]['statuskey'] == 2) 
                $this->sendReceiptApprovedEmail($rsHeader);
            else if ($rsHeader[0]['statuskey'] == 3) 
                $this->sendReceiptRejectedEmail($rsHeader);
        }
    }
    
    function normalizeParameter($arrParam, $trim=false){  
        $arrParam['invoiceNumber'] =  $str = preg_replace( '/[\W]/', '', $arrParam['invoiceNumber']);
        
        // update nilai point per transaksi 
        $arrItem = $arrParam['hidItemKey']; 
        
        $item = new Item();
        $rsItem = $item->searchDataRow(array($item->tableName.'.pkey,'.$item->tableName.'.pointvalue'), ' and '.$item->tableName.'.pkey in ('. $this->oDbCon->paramString($arrItem,',') .') ');
        $arrItemPoint = array_column($rsItem,'pointvalue','pkey');
        
        $arrParam['point'] = array();
        foreach($arrItem as $itemkey) 
            array_push($arrParam['point'],$arrItemPoint[$itemkey]);
          
        if($arrParam['selStatus'] <> 3){ 
            $arrParam['selCancelReasonKey'] = 0;
            $arrParam['cancelreason'] = '';
        }else{ 
            $cancelReason = new CancelReason();
            $rsCancelReason = $cancelReason->getDataRowById($arrParam['selCancelReasonKey']);
            $arrParam['cancelReason'] = $rsCancelReason[0]['reason']; 
        }
        
        // kalo bukan dr front end
        if(empty($arrParam['fromFE'])){
            unset($this->arrData['pkey'][1]);
        }
         
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
    
     function validateDelete($id,$forceDelete = false){ 
  	     $arrayToJs = array();  
				 
		 $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
		 
		return $arrayToJs;
     }
    
/*
    function updateImage($pkey,$token,$arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
        $this->setLog($sourcePath,true);
        $this->setLog($destinationPath,true);
        
		if(!is_dir($sourcePath))  return;
	 
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
 
 		//delete previous images	    
		$this->deleteAll($destinationPath);   
		 
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage); 
			$this->uploadImage($sourcePath, $destinationPath,$arrImage[0]); 
			return $arrImage[0]; 
		}
		
		return '';
		
	} 
    */

     function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname, 
            '.$this->tableItem.'.code as itemcode ,
            '.$this->tableItem.'.sellingprice  
          from
            '.$this->tableNameDetail.',
            '.$this->tableItem.'
          where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
      
    
    function changeStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
        
        if (!is_numeric($status))  die;
          
        $rsHeader = $this->getDataRowById($id); 
         
      	try{   
            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus())) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[212],true);   
    
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
   
        }catch(Exception $e){ 
 		     return $this->getErrorLog(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 

		try{ 
            
            // ================== VALIDATION 
            if (!$ignoreValidation){ 
                switch ($status){
                    case 1 : $this->validateInput($rsHeader); 
                              break;
                    case 2 : if ($rsHeader[0]['statuskey'] < $status )
                                $this->validateConfirm($rsHeader);
                             else
                                $this->validateBackConfirm($rsHeader); 
                              break;
                    case 3 : $this->validateClose($rsHeader,$reason); 
                              break;
                    case 4 : $this->validateCancel($rsHeader, $autoChangeStatus);
                              break; 
                }  
            } 
             
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
             
            
            // ================== VALIDATION OK !
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					  
			switch ($status){ 
				case 2 : if ($rsHeader[0]['statuskey'] < $status ){ 
                            $this->acceptReceipt($rsHeader); 
                            //$this->afterAcceptReceipt($rsHeader);
                        }else{ 
                            $this->backAcceptReceipt($rsHeader); 
                            //$this->afterBackAcceptReceipt($rsHeader);
                        }
                         break; 
				case 3 : $this->declineReceipt($rsHeader); 
                         $this->afterDeclineReceipt($rsHeader); 
                         break; 
				case 4 : $this->cancelTrans($rsHeader,$copy);
                         $this->afterCancelTrans($rsHeader);
                         break;  
			}

			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id); 
            $this->oDbCon->execute($sql);  
             
            $this->setTransactionLog($status,$id,'',$reason);
            
            $this->afterStatusChanged($rsHeader);
            
			$this->oDbCon->endTrans();  
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){ 
            
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        return $this->getErrorLog(); 
  }   
    
    
    function validateInput($rsHeader){
         $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[212],true);   
    }
    
    function validateBackConfirm($rsHeader){
         $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[212],true);   
    }
    
    function validateClose($rsHeader,$reason){
        if($rsHeader[0]['statuskey'] <> 1){ 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[212],true);   
        }else{
            if (empty(trim($reason)) || $reason == $this->noCancelReason)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[903],true);    
        }
        
    }
    
    function acceptReceipt($rsHeader){ 

        $sql = 'update '.$this->tableName.' set  
              cancelreason = \'\',cancelreasonkey = 0
              where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
        $this->oDbCon->execute($sql);
    }
    
    function declineReceipt($rsHeader){
         
    }
    
    function afterStatusChanged($rsHeader){
         // kalo dr perubahan status
         // harus set ulang cancelreason
         $pkey = $rsHeader[0]['pkey'];
         $this->updatePoint($pkey);
         
        // harus ambil ulang status terbaru
        $rsHeader = $this->getDataRowById($pkey); 
        if ($rsHeader[0]['statuskey'] == 2) 
            $this->sendReceiptApprovedEmail($rsHeader);
        else if ($rsHeader[0]['statuskey'] == 3) 
            $this->sendReceiptRejectedEmail($rsHeader);
    }
    
    function updatePoint($pkey){
        if(empty($pkey)) return;
         
        $totalPoint = 0; 
 
        // hitung ulang dulu, kalo batal langsung set 0 saja
        // harus ambil ulang status terbaru
        $rsHeader = $this->getDataRowById($pkey);
        if ($rsHeader[0]['statuskey'] == 2){
            $sql = 'select sum(point*qty) as totalpoint from '.$this->tableNameDetail.' where refkey = ' . $this->oDbCon->paramString($pkey);
            $rs = $this->oDbCon->doQuery($sql);
            
            $totalPoint = $rs[0]['totalpoint']; 
        }
            
        // biarin saja dibawah utk jaga2, recount ulang, kalo nanti bisa berubah status
        
        $sql = 'update '.$this->tableName.' set 
                totalpoint = '. $this->oDbCon->paramString($totalPoint) .' 
                where pkey = ' . $this->oDbCon->paramString($pkey);
         
        $this->oDbCon->execute($sql);
        
        // update ke total point customer
        $this->resyncCustomerPoint($rsHeader[0]['customerkey']); 

        //update voucher
        $this->updateVoucher($rsHeader[0]['customerkey']);
    }

   function updateVoucher($customerkey){
         $voucher = new Voucher();
         $customer = new Customer();
            
        //cek customer nya yang aktif
        $rsCustomer = $customer->getDataRowById($customerkey);
        if(empty($rsCustomer)) return;
  
          //cek point customer jika lebih dari 60 
         if($rsCustomer[0]['point'] >= 60){ 
            $sql = 'select pkey from '.$voucher->tableName.' where customerkey = ' . $this->oDbCon->paramString($rsCustomer[0]['pkey']);
            $this->setLog($sql,true);
            $rsVoucher = $this->oDbCon->doQuery($sql);   

             $this->setLog($rsVoucher,true);
             
            //cek vouchernya blm ada
            if(empty($rsVoucher)){ 
                    $arr = array();
                    $arr['code'] = array('code'); 
                    $arr['startDate'] = date('d / m / Y');
                    $arr['hidCustomerKey'] = $rsCustomer[0]['pkey']; 
                    $arr['value'] = 1; 
                    $arr['selCategory'] = 2; 
                    $arr['selType'] = 1; 

                    $rsVoucherResponse = $voucher->addData($arr); 
                    $rsVoucherResponse = $rsVoucherResponse[0]['data']; 
                   // $this->sendVoucherEmail($rsVoucherResponse['customerkey'],$rsVoucherResponse['code']);
            }
             
         }else{ 
                //delete voucher jika point kurang dari 60
                $sql = 'delete from '.$voucher->tableName.' where customerkey = '.$this->oDbCon->paramString($rsCustomer[0]['pkey']);
                $this->oDbCon->execute($sql);    
         }
         
    }
    
    function resyncCustomerPoint($customerkey){
        $sql = 'update '.$this->tableCustomer.' 
                set '.$this->tableCustomer.'.point = (
                    select sum('.$this->tableName.'.totalpoint) as totalpoint 
                    from '.$this->tableName.' 
                    where '.$this->tableName.'.statuskey = 2 and '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' )
                where '.$this->tableCustomer.'.pkey = '. $this->oDbCon->paramString($customerkey);
         
       $this->oDbCon->execute($sql); 
    }
    
    	
    function sendReceiptUploadedEmail($customerkey,$code){
		
        global $twig;
        
        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);
     
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name']; 
        $arrTwigVar['TRANS_CODE'] = $code;
         
        $twig->render('email-template.html');  
        $content = $twig->render('email-receipt-uploaded.html', $arrTwigVar);

        //$this->setLog($content,true);
        $this->sendMail('','', 'Struk berhasil diupload' . ' - ' . DOMAIN_NAME,$content,$rsCust[0]['email']); 
		 
	}
    
    
    function sendReceiptApprovedEmail($rsHeader){

        require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
        Twig_Autoloader::register(); 
        $loader = new Twig_Loader_Filesystem($this->templateDocPath); 

        $twig = new Twig_Environment($loader); 
        $twig->addExtension(new Twig_Extension_Array());   

        require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';
 
        
        $customerkey = $rsHeader[0]['customerkey'];
        
        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);
     
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['POINT_NEEDED'] = 60 - $rsCust[0]['point']; 
        $arrTwigVar['POINT'] = $rsHeader[0]['totalpoint']; 
        $arrTwigVar['TRANS_DATE'] = $rsHeader[0]['trdate'];
         
        $twig->render('email-template.html');  
        $content = $twig->render('email-receipt-approved.html', $arrTwigVar);

        $this->sendMail('','', 'Hasil Validasi Struk' . ' - ' . DOMAIN_NAME,$content,$rsCust[0]['email']); 
		 
	}

    
    function sendReceiptRejectedEmail($rsHeader){
		
        require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
        Twig_Autoloader::register(); 
        $loader = new Twig_Loader_Filesystem($this->templateDocPath); 

        $twig = new Twig_Environment($loader); 
        $twig->addExtension(new Twig_Extension_Array());   

        require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';
        
        
        $customerkey = $rsHeader[0]['customerkey'];
        
        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);
     
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $rsHeader[0]['code'];
        $arrTwigVar['TRANS_DATE'] = $rsHeader[0]['trdate'];
        $arrTwigVar['CANCEL_REASON'] = $rsHeader[0]['cancelreason'];
         
        $twig->render('email-template.html');  
        $content = $twig->render('email-receipt-rejected.html', $arrTwigVar);

        //$this->setLog($content,true);
        $this->sendMail('','', 'Hasil Validasi Struk' . ' - ' . DOMAIN_NAME,$content,$rsCust[0]['email']); 
		 
	}

    function sendVoucherEmail($customerkey, $code){
		
        require_once  $_SERVER ['DOCUMENT_ROOT'].'/Twig/Autoloader.php';
        Twig_Autoloader::register(); 
        $loader = new Twig_Loader_Filesystem($this->templateDocPath); 

        $twig = new Twig_Environment($loader); 
        $twig->addExtension(new Twig_Extension_Array());   

        require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';
         
        $customer = new Customer();
        $rsCust = $customer->getDataRowById($customerkey);
     
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name'];
        $arrTwigVar['TRANS_CODE'] = $code;
        
        $twig->render('email-template.html');  
        $content = $twig->render('email-voucher.html', $arrTwigVar);

        $this->sendMail('','', 'Tiket Lucky Draw' . ' - ' . DOMAIN_NAME,$content,$rsCust[0]['email']); 
		 
	}
    
    function isAgeValid($trdate, $ageLimit = 12){
        // format $trdate = Y-m-d
        $todayYear = date('Y');
        $todayMonth = date('m');
        $todayDate = date('d');
        
        $dobYear = $this->formatDBDate($trdate,'Y');
        $dobMonth = $this->formatDBDate($trdate,'m');
        $dobDate = $this->formatDBDate($trdate,'d');
        
        if ( ($todayYear - $dobYear) <  $ageLimit )  return false; // kalo secara tahun sudah dibawah 12, blm jalan 12
        if ($todayYear == ($dobYear+$ageLimit) && $dobMonth > $todayMonth ) return false; // kalo jln 12, tp blm sampe bulannya
        if ($todayYear == ($dobYear+$ageLimit) && $dobMonth == $todayMonth && $dobDate > $todayDate) return false;
        
        return true;
    }
    
}

?>