<?php

$PRINT_SETTINGS = array( 
 'logoSize' => '25,21', 
 'footer' => '', 
);
 
includeClass(array('NotificationLetter.class.php', 'Customer.class.php'));
$notificationLetter = createObjAndAddToCol(new NotificationLetter());
$obj = $notificationLetter;

  
$generateContentReport  = function ($dataset) {
     
    $obj = new NotificationLetter();
    
    $rs = $dataset['rs'];
 
    $html = $obj->printSetting['defaultStyle'];
    $html .= '<br><br><table><tr><td style="width: 200px"></td><td style="border-bottom:1px solid #333; width: 270px;"><h1 style="text-align:center;">SURAT PEMBERITAHUAN</h1></td><td style="width: 200px"></td></tr>';
    $html .= '<tr><td colspan="3" style="text-align:center"><h2>'.$rs[0]['code'].'</h2></td></tr></table>';
    $html .= '<br><br><table cellpadding="4"><tr><td>Kepada Yth,<br><b>'.$rs[0]['customername'].'</b><br>di tempat</td></tr></table>';
    $html .= '<br><br><table cellpadding="4" style="text-align:justify; line-height:15em">';
    $html .= '<tr><td>'; 
    $html .=  $rs[0]['detail'];
    $html .= '</td></tr>'; 
    $html .= '</table>';
    $html .= '<br><br><br>Jakarta, '.$obj->toLocalDate($obj->formatDBDate($rs[0]['trdate'],'d F Y')).'<br>Pengurus RT 014';
      

    return $html;
};


$generateARDetail  = function ($dataset) {
 
    $obj = new NotificationLetter();
    
    $rs = $dataset['rs'];
    $rsDetail = $obj->getDetailById($rs[0]['pkey']);
  
    $html  =  $rsDetail[0]['detail'];
        
    return $html;
};

$generateReportContent = array();
array_push($generateReportContent , $generateContentReport);
array_push($generateReportContent , $generateARDetail);

?>