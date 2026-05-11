<?php     
ini_set('display_errors', 1);
error_reporting(E_ALL);

if(file_exists('../_development.php')) 
    require_once '_include-cron.php';
else
   require_once dirname(__FILE__).'/_include-cron.php';

includeClass(array('Employee.class.php','Customer.class.php','CampaignNewsletter.class.php'));
 
$campaignNewsletter = new CampaignNewsletter(); 
$customer = new Customer(); 

$rsLang = $lang->searchDataRow(array($lang->tableName.'.pkey',$lang->tableName.'.code'));
$rsLang = array_column($rsLang,'code','pkey');

$rsNewsletter = $campaignNewsletter->searchData($campaignNewsletter->tableName.'.statuskey',2,true,' and ' . $campaignNewsletter->tableName.'.trdate <= now() '); // kalo dr pro, akan dihitung pas ikut meeting, kalo sudah master host gk perlu cek lg

//set lang dulu
foreach($rsLang as $langkey=>$langrow){ 
       
    $rsLangNewsletter = $campaignNewsletter->updateContentLang($rsNewsletter, $langrow);
 
      foreach($rsNewsletter as $key=>$row){ 
            if(!isset($rsNewsletter[$key]['langdetail']))
                $rsNewsletter[$key]['langdetail'] = array();
          
            $rsNewsletter[$key]['langdetail'][$langrow]['detail'] = $rsLangNewsletter[$key]['detail'];
      }
    
    
}

//echo(json_encode($rsNewsletter));
//echo '<br><br>total Newsletter '. count($rsNewsletter) .'<br><br>';

foreach($rsNewsletter as $newsletterRow){
 
     $arrBusinessKey = json_decode($newsletterRow['businesskey']); 
     $hasBussinessCriteria = (!empty($arrBusinessKey)) ? true : false;
    
     $arrCriteria = array();
    
     if(!empty($newsletterRow['membershipkey'])) array_push($arrCriteria,$customer->tableName.'.membershiplevel in ('.$class->oDbCon->paramString(json_decode($newsletterRow['membershipkey']),',').')');
     if(!empty($newsletterRow['jobpositionkey'])) array_push($arrCriteria,$customer->tableName.'.jobpositionkey in ('.$class->oDbCon->paramString(json_decode($newsletterRow['jobpositionkey']),',').')');
     if(!empty($newsletterRow['sexkey'])) array_push($arrCriteria,$customer->tableName.'.sexkey in ('.$class->oDbCon->paramString(json_decode($newsletterRow['sexkey']),',').')');
     if(!empty($newsletterRow['citykey'])) array_push($arrCriteria,$customer->tableName.'.citykey in ('.$class->oDbCon->paramString(json_decode($newsletterRow['citykey']),',').')');
     if(!empty($newsletterRow['countrykey'])) array_push($arrCriteria,$customer->tableName.'.countrykey  in ('.$class->oDbCon->paramString(json_decode($newsletterRow['countrykey']),',').')');
     if(!empty($newsletterRow['nationalitykey'])) array_push($arrCriteria,$customer->tableName.'.nationalitykey in ('.$class->oDbCon->paramString(json_decode($newsletterRow['nationalitykey']),',').')');
     if(!empty($newsletterRow['businesskey'])) array_push($arrCriteria,$customer->tableName.'.mainbusinesskey in ('.$class->oDbCon->paramString($arrBusinessKey,',').')');
     if(!empty($newsletterRow['langkey'])) array_push($arrCriteria,$customer->tableName.'.langkey in ('.$class->oDbCon->paramString(json_decode($newsletterRow['langkey']),',').')');
          
    if ($hasBussinessCriteria) array_push($arrCriteria, $customer->tableCustomerBusiness.'.refbusinesskey in ('.$class->oDbCon->paramString($arrBusinessKey,',').')');
     
    $sql = 'select
                distinct('.$customer->tableName.'.pkey) as pkey 
            from '.$customer->tableName;    
    
    if ($hasBussinessCriteria)
        $sql .= ', '.$customer->tableCustomerBusiness;
        
    $sql .= '  where '.$customer->tableName.'.statuskey = 2  ';
    
    if(!empty($arrCriteria))
         $sql .= ' and '.implode(' and ',$arrCriteria);
        
    
    $rsCustomer = $class->oDbCon->doQuery($sql);
    $arrCustomerKey = array_column($rsCustomer,'pkey');
     
    //test 
//    $arrCustomerKey = array();
//    array_push($arrCustomerKey,8014);
//    array_push($arrCustomerKey,8015); 
        
    $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.code',$customer->tableName.'.name',$customer->tableName.'.email',$customer->tableName.'.mobile',$customer->tableName.'.mobilecode',$customer->tableName.'.langkey'),
                                            ' and ' .$customer->tableName.'.pkey in ('.$class->oDbCon->paramString($arrCustomerKey,',').')' 
                                            );
    
   
    foreach($rsCustomer as $customerRow){
       $langcode = $rsLang[$customerRow['langkey']];
       $customerRow['langcode'] = $langcode;
    
        // cek lang dulu
        $newsletterRow['detail'] = $newsletterRow['langdetail'][$customerRow['langcode']]['detail']; 
        $campaignNewsletter->sendNewsletterEmail($customerRow, $newsletterRow);
    }
    
     try{  
        $class->oDbCon->startTrans();

        $sql = 'update '.$campaignNewsletter->tableName.' set statuskey = 3 where pkey = '.$class->oDbCon->paramString($newsletterRow['pkey']);
        $class->oDbCon->execute($sql);

        $class->oDbCon->endTrans(); 

    }catch(Exception $e){
        $class->oDbCon->rollback();  
    }

}
echo 'Newsletter Done ! ';
	
?>