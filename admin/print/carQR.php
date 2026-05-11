<?php  
require_once '../../_config.php';  
require_once '../../_include-v2.php'; 
 
if(!isset($_GET['id']) || empty($_GET['id'])) die;

includeClass(array('Car.class.php'));
$car = new Car();

$profileImg = $car->loadSetting('companyLogo'); 
$logo = '';
if (!empty($profileImg)) 
    $logo = '<div class="logo" style="background-image:url(\'/phpthumb/phpThumb.php?src='.$class->phpThumbURLSrc .'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg).'\')"></div>';  
else
    $logo = '<div class="logo" style="background-image:url(\'/include/img/avatar-default.jpg\')"></div>';  

$id = explode(',', $_GET['id']); 
$rsCar = $car->searchData('','',true,' and '.$car->tableName.'.pkey in ('.$car->oDbCon->paramString($id,',').') ');
       
$html = '<style>
            @font-face {
                font-family: Palanquin;
                src: url(\'/include/fonts/Palanquin/Palanquin-Light.ttf\');
                font-weight:300;
            }
            
            @font-face {
                font-family: \'PT Sans Narrow\';
                src: url(\'/include/fonts/sans-narrow/PT_Sans-Narrow-Web-Regular.ttf\') format(\'truetype\');
                font-weight: normal; 
            }

            .logo { background-position:center; background-repeat:no-repeat; background-size:contain; width: 50px; height: 50px; border:1px solid #dedede; background-color: #fff; margin:auto; border-radius: 25em; position:absolute; top: 100px; left : 100px; }
            .qr-list {color:#fff; float:left; width: 250px;  background-color: #912785; border:1px solid #dedede; border-radius:0.5em; margin: 0.5emem; padding: 1em}
            .qr-panel {height: 250px; width: 250px; position:relative; text-align:center; background-color:#fff; background-position:center; background-repeat:no-repeat; background-size:contain; }
            .registration-number {line-height:2em; text-align:center; font-size: 2em; font-family : \'PT Sans Narrow\'}
            .footer { font-size: 1.2em; line-height: 1em; font-family : Palanquin}
            .wintera-logo {width: 40px; height: 40px; background-position:center; background-size: cover; border-radius : 0.2em}
        </style>';

foreach($rsCar as $row){
    
	$qrResult = $car->createQR($row['policenumber'],10, array('phpthumb' => array('w'=>200,'h'=>200)));
	
    $html .= '<div class="qr-list">
                <div class="qr-panel" style="background-image: url(\''.$qrResult['url_phpthumb'].'\')">'.$logo.'</div>
                <div class="registration-number">'.$row['policenumber'].'</div>
                <div class="footer"> 
                    <div style="float:left">
                        wintera.co.id<br>
                        accugps.com
                    </div> 
                    <div class="wintera-logo" style="float:right;  background-image:url(\'/include/img/avatar-default.jpg\')"> </div>
                </div>
             </div>
			 ';  
}
 
           
echo $html; 


?>