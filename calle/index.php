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

@error_reporting(E_ALL);

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
$usuario = 'carlosg';

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
$msg_update = '';
$actualizarRegistroCalle = false;
$ponerComa = false;

while($reg_calleOrigen = $rst_vw_calles26_02->fetchObject()){

    $stm_calleDestino->execute(array(':p1' => $reg_calleOrigen->id_calles));

    $reg_calleDestino = $stm_calleDestino->fetchObject();

    if($reg_calleDestino){


        // existe el id_calles en la tabla calles
        $qry_update = 'update gismcc.calles set ';

        $qry_update .= genStrUpdate('id_calle', $reg_calleOrigen->id_calle, $reg_calleDestino->id_calle);
        $qry_update .= genStrUpdate('nombre_calles', $reg_calleOrigen->nombre, $reg_calleDestino->nombre_calles);
        $qry_update .= genStrUpdate('id_tipo_calle', $reg_calleOrigen->id_tipo_ca, $reg_calleDestino->id_tipo_calle);
        $qry_update .= genStrUpdate('id_tipo_calzada', $reg_calleOrigen->id_tipo__1, $reg_calleDestino->id_tipo_calzada);
        $qry_update .= genStrUpdate('id_barrios', $reg_calleOrigen->id_barrios, $reg_calleDestino->id_barrios);
        $qry_update .= genStrUpdate('limite', $reg_calleOrigen->limite, $reg_calleDestino->limite);
        $qry_update .= genStrUpdate('altur_par', $reg_calleOrigen->altur_par, $reg_calleDestino->altur_par);
        $qry_update .= genStrUpdate('altur_impar', $reg_calleOrigen->altur_impa, $reg_calleDestino->altur_impar);
        $qry_update .= genStrUpdate('id_zonas_mantenimiento', $reg_calleOrigen->zonas_ct, $reg_calleDestino->id_zonas_mantenimiento);
        $qry_update .= genStrUpdate('nro_ordenanza', $reg_calleOrigen->nro_ordena, $reg_calleDestino->nro_ordenanza);
        $qry_update .= genStrUpdate('observacion', $reg_calleOrigen->observacio, $reg_calleDestino->observacion);
        $qry_update .= genStrUpdate('the_geom_calles', $reg_calleOrigen->the_geom_calles, $reg_calleDestino->the_geom_calles);
        $qry_update .= genStrUpdate('fecha_modificacion', "'" . date('Y-m-d H:m:s') . "'", '');
        $qry_update .= genStrUpdate('usuario', "'" . $usuario . "'", '');
        $qry_update .= genStrUpdate('id_traza', $reg_calleOrigen->id_traza, $reg_calleDestino->id_traza);
        $qry_update .= genStrUpdate('id_barrio_par', $reg_calleOrigen->id_barrio_, $reg_calleDestino->id_barrio_par);
        $qry_update .= genStrUpdate('id_barrio_impar', $reg_calleOrigen->id_barri_1, $reg_calleDestino->id_barrio_impar);

       $qry_update .= " where id_calles = $reg_calleOrigen->id_calles";

       echo $qry_update;

    } else {

        echo '<br />No existe el id_calles ' . $reg_calleOrigen;
    
    }

}




fclose($file);

$stm_calleDestino = null;
$rst_vw_calles26_02 = null;
$conPdoPg = null;

function genStrUpdate($campoDestino, $origen, $destino){

    global $actualizarRegistroCalle;

    $ret = '';
    
    $distintos = false;

    if($origen != $destino){

        if($actualizarRegistroCalle){

            $ret = ', ' . $campoDestino . ' = ';
            $ret .= is_null($origen) ? "null" : $origen;

        } else {

            $ret = $campoDestino . ' = ' . $origen;

            $actualizarRegistroCalle = true;

        }

    }

    return $ret;

}