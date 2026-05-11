<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  
 
$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;
 

$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage');
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
     
$criteria = ' and ' .$testimonial->tableName.'.statuskey = 1 ' ;
  
$rsTestimonial = $testimonial->searchData('','',true,$criteria,'order by '.$testimonial->tableName.'.pkey desc',$limit);

$totalPages = ceil( $testimonial->getTotalRows($criteria) / $totalrowsperpage); 

$arrTwigVar ['rsTestimonial'] =  $rsTestimonial;
$arrTwigVar ['totalPages'] =  $totalPages;   
 
$arrTwigVar ['inputHidCityKey'] =  $class->input('hidden','hidCityKey'); 
$arrTwigVar ['inputHidRatingKey'] =  $class->input('hidden','selRatingKey');  
$arrTwigVar ['inputName'] =  $class->input('text','name'); 
$arrTwigVar ['inputCity'] =  $class->input('text','cityName');   
$arrTwigVar ['inputMessage'] =   $class->inputTextArea('review',true,'','style="height:10em"'); 
$arrTwigVar ['btnSubmit'] =   $class->input('submit','btnSave',false,$class->lang['send'], 'btn btn-primary'); 
 
    
echo $twig->render('testimonial.html', $arrTwigVar);

?>
