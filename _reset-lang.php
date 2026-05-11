<?php  
    // set ulang bahasa sesuai dengan lang customer
    //overwrite lang dr preferensi customers

    $customer = new Customer();
    $rsCustomer = $customer->searchDataRow(array( $customer->tableName.'.langkey'),' and '. $customer->tableName.'.pkey = ' .  $customer->oDbCon->paramString($LANG_USER_KEY));
    $rsLang = $lang->searchDataRow(array( $lang->tableName.'.code'),' and '. $lang->tableName.'.pkey = ' .  $lang->oDbCon->paramString($rsCustomer[0]['langkey']));
     
    if(!empty($rsLang)){
        $_SESSION['lang'] = $rsLang[0]['code']; 
        $class->setActiveLang(); 
        $arrTwigVar ['activeLangIndex'] = $class->langCode;
        $arrTwigVar ['LANG'] = $class->lang;
    }
?>