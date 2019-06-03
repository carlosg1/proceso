<?php 
 /* 
  * proceso de control de la tabla de calles
  * Developer: Carlos Garcia > carlosgctes@gmail.com
  * Modificado: 03/06/2019
  *
  */

@error_reporting(E_ALL);

set_time_limit(2400);

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
$tablaOrigen = "vw_calles31_05";
$esquemaOrigen = "actualizar";
$usuario = 'carlosg';

/************************ verificaciones sobre la llave *******************************/

// verifico si hay id_calles duplicados
try{

    $qry_duplicados = 'select id_calles, "count"(id_calles) as cantidad
    from "' . $esquemaOrigen .  '"."' . $tablaOrigen . '" group by id_calles having "count"(id_calles) > 1';

    $rst_duplicados = $conPdoPg->query($qry_duplicados);

    if($rst_duplicados && $rst_duplicados->rowCount() > 0){

        $ejecutar = false;

        fwrite($file, "\n\nHay registros duplicados en la tabla: " . $tablaOrigen . PHP_EOL);

        echo "<br /><br />Hay registros duplicados en la tabla: " . $tablaOrigen;

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

    $qry_vw_calles26_02 = 'select * from "' . $esquemaOrigen . '"."' . $tablaOrigen . '" order by id_calles';

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

    $seguir = true;

    // controla id_tipo_calle
    if(is_null($reg_calleOrigen->id_tipo_ca)) { 
        //$seguir = false; 
        echo '<br /><br />ERROR!!!  id_calles: ' . $reg_calleOrigen->id_calles . ' -  FALTA TIPO DE CALLE...<br /><br />';
    }

    // controlo tipo de calzada
    if(is_null($reg_calleOrigen->id_tipo__1)) { 
        //$seguir = false; 
        echo '<br /><br />ERROR!!!  id_calles: ' . $reg_calleOrigen->id_calles . ' - FALTA TIPO DE CALZADA...<br /><br />';
    }


    if($seguir) {
        
        if($reg_calleDestino){

            $qry_update = 'update gismcc.calles set ';
            
            $qry_update .= genStrUpdate('nombre_calles', "'" . $reg_calleOrigen->nombre . "'", "'" . $reg_calleDestino->nombre_calles . "'");
            $qry_update .= genStrUpdate('id_tipo_calle', $reg_calleOrigen->id_tipo_ca, $reg_calleDestino->id_tipo_calle);
            $qry_update .= genStrUpdate('id_tipo_calzada', $reg_calleOrigen->id_tipo__1, $reg_calleDestino->id_tipo_calzada);
            $qry_update .= genStrUpdate('id_barrios', $reg_calleOrigen->id_barrios, $reg_calleDestino->id_barrios);
            $qry_update .= genStrUpdate('limite', "'" . $reg_calleOrigen->limite . "'", "'" . $reg_calleDestino->limite . "'");
            $qry_update .= genStrUpdate('altur_par', $reg_calleOrigen->altur_par, $reg_calleDestino->altur_par);
            $qry_update .= genStrUpdate('altur_impar', $reg_calleOrigen->altur_impa, $reg_calleDestino->altur_impar);
            $qry_update .= genStrUpdate('id_zonas_mantenimiento', $reg_calleOrigen->zonas_ct, $reg_calleDestino->id_zonas_mantenimiento);
            $qry_update .= genStrUpdate('nro_ordenanza', "'" . $reg_calleOrigen->nro_ordena . "'", "'" . $reg_calleDestino->nro_ordenanza . "'");
            $qry_update .= genStrUpdate('observacion', "'" . $reg_calleOrigen->observacio . "'", "'" . $reg_calleDestino->observacion . "'");
            $qry_update .= genStrUpdate('the_geom_calles', "'" . $reg_calleOrigen->the_geom_calles . "'", "'" . $reg_calleDestino->the_geom_calles . "'");
            $qry_update .= genStrUpdate('fecha_modificacion', "'" . date('Y-m-d H:m:s') . "'", '');
            /* fecha_alta dejo como esta, sin modificar */
            $qry_update .= genStrUpdate('usuario', "'" . $usuario . "'", "'" . $reg_calleDestino->usuario . "'");
            $qry_update .= genStrUpdate('id_traza', $reg_calleOrigen->id_traza, $reg_calleDestino->id_traza);
            $qry_update .= genStrUpdate('id_barrio_par', $reg_calleOrigen->id_barrio_, $reg_calleDestino->id_barrio_par);
            $qry_update .= genStrUpdate('id_barrio_impar', $reg_calleOrigen->id_barri_1, $reg_calleDestino->id_barrio_impar);
            $qry_update .= genStrUpdate('estado_calle', "'" . $reg_calleOrigen->estado_cal . "'", "'" . $reg_calleDestino->estado_calle . "'");
            $qry_update .= genStrUpdate('sentido_circulacion', "'" . $reg_calleOrigen->sentido_ci . "'", "'" . $reg_calleDestino->sentido_circulacion . "'");
            $qry_update .= genStrUpdate('id_secuencia_traza', $reg_calleOrigen->id_secuenc, $reg_calleDestino->id_secuencia_traza);
            $qry_update .= genStrUpdate('altura', $reg_calleOrigen->altura, $reg_calleDestino->altura);
            $qry_update .= genStrUpdate('resolucion', "'" . $reg_calleOrigen->resol . "'", "'" . $reg_calleDestino->resolucion . "'");
            $qry_update .= genStrUpdate('clasif_vial', $reg_calleOrigen->clasif_via, $reg_calleDestino->clasif_vial);
            $qry_update .= genStrUpdate('clasif_red', $reg_calleOrigen->clasif_red, $reg_calleDestino->clasif_red);
            $qry_update .= genStrUpdate('zonas_ct', $reg_calleOrigen->zonas_ct, $reg_calleDestino->zonas_ct);
            $qry_update .= genStrUpdate('id_calle', $reg_calleOrigen->id_calle, $reg_calleDestino->id_calle);

            $qry_update .= " where id_calles = $reg_calleOrigen->id_calles; ";

            try {

                $rst_update = $conPdoPg->query($qry_update);

            } catch (Exception $e){

                echo $e->getMessage();

            }

            // $rst_update = true; // borrar despues

            if($rst_update){

                // echo $qry_update . '<br /><br />';

                echo '"' . $reg_calleOrigen->id_calles . '";"Actualizado"' . '<br />';

                fwrite($file, '"' . $reg_calleOrigen->id_calles . '";"Actualizado"' . PHP_EOL);

            } else {

                echo 'ERROR: No se actualizo el registro: ' . $reg_calleOrigen->id_calles . '<br /><br />';

                echo $qry_update . '<br /><br />';

            }

            $rst_update = null;

        } else {

            // agrego el registro que no existe en la tabla calles

            $id_calles = is_null($reg_calleOrigen->id_calles) ? 'null' : "'" . $reg_calleOrigen->id_calles . "'";
            $nombre = is_null($reg_calleOrigen->nombre) ? 'null' : "'" . $reg_calleOrigen->nombre . "'";
            $id_tipo_ca = is_null($reg_calleOrigen->id_tipo_ca) ? 'null' : $reg_calleOrigen->id_tipo_ca;
            $id_tipo__1 = is_null($reg_calleOrigen->id_tipo__1) ? 'null' : $reg_calleOrigen->id_tipo__1;
            $id_barrios = is_null($reg_calleOrigen->id_barrios) ? 'null' : $reg_calleOrigen->id_barrios;
            $limite = is_null($reg_calleOrigen->limite) ? 'null' : "'" . $reg_calleOrigen->limite . "'";
            $altur_par = is_null($reg_calleOrigen->altur_par) ? 'null' : $reg_calleOrigen->altur_par;
            $altur_impa = is_null($reg_calleOrigen->altur_impa) ? 'null' : $reg_calleOrigen->altur_impa;
            $zonas_ct = is_null($reg_calleOrigen->zonas_ct) ? 'null' : $reg_calleOrigen->zonas_ct;
            $nro_ordena = is_null($reg_calleOrigen->nro_ordena) ? 'null' : "'" .  $reg_calleOrigen->nro_ordena . "'";
            $observacio = is_null($reg_calleOrigen->observacio) ? 'null' : "'" .  $reg_calleOrigen->observacio . "'";
            $the_geom = is_null($reg_calleOrigen->the_geom_calles) ? 'null' : "'" .  $reg_calleOrigen->the_geom_calles . "'";
            $id_traza = is_null($reg_calleOrigen->id_traza) ? 'null' : $reg_calleOrigen->id_traza;
            $id_barrio_ = is_null($reg_calleOrigen->id_barrio_) ? 'null' : $reg_calleOrigen->id_barrio_;
            $id_barri_1 = is_null($reg_calleOrigen->id_barri_1) ? 'null' : $reg_calleOrigen->id_barri_1;
            $estado_cal = is_null($reg_calleOrigen->estado_cal) ? 'null' : "'" .  $reg_calleOrigen->estado_cal . "'";
            $sentido_ci = is_null($reg_calleOrigen->sentido_ci) ? 'null' : "'" .  $reg_calleOrigen->sentido_ci . "'";
            $id_secuenc = is_null($reg_calleOrigen->id_secuenc) ? 'null' : $reg_calleOrigen->id_secuenc;
            $altura = is_null($reg_calleOrigen->altura) ? 'null' : $reg_calleOrigen->altura;
            $clasif_via = is_null($reg_calleOrigen->clasif_via) ? 'null' : $reg_calleOrigen->clasif_via;
            $clasif_red = is_null($reg_calleOrigen->clasif_red) ? 'null' : $reg_calleOrigen->clasif_red;
            $id_calle = is_null($reg_calleOrigen->id_calle) ? 'null' : $reg_calleOrigen->id_calle;
            $resol = is_null($reg_calleOrigen->resol) ? 'null' : "'" .  $reg_calleOrigen->resol . "'";
            

            try {

                $qry_insert = "insert into gismcc.calles values($id_calles, $nombre, $id_tipo_ca, $id_tipo__1, $id_barrios, $limite, $altur_par, $altur_impa, $zonas_ct, $nro_ordena, $observacio, $the_geom, now(), now(), '$usuario', $id_traza, null, null, $id_barrio_, $id_barri_1, $estado_cal, $sentido_ci, $id_secuenc, $altura, $resol, $clasif_via, $clasif_red, $zonas_ct, $id_calle); ";

                $rst_insert = $conPdoPg->query($qry_insert);

                if($rst_insert){

                    echo '"' . $reg_calleOrigen->id_calles . '"' . ';"Insertado"' . '<br />';
                
                    fwrite($file, '"' . $reg_calleOrigen->id_calles . '"' . ';"Insertado"' . PHP_EOL);

                } else {

                    echo 'ERROR!!! No se pudo insertar el registro: id_calles: ' . $reg_calleOrigen->id_calles . '<br /><br />';

                    echo $qry_insert . '<br /><br />';

                }
            } catch (PDOException $e){
                $e->getMessage();
            }

            $rst_insert = null;

        }

    }

    $actualizarRegistroCalle=false;

}

fclose($file);

$stm_calleDestino = null;
$rst_vw_calles26_02 = null;
$conPdoPg = null;

print "***** PROCESO FINALIZADO *****";

function genStrUpdate($campoDestino, $origen, $destino){

    global $actualizarRegistroCalle;

    $ret = '';
    
    $distintos = false;

    if($origen != $destino){

        if($actualizarRegistroCalle){

            $ret = ', ' . $campoDestino . ' = ';
            $ret .= is_null($origen) ? "null" : $origen;

            $actualizarRegistroCalle = true;

        } else {

            $ret = $campoDestino . ' = ' . $origen;

            $actualizarRegistroCalle = true;

        }

    }

    return $ret;

}