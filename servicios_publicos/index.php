<?php
    session_name('servicio_publico');
    session_start();

    require_once('../conexion.php');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Servicios Publicos</title>
</head>
<body>

<?php 

$insert_var = "INSERT INTO actualizar.servicio_publcico_prb() \n";

$insert_var .= "VALUES(";

$qry_sp = "SELECT
t1.\"id\",
t1.fecha,
t2.\"id\" AS id_actividad,
t1.actividad,
t1.cantidad,
t1.unidad_de_medida,
t3.id_barrio,
t1.barrio,
t1.desde_calle_nombre,
t1.desde_calle_nro,
t1.hasta_calle_nombre,
t1.hasta_calle_nro,
t1.sobrestante,
t1.\"vehículo\" AS vehiculo,
t1.empresa,
t1.observaciones
FROM actualizar.tabla_servicio_publico t1 
INNER JOIN actualizar.servicio_publico_actividad t2 ON t1.actividad = t2.actividad
INNER JOIN gismcc.barrio t3 ON t3.nombre_barrio = t1.barrio
where t2.\"id\" in (2, 5, 8, 13, 14)
limit 10
;";

$rst_sp = $conPdoPg->query($qry_sp);

while($reg_sp = $rst_sp->fetchObject()){

    // echo $reg_sp->id, $reg_sp->fecha, $reg_sp->actividad;
    // echo '<br>';

    if( !is_null($reg_sp->desde_calle_nombre) ){

        if( !is_null($reg_sp->hasta_calle_nombre )){

            if( $reg_sp->desde_calle_nombre == $reg_sp->hasta_calle_nombre ){

                if( !is_null($reg_sp->desde_calle_nro) ){

                    if( !is_null($reg_sp->hasta_calle_nro) ){

                        if( $reg_sp->desde_calle_nro == $reg_sp->hasta_calle_nro ){

                            $qry_calle = "SELECT *
                            FROM gismcc.calles t1
                            WHERE t1.nombre_calles = $reg_sp->desde_calle_nombre 
                            AND (t1.altura >= $reg_sp->desde_calle_nro)
                            ORDER BY t1.altura 
                            LIMIT 1
                            ;";

                            $rst_calle = $conPdoPg->query($qry_calle);

                            $str_value = '';

                            while( $reg_calle = $rst_calle->fetchObject() ){

                                $str_value .= $reg_sp->id . ", ";
                                $str_value .= "'" . $reg_sp->fecha . "', ";
                                $str_value .= $reg_sp->id_actividad . ", ";
                                $str_value .= $reg_sp->cantidad . ", ";
                                $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                                $str_value .= $reg_sp->id_barrio . ", ";
                                $str_value .= $reg_calle->id_calles . ", ";
                                $str_value .= "'" . $reg_sp->sobrestante . "', ";
                                $str_value .= "'" . $reg_sp->vehiculo . "', ";
                                $str_value .= "'" . $reg_sp->empresa . "', ";
                                $str_value .= "'" . $reg_sp->observaciones . "'";

                            }

                            $reg_calle = null;
                            $rst_calle = null;
                            
                        } else {

                            if( $reg_sp->desde_calle_nro > $reg_sp->hasta_calle_nro ){

                                $qry_calle = "SELECT *
                                FROM gismcc.calles t1
                                WHERE t1.nombre_calles = 'FRANCISCO ROMERO'
                                AND (t1.altura >= 3000 AND t1.altura <= 3300) -- AND (t1.altura >= hasta_calle_nro AND t1.altura <= desde_calle_nro)
                                ORDER BY t1.altura
                                ;";

                                $rst_calle = $conPdoPg->query($qry_calle);

                                while( $reg_calle = $rst_calle->fetchObject() ){

                                    $str_value .= $reg_sp->id . ", ";
                                    $str_value .= "'" . $reg_sp->fecha . "', ";
                                    $str_value .= $reg_sp->id_actividad . ", ";
                                    $str_value .= $reg_sp->cantidad . ", ";
                                    $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                                    $str_value .= $reg_sp->id_barrio . ", ";
                                    $str_value .= $reg_calle->id_calles . ", ";
                                    $str_value .= "'" . $reg_sp->sobrestante . "', ";
                                    $str_value .= "'" . $reg_sp->vehiculo . "', ";
                                    $str_value .= "'" . $reg_sp->empresa . "', ";
                                    $str_value .= "'" . $reg_sp->observaciones . "'";
                                }

                                $reg_calle = null;
                                $rst_calle = null;

                            } else {

                                $qry_calle = "SELECT *
                                FROM gismcc.calles t1
                                WHERE t1.nombre_calles = 'FRANCISCO ROMERO'
                                AND (t1.altura >= 3000 AND t1.altura <= 3300) -- AND (t1.altura >= desde_calle_nro AND t1.altura <= hasta_calle_nro)
                                ORDER BY t1.altura
                                ;";

                                $rst_calle = $conPdoPg->query($qry_calle);

                                while( $reg_calle = $rst_calle->fetchObject() ){

                                    $str_value .= $reg_sp->id . ", ";
                                    $str_value .= "'" . $reg_sp->fecha . "', ";
                                    $str_value .= $reg_sp->id_actividad . ", ";
                                    $str_value .= $reg_sp->cantidad . ", ";
                                    $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                                    $str_value .= $reg_sp->id_barrio . ", ";
                                    $str_value .= $reg_calle->id_calles . ", ";
                                    $str_value .= "'" . $reg_sp->sobrestante . "', ";
                                    $str_value .= "'" . $reg_sp->vehiculo . "', ";
                                    $str_value .= "'" . $reg_sp->empresa . "', ";
                                    $str_value .= "'" . $reg_sp->observaciones . "'";
                                }

                                $reg_calle = null;
                                $rst_calle = null;
                            }
                        }
                    } else {

                        $qry_calle = "SELECT *
                        FROM gismcc.calles t1
                        WHERE t1.nombre_calles = 'EVARISTO GONZALEZ'
                        AND (t1.altura >= 3050) -- t1.altura >= desde_calle_nro 
                        ORDER BY t1.altura 
                        LIMIT 1
                        ;";

                        $rst_calle = $conPdoPg->query($qry_calle);

                        while( $reg_calle = $rst_calle->fetchObject() ){

                            $str_value .= $reg_sp->id . ", ";
                            $str_value .= "'" . $reg_sp->fecha . "', ";
                            $str_value .= $reg_sp->id_actividad . ", ";
                            $str_value .= $reg_sp->cantidad . ", ";
                            $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                            $str_value .= $reg_sp->id_barrio . ", ";
                            $str_value .= $reg_calle->id_calles . ", ";
                            $str_value .= "'" . $reg_sp->sobrestante . "', ";
                            $str_value .= "'" . $reg_sp->vehiculo . "', ";
                            $str_value .= "'" . $reg_sp->empresa . "', ";
                            $str_value .= "'" . $reg_sp->observaciones . "'";
                        }

                        $reg_calle = null;
                        $rst_calle = null;
                    }
                } else {

                    if( !is_null($reg_calle->hasta_calle_nro)) {

                        $qry_calle = "SELECT *
                        FROM gismcc.calles t1
                        WHERE t1.nombre_calles = 'EVARISTO GONZALEZ'
                        AND (t1.altura >= 3050) -- t1.altura <= hasta_calle_nro 
                        ORDER BY t1.altura 
                        LIMIT 1
                        ;";

                        $rst_calle = $conPdoPg->query($qry_calle);

                        while( $reg_calle = $rst_calle->fetchObject() ){

                            $str_value .= $reg_sp->id . ", ";
                            $str_value .= "'" . $reg_sp->fecha . "', ";
                            $str_value .= $reg_sp->id_actividad . ", ";
                            $str_value .= $reg_sp->cantidad . ", ";
                            $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                            $str_value .= $reg_sp->id_barrio . ", ";
                            $str_value .= $reg_calle->id_calles . ", ";
                            $str_value .= "'" . $reg_sp->sobrestante . "', ";
                            $str_value .= "'" . $reg_sp->vehiculo . "', ";
                            $str_value .= "'" . $reg_sp->empresa . "', ";
                            $str_value .= "'" . $reg_sp->observaciones . "'";
                        }

                        $reg_calle = null;
                        $rst_calle = null;
                    } else {

                        echo $reg_sp->id, ' - ', 'ERROR >>>> Los campo "desde_calle_nro" y "hasta_calle_nro" estan con valor nulo';
                    }
                }
            }
        } else { // hasta_calle_nombre is null 

            if( !is_null($reg_sp->desde_calle_nro) ){ // desde_calle_nro is not null

                if( !is_null($reg_sp->hasta_calle_nro) ){ // hasta_calle_nro is not null

                    if( $reg_sp->desde_calle_nro == $reg_sp->hasta_calle_nro ){

                        $qry_calle = "SELECT *
                        FROM gismcc.calles t1
                        WHERE t1.nombre_calles = 'EVARISTO GONZALEZ'
                        AND (t1.altura >= desde_calle_nro)
                        LIMIT 1
                        ;";

                        $rst_calle = $conPdoPg->query($qry_calle);

                        while( $reg_calle = $rst_calle->fetchObject() ){

                            $str_value .= $reg_sp->id . ", ";
                            $str_value .= "'" . $reg_sp->fecha . "', ";
                            $str_value .= $reg_sp->id_actividad . ", ";
                            $str_value .= $reg_sp->cantidad . ", ";
                            $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                            $str_value .= $reg_sp->id_barrio . ", ";
                            $str_value .= $reg_calle->id_calles . ", ";
                            $str_value .= "'" . $reg_sp->sobrestante . "', ";
                            $str_value .= "'" . $reg_sp->vehiculo . "', ";
                            $str_value .= "'" . $reg_sp->empresa . "', ";
                            $str_value .= "'" . $reg_sp->observaciones . "'";
                        }

                        $reg_calle = null;
                        $rst_calle = null;
                    } else {

                        if( $reg_sp->desde_calle_nro > $reg_sp->hasta_calle_nro ){

                            $qry_calle = "SELECT *
                            FROM gismcc.calles t1
                            WHERE t1.nombre_calles = 'FRANCISCO ROMERO'
                            AND (t1.altura >= 3000 AND t1.altura <= 3300) -- AND (t1.altura >= hasta_calle_nro AND t1.altura <= desde_calle_nro)
                            ORDER BY t1.altura
                            ;";

                            $rst_calle = $conPdoPg->query($qry_calle);

                            while( $reg_calle = $rst_calle->fetchObject() ){

                                $str_value .= $reg_sp->id . ", ";
                                $str_value .= "'" . $reg_sp->fecha . "', ";
                                $str_value .= $reg_sp->id_actividad . ", ";
                                $str_value .= $reg_sp->cantidad . ", ";
                                $str_value .= "'" . $reg_sp->unidad_de_medida . "', ";
                                $str_value .= $reg_sp->id_barrio . ", ";
                                $str_value .= $reg_calle->id_calles . ", ";
                                $str_value .= "'" . $reg_sp->sobrestante . "', ";
                                $str_value .= "'" . $reg_sp->vehiculo . "', ";
                                $str_value .= "'" . $reg_sp->empresa . "', ";
                                $str_value .= "'" . $reg_sp->observaciones . "'";
                            }

                            $reg_calle = null;
                            $rst_calle = null;
                        }
                    }

                    
                } else {

                    if( $reg_sp->desde_calle_nro > $reg_sp->hasta_calle_nro ){

                    }
                }

            } else {
                
                if( !is_null($reg_sp->hasta_calle_nro) ){


                } else {

                    echo $reg_sp->id, ' - ', 'ERROR >>>> Los campo "desde_calle_nro" y "hasta_calle_nro" estan con valor nulo';
                }
            }
        }
    }

    if( !is_null($str_value) ){

        $insert_var .= $str_value . ')';

        echo $insert_var;

        echo '<br>';

    }
}

$reg_sp   = null;
$rst_sp   = null;
$conPdoPg = null;


?>
    
</body>
</html>