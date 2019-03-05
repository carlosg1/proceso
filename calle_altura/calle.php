<?php

//
// crea un archivo csv donde pone las alturas desde y hasta
// obteniendo el siguiente tramo, desde el campo secuencia de tramos
// Desarrollo: Lic. Carlos Garcia - carlosgctes@gmail.com
//
header("Content-Type: text/html");
header("charset=latin-1");

include('con_pdopg_8_81.php');

$qry_id_traza = "SELECT DISTINCT ca.id_traza FROM gismcc.calles ca WHERE ((ca.id_traza > 0) AND (ca.id_traza is not null)) ORDER BY ca.id_traza;";

$stm_id_traza = $conPdoPg->query($qry_id_traza);

if (!$stm_id_traza->rowCount() > 0) {
    print 'No se encontraron registros en la tabla calles';
    exit;
}

$reg = 1;

$file = fopen("callesSec1.csv", "w");

$res = '"N";"id_calles";"nombre_calles";"id_traza";"id_secuencia_traza";"altura_desde";"altura_hasta";"the_geom";"marca"';
echo $res . '<br />';
fwrite($file, $res . PHP_EOL);

@set_time_limit(60);

while ($registro = $stm_id_traza->fetch(PDO::FETCH_ASSOC)) {
    $id_traza = $registro['id_traza'];
	
    $qry_sec_calle = " SELECT ca.id_calles, ca.nombre_calles, ca.id_traza, ca.estado_calle, ca.id_secuencia_traza, ca.altura, ca.the_geom_calles FROM gismcc.calles ca WHERE ca.id_traza = $id_traza ORDER BY ca.id_traza ASC, ca.id_secuencia_traza ASC;";
	
    $stm_sec_calle = $conPdoPg->query($qry_sec_calle);

    if ($stm_sec_calle->rowCount() > 0) {
        $cantFila = $stm_sec_calle->rowCount();
        $f = 0;

        if ($cantFila == 1) { // lee un solo registro
            
            $regSecCalle = $stm_sec_calle->fetch(PDO::FETCH_ASSOC);

//            if ($regSecCalle['id_traza'] == 1203) {
//                $parar = true;
//            }

            $idCalleAnt          = $regSecCalle['id_calles'];
            $nombreCalleAnt      = $regSecCalle['nombre_calles'];
            $idTrazaAnt          = $regSecCalle['id_traza'];
            $idSecuenciaTrazaAnt = $regSecCalle['id_secuencia_traza'];
            $alturaDesdeAnt      = verifAltura($regSecCalle['altura']);
            $alturaHasta         = $alturaDesdeAnt + 99;
            $the_geomAnt         = $regSecCalle['the_geom_calles'];

            $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesdeAnt . ';' . $alturaHasta . ';' . $the_geomAnt . ';""'; // -- UNO --';

            echo $res . "<br />";
            fwrite($file, $res . PHP_EOL);

            $reg++;
            $f = 0;

            $idCalleAnt = null;
            $nombreCalleAnt = null;
            $idTrazaAnt = null;
            $idSecuenciaTrazaAnt = null;
            $alturaDesdeAnt      = null;
            $alturaHastaAnt      = null;
            $the_geomAnt         = null;

            $idCallePos = null;
            $nombreCallePos = null;
            $idTrazaPos = null;
            $idSecuenciaTrazaPos = null;
            $alturaDesdePos = null;
            $alturaHastaPos = null;
            $the_geomPos = null;
            
        } else { // viene mas de un registro
            
            while ($regSecCalle = $stm_sec_calle->fetch(PDO::FETCH_ASSOC)) { // leo el primer registro
                
//                if ($regSecCalle['id_traza'] == 1203) {
//                    $parar = true;
//                }
                
                $marca = '';

                if ($f == 0) { // lee los dos primeros registros
                    
                    $idCalleAnt = $regSecCalle['id_calles'];
                    $nombreCalleAnt = $regSecCalle['nombre_calles'];
                    $idTrazaAnt = $regSecCalle['id_traza'];
                    $idSecuenciaTrazaAnt = $regSecCalle['id_secuencia_traza'];
                    $alturaDesdeAnt = verifAltura($regSecCalle['altura']);
                    $the_geomAnt = $regSecCalle['the_geom_calles'];

                    
                    $regSecCalle1 = $stm_sec_calle->fetch(PDO::FETCH_ASSOC); // leo un segundo registro

                    $idCallePos = $regSecCalle1['id_calles'];
                    $nombreCallePos = $regSecCalle1['nombre_calles'];
                    $idTrazaPos = $regSecCalle1['id_traza'];
                    $idSecuenciaTrazaPos = $regSecCalle1['id_secuencia_traza'];
                    $alturaDesdePos = verifAltura($regSecCalle1['altura']);
                    $the_geomPos = $regSecCalle1['the_geom_calles'];

                    if ($alturaDesdeAnt <= 0) {
                        $alturaDesde = 0;
                    } else {
                        $alturaDesde = $alturaDesdeAnt;
                    }

                    if ($alturaDesdePos <= 0) {
                        $alturaHasta = 0;
                    } else {
                        $alturaHasta = $alturaDesdePos - 1;
                    }

                    $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesde . ';' . $alturaHasta . ';' . $the_geomAnt . ';""'; // . ' -- DOS --';

                    echo $res . "<br />";
                    fwrite($file, $res . PHP_EOL);

                    $idCalleAnt = $idCallePos;
                    $nombreCalleAnt = $nombreCallePos;
                    $idTrazaAnt = $idTrazaPos;
                    $idSecuenciaTrazaAnt = $idSecuenciaTrazaPos;
                    $alturaDesdeAnt = $alturaDesdePos;
                    $the_geomAnt = $the_geomPos;

                    $idCallePos = null;
                    $nombreCallePos = null;
                    $idTrazaPos = null;
                    $idSecuenciaTrazaPos = null;
                    $alturaDesdePos = null;
                    $the_geomPos = null;

                    $reg++;

                    $f = 1;
                    
                } else { // aca llega a partir del tercer registro, si los hay
                    
                    $idCallePos = $regSecCalle['id_calles'];
                    $nombreCallePos = $regSecCalle['nombre_calles'];
                    $idTrazaPos = $regSecCalle['id_traza'];
                    $idSecuenciaTrazaPos = $regSecCalle['id_secuencia_traza'];
                    $alturaDesdePos = verifAltura($regSecCalle['altura']);
                    $the_geomPos = $regSecCalle['the_geom_calles'];

                    if ($idTrazaAnt != $idTrazaPos) {
                        
                        // las trazas son distintas, cambio de id_T_traza
                        $alturaHasta = $alturaDesdeAnt + 99;

                        $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesdeAnt . ';' . $alturaHasta . ';' . $the_geomAnt . ';""'; // . ' -- TRES --';

                        echo $res . "<br />";
                        fwrite($file, $res . PHP_EOL);

                        $idCalleAnt = $idCallePos;
                        $nombreCalleAnt = $nombreCallePos;
                        $idTrazaAnt = $idTrazaPos;
                        $idSecuenciaTrazaAnt = $idSecuenciaTrazaPos;
                        $alturaDesdeAnt = $alturaDesdePos;
                        $the_geomAnt = $the_geomPos;

                        $idCallePos = null;
                        $nombreCallePos = null;
                        $idTrazaPos = null;
                        $idSecuenciaTrazaPos = null;
                        $alturaDesdePos = null;
                        $the_geomPos = null;

                        // aca va el CINCO 
                        $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesdeAnt . ';' . $alturaHasta . ';' . $the_geomAnt . ';""'; // . ' -- CINCO --';

                        echo $res . "<br />";
                        fwrite($file, $res . PHP_EOL);

                        $reg++;
                        
                    } else {
                        
                        // son registros de la misma id_traza
                        if ($alturaDesdeAnt <= 0) {
                            $alturaDesde = 0;
                        } else {
                            $alturaDesde = $alturaDesdeAnt;
                        }

                        if ($alturaDesdePos <= 0) {
                            $alturaHasta = 0;
                        } else {
                            $alturaHasta = $alturaDesdePos - 1;
                        }

//                        if ($idTrazaAnt == 1203) {
//                            $parar = true;
//                        }
                        
                        $marca = ($alturaHasta > 100) ? '+100' : null;

                        $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesde . ';' . $alturaHasta . ';'. $the_geomAnt . ';"' . $marca . '"'; // . ' -- CUATRO --';

                        echo $res . "<br />";
                        fwrite($file, $res . PHP_EOL);

                        $idCalleAnt = $idCallePos;
                        $nombreCalleAnt = $nombreCallePos;
                        $idTrazaAnt = $idTrazaPos;
                        $idSecuenciaTrazaAnt = $idSecuenciaTrazaPos;
                        $alturaDesdeAnt = $alturaDesdePos;
                        $the_geomAnt = $the_geomPos;

                        $idCallePos = null;
                        $nombreCallePos = null;
                        $idTrazaPos = null;
                        $idSecuenciaTrazaPos = null;
                        $alturaDesdePos = null;
                        $the_geomPos = null;

                        $reg++;
                    }
                }
            }
//            if ($idTrazaAnt == 83) {
//                $parar = true;
//            }

            if ($alturaDesdeAnt <= 0 || is_null($alturaDesdeAnt)) {
                
                $alturaDesde = 0;
                $alturaHasta = 0;
                
            } else {
                
                $alturaDesde = $alturaDesdeAnt;

                if (strlen($alturaDesde) == 3) {
                    
                    if (substr($alturaDesde, 2, 1) == 0) {
                        $alturaHasta = $alturaDesde + 99;
                    } else if (substr($alturaDesde, 2, 1) == 5) {
                        $alturaHasta = $alturaDesde + 49;
                    } else {
                        $alturaHasta = $alturaDesde + 99;
                    }
                    
                } else if (strlen($alturaDesde) == 4) {
                    if (substr($alturaDesde, 2, 1) == 0) {
                        $alturaHasta = $alturaDesde + 99;
                    } else if (substr($alturaDesde, 2, 1) == 5) {
                        $alturaHasta = $alturaDesde + 49;
                    } else {
                        $alturaHasta = $alturaDesde + 99;
                    }
                } else {
                    $alturaHasta = $alturaDesde + 99;
                }
            }

//            if ($idTrazaAnt == 83) {
//                $parar = true;
//            }

            if ($idTrazaAnt == 1267) {
                $alturaHasta = $alturaDesde + 99;
            }

            $res = $reg . ';' . $idCalleAnt . ';"' . $nombreCalleAnt . '"' . ';' . $idTrazaAnt . ';' . $idSecuenciaTrazaAnt . ';' . $alturaDesde . ';' . $alturaHasta . ';' . $the_geomAnt . ';""'; // . ' -- SEIS --';

            echo $res . "<br />";
            fwrite($file, $res . PHP_EOL);
            $reg++;
        }
    }
    $stm_sec_calle = null;
}

fclose($file);
$stm_id_traza = null;

function verifAltura($altura) {
    if (is_null($altura)) {
        $alturaDesde = 0;
    } else if ($altura <= 0) {
        $alturaDesde = 0;
    } else {
        $alturaDesde = $altura;
    }
    return $alturaDesde;
}

?>