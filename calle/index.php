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
  * fecha: 05/03/2019
  *
  */

header("Content-Type: text/html");
header("charset: utf-8");

@error_reporting(E_ALL);

require('../conexion.php');

$qry_vw_calles26_02 = "select * from \"gismcc\".\"vw_calles26-02\" order by id_calles limit 20";

$rst_vw_calles26_02 = $conPdoPg->query($qry_vw_calles26_02);

$file = fopen("AnalisisCalle.log", "w");

fwrite($file, "\n\nInicio Analisis " . date('d/m/Y H:i:s') . PHP_EOL);

/***** imprime los resultados  *****/
echo 'id_calles | Observacion' . "<br />";

fwrite($file, 'id_calles | Observacion' . PHP_EOL);

while($registro = $rst_vw_calles26_02->fetchObject()){

    echo $registro->id_calles . ' => ' . "Se recorrio el registro como prueba", '<br />';

    fwrite($file, $registro->id_calles . ' => ' . "Se recorrio el registro como prueba" . PHP_EOL);

}

fclose($file);

$rst_vw_calles26_02 = null;
$conPdoPg = null;
?>
