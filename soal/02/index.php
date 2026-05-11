Rules :<br>
1. Generate angka random (1 hingga 100) hingga jumlah kedua bilangan terakhir adalah bilangan prima.
<br>

<?php  
$arrNumbers = array();

$flag = true;
while($flag){
    $newNumber = rand(1,100);
    
    $count = count($arrNumbers);
    if($count > 1  && hitungPrima( $arrNumbers[$count-1] + $newNumber  ) )  $flag = false;
    
    array_push($arrNumbers, $newNumber); 
}

for($i=0;$i<count($arrNumbers); $i++){ 
    echo ($i==count($arrNumbers)-1 || $i==count($arrNumbers)-2) ? '<span style="color:#F00">'.$arrNumbers[$i].'</span>' : $arrNumbers[$i];
    echo ' '; 
}

function hitungPrima($angka){
    
    if ($angka == 0 || $angka == 1) return false;
    
    for($i=2;$i<$angka/2;$i++)
        if($angka % $i == 0) return false;
        
    return true;
}

?>  