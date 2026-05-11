<?php

require_once '_config.php';
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

$class_methods = get_class_methods($class);
sort($class_methods);
foreach($class_methods as $method){ 
    echo '<div style="font-weight:bold">'.$method.'</div>';  
    echo '<div style="height:0.5em"></div>';  
    
    $r = new ReflectionMethod($class, $method);
    $params = $r->getParameters();
    echo '<table  style="margin:0; border:1px solid #999;">';
    echo '<tr>'; 
    echo '<td style="width:300px; font-weight:bold">Argument</td>';
    echo '<td style="width:100px; text-align:center; font-weight:bold">Optional</td>';  
    echo '</tr>';
    foreach ($params as $param) {
        echo '<tr>'; 
        echo '<td style="border-bottom:1px solid #999;">'.$param->getName().'</td>';
        echo '<td style="border-bottom:1px solid #999;text-align:center;">'.(($param->isOptional()) ? 'Optional' : '').'</td>';  
        echo '</tr>';
    }
    echo '</table>';
    echo '<div style="height:2em"></div>';  
}
 
?>