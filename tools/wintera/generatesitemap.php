<?php

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';   
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Spreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;  


require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Article.class.php';

$arrData = array();

// nanti bisa diganti2
$lastmod = '2025-10-12T00:00:00+07:00'; 

array_push($arrData, array( 'url' => array(
                                        'loc' => 'https://'.DOMAIN_NAME.'/',
                                        'lastmod' => $lastmod,
                                        'priority' => '1',
                                    )

                           ));


if (DOMAIN_NAME == 'thomastrans.co.id'){
    
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/services',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
    
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/articles',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
    
}else{ // wintera
   
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/transportation-management-system.html',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/freight-forwarding-system.html',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));

    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/winstock.html',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));

    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/website-development.html',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               )); 
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/seo.html',
                                            'lastmod' => '2025-12-18T00:00:00+07:00',
                                            'priority' => '0.8',
                                        )

                               )); 
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/articles',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/contact-us',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
    array_push($arrData, array( 'url' => array(
                                             'loc' => 'https://'.DOMAIN_NAME.'/customer-portal',
                                            'lastmod' => $lastmod,
                                            'priority' => '0.8',
                                        )

                               ));
}

 

$article = new Article();
$rsArticle = $article->searchData($article->statuskey,1,true);

echo count($rsArticle).'<br>';

foreach($rsArticle as $row){
    echo $class->URLFilter($row['title']).'<br>';
    $date = new DateTime($row['createdon']);
    $lastmod = $isoDate = $date->format('c'); // or 'c'
    array_push($arrData, array( 'url' => array(
                                            'loc' => 'https://'.DOMAIN_NAME.'/article-detail/'.$class->URLFilter($row['title']).'/'.$row['pkey'],
                                            'lastmod' => $lastmod,
                                            'priority' => '0.64',
                                        ), 
                               )
               );
}

                         
    $xmlData = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>');

    $class->arrayToXML($arrData,$xmlData); 
    $xmlContent = $xmlData->asXML(); 
    printToXML($xmlContent, $_SERVER ['DOCUMENT_ROOT'].'/sitemap/sitemap-'.DOMAIN_NAME.'.xml');


    function printToXML($dataToWrite, $fileName){
		 
//        $this->setLog($path.$fileName,true);
        echo '<br>'.$fileName.'<br>';
		file_put_contents($fileName,$dataToWrite); 
        
        echo 'done';
    }
?>