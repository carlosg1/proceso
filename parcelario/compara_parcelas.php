<?php

require_once('conPDO1921681051.php');

$qry = "select t1.column_name as columna
from information_schema.columns  t1 
where t1.table_schema = 'gismcc' and t1.table_name = 'parcelas' order by ordinal_position;";

$qry = $conn->query($qry);

echo '<div style="width:48%;border:1px solid #7c7c7c;">';

while($ren = $qry->fetch(PDO::FETCH_OBJ)){
    echo $ren->columna . '<br>';
}

echo '</div>';

$qry = null;


?>