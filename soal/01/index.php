<?php 

$inputSorted = array();
$inputError = array();
$inputOdd = array();
$inputEven = array();

if (isset($_POST) && !empty($_POST['textInput'])){
    $input = explode(chr(13),$_POST['textInput']);
    
    foreach($input as $row){
        $row = trim($row);
        if(empty($row)) continue;
        
        if (!is_numeric($row)){ 
            array_push($inputError, $row); 
                
        }else{ 
            
            if($row%2==0)
                array_push($inputOdd, $row);
            else
                array_push($inputEven, $row);
            
            array_push($inputSorted, $row);
        }
    }  
   sort($inputSorted); 
   sort($inputOdd); 
   sort($inputEven); 
}else{
    $arr = array('','  ', 'Eko', 'Curut', '23', 'Agung', '5','1', 'Edo','','   ','','100','25','Leo','22','14','0','Herman','Irman','74','   ','','21','53','2');
    $_POST['textInput'] = implode(chr(13),$arr);
}

?>
<body>
    
Rules :<br>
1. Pisahkan antara angka dan bukan angka yang diinput di kolom "A"<br>    
2. Untuk inputan angka dipisahkan dan <b>diurutkan</b> di kolom "B"<br>    
3. Untuk inputan angka <b>genap</b> dipisahkan dan <b>diurutkan</b> di kolom "C"<br>    
4. Untuk inputan angka <b>ganjil</b> dipisahkan dan <b>diurutkan</b> di kolom "D"<br>    
5. Untuk inputan bukan angka dipisahkan di kolom "E"<br>    
6. Untuk baris kosong, diabaikan / dilewatkan.<br>    
    
    
<br><br>    
<form method="post">
<table>
<tr>
    <td>A. Input</td>
    <td>B. Angka</td>
    <td>C. Angka Genap</td>
    <td>D. Angka Ganjil</td>
    <td>E. Bukan Angka</td>
</tr>    
<tr>
    <td><textarea name="textInput" style="height: 20em; width: 10em"><?php  echo (isset($_POST) && !empty($_POST['textInput'])) ? $_POST['textInput'] : ''; ?></textarea> </td>
    <td><textarea name="textInputSorted" style="height: 20em;  width: 10em"><?php  echo implode(chr(13),$inputSorted);?></textarea></td>
    <td><textarea name="textInputOdd" style="height: 20em;  width: 10em"><?php  echo implode(chr(13),$inputOdd);?></textarea></td>
    <td><textarea name="textInputEven" style="height: 20em;  width: 10em"><?php  echo implode(chr(13),$inputEven);?></textarea></td>
    <td><textarea name="textInputError" style="height: 20em;  width: 10em"><?php  echo implode(chr(13),$inputError); ?></textarea></td>
</tr>    
</table>     
<br><br>    
<input type="submit" >
</form>
</body>