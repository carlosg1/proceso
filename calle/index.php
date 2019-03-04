<?php 
 /* 
  * proceso de control de la tabla de calles
  * Developer: Carlos Garcia > carlosgctes@gmail
  */

require('../conexion.php');

$qry_vw_calles26_02 = "select * from \"gismcc\".\"vw_calles26-02\" order by id_calles limit 15";

$rst_vw_calles26_02 = $conPdoPg->query($qry_vw_calles26_02);

/***** imprime los resultados  *****/
echo '"id_calles";"the_geom"' . "<br />";

while($registro = $rst_vw_calles26_02->fetchObject()){

    echo $registro->id_calles, ';', $registro->the_geom_calles, '<br />';


}

$rst_vw_calles26_02 = null;
$conPdoPg = null;
?>