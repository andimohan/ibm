<?php  

// tarik dari tmp tujuan aj, biar gk repot akses db di class
// credential db tujuan dan asal harus sama

require_once '../_config.php';
require_once '../_include-v2.php';


if(!$security->isAdminLogin('ChartOfAccount',10,true)); 

ini_set('max_execution_time', '6000'); //300 seconds = 5 minutes


includeClass(array('ChartOfAccount.class.php','GeneralJournal.class.php'));
$coa = new ChartOfAccount();
$glObj = new GeneralJournal();

$result = '';

// tembak mati aj agar aman

if (DOMAIN_NAME != 'okldemo.wintera.co.id') die;

$sourceDomain = 'okl.wintera.co.id';
$sourceDbCon = newConnection($sourceDomain);

$sql = 'select distinct(year(trdate)) as year from general_journal_header order by year(trdate) desc';
$rsYear = $sourceDbCon->doQuery($sql); 
 
$arrAvailableYear = array();
foreach($rsYear as $row) 
    $arrAvailableYear[$row['year']] = array('label' => $row['year']);

$arrAction = array();
$arrAction[1] = array('label' => 'Hapus');
$arrAction[2] = array('label' => 'Hapus & Import');

if (isset($_POST) && !empty($_POST['action']) && !empty($_POST['year'])){

    if ($_POST['action'] != 'importData') die;
    
    // import ulang COA, karena ad kemungkinan ganti atau nambah COA
    
    $activeYear = $_POST['year'];
    
    // ambi ldata dari source
    
    // gk bisa pake insert select karena pkeynya berbeda dengan tujuan
    $sql = 'select * from general_journal_header  where statuskey in (2,3) and year(trdate) = ' . $sourceDbCon->paramString($activeYear);
    $rsHeader = $sourceDbCon->doQuery($sql);
    $arrPkey = array_column($rsHeader,'pkey');
    
    $sql = 'select * from general_journal_detail  where  refkey in ('.$sourceDbCon->paramString($arrPkey,',').')';
    $rsDetailCol = $sourceDbCon->doQuery($sql);
    $rsDetailCol = $class->reindexDetailCollections($rsDetailCol, 'refkey');
    
    $sql = 'select * from chart_of_account';
    $rsCOA = $sourceDbCon->doQuery($sql);
  
    

    // update db tujuan
    $sql = array();
    array_push($sql,'delete from general_journal_header where annualclosingjournal = 0 and year(trdate) = ' . $class->oDbCon->paramString($activeYear));
    array_push($sql,'delete from general_journal_detail where refkey not in (select pkey from general_journal_header)');


    $class->oDbCon->startTrans(true);
    foreach($sql as $row){
       $class->oDbCon->execute($row); 
    }

    $class->oDbCon->endTrans();

    // kalo tombolnya reiimport ulang 
    
    if ($_POST['selAction'] == 2){  
        
            // import ulang COA, karena ad kemungkinan nambah akun baru 
            // gk bisa pake kode karena bisa berbeda
            $sql = 'delete from chart_of_account';
            $class->oDbCon->execute($sql);
            
            $sql = 'show columns from chart_of_account';
            $rsHeaderColumns = $class->oDbCon->doQuery($sql);
            $rsHeaderColumns = array_column($rsHeaderColumns,'Field');
        

            // utk menghindari field yg mirip keyword
            foreach($rsHeaderColumns as $key => $fieldRow)
                $rsHeaderColumns[$key] = '`'.$fieldRow.'`';
        
            foreach($rsCOA as $row){
                 $headerValues = array();
                 foreach($rsHeaderColumns as $headerCol)  { 
                    array_push($headerValues, $class->oDbCon->paramString($row[str_replace('`','',$headerCol)]) );

                 }
                
                $sql = 'insert into chart_of_account ('.implode(',',$rsHeaderColumns).') values('.implode(',',$headerValues).')';
                $class->oDbCon->execute($sql); 
            }
            
        
            // import jurnal
            // ambil pkey terakhir dari destination
            $sql = 'select pkey from  general_journal_header order by pkey desc limit 1'; 
            $rsHeaderPkey = $class->oDbCon->doQuery($sql);
            $lastPkey = $rsHeaderPkey[0]['pkey'];

            // ambil struktur kolom selain pkey
            $sql = 'show columns from general_journal_header';
            $rsHeaderColumns = $class->oDbCon->doQuery($sql);
            $rsHeaderColumns = array_column($rsHeaderColumns,'Field');

            $sql = 'show columns from general_journal_detail where field <> \'pkey\'';
            $rsDetailColumns = $class->oDbCon->doQuery($sql);
            $rsDetailColumns = array_column($rsDetailColumns,'Field');

            $class->oDbCon->startTrans(true);


            foreach($rsHeader as $headerRow){

                $lastPkey++;

                $rsDetail = $rsDetailCol[$headerRow['pkey']];

                $headerValues = array();
                $pkey = 0;
                foreach($rsHeaderColumns as $headerCol)  {
                    if($headerCol == 'pkey')  
                        $headerRow[$headerCol] = $lastPkey;

                    if ($headerCol == 'code'){
                        $headerRow[$headerCol] = $glObj->getNewCustomCode(array('code' => 'xxxxx','trDate' => $class->formatDBDate($headerRow['trdate'])))[0]; 
                    }

                    array_push($headerValues, $class->oDbCon->paramString($headerRow[$headerCol]) );

                }

                $sql = 'insert into general_journal_header ('.implode(',',$rsHeaderColumns).') values('.implode(',',$headerValues).')';
                $class->oDbCon->execute($sql);


                foreach($rsDetail as $detailRow){

                    $detailValues = array();  

                     foreach($rsDetailColumns as $detailColumn){ 
                        if($detailColumn == 'refkey')
                            $detailRow[$detailColumn] = $lastPkey;  

                        array_push($detailValues, $class->oDbCon->paramString($detailRow[$detailColumn]) );
                    }



                    $sql = 'insert into general_journal_detail ('.implode(',',$rsDetailColumns).') values('.implode(',',$detailValues).')';
                    $class->oDbCon->execute($sql); 

                } 

            }

            // hitung ulang semua COA 
            $sql = 'select pkey from   chart_of_account where parentkey = 0  '; 
            $rs = $class->oDbCon->doQuery($sql);

            for($i=0;$i<count($rs);$i++)
                $coa->updateParentAmountFromRoot($rs[$i]['pkey']); 

            $coa->updateCurrentYearEarnings();  

            $class->oDbCon->endTrans();

    }
    
    $result  = 'Update data Tahun '.$activeYear.' selesai';
    $class->setLog($result,true);
    echo $result;
    
    die;
}



 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">   
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  

<script>
    jQuery(document).ready(function(){  
         
        function disabledButton($obj,status){

            if (status == undefined)
                status = true; 

            $obj.each(function(i) {     
                $(this).prop('disabled', status);

                if (status == true) 
                    $(this).find(".loading-icon").show(); 
                else 
                    $(this).find(".loading-icon").hide();  

            });
 
        }

         
        function parseJSON(data){ 

            data = $.trim(data);

            if(!data) data = '[]'; 
            if(data.length == 0) data = '[]'; 

            return JSON.parse(data);
        }


        $("[name=btnSubmit]").on('click',function() { 
            
            var year = $("[name=selYear]").val();
            
            if (confirm('Apakah Anda yakin akan mengimport data tahun '+year+' ?')) {
              // Save it!
                $.ajax({
                    type: "POST",
                    timeout: 3600000,
                    url:  'exportToArchive.php',  
                    data: "action=importData&year=" +  year +'&selAction='+ $("[name=selAction]").val(),  
                    beforeSend : function (){
                         $("#result-panel").html("");
                         disabledButton($("[name=btnSubmit]"));
                     },
                }).done(function( data ) {  

                     $("#result-panel").html(data);
                     disabledButton($("[name=btnSubmit]"), false);

                }); 
            } else {
              // Do nothing!
             
            }
          
            
        }); 
         
        
    }) ;
     
</script>    
    
<title>Import Data</title>  
</head> 
<body>    
    
<div style="padding: 1em">  
    <form action="exportToArchive.php" method="post" enctype="multipart/form-data" id="form-import"> 
        <div class="div-table">
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Periode</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSelect('selYear', $arrAvailableYear); ?></div>
            </div>
           <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Aksi</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSelect('selAction',$arrAction); ?></div>
            </div>
             
            <div class="div-table-row">
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"></div> 
                <div class="div-table-col-5"><?php echo $class->inputButton('btnSubmit','Submit'); ?></div> 
            </div>
        </div> 
    </form>
</div>  
    
<div id="result-panel" style="border-top:1px solid #333;  padding: 1em">
    
</div>    
</body> 
</html> 
