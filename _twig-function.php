<?php  

$twig->addFilter( new \Twig\TwigFilter('html_entity_decode','html_entity_decode') );

$URLfilter = new \Twig\TwigFilter('urlfilter', function ($string) {
    global $class; 
	return $class->URLFilter($string);
});
$twig->addFilter($URLfilter);

$ucwords = new \Twig\TwigFilter('ucwords', function ($string) {
	return ucwords(strtolower($string));
});
$twig->addFilter($ucwords);
  
$ucwords = new \Twig\TwigFilter('ucfirst', function ($string) {
	return ucfirst(strtolower($string));
});
$twig->addFilter($ucwords);
  
$ucwords = new \Twig\TwigFilter('formatGMT', function ($gmt) { 
    
    $string = 'GMT';
    
    if($gmt == 0) return $string;
    
    if ($gmt > 0) $gmt = '+'.$gmt;
   // elseif ($string < 0) $string = '-'.$string;
    
	return $string . ' '.$gmt;
});
$twig->addFilter($ucwords);

$WAfilter = new \Twig\TwigFilter('wafilter', function ($string) {
    global $class;
	$string = str_replace(' ','',$string);
	$string = str_replace('-','',$string);
	$string = ltrim($string, "0");
	$string = ltrim($string, "+62");
	$string = ltrim($string, "+");
	$string = '62'.$string;
	$string = strtolower($string);
	
	return $string;
});
$twig->addFilter($WAfilter);

$dateFormat = new \Twig\TwigFilter('format_date', function ($string, $format = '') {
    global $class; 
	return  ($class->isEmptyDate($string)) ? '00 / 00 / 0000' : $class->formatDBDate($string,$format);
});
$twig->addFilter($dateFormat);


$numberFormat = new \Twig\TwigFilter('format_number', function ($string,$opt = array()) {
    global $class; 
    
    $decimal = (isset($opt['decimal']) && !empty($opt['decimal'])) ? $opt['decimal'] : 0 ;
    $decimalSeparator = (isset($opt['decimalSeparator']) && !empty($opt['decimalSeparator'])) ? $opt['decimalSeparator'] : '.' ;
    $thousandSeparator = (isset($opt['thousandSeparator']) && !empty($opt['thousandSeparator'])) ? $opt['thousandSeparator'] : ',' ;
    $shortTerm = (isset($opt['shortTerm']) && !empty($opt['shortTerm'])) ? $opt['shortTerm'] : false;
    
    $hasShortTerm = false;
    $amount = floatVal($string);
    if($shortTerm && $amount >= 1000){     
        $amount /= 1000;
        $hasShortTerm = true;
    }
                                   
    $amount = number_format($amount,$decimal,$decimalSeparator,$thousandSeparator);
    
    if($hasShortTerm) $amount .= ' ' . $class->lang['shortThousand']; 
    
	return  $amount;
});
$twig->addFilter($numberFormat);


$dateLocal = new \Twig\TwigFilter('date_local', function ($string) {
    global $class;
	return $class->toLocalDate($string);
});
$twig->addFilter($dateLocal);

$url_decode = new \Twig\TwigFilter('url_decode', function ($string) {
	return urldecode($string);
});
$twig->addFilter($url_decode);
  

$WAIcon = new \Twig\TwigFilter('to_icon', function ($string) { 
    global $class; 
	$string  = strtolower($string);
	
	switch($string){
		case 'fb' : 
		case 'facebook' : return 'fab fa-facebook-square'; break;
		case 'ig' : 
		case 'insta' : 
		case 'instagram' : return 'fab fa-instagram'; break;
		case 'twitter' : return 'fab fa-twitter'; break;
		case 'youtube' : return 'fab fa-youtube'; break;
		case 'linkedin' : return 'fab fa-linkedin'; break; 
	}
				  	
	return '';
});
$twig->addFilter($WAIcon);


$breakWord = new \Twig\TwigFilter('break_word', function ($string) { 
    global $class; 
	return implode( '<br>' , explode(' ',$string) );  
});
$twig->addFilter($breakWord);

 
$function = new \Twig\TwigFilter('ext', function ($filepath) { 
     global $class; 
    
    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
    
    return $ext;
});
$twig->addFilter($function);

$function = new \Twig\TwigFilter('sliceArrayPkey', function ($arr,$sliceArr) { 
    global $class;
    
    $returnArr = array();
     
    foreach($arr as $data){ 
        if(!in_array($data['pkey'],$sliceArr)) continue; 
        array_push($returnArr, $data);
    }
    
    return $returnArr;
});
$twig->addFilter($function);

$twig->addFilter(new \Twig\TwigFilter('add_placeholder', function ($html, $placeholder) {
    // Add placeholder to <input> if not already present
    $html = preg_replace(
        '/<input(?![^>]*\bplaceholder=)([^>]*)>/i',
        '<input$1 placeholder="'.htmlspecialchars($placeholder, ENT_QUOTES).'">',
        $html
    );

    // Add placeholder to <textarea> if not already present
    $html = preg_replace(
        '/<textarea(?![^>]*\bplaceholder=)([^>]*)>/i',
        '<textarea$1 placeholder="'.htmlspecialchars($placeholder, ENT_QUOTES).'">',
        $html
    );

    return $html;
}, ['is_safe' => ['html']]));


$twig->addFilter(new \Twig\TwigFilter('add_class', function ($html, $newClass) {
    return preg_replace_callback(
        '/<([a-zA-Z0-9]+)([^>]*)>/',
        function ($matches) use ($newClass) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Find existing class
            if (preg_match('/class="([^"]*)"/', $attributes, $classMatch)) {
                $existing = $classMatch[1];
                $updatedClass = $existing . ' ' . $newClass;
                $attributes = preg_replace(
                    '/class="[^"]*"/',
                    'class="' . trim($updatedClass) . '"',
                    $attributes
                );
            } else {
                $attributes .= ' class="' . $newClass . '"';
            }

            return "<$tag$attributes>";
        },
        $html,
        1 // Only replace the FIRST tag (outermost)
    );
}, ['is_safe' => ['html']]));


$twig->addFilter(new \Twig\TwigFilter('upper_element', function ($buttonHtml) {
    // Regex to find the text between > and < 
        return preg_replace_callback('/>(.*?)</', function ($matches) {
            // Apply strtoupper to convert the button text to uppercase
            return '>' . strtoupper($matches[1]) . '<';
        }, $buttonHtml);
}));

 
$function = new \Twig\TwigFunction('generate_report_row_attr', function ($dataStructure, $currKey,$order) { 
    
    global $class;
    
    $el = $dataStructure[$currKey];
    $width = (isset($el['width']) && !empty($el['width']))  ? 'width:'.$el['width'].';' : '';
    
    $align = '';
    if (isset($el['format'])){
        switch($el['format']){
            case 'integer' : 
            case 'number' : 
            case 'accounting' : 
            case 'autodecimal' : 
            case 'decimal' : $align = "text-align:right;" ;
                             break;
            case 'date' : $align = "text-align:center;" ;
                             break;
            case 'datetime' : $align = "text-align:center;" ;
                             break;
        } 
    }
                     
    $align = (isset($el['align']) && !empty($el['align'])) ? 'text-align:'.$el['align'].';' : $align; 
    
    $styleHTML = $width.' '.$align ;
    
         
    $orderType = -1;
    $orderIcon = '';
    $sortableActive = '';
    /*$updateOrderScript = '';*/
    
    $sortable = (isset($el['sortable']) && !$el['sortable']) ?  '' : 'sortable' ;  
                          
    if(!empty($sortable)){
        
        if (isset($order['orderBy']) && isset($el['dbfield']) && $el['dbfield'] == $order['orderBy']){
            $sortableActive = 'sortable-active';
            $orderType = $order['orderType'] * -1;
            $arrowIcon = ($order['orderType'] == 1) ? 'arrow-down' : 'arrow-up';
            $orderIcon = '<div class="order-type ' . $arrowIcon . '" style="display:inline"></div>';
            
            /*$updateOrderScript = '<script>
                            jQuery(document).ready(function(){        
                                $("[name=hidOrderBy]").val(\''.$order['orderBy'].'\'); 
                                $("[name=hidOrderType]").val(\''.$order['orderType'].'\'); 
                            })
                        </script>';*/
        }
    } 
    
    // kalo group
    $groupBorderL = '';
    if (isset($el['group'])){
        // kalo kolom pertama
        $groupName = '';
        foreach($dataStructure as $key=>$row){
            if (!isset($row['group']))
                continue;
            
            if($key == $currKey){
                $groupBorderL = ($row['group'] != $groupName) ?  'header-group-bl' : '';
                break;
            }   
                
            $groupName = $row['group'];
        }
     
    }
    
    $classHTML =  $sortable.' '.$sortableActive. ' ' .$groupBorderL;  
    
    return array('style' => $styleHTML, 'class' => $classHTML, 'orderType' => $orderType,  'orderIcon' => $orderIcon) ;
});
$twig->addFunction($function);


$function = new \Twig\TwigFunction('calculate_total_col_width', function ($dataStructure) { 
      
    $totalWidth = 0;
  
    foreach($dataStructure as $key=>$row){ 
       $ittrWidth = str_replace('px','', (isset($row['width'])) ? $row['width'] : 0); 
       $hidWidth = str_replace('px','', (isset($row['hidWidth'])) ? $row['hidWidth'] : 0); 
        
            
       $totalWidth += $ittrWidth + $hidWidth;
    }
    
    $totalWidth += 50;
    
    return $totalWidth ;
});
$twig->addFunction($function); 

$function = new \Twig\TwigFunction('generate_report_group_header_attr', function ($dataStructure,$currKey) { 
    global $class;
    
    $prevGroup = '';
    
    $firstOfGroup = false;
    foreach($dataStructure as $key=>$row){
        $ittrGroup = (isset($row['group'])) ? $row['group'] : ''; 
        if ($key == $currKey && $prevGroup != $ittrGroup){
            $firstOfGroup = true;
            break;
        }
        $prevGroup = $ittrGroup;
    }
    
    
    $totalWidth = 0;
    $colspan=0;
    $startCounting = false;
    $currGroup = $dataStructure[$currKey]['group'];
    
    foreach($dataStructure as $key=>$row){
       $ittrGroup = (isset($row['group'])) ? $row['group'] : '';
       $ittrWidth = str_replace('px','', (isset($row['width'])) ? $row['width'] : 0); 
        
       if ($ittrGroup == $currGroup){
           $startCounting = true;
           $colspan++;
           $totalWidth += $ittrWidth;
       }elseif ($startCounting){
           break;
       }
    }
    
    // margin
    $margin = $colspan * 2 * 4 ;
    $totalWidth += $margin + 100;
    
    //$class->setLog($colspan);
    return array('firstOfGroup' => $firstOfGroup, 'totalWidth' => 'width:'.$totalWidth.'px', 'colspan' => (!empty($colspan)) ? 'colspan="'.$colspan.'"' : ''  ) ;
});
$twig->addFunction($function); 
 
 
$function = new \Twig\TwigFunction('propotional_size', function ($width,$height,$maxWidth,$maxHeight) {  
    $diffWidth = $width - $maxWidth;
    $diffHeight = $height - $maxHeight;
     
    if($diffWidth < 0 && $diffHeight < 0)
        return  array('width' => $width, 'height' => $height) ;
        
    $useWidth = ($diffWidth > $diffHeight) ? true : false; 
    
    $perc = ($useWidth) ? $diffWidth / $width * 100 : $diffHeight / $height * 100;
     
    $newWidth = $width - ($perc / 100 * $width);
    $newHeight = $height - ($perc / 100 * $height);
    
    return array('width' => $newWidth, 'height' => $newHeight) ;
});
$twig->addFunction($function); 


 
$function = new \Twig\TwigFunction('getPHPThumbHash', function ($fileName) {    
    return getPHPThumbHash($fileName);
});
$twig->addFunction($function);
 
$function = new \Twig\TwigFunction('reindexDetailCollections', function ($arr,$key, $sort=false) {  
    global $class;
    if ($sort)  $class->mknatsort($arr,$key);
    return $class->reindexDetailCollections($arr,$key);
});
$twig->addFunction($function); 

 
$function = new \Twig\TwigFunction('addDummyCol', function ($colLength, $totalData, $template) {   
    $mod = $totalData % $colLength;
    if($mod > 0)
        $mod = $colLength - $mod;
     
    $return = '';
    
    for($i=0;$i<$mod;$i++)
        $return .= $template;
    
    return $return;
});
$twig->addFunction($function); 


$function = new \Twig\TwigFunction('isEmptyDate', function ($string) {   
	global $class;
	return $class->isEmptyDate($string);
});
$twig->addFunction($function); 

$function = new \Twig\TwigFunction('createPresignedURL', function ($pkey, $fileName, $uploadFolder) {    
    try{ 
        global $class;
        return $class->createPresignedURL(DOMAIN_NAME.'/'.$uploadFolder.$pkey.'/'.$fileName);    
    }catch(Exception $e){
        return '';
   }	 

});
$twig->addFunction($function);

?>