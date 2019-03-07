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
  * fecha: 06/03/2019
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
echo '<br />';

// variables de apoyo
$ejecutar = true;
$tablaOrigen = "vw_calles26-02";
$esquemaOrigen = "gismcc";

/************************ verificaciones sobre la llave *******************************/

// verifico si hay id_calles duplicados
try{

    $qry_duplicados = 'select id_calles, "count"(id_calles) as cantidad
    from "' . $esquemaOrigen .  '"."' . $tablaOrigen . '" group by id_calles having "count"(id_calles) > 1';

    $rst_duplicados = $conPdoPg->query($qry_duplicados);

    if($rst_duplicados && $rst_duplicados->rowCount() > 0){

        $ejecutar = false;

        fwrite($file, "\n\nHay registros duplicados en la tabla: \"vw_calles26-02\" " . PHP_EOL);

        echo "<br /><br />Hay registros duplicados en la tabla: \"vw_calles26-02\"";

    }

} catch (Exception $e){
    print $e->getMessage();
    exit;
}

// $rst_duplicados = null;

// verifico si hay id_calles con valor cero

// verifico si hay id_calles en null


if(!$ejecutar){
    fclose($file);
    exit;
}


/***** inicio del recorrido de la tabla usada para actualizar la tabla de calles *****/

try {

    $qry_vw_calles26_02 = 'select * from "' . $esquemaOrigen . '"."' . $tablaOrigen . '" where id_calles = 1683 order by id_calles limit 10';

    $rst_vw_calles26_02 = $conPdoPg->query($qry_vw_calles26_02);

} catch (PDOException $e){
    print $e->getMessage();
    exit;
}

/***** imprime los resultados  *****/
fwrite($file, 'id_calles | Observacion' . PHP_EOL);
// echo 'id_calles | Observacion' . "<br />";

/* preparo la consulta para consultar la tabla calles */
$qry_calles = 'select * from gismcc.calles where id_calles = :p1';

$stm_calleDestino = $conPdoPg->prepare($qry_calles, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

/* preparo la consulta para actualizar */
$qry_update = 'update gismcc.calles set ';
$msg_update = '';
$actualizarRegistroCalle = false;
$ponerComa = false;

while($reg_calleOrigen = $rst_vw_calles26_02->fetchObject()){

    $stm_calleDestino->execute(array(':p1' => $reg_calleOrigen->id_calles));

    $reg_calleDestino = $stm_calleDestino->fetchObject();

    if($reg_calleDestino){
        // existe el id_calles en la tabla calles
       $qry_update = 'update gismcc.calles '; 
       $qry_update .= 'set id_calle = ' . $reg_calleOrigen->id_calle;
       $qry_update .= ", nombre_calles = '$reg_calleOrigen->nombre'";
       $qry_update .= ", id_tipo_calle = $reg_calleOrigen->id_tipo_ca";
       $qry_update .= ", id_tipo_calzada = " . (is_null($reg_calleOrigen->id_tipo__1) ? "null" : $reg_calleOrigen->id_tipo__1);
       $qry_update .= ", id_barrios = $reg_calleOrigen->id_barrios";


       $qry_update .= " where id_calles = $reg_calleOrigen->id_calles";

       echo $qry_update;

    } else {
        echo '<br />No existe el id_calles ' . $reg_calleOrigen;
    }

    /*
    if($reg_calleDestino->id_calle != $reg_calleOrigen->id_calle){

        $qry_update .= 'id_calle = ' . $reg_calleOrigen->id_calle;
        
        $msg_update .= '&nbsp;&nbsp;Se actualizo id_calle : ' . (is_null($reg_calleDestino->id_calle) ? 'NULL' : $reg_calleDestino->id_calle) . ' => ' . $reg_calleOrigen->id_calle;

        $actualizarRegistroCalle = true; // quiere decir que hay que actualizar el registro al final de lo if's

        $ponerComa = true; // para que ponga una coma adelante de los siguientes campos que se van a actualizar
    
    }
*/
/*
    if($actualizarRegistroCalle){
        echo '<br />' . $qry_update;
        echo '<br />' . $msg_update;
    }
*/
}




fclose($file);

$stm_calleDestino = null;
$rst_vw_calles26_02 = null;
$conPdoPg = null;

