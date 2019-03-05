<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" type="image/vnd.microsoft.icon" href="../images/favicon.ico">

    <title>Analisis de calles</title>
</head>
<body>
    
</body>
</html><?php 
 /* 
  * proceso de control de la tabla de calles
  * Developer: Carlos Garcia > carlosgctes@gmail
  / fecha: 05/03/2019
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
