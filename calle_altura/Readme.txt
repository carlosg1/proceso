Nombre de carpeta: calle_altura (Calle con altura desde hasta)
Se pone la altura desde, hasta
los datos se toman de la calles
de la base de datos de produccion

OBSERVACION:

* CALLE LAPLACE, barrio Libertad, tiene id_traza y id_secuencia_traza en cero
* id_calle = 9666, sin nombre de calle, barrio la rosada, id_traza y id_secuencia_traza en cero 
* id_traza = 180, esta mal cargada la secuencia de esa id_traza
* hay calles cuya altura es nulo o esta sin numeracion (en cero), en este caso se deja con altura desde y altura hasta en cero (0)
* id_traza = 30, los dos ultimos registros tienen repetido el id_secuencia_traza = 46
* id_traza = 1258, las alturas de las calles no se corresponde con las secuencias (id_secuencia_traza) de los tramos
* Hay varios tramos que tienen el mismo id_secuencia_traza para un mismo id_traza
* hay id_traza = 1225 (ruta nacional 12) tiene la id_secuencia_traza todo en nulo, pero ademas tiene el mismo id_traza = 1225 la calle SCOOL THE SAC en el barrio la rosada (id_calles = 9665)
* id_traza = 78, tiene varios id_secuencia_traza en nulo

