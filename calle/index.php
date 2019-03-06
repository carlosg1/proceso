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
    <h2>Analiza la tabla de calles</h2>
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

/* validacion de ususario */
/*
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Mi dominio"');
    header('HTTP/1.0 401 Unauthorized');
    echo '<h1>Acceso cancelado por el usuario</h1>';
    exit;
} else {
    echo "<p>Hola {$_SERVER['PHP_AUTH_USER']}.</p>";
    echo "<p>Introdujo {$_SERVER['PHP_AUTH_PW']} como su contraseña.</p>";
}
*/

@error_reporting(E_ALL);

require('../conexion.php');

// archivo para guardar datos y escribo el timestamp del inicio del analisis
$file = fopen("AnalisisCalle.log", "w");

fwrite($file, "\n\nInicio Analisis " . date('d/m/Y H:i:s') . PHP_EOL);

echo "Inicio Analisis ", date('d/m/Y H:i:s');

// variables de apoyo
$ejecutar = true;
$tabla = "vw_calles26-02";

/************************ verificaciones sobre la llave *******************************/

// verifico si hay id_calles duplicados
$qry_duplicados = 'select id_calles, "count"(id_calles) as cantidad
from "gismcc"."vw_calles26-02"
group by id_calles 
having "count"(id_calles) > 1';

try{
    $rst_duplicados = $conPdoPg->query($qry_duplicados);

    if($rst_duplicados->rowCount() > 0){

        $ejecutar = false;

        fwrite($file, "\n\nHay registros duplicados en la tabla: \"vw_calles26-02\" " . PHP_EOL);

        echo "<br /><br />Hay registros duplicados en la tabla: \"vw_calles26-02\"";

    }

} catch (Exception $e){
    print $e->getMessage();
    exit;
}

$rst_duplicados = null;


// verifico si hay id_calles con valor cero

// verifico si hay id_calles en null


if(!$ejecutar){
    fclose($file);
    exit;
}


/***** inicio del recorrido de la tabla usada para actualizar la tabla de calles *****/

$qry_vw_calles26_02 = "select * from \"gismcc\".\"vw_calles26-02\" where id_calles = 1638 order by id_calles limit 10";


try {

    /* leo la capa vw_calles26-02 */
    $qry_vw_calles26_02 = "select * from \"gismcc\".\"vw_calles26-02\" order by id_calles limit 10";

    $rst_vw_calles26_02 = $conPdoPg->query($qry_vw_calles26_02);

} catch (PDOException $e){
    print $e;
    exit;
}

/***** imprime los resultados  *****/
echo 'id_calles | Observacion' . "<br />";

fwrite($file, 'id_calles | Observacion' . PHP_EOL);

/* preparo la consulta para consultar la tabla calles */
$qry_calles = 'select * from gismcc.calles where id_calles = :p1';

$stm_calles = $conPdoPg->prepare($qry_calles, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));


while($registro = $rst_vw_calles26_02->fetchObject()){

    $stm_calles->execute(array(':p1' => $registro->id_calles));

    $resultado = $stm_calles->fetchAll();

    var_dump( $resultado );

}

fclose($file);

$stm_calles = null;
$rst_vw_calles26_02 = null;
$conPdoPg = null;

