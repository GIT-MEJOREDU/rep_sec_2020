<?php 
	include("./lib/escuelaslib.php");
	#Ent = 20
	#Mpio = 1041
	#conecta a la base
	$arrv = valida_variable(strtoupper(escapa($_REQUEST['va'])));

	$verde_insuficiente = '#fb4f57';
	$verde_basico = '#fdd16c';
	$verde_satisfactorio = '#6acb9c';
	$verde_sobresaliente = '#90b0d9';

	$tabulador = '&nbsp;&nbsp;&nbsp;&nbsp;';

	if(is_array($arrv)){
		#si es válida la variable CCT
		$escuela = substr($arrv['vent'],0,11);
		$turno = substr($arrv['vent'],10,1);
		#$extension = substr($arrv['vent'],11);
		$cicloEscolar = 21;

		#Se obtiene conexion a BD
		$db = dbc();

		#se obtienen datos generales de la escuela, para el encabezado
		#consulta de datos generales
		$qw = <<<EOD
		SELECT ct."cNombreCentroTrabajo", te."cNombreTurnoEscolar", 
		gm."cNombreGradoMarginacionPlanea", ef."cNombreEntidad", ef."iPkEntidadFederativa",
		m."cNombreMunicipio", ze."cClaveZonaEscolar"
		FROM hechos."hechosReporteSecundaria1819" AS h
		FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."gradosMarginacionPlanea" AS gm ON gm."iPkGradoMarginacionPlanea" = h."iFkGradoMarginacionPlanea"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
		FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio"=h."iFkMunicipio"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"= '$turno' AND h."iFkCicloEscolar"= '$cicloEscolar'
EOD;
	#echo "<br/>DATOS GENERALES: ".$qw."<br/>";
	$res = pg_query($db, $qw);
	#echo "<br/>Numero de registros: ".pg_num_rows($res)."<br/>";

# El siguiente IF comentado es para validar si el CCT y turno existe en la base de datos
	#if(pg_num_rows($res)>0){
	#Se asignan valores de datos generales de la escuela, para mostrarlas en encabezado del reporte
	$row = pg_fetch_assoc($res);
	$nom_cct = $row['cNombreCentroTrabajo'];
	$cve_cct = $escuela;
	$gdo_marginacion = $row['cNombreGradoMarginacionPlanea'];
	$nom_turno = $row['cNombreTurnoEscolar'];
	$nom_entidad = $row['cNombreEntidad'];
	$ipk_entidad = $row['iPkEntidadFederativa'];
	$nom_mun = $row['cNombreMunicipio'];
	$cve_zona_e = $row['cClaveZonaEscolar'];
	$ciclo1 = 17;
	$ciclo2 = 19;

#Consulta comparativo ciclos de PLANEA Lenguaje y Comunicacion
$qw_compara_LyC = <<<EOD
	SELECT ce."cCicloEscolar" AS "CicloEscolar",
	CASE WHEN h."dPorcentAlumnsEscNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrILyC" AS NUMERIC (5)) END AS "I_Insuficiente", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIILyC" AS NUMERIC (5)) END AS "II_Elemental", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIIILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIILyC" AS NUMERIC (5)) END AS "III_Bueno", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIVLyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVLyC" AS NUMERIC (5)) END AS "IV_Excelente"
	FROM hechos."hechosReporteSecundaria1819" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"= '$turno' AND h."iFkCicloEscolar" IN (17,19,21)
	ORDER BY ce."cCicloEscolar" DESC;
EOD;
$res_compara_LyC = pg_query($db, $qw_compara_LyC);

#Consulta comparativo ciclos de PLANEA Matemáticas
$qw_compara_mat = <<<EOD
	SELECT  ce."cCicloEscolar" AS "CicloEscolar", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIMat" AS NUMERIC (5)) END AS "I_Insuficiente", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIMat" AS NUMERIC (5)) END AS "II_Elemental", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIIIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIIMat" AS NUMERIC (5)) END AS "III_Bueno", 
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIVMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVMat" AS NUMERIC (5)) END AS "IV_Excelente"
	FROM hechos."hechosReporteSecundaria1819" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN (17,19,21)
	ORDER BY ce."cCicloEscolar" DESC;
EOD;
$res_compara_mat = pg_query($db, $qw_compara_mat);


#Consulta de Comparativo con las escuelas de la entidad y el mismo subsistema

#---- Escuela promedio en el mismo subsistema ----
#$qw_prom_subs = <<<EOD
#SELECT  CASE WHEN h."dPuntajePromedioEscGpoCompLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscGpoCompLyC" END AS "dPuntajePromedioEscGpoCompLyC" ,
#		CASE WHEN h."dPuntajePromedioEscGpoCompMat"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscGpoCompMat" END AS "dPuntajePromedioEscGpoCompMat" 
#FROM hechos."hechosReporteEmsInee" AS h
#FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
#FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
#FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
#FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
#FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
#WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
#EOD;
#$res_prom_subs = pg_query($db, $qw_prom_subs);

#---- Mi escuela ----
$qw_escuela_subs = <<<EOD
SELECT h."dPorcentAlumnsEscNvlLgrIMat", h."dPorcentAlumnsEscNvlLgrILyC"
FROM hechos."hechosReporteSecundaria1819" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
#echo "<br/>MI ESCUELA: ".$qw_escuela_subs."<br/>";
$res_escuela_subs = pg_query($db, $qw_escuela_subs);

#---- Escuelas en el mismo subsistema, misma entidad y con el mismo grado de marginación ----
$qw_ent_subs = <<<EOD
SELECT h."dPorcentAlumnsEscNvlLgrIMat", h."dPorcentAlumnsEscNvlLgrILyC"
FROM hechos."hechosReporteSecundaria1819" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ef."iPkEntidadFederativa" = '$ipk_entidad' AND ze."cClaveZonaEscolar"= '$cve_zona_e' AND h."iFkCicloEscolar" = '$cicloEscolar'
ORDER BY ct."cClaveCentroTrabajo";
EOD;
//echo "<br/>ESCUELAS ENTIDAD: ".$qw_ent_subs."<br/>";
$res_ent_subs = pg_query($db, $qw_ent_subs);

#---- Promedio de la entidad ----
#$qw_ent_prom = <<<EOD
#SELECT  CASE WHEN h."dPuntajePromedioEstatalLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEstatalLyC" END AS "dPuntajePromedioEstatalLyC" ,
#		CASE WHEN h."dPuntajePromedioEstatalMat"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEstatalMat" END AS "dPuntajePromedioEstatalMat" 
#FROM hechos."hechosReporteEmsInee" AS h
#FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
#FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
#FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
#FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
#FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
#WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
#EOD;
#echo "<br/>PROMEDIO ENTIDAD: ".$qw_ent_prom."<br/>";
#$res_ent_prom = pg_query($db, $qw_ent_prom);

#---- Textos ----
$qw_miescuela_texto = <<<EOD
SELECT h."dPorcentAlumnsEscNvlLgrIMat", h."dPorcentAlumnsEscNvlLgrILyC",
	CASE WHEN h."dPorcentAlumnsEscNvlLgrIMat" > 50 AND h."dPorcentAlumnsEscNvlLgrILyC" > 50 THEN '<strong>Esta escuela tiene <span style="color:#b4cfa8";>mas de la mitad</span> de los alumnos en nivel Insuficiente en Lenguaje y Comunicación (LyC) y Matemáticas (MAT)</strong>.'
		ELSE '<strong>Esta escuela tiene <span style="color:#b4cfa8";>menos de la mitad</span> de los alumnos en nivel Insuficiente en Lenguaje y Comunicación (LyC) y Matemáticas (MAT)</strong>.'
	END
FROM hechos."hechosReporteSecundaria1819" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = '$cicloEscolar';
EOD;
$res_miescuela_texto = pg_query($db, $qw_miescuela_texto);
$row_miescuela_texto = pg_fetch_assoc($res_miescuela_texto);

$qw_zona_texto = <<<EOD
SELECT CONCAT('En la zona escolar hay ',COUNT(h."dPorcentAlumnsEscNvlLgrIMat"),' escuelas que tienen más de 50% de sus alumnos en nivel Insuficiente en Lenguaje y Comunicación (LyC) y Matemáticas (MAT).') AS texto
FROM hechos."hechosReporteSecundaria1819" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ef."iPkEntidadFederativa" = '$ipk_entidad' AND ze."cClaveZonaEscolar"= '$cve_zona_e' AND h."iFkCicloEscolar" = '$cicloEscolar' AND h."dPorcentAlumnsEscNvlLgrIMat" > 50 AND h."dPorcentAlumnsEscNvlLgrILyC" > 50
EOD;
$res_zona_texto = pg_query($db, $qw_zona_texto);
$row_zona_texto = pg_fetch_assoc($res_zona_texto);


#--Textos para el cuadrante.
$txt_1 = $row_miescuela_texto['case'];
$txt_2 = $row_zona_texto['texto'];
#echo "texto1:".$txt_1;
#echo "texto2:".$txt_2;
#echo "texto3:".$txt_3;
#echo "texto4:".$txt_4;

/**
 * Inicio de Bloque de consultas para
 * Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN Unidad diagnóstica
*/

#---- Aspecto de evaluación: Manejo y construcción de la información ----
//Manejo y construcción de textos
$evaluacion = "Comprensión lectora";
$qw_manejo_txts = <<<EOD
	SELECT ue."cUnidadEvaluacion", CAST(SUM(rlc."dPorcentAlumnsAcertReactivo")/COUNT(rlc."dPorcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "porcentaje"
	FROM hechos."hechosReporteSecundaria1819" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC1819" AS rlc ON rlc."iPkResultadoLyC1819" = h."iFkResultadoLyC1819"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rlc."iFkUnidadEvaluacion"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = '$cicloEscolar'
	AND ue."cUnidadEvaluacion" = 'Reflexión sobre la lengua'
	GROUP BY ue."cUnidadEvaluacion";
EOD;
$res_manejo_txts = pg_query($db, $qw_manejo_txts);
//echo "Consulta: ".$qw_manejo_txts;

#-- Resultados por temas y reactivos - LyC --
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5 = <<<EOD
SELECT * FROM ( 
	SELECT CONCAT('Reactivo #',rlc."cNumeroReactivo",' ',tem."cContenidoTematico"), CAST(rlc."dPorcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje 
	FROM hechos."hechosReporteSecundaria1819" AS h 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC1819" AS rlc ON rlc."iPkResultadoLyC1819" = h."iFkResultadoLyC1819" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rlc."iFkUnidadEvaluacion"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."contenidosTematicos" AS tem ON tem."iPkContenidoTematico" = rlc."iFkContenidoTematico"
	WHERE ct."cClaveCentroTrabajo"='$escuela' 
	AND te."iPkTurnoEscolar"='$turno' 
	AND h."iFkCicloEscolar"='$cicloEscolar'
	AND ue."cUnidadEvaluacion" = 'Reflexión sobre la lengua'
	ORDER BY ue."cUnidadEvaluacion",Porcentaje ASC LIMIT 5) 
AS nTable ORDER BY Porcentaje DESC;
EOD;
$res_react_menor_5 = pg_query($db, $qw_react_menor_5);
//echo "Consulta: ".$qw_react_menor_5;

#---- Eje temático: Comprensión lectora
$qw_txt_argumentativo = <<<EOD
SELECT ue."cUnidadEvaluacion", CAST(SUM(rlc."dPorcentAlumnsAcertReactivo")/COUNT(rlc."dPorcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "porcentaje"
FROM hechos."hechosReporteSecundaria1819" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC1819" AS rlc ON rlc."iPkResultadoLyC1819" = h."iFkResultadoLyC1819"
FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rlc."iFkUnidadEvaluacion"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = '$cicloEscolar'
AND ue."cUnidadEvaluacion" = 'Comprensión lectora'
GROUP BY ue."cUnidadEvaluacion";
EOD;
//echo "CONSULTA: ".$qw_txt_argumentativo;
$res_txt_argumentativo = pg_query($db, $qw_txt_argumentativo);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_arg = <<<EOD
SELECT * FROM ( 
	SELECT CONCAT('Reactivo #',rlc."cNumeroReactivo",' ',tem."cContenidoTematico"), CAST(rlc."dPorcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje 
	FROM hechos."hechosReporteSecundaria1819" AS h 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC1819" AS rlc ON rlc."iPkResultadoLyC1819" = h."iFkResultadoLyC1819" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rlc."iFkUnidadEvaluacion"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."contenidosTematicos" AS tem ON tem."iPkContenidoTematico" = rlc."iFkContenidoTematico"
	WHERE ct."cClaveCentroTrabajo"='$escuela' 
	AND te."iPkTurnoEscolar"='$turno' 
	AND h."iFkCicloEscolar"='$cicloEscolar'
	AND ue."cUnidadEvaluacion" = 'Comprensión lectora'
	ORDER BY ue."cUnidadEvaluacion",Porcentaje ASC LIMIT 5) 
AS nTable ORDER BY Porcentaje DESC;
EOD;
$res_react_menor_5_arg = pg_query($db, $qw_react_menor_5_arg);

/**
 * Fin de Bloque de consultas para
 * Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN Unidad diagnóstica
*/


/**
 * Inicio de Bloque de consultas para
 * Porcentaje de aciertos en MATEMÁTICAS Unidad diagnóstica
*/

#Unidad diagnóstica: Manejo de la información
$qw_mdi = <<<EOD
SELECT ue."cUnidadEvaluacion", CAST(SUM(rm."dPorcentAlumnsAcertReactivo")/COUNT(rm."dPorcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje" 
FROM hechos."hechosReporteSecundaria1819" AS h 
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
WHERE ct."cClaveCentroTrabajo"='$escuela' 
--AND te."iPkTurnoEscolar"='$turno' 
--AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND ue."cUnidadEvaluacion"= 'Manejo de la información'
GROUP BY ue."cUnidadEvaluacion";
EOD;
//echo "CONSULTA: ".$qw_mdi;
$res_mdi = pg_query($db, $qw_mdi);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_mdi = <<<EOD
SELECT * FROM ( 
	SELECT CONCAT('Reactivo #',rm."cNumeroReactivo",' ',tem."cContenidoTematico"), CAST(rm."dPorcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje" 
	FROM hechos."hechosReporteSecundaria1819" AS h 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."contenidosTematicos" AS tem ON tem."iPkContenidoTematico" = rm."iFkContenidoTematico"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
	WHERE ct."cClaveCentroTrabajo"='$escuela' 
	AND te."iPkTurnoEscolar"='$turno' 
	AND h."iFkCicloEscolar"= '$cicloEscolar' 
	AND ue."cUnidadEvaluacion"= 'Manejo de la información' 
	ORDER BY ue."cUnidadEvaluacion","Porcentaje" ASC LIMIT 5) 
AS nTable ORDER BY "Porcentaje" DESC;
EOD;
//echo "CONSULTA: ".$qw_react_menor_5_mdi;
$res_react_menor_5_mdi = pg_query($db, $qw_react_menor_5_mdi);

#Unidad diagnóstica: Sentido numérico y pensamiento algebraico
#AQUI
$qw_snpa = <<<EOD
SELECT ue."cUnidadEvaluacion", CAST(SUM(rm."dPorcentAlumnsAcertReactivo")/COUNT(rm."dPorcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje" 
FROM hechos."hechosReporteSecundaria1819" AS h 
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
WHERE ct."cClaveCentroTrabajo"='$escuela' 
AND te."iPkTurnoEscolar"='$turno' 
AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND ue."cUnidadEvaluacion"= 'Sentido numérico y pensamiento algebraico' 
GROUP BY ue."cUnidadEvaluacion";
EOD;
//echo "CONSULTA: ".$qw_snpa;
$res_snpa = pg_query($db, $qw_snpa);

#--Sentido numérico y pensamiento algebraico
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_snpa = <<<EOD
SELECT * FROM ( 
	SELECT CONCAT('Reactivo #',rm."cNumeroReactivo",' ',tem."cContenidoTematico"), CAST(rm."dPorcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje" 
	FROM hechos."hechosReporteSecundaria1819" AS h 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."contenidosTematicos" AS tem ON tem."iPkContenidoTematico" = rm."iFkContenidoTematico"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
	WHERE ct."cClaveCentroTrabajo"='$escuela' 
	AND te."iPkTurnoEscolar"='$turno' 
	AND h."iFkCicloEscolar"= '$cicloEscolar' 
	AND ue."cUnidadEvaluacion"= 'Sentido numérico y pensamiento algebraico' 
	ORDER BY ue."cUnidadEvaluacion","Porcentaje" ASC LIMIT 5) 
AS nTable ORDER BY "Porcentaje" DESC;
EOD;
//echo "CONSULTA: ".$qw_react_menor_5_snpa;
$res_react_menor_5_snpa = pg_query($db, $qw_react_menor_5_snpa);

#Unidad diagnóstica: Forma, espacio y medida
#---- Aspecto de evaluación: Forma, Espacio y Medida ----
$qw_fem = <<<EOD
SELECT ue."cUnidadEvaluacion", CAST(SUM(rm."dPorcentAlumnsAcertReactivo")/COUNT(rm."dPorcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje" 
FROM hechos."hechosReporteSecundaria1819" AS h 
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
WHERE ct."cClaveCentroTrabajo"='$escuela' 
AND te."iPkTurnoEscolar"='$turno' 
AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND ue."cUnidadEvaluacion"= 'Forma, Espacio y Medida' 
GROUP BY ue."cUnidadEvaluacion";
EOD;
//echo "CONSULTA: ".$qw_fem;
$res_fem = pg_query($db, $qw_fem);

#--Sentido Forma espacio y medida
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_fem = <<<EOD
SELECT * FROM ( 
	SELECT CONCAT('Reactivo #',rm."cNumeroReactivo",' ',tem."cContenidoTematico"), CAST(rm."dPorcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje" 
	FROM hechos."hechosReporteSecundaria1819" AS h 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo1819" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat1819" AS rm ON rm."iPkResultadoMat1819" = h."iFkResultadoMat1819" 
	FULL OUTER JOIN "dimensionesReporteSecundaria"."contenidosTematicos" AS tem ON tem."iPkContenidoTematico" = rm."iFkContenidoTematico"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."unidadesEvaluacion" AS ue ON ue."iPkUnidadEvaluacion" = rm."iFkUnidadEvaluacion"
	WHERE ct."cClaveCentroTrabajo"='$escuela' 
	AND te."iPkTurnoEscolar"='$turno' 
	AND h."iFkCicloEscolar"= '$cicloEscolar' 
	AND ue."cUnidadEvaluacion"= 'Forma, Espacio y Medida' 
	ORDER BY ue."cUnidadEvaluacion","Porcentaje" ASC LIMIT 5) 
AS nTable ORDER BY "Porcentaje" DESC;
EOD;
//echo "CONSULTA: ".$qw_react_menor_5_fem;
$res_react_menor_5_fem = pg_query($db, $qw_react_menor_5_fem);

/**
 * Fin de Bloque de consultas para
 * Porcentaje de aciertos en MATEMÁTICAS Unidad diagnóstica
*/

# las sigueintes lineas comentadas son en caso de que el CCT y tuno no existan en la base de datos
#	}else{
#	    echo "No existe información para este CCT y turno.";
#	}
  	}else{
			echo "El criterio de búsqueda no es válido se debe concatenar CCT y TURNO";
			die();
  	}
?>
<html>
<head>
<title>Reporte por escuelas - <?php echo $escuela;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
		@media all {
			div.saltopagina{
				display: block;
				/*border: 2px solid blue;*/
			}
		}
				
		@media print{
			div.saltopagina{ 
				display:block; 
				page-break-before:always;
			}
		}
		
		body{
			/*font-family: arial,verdana;
			*/
			font-family: 'Montserrat';
			font-style: normal;
			font-weight: 400;
			font-display: swap;
			font-size: 10px;
			src: local('Montserrat Regular'), local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v14/JTUSjIg1_i6t8kCHKm459WlhyyTh89Y.woff2) format('woff2');
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
			background-color: #ffffff;
		}
		
		table{
			width: 90%;
			border: 0;
			padding: 0;
			border-spacing: 0px;
			border-collapse: collapse;
			margin: 0 auto;
		}
		
		td{
			font-size: 12px;
			text-align: left;
			padding-left: 10px;
		}
		
		.celda_normal{
			color: #000000;
			border-style: solid;
			border-color: black;
			border-top-width: 1px;
			border-left-width: 1px;
			border-bottom-width: 1px;
			border-right-width: 1px;
			margin: 0px;
		}

		.td_green{
			background-color: #3d5a4f; /*#bdd2b0;*/
			color: #ffffff;/*#212449;*/
			font-weight: bold;
			border-style: solid;
			border-color: #212449;
			border-top-width: 1px;
			border-left-width: 0px;
			border-bottom-width: 1px;
			border-right-width: 0px;
		}
		
		.td_gold{
			background-color: #BC955C;
			color: #ffffff;/*#212449;*/
			font-weight: bold;
			border-style: solid;
			border-color: black;
			border-top-width: 1px;
			border-left-width: 1px;
			border-bottom-width: 1px;
			border-right-width: 1px;
		}

		.td_data{
			color: #000000;
			border-style: solid;
			border-color: #212449;
			border-top-width: 1px;
			border-left-width: 0px;
			border-bottom-width: 1px;
			border-right-width: 0px;
			margin: 0px;
		}
		
		.td_center{
			text-align: center;
			color: #333e75;
			font-size: 12px;
			font-weight: bold;
		}
		
		.td_center_gray{
			text-align: center;
			color: #999999;
			font-size: 12px;
			font-weight: bold;
		}
		

		.td_nvl{
			width: 25%;
			height: 45px;
			text-align: center;
			vertical-align: middle;
			background-color: #FB4F57;
			font-size: 12px;
			font-weight: bold;
		}

		.td_bg_red{
			background-color: #FB4F57; /*#5b8e39;*/
		}

		.td_bg_yellow{
			background-color: #FDD16C; /*#70ad47;*/
		}

		.td_bg_green{
			background-color: #6ACB9C; /*#b4cfa8;*/
		}

		.td_bg_blue{
			background-color: #90B0D9; /*#ceddc4;*/
		}

		.td_left{
		text-align: left;
		}
		
		.td_right{
			text-align: right;
			color: #333e75;
			font-weight: bold;
			font-size: 13px;
		}
		
		.td_just{
			font-size: 10px;
			text-align: justify;
			margin: 0 auto;
			width: 82%;
			padding: 0.2% 0;
		}
		
		.td_top{
			vertical-align:top;
			color: #3d5a4f;
			font-size: 13px;
			font-weight: bold;
			text-align: center;
		}
		.max-width{
			width: 88%;
		}
		.page_container{
			margin: 0 auto;
    			text-align: center;
		}
		.header{
			margin-left: auto;
			margin-right: auto;
			justify-content: center;
		}
		.elem_center{
			/*width: 55%;*/
			height: 140px;
		}
		.container {
			width: 100%;
    		text-align: center;
			font-size: 15;
		}
		.left_container {
			width: 100%;
    		text-align: left;
			font-size: 13;
		}
		.reac_container{
			width: 100%;
    			margin: 0 auto;
    			text-align: center;
		}
		.text_container {
			width: 85%;
    			margin: 0 auto;
    			/*min-height: 250px;*/
    			display: block;
		}
		/*.left_container{
			width: 15%;
    			float: left;
    			clear: both;
			
		}
		.rigth_container {
			
			float: right;
      			clear: both;
		}
		.sangria {
			margin-left: 6%;
		}*/

		.center{
			width: 50%;
			margin: 0 auto;
		}
		ul {
			list-style: none;
			text-align: justify;
		}
		.large_text{
			width: 98%;
    			text-align: center;
    			margin: 0 auto;
			font-size: 12px;
		}
		.split{
			width: 90%;
			margin: 0 auto;
		}
		.bullet_container{
			width: 100%;
    			text-align: center;
		}
		.first_graph_container{
			width: 60%;
			margin: 0 auto;
		}
		/*.graph_container {
			width: 100%;
    			text-align: center;
		}*/
		.section_tittle{
			color: #3d5a4f;
		}
		.sub_tittle{
			font-size: 12px;
		}
		h4 > span{
			color: black;
		}
		section>img{
			/*height: 500px;*/
		}
		.bullet_text{
			font-size: 10px
		}
		/*.last_text{
			text-align: end;
		}*/
		.float-rigth{
			float: right;
			clear: both:
		}
		section {
			display: inline;
		}
		section > .cuadrantes {
			margin-left: 12px;
			/*height: 500px;*/
		}
		/*.container_rigth{
			margin: 0 auto;
    			width: 75%;
   	 		text-align: right;
		}*/
		.left{
			width: 130px;
			text-align: left;

		}
		.right{
			text-align: right;
		}
		.text {
			width: 92%;
			
		}
		.text > ul {
			font-size: 12.5px;
			line-height: 20px;
 		}
		h5 {
			margin: 0.5em;
			font-size: 18px;
		}
		.google-visualization-table-td {
			text-align: center !important;
		}
	</style>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {'packages': ['corechart']});    
google.charts.load('current', {'packages':['bar']});
//google.charts.load('current', {'packages':['table']});
google.charts.setOnLoadCallback(drawChart);
//google.charts.setOnLoadCallback(drawTable);


function drawChart() {

/*INICIO
* Bloque para generar gráfica de PLANEA Lenguaje y Comunicacion
*/

var data_comp_LyC = google.visualization.arrayToDataTable([
	['Alcance','Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
				//['Lenguaje y Comunicacion', 4.55,'4.55%', 4.55,'4.55%', 45.45,'45.45%', 45.45,'45.45%']
				<?php
				while($row_comp_lyc = pg_fetch_assoc($res_compara_LyC)){
					if($row_comp_lyc['I_Insuficiente']<0){$txtstyle_I="Dato no disponible"; $val_I=0;}else{$txtstyle_I=$row_comp_lyc['I_Insuficiente']."%"; $val_I=$row_comp_lyc['I_Insuficiente'];}
					if($row_comp_lyc['II_Elemental']<0){$txtstyle_II="Dato no disponible"; $val_II=0;}else{$txtstyle_II=$row_comp_lyc['II_Elemental']."%"; $val_II=$row_comp_lyc['II_Elemental'];}
					if($row_comp_lyc['III_Bueno']<0){$txtstyle_III="Dato no disponible"; $val_III=0;}else{$txtstyle_III=$row_comp_lyc['III_Bueno']."%"; $val_III=$row_comp_lyc['III_Bueno'];}
					if($row_comp_lyc['IV_Excelente']<0){$txtstyle_IV="Dato no disponible"; $val_IV=0;}else{$txtstyle_IV=$row_comp_lyc['IV_Excelente']."%"; $val_IV=$row_comp_lyc['IV_Excelente'];}
					echo "['".substr($row_comp_lyc['CicloEscolar'],5,4)."',".
					$row_comp_lyc['I_Insuficiente']*(-1).",'".$txtstyle_I."',".$row_comp_lyc['II_Elemental'].",'".$txtstyle_II."',".
					$row_comp_lyc['III_Bueno'].",'".$txtstyle_III."',".$row_comp_lyc['IV_Excelente'].",'".$txtstyle_IV."'],";
				}
				?>
]);

	var options_comp_nacional = {
		tooltip: {trigger: 'none'},
		//titleTextStyle: {fontSize: 18, bold: true, color: '#000000'},
		title: 'Lenguaje y Comunicación',
		titlePosition: 'out',
		titleTextStyle: {
			fontSize: 18,
		},
		hAxis: {
			textPosition: 'none',
			gridlines: {color: 'transparent'}},
		vAxis: {
			gridlines: {color: 'transparent'}},
		bar: {groupWidth: '80%'},
		annotations:{textStyle: {fontSize: 18, bold: true, color: '#000000',auraColor: '#f0f0f0' }},
  		chartArea: {left: 300,right: 0,top: 30,bottom: 0},
  		series:{
			0:{color: '<?php echo $verde_insuficiente ?>'}, //{color: '#FB4F57'}
  			1:{color: '<?php echo $verde_basico ?>'}, //{color: '#FDD16C'},
  			2:{color: '<?php echo $verde_satisfactorio ?>'}, //{color: '#6ACB9C'},
  			3:{color: '<?php echo $verde_sobresaliente ?>'}}, //{color: '#90B0D9'}},
		legend: 'none',
		width: 1100,
		height: 280,
		isStacked:true
	};  

// Instantiate and draw the chart.
var container5 = document.getElementById('comp_escuelas_ent_lyc');
var chart5 = new google.visualization.BarChart(container5);
google.visualization.events.addListener(chart5, 'ready', function () {
container5.innerHTML = '<img src="' + chart5.getImageURI() + '">';
});
chart5.draw(data_comp_LyC, options_comp_nacional);

/*FIN
* Bloque para generar gráfica de PLANEA Lenguaje y Comunicacion
*/

/*INICIO
* Bloque para generar grafica del Comparativo con escuelas promedio de la entidad y del país Matemáticas
*/

var data_comp_mat = google.visualization.arrayToDataTable([
	['Alcance','Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
				//['Lenguaje y Comunicacion', 4.55,'4.55%', 4.55,'4.55%', 45.45,'45.45%', 45.45,'45.45%']
				<?php
				while($row_comp_mat = pg_fetch_assoc($res_compara_mat)){
					if($row_comp_mat['I_Insuficiente']<0){$txtstyle_I="Dato no disponible"; $val_I=0;}else{$txtstyle_I=$row_comp_mat['I_Insuficiente']."%"; $val_I=$row_comp_mat['I_Insuficiente'];}
					if($row_comp_mat['II_Elemental']<0){$txtstyle_II="Dato no disponible"; $val_II=0;}else{$txtstyle_II=$row_comp_mat['II_Elemental']."%"; $val_II=$row_comp_mat['II_Elemental'];}
					if($row_comp_mat['III_Bueno']<0){$txtstyle_III="Dato no disponible"; $val_III=0;}else{$txtstyle_III=$row_comp_mat['III_Bueno']."%"; $val_III=$row_comp_mat['III_Bueno'];}
					if($row_comp_mat['IV_Excelente']<0){$txtstyle_IV="Dato no disponible"; $val_IV=0;}else{$txtstyle_IV=$row_comp_mat['IV_Excelente']."%"; $val_IV=$row_comp_mat['IV_Excelente'];}
					echo "['".substr($row_comp_mat['CicloEscolar'],5,4)."',".
					$val_I*(-1).",'".$txtstyle_I."',".$val_II.",'".$txtstyle_II."',".
					$val_III.",'".$txtstyle_III."',".$val_IV.",'".$txtstyle_IV."'],";
				}
				?>
]);

	var options_comp_nacional_mat = {
	tooltip: {trigger: 'none'},
	title: 'Matemáticas',
	titlePosition: 'out',
		titleTextStyle: {
			fontSize: 18,
		},
	hAxis: {
		textPosition: 'none',
		gridlines: {color: 'transparent'}},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 18, bold: true, color: '#000000',auraColor: '#f0f0f0' }},
  chartArea: {left: 300,right: 0,top: 30,bottom: 0},
  series:{0:{color: '<?php echo $verde_insuficiente ?>'}, //{color: '#FB4F57'}
  1:{color: '<?php echo $verde_basico ?>'}, //{color: '#FDD16C'},
  2:{color: '<?php echo $verde_satisfactorio ?>'}, //{color: '#6ACB9C'},
  3:{color: '<?php echo $verde_sobresaliente ?>'}}, //{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 280,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mat = document.getElementById('comp_escuelas_ent_mat');
var chart_mat = new google.visualization.BarChart(container_mat);
google.visualization.events.addListener(chart_mat, 'ready', function () {
container_mat.innerHTML = '<img src="' + chart_mat.getImageURI() + '">';
});
chart_mat.draw(data_comp_mat, options_comp_nacional_mat);

/*FIN
* Bloque para generar grafica del Comparativo con escuelas promedio de la entidad y del país Matemáticas
*/

/*INICIO
* Bloque para generar grafica del Comparativo con las escuelas de la entidad y el mismo subsistema
*/

var data_comp_subs = google.visualization.arrayToDataTable
	([['LyC', 'Mate', {'type': 'string', 'role': 'style'}],
	<?php
		//while($row_prom_subs = pg_fetch_assoc($res_prom_subs)){
		//	echo "[".$row_prom_subs['dPuntajePromedioEscGpoCompLyC'].",".$row_prom_subs['dPuntajePromedioEscGpoCompMat'].",'point { size: 1; shape-type: circle; fill-color: #000000;}'],";
		//}
		while($row_ent_subs = pg_fetch_assoc($res_ent_subs)){
			echo "[".$row_ent_subs['dPorcentAlumnsEscNvlLgrILyC'].",".$row_ent_subs['dPorcentAlumnsEscNvlLgrIMat'].",'point { size: 6; shape-type: circle; fill-color: #686868;}'],";
		}	
		//while($row_ent_prom = pg_fetch_assoc($res_ent_prom)){
		//	echo "[".$row_ent_prom['dPuntajePromedioEstatalLyC'].",".$row_ent_prom['dPuntajePromedioEstatalMat'].",'point { size: 12; shape-type: triangle; fill-color: #808080;}'],";
		//}	
		while($row_escuela_subs = pg_fetch_assoc($res_escuela_subs)){
			echo "[".$row_escuela_subs['dPorcentAlumnsEscNvlLgrILyC'].",".$row_escuela_subs['dPorcentAlumnsEscNvlLgrIMat'].",'point { size: 12; shape-type: square; fill-color: #1E8207;}'],";
		}
	?>
]);

//$row_limits_lyc = pg_fetch_assoc(res_limits_lyc);
//$minlyc = 400;
//$maxlyc = 800;
$minlyc = 50;
$maxlyc = 100;

var options_comp = {
	chartArea:{left:50,top:15,width:'80%',height:'80%'},
	chart:{
		title: 'Puntajes promedio PLANEA 2017',
		subtitle: 'Mi escuela en mi zona escolar'
		},
	legend: 'none',
	height: 350,
	width: 350,
	//crosshair: {trigger: 'both', orientation: 'both', color: 'gray'},
	//crosshair: {trigger: 'selection', orientation: 'both', color: 'gray'},
	vAxis: {
		gridlines: {color: 'transparent', count: -1}, 
		title: '% de alumnos en nivel Insuficiente en MAT',
		ticks: [10,20,30,40,50,60,70,80,90,100]
		},
	hAxis: {
		gridlines: {color: 'transparent', count: -1}, 
		title: '% de alumnos en nivel Insuficiente en LyC',
		ticks: [20,40,60,80,100]
		}
};

// Instantiate and draw the chart.
var container_comp = document.getElementById('cuadrantes');
var chart_comp = new google.visualization.ScatterChart(container_comp);
google.visualization.events.addListener(chart_comp, 'ready', function () {
			chart_comp.setSelection([{"row":0}]);
container_comp.innerHTML = '<img id="cuadrante" src="' + chart_comp.getImageURI() + '">';
});
chart_comp.draw(data_comp_subs, options_comp);

	/*FIN
* Bloque para generar grafica del Comparativo con las escuelas de la entidad y el mismo subsistema
*/

/*INICIO
* Manejo y construcción de la información
*/
var data_const_txts = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_manejo_txts = pg_fetch_assoc($res_manejo_txts)){
			if($row_manejo_txts['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_manejo_txts['porcentaje']."%";}
			if($row_manejo_txts['porcentaje']>=0 && $row_manejo_txts['porcentaje']<=40){
				$color = $verde_insuficiente; //"#FB4F57";
			}elseif($row_manejo_txts['porcentaje']>40 && $row_manejo_txts['porcentaje']<=60){
				$color = $verde_basico; //"#FDD16C";
			}else{
				$color = $verde_satisfactorio;//"#6ACB9C";
			}
			echo "['".$row_manejo_txts['cUnidadEvaluacion']."',".$row_manejo_txts['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_manejo_txts = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 70,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:5,
		bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente ?>'} //'#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_manejo_txts = document.getElementById('manejo_const_txt');
var chart_manejo_txts = new google.visualization.BarChart(container_manejo_txts);
google.visualization.events.addListener(chart_manejo_txts, 'ready', function () {
container_manejo_txts.innerHTML = '<img src="' + chart_manejo_txts.getImageURI() + '">';
});
chart_manejo_txts.draw(data_const_txts, options_manejo_txts);

<?php if (pg_num_rows ( $res_manejo_txts ) == 0){ ?>
				//document.getElementById('manejo_const_txt').style.visibility = "hidden";
	document.getElementById('container_manejo_const_txt').style.display = "none";
	document.getElementById('manejo_const_txt').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Manejo y construcción de la información
*/

/*INICIO
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5 = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5 = pg_fetch_assoc($res_react_menor_5)){
			if($row_react_menor_5['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5['porcentaje']."%";}
			if($row_react_menor_5['porcentaje']>=0 && $row_react_menor_5['porcentaje']<=40){
				$color = $verde_insuficiente;//"#FB4F57";
			}elseif($row_react_menor_5['porcentaje']>40 && $row_react_menor_5['porcentaje']<=60){
				$color = $verde_basico;//"#FDD16C";
			}else{
				$color = $verde_satisfactorio; //"#6ACB9C";
			}
			echo "['".$row_react_menor_5['concat']."',".$row_react_menor_5['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5 = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 350,
  width: 1100,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
	  bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'} //'#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_react_menor_5 = document.getElementById('react_menor_5');
var chart_react_menor_5 = new google.visualization.BarChart(container_react_menor_5);
google.visualization.events.addListener(chart_react_menor_5, 'ready', function () {
container_react_menor_5.innerHTML = '<img src="' + chart_react_menor_5.getImageURI() + '">';
});
chart_react_menor_5.draw(data_react_menor_5, options_react_menor_5);

<?php if (pg_num_rows ( $res_react_menor_5 ) == 0){ ?>
	document.getElementById('container_react_menor_5').style.display = "none";
	document.getElementById('react_menor_5').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* Unidad diagnóstica: Texto argumentativo
*/
var data_txt_argumentativo = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_txt_argumentativo = pg_fetch_assoc($res_txt_argumentativo)){
			if($row_txt_argumentativo['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_txt_argumentativo['porcentaje']."%";}
			if($row_txt_argumentativo['porcentaje']>0 &&$row_txt_argumentativo['porcentaje']<=40){
				$color = $verde_insuficiente;//"#FB4F57";
			}elseif($row_txt_argumentativo['porcentaje']>40 && $row_txt_argumentativo['porcentaje']<=60){
				$color = $verde_basico;//"#FDD16C";
			}else{
				$color = $verde_satisfactorio;//"#6ACB9C";
			}
			echo "['".$row_txt_argumentativo['cUnidadEvaluacion']."',".$row_txt_argumentativo['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_txt_argumentativo = {
tooltip: {trigger: 'none'},
  title: 'Texto Argumentativo',	  
  height: 70,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:5,
		bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_txt_argumentativo = document.getElementById('txt_arg');
var chart_txt_argumentativo = new google.visualization.BarChart(container_txt_argumentativo);
google.visualization.events.addListener(chart_txt_argumentativo, 'ready', function () {
container_txt_argumentativo.innerHTML = '<img src="' + chart_txt_argumentativo.getImageURI() + '">';
});
chart_txt_argumentativo.draw(data_txt_argumentativo, options_txt_argumentativo);

<?php if (pg_num_rows ( $res_txt_argumentativo ) == 0){ ?>
	document.getElementById('container_txt_arg').style.display = "none";
	document.getElementById('txt_arg').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Unidad diagnóstica: Texto argumentativo
*/

/*INICIO
* "texto argumentativo"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático "texto argumentativo"
*/
var data_react_menor_5_arg = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_arg = pg_fetch_assoc($res_react_menor_5_arg)){
			if($row_react_menor_5_arg['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_arg['porcentaje']."%";}
			if($row_react_menor_5_arg['porcentaje']>0 &&$row_react_menor_5_arg['porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_react_menor_5_arg['porcentaje']>40 && $row_react_menor_5_arg['porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_react_menor_5_arg['concat']."',".$row_react_menor_5_arg['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);


var options_react_menor_5_arg = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 350,
  width: 1100,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
	  bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_react_menor_5_arg = document.getElementById('react_menor_5_arg');
var chart_react_menor_5_arg = new google.visualization.BarChart(container_react_menor_5_arg);
google.visualization.events.addListener(chart_react_menor_5_arg, 'ready', function () {
container_react_menor_5_arg.innerHTML = '<img src="' + chart_react_menor_5_arg.getImageURI() + '">';
});
chart_react_menor_5_arg.draw(data_react_menor_5_arg, options_react_menor_5_arg);

<?php if (pg_num_rows ( $res_react_menor_5_arg ) == 0){ ?>
	document.getElementById('container_react_menor_5_arg').style.display = "none";
	document.getElementById('react_menor_5_arg').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* MATEMÁTICAS
* Unidad diagnóstica: Manejo de la Informacion
*/
var data_mdi = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_mdi = pg_fetch_assoc($res_mdi)){
			if($row_mdi['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mdi['Porcentaje']."%";}
			if($row_mdi['Porcentaje']>0 && $row_mdi['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_mdi['Porcentaje']>40 && $row_mdi['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_mdi['cUnidadEvaluacion']."',".$row_mdi['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_mdi = {
tooltip: {trigger: 'none'},
  title: 'Manejo de la información',	  
  height: 70,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_mdi = document.getElementById('txt_mdi');
var chart_mdi = new google.visualization.BarChart(container_mdi);
google.visualization.events.addListener(chart_mdi, 'ready', function () {
container_mdi.innerHTML = '<img src="' + chart_mdi.getImageURI() + '">';
});
chart_mdi.draw(data_mdi, options_mdi);

<?php if (pg_num_rows ( $res_mdi ) == 0){ ?>
	document.getElementById('container_txt_mdi').style.display = "none";
	document.getElementById('txt_mdi').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Unidad diagnóstica: Manejo de la Informacion
*/

/*INICIO
* "Manejo de la Informacion"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_mdi = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_mdi = pg_fetch_assoc($res_react_menor_5_mdi)){
			if($row_react_menor_5_mdi['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_mdi['Porcentaje']."%";}
			if($row_react_menor_5_mdi['Porcentaje']>0 && $row_react_menor_5_mdi['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_react_menor_5_mdi['Porcentaje']>40 && $row_react_menor_5_mdi['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_react_menor_5_mdi['concat']."',".$row_react_menor_5_mdi['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_mdi = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 350,
  width: 1100,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
	  bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_react_menor_5_mdi = document.getElementById('react_menor_5_mdi');
var chart_react_menor_5_mdi = new google.visualization.BarChart(container_react_menor_5_mdi);
google.visualization.events.addListener(chart_react_menor_5_mdi, 'ready', function () {
container_react_menor_5_mdi.innerHTML = '<img src="' + chart_react_menor_5_mdi.getImageURI() + '">';
});
chart_react_menor_5_mdi.draw(data_react_menor_5_mdi, options_react_menor_5_mdi);

<?php if (pg_num_rows ( $res_react_menor_5_mdi ) == 0){ ?>
	document.getElementById('container_react_menor_5_mdi').style.display = "none";
	document.getElementById('react_menor_5_mdi').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* MATEMÁTICAS
* Unidad diagnóstica: Sentido numérico y pensamiento algebraico
*/
var data_snpa = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_snpa = pg_fetch_assoc($res_snpa)){
			if($row_snpa['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_snpa['Porcentaje']."%";}
			if($row_snpa['Porcentaje']>0 && $row_snpa['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_snpa['Porcentaje']>40 && $row_snpa['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_snpa['cUnidadEvaluacion']."',".$row_snpa['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_snpa = {
tooltip: {trigger: 'none'},
  title: 'Manejo de la información',	  
  height: 70,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_snpa = document.getElementById('txt_snpa');
var chart_snpa = new google.visualization.BarChart(container_snpa);
google.visualization.events.addListener(chart_snpa, 'ready', function () {
container_snpa.innerHTML = '<img src="' + chart_snpa.getImageURI() + '">';
});
chart_snpa.draw(data_snpa, options_snpa);

<?php if (pg_num_rows ( $res_snpa ) == 0){ ?>
	document.getElementById('container_txt_snpa').style.display = "none";
	document.getElementById('txt_snpa').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Unidad diagnóstica: Sentido numérico y pensamiento algebraico
*/

/*INICIO
* "Sentido numérico y pensamiento algebraico"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_snpa = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_snpa = pg_fetch_assoc($res_react_menor_5_snpa)){
			if($row_react_menor_5_snpa['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_snpa['Porcentaje']."%";}
			if($row_react_menor_5_snpa['Porcentaje']>0 && $row_react_menor_5_snpa['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_react_menor_5_snpa['Porcentaje']>40 && $row_react_menor_5_snpa['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_react_menor_5_snpa['concat']."',".$row_react_menor_5_snpa['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_snpa = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 350,
  width: 1100,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
	  bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_react_menor_5_snpa = document.getElementById('react_menor_5_snpa');
var chart_react_menor_5_snpa = new google.visualization.BarChart(container_react_menor_5_snpa);
google.visualization.events.addListener(chart_react_menor_5_snpa, 'ready', function () {
container_react_menor_5_snpa.innerHTML = '<img src="' + chart_react_menor_5_snpa.getImageURI() + '">';
});
chart_react_menor_5_snpa.draw(data_react_menor_5_snpa, options_react_menor_5_snpa);

<?php if (pg_num_rows ( $res_react_menor_5_snpa ) == 0){ ?>
	document.getElementById('container_react_menor_5_snpa').style.display = "none";
	document.getElementById('react_menor_5_snpa').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* MATEMÁTICAS
* Unidad diagnóstica: Forma espacio y medida
*/
var data_fem = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_fem = pg_fetch_assoc($res_fem)){
			if($row_fem['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_fem['Porcentaje']."%";}
			if($row_fem['Porcentaje']>0 && $row_fem['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_fem['Porcentaje']>40 && $row_fem['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_fem['cUnidadEvaluacion']."',".$row_fem['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_fem = {
tooltip: {trigger: 'none'},
  title: 'Forma espacio y medida',	  
  height: 70,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom: 20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_fem = document.getElementById('txt_fem');
var chart_fem = new google.visualization.BarChart(container_fem);
google.visualization.events.addListener(chart_fem, 'ready', function () {
container_fem.innerHTML = '<img src="' + chart_fem.getImageURI() + '">';
});
chart_fem.draw(data_fem, options_fem);

<?php if (pg_num_rows ( $res_fem ) == 0){ ?>
	document.getElementById('container_txt_fem').style.display = "none";
	document.getElementById('txt_fem').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>

/*FIN
* Unidad diagnóstica: Sentido numérico y pensamiento algebraico
*/

/*INICIO
* "Sentido Forma espacio y medida"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_fem = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_fem = pg_fetch_assoc($res_react_menor_5_fem)){
			if($row_react_menor_5_fem['Porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_fem['Porcentaje']."%";}
			if($row_react_menor_5_fem['Porcentaje']>0 && $row_react_menor_5_fem['Porcentaje']<=40){
				$color = $verde_insuficiente;
			}elseif($row_react_menor_5_fem['Porcentaje']>40 && $row_react_menor_5_fem['Porcentaje']<=60){
				$color = $verde_basico;
			}else{
				$color = $verde_satisfactorio;
			}
			echo "['".$row_react_menor_5_fem['concat']."',".$row_react_menor_5_fem['Porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_fem = {
tooltip: {trigger: 'none'},
  title: 'Eje temático',	  
  height: 350,
  width: 1100,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
	  bottom:20},
  hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
  series:{
	  0:{color: '<?php echo $verde_sobresaliente?>'}
},
  annotations:{
	  textStyle: {fontSize: 16, bold: true, color: '#000000',auraColor: '#f0f0f0'}
  }
};

var container_react_menor_5_fem = document.getElementById('react_menor_5_fem');
var chart_react_menor_5_fem = new google.visualization.BarChart(container_react_menor_5_fem);
google.visualization.events.addListener(chart_react_menor_5_fem, 'ready', function () {
container_react_menor_5_fem.innerHTML = '<img src="' + chart_react_menor_5_fem.getImageURI() + '">';
});
chart_react_menor_5_fem.draw(data_react_menor_5_fem, options_react_menor_5_fem);

<?php if (pg_num_rows ( $res_react_menor_5_fem ) == 0){ ?>
	document.getElementById('container_react_menor_5_fem').style.display = "none";
	document.getElementById('react_menor_5_fem').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
	//document.getElementById('matematicas').style.display = "none";
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
}

</script>
</head>
<body>

	<!--Inicia Contenedor principal-->
	<div class="main_container"> 
	<!--Inicio de página-->
		<div class="page_container">
		<!--Encabezado de página-->
			<div class="header">
					<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" align="right">
			</div>
		<!--Fini del encabezado de página-->	
				<div class="container">
					<br/>
					<table>
						<tr>
							<td class="td_green" style="width: 120px;text-align: left;padding-left: 10px;">
								Escuela:
							</td>
							<td colspan="3" class="td_data" style="text-align: left;<?php if(strlen($nom_cct)>55){echo 'font-size: 12px;';}?>">
								<?php echo $nom_cct;?>
							</td>
							<td class="td_green" style="120px;text-align: left;padding-left: 10px;">
								Nivel de marginación:
							</td>
							<td class="td_data" style="text-align: left;<?php if(strlen($nivel)>55){echo 'font-size: 12px;';}?>">
								<?php echo $gdo_marginacion;?>
							</td>
						</tr>
						<tr>
							<td class="td_green" style="width: 120px;text-align: left;padding-left: 10px;">
								Entidad:
							</td>
							<td colspan="3" class="td_data" style="text-align: left;<?php if(strlen($nom_entidad)>55){echo 'font-size: 12px;';}?>">
								<?php echo $nom_entidad;?>
							</td>
							<td class="td_green" style="120px;text-align: left;padding-left: 10px;">
								Municipio:
							</td>
							<td class="td_data" style="text-align: left;<?php if(strlen($nom_mun)>55){echo 'font-size: 12px;';}?>">
								<?php echo $nom_mun;?>
							</td>
						</tr>
						<tr>
							<td class="td_green" style="width: 120px;text-align: left;">
								Clave:
							</td>
							<td class="td_data">
								<?php echo substr($cve_cct,0,10);?>
							</td>
							<td class="td_green" style="width: 120px;">
								Turno:
							</td>
							<td class="td_data">
								<?php echo $nom_turno;?>
							</td>
							<td class="td_green" style="width: 120px;">
								Zona:
							</td>
							<td class="td_data">
								<?php echo $cve_zona_e;?>
							</td>
						</tr>
						<tr>
							<td colspan="6" class="td_left">
								<br/>
							</td>
						</tr>
					</table>
				</div>

				<div>
						<table class="max-width">
							<tr>
								<td rowspan="3" class="td_top left">¿Para qué sirve?</td>
								<td class="td_just">
								<?php echo $tabulador?><strong>MEJOREDU</strong> concibe a la educación como un derecho de todos los niños, niñas, adolescentes y 
								jóvenes que implica asegurarles el acceso, tránsito y permanencia a los centros escolares, así como un aprendizaje 
								pertinente, significativo y relevante. La valoración de este aprendizaje está articulada en una relación en donde 
								las evidencias de la evaluación sirvan a los centros escolares y a las autoridades educativas a generar orientaciones 
								que permitan a los alumnos aprender más y mejor.<br/>
								<?php echo $tabulador?>Con el <strong>propósito de coadyuvar con las escuelas a identificar las fortalezas y oportunidades</strong> respecto del aprendizaje de sus estudiantes y <strong>generar orientaciones que promuevan y faciliten procesos de mejora</strong> a través de identificar sus necesidades, retos y avances en los logros alcanzados, se presenta este reporte escolar con información de la última aplicación de la prueba <strong>PLANEA</strong>. Este reporte se generó para las <strong>escuelas secundarias que participaron en 2019 y en las que más de la mitad de los estudiantes</strong> que tomaron la prueba <strong>fueron ubicados en el nivel Insuficiente tanto en Lenguaje y Comunicación como en Matemáticas</strong>. Estas <strong>escuelas</strong> se consideran <strong>prioritarias</strong> de atención para las autoridades educativas en todos los niveles ya que en ellas más de la mitad de los alumnos que tomaron la prueba no lograron tener los aprendizajes mínimos esperados hacia el final de la secundaria y que les permitirían continuar avanzando sin dificultad hacia la educación media superior.
								<br/>
								<?php echo $tabulador?>El reporte integra información que debe verse como el resultado acumulado del proceso de aprendizaje de los estudiantes de la escuela a lo largo de los seis años de primaria y los tres de secundaria, además de las condiciones particulares de los estudiantes.
								<br/>
								<?php echo $tabulador?>En la lógica de la <strong>mejora continua, se espera que estos reportes sean uno de los múltiples apoyos que ayuden a los centros escolares a emprender procesos que propicien los ajustes o cambios que se requieren en la práctica para satisfacer sus necesidades, afrontar los retos y sostener o acrecentar los avances logrados en el aprendizaje de los alumnos</strong>. Se sugiere que, con base en esta información, en el conocimiento de las condiciones escolares y en el marco de una reflexión colectiva, el personal docente y directivo de cada escuela defina las acciones a seguir. Es importante considerar que, para fortalecer los aprendizajes y habilidades que se identifiquen como susceptibles de mejora, será necesario reforzar no sólo las acciones en el tercer grado, sino también en los grados previos en los que se aportan las bases académicas para alcanzar mejores logros educativos.
								<br/>
								</td>
							</tr>
						</table>
			  </div>
				<br/>
				<!--Separador de contenido-->
				<div>
						<div class="split">
							<img src="./imagenes/separador.png" style="width: 95%">
						</div>
				</div>
				<br/>
				<div>
					<table class="max-width">
						<tr>
							<td rowspan="6" class="td_top left">¿Qué contiene?</th>
							<td class="td_just"><strong>A.</strong><?php echo $tabulador;?>El <strong>porcentaje de alumnos en cada uno de los niveles de logro</strong>, tanto en Lenguaje y Comunicación como en Matemáticas en las aplicaciones de Planea 2015, 2017 y 2019.</th>
						</tr>
						<tr></tr>
						<tr>
							<td class="td_just">
								<strong>B.</strong><?php echo $tabulador;?>Un gráfico que muestra las escuelas en la zona escolar de acuerdo con el porcentaje de sus estudiantes que se ubicaron en el nivel insuficiente tanto en Lenguaje y Comunicación como en Matemáticas. Esto permite a la escuela darse una idea de los resultados obtenidos por otras escuelas similares.
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td class="td_just">
								<strong>C.</strong><?php echo $tabulador;?>Las <strong>prioridades de atención académica</strong>, con base en los reactivos en los que los estudiantes obtuvieron menor porcentaje de aciertos por cada eje temático, en los dos campos de conocimiento evaluados.
						  </td>
						</tr>
						<tr></tr>
						<tr>
							<td class="td_just">
							<strong>D.</strong><?php echo $tabulador;?>Las <strong>argumentaciones de</strong> cada uno de <strong>los reactivos de Lenguaje y Comunicación y Matemáticas</strong>en las que se expone el razonamiento de la respuesta correcta y las razones por las que las opciones restantes no son correctas.
						  </td>
						</tr>
					</table>
			  	</div>		

	</div>
		<!--Fin de página 1-->
	<div class="saltopagina"></div>


	<!--Inicio de página 2-->
	<div class="page_container">

	<!--Inicio del encabezado de página-->	
			<div class="header">
				<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center">
			</div>
	<!--Fini del encabezado de página-->	
			<div class="container">
				<h4 class="section_tittle"><strong>A. Resultados de la escuela por niveles de logro en Planea 2015, 2017 y 2019</strong></h4>
			</div>
			<!--Separador de contenido-->
			<div>
					<div class="split">
						<img src="./imagenes/separador.png" style="width: 95%">
					</div>
			</div>
			<br/>
			<div class="left_container">
			La prueba PLANEA valora el aprendizaje de los alumnos de 3ro de secundaria en Matemáticas y Lenguaje y comunicación y 
			de acuerdo con el puntaje obtenido en cada materia, cada alumno es clasificado en cada uno de los cuatro niveles de dominio:
			</div><br/>

			<div class="container">
				<table class="max-width">
					<tr>
						<td class="td_nvl td_bg_red">I - Insuficiente</td>
						<td class="td_nvl td_bg_yellow">II - Básico</td>
						<td class="td_nvl td_bg_green">III - Satisfactorio</td>
						<td class="td_nvl td_bg_blue">IV - Sobresaliente</td>
					</tr>
				</table>
			</div><br/>

			<div class="left_container">
			A continuación, se presentan los resultados que obtuvo esta escuela en tres de las aplicaciones de Planea, incluyendo la de 2019.
			</div>

		<div>
            <table>
                <tr>
                    <td>
                      <div id="comp_escuelas_ent_lyc"></div>
                    </td>
                </tr> 
                <tr>  
                    <td>
                      <div id="comp_escuelas_ent_mat"></div>
                    </td>
                </tr>
            </table>
		</div>
		<br/>
		<div>
			<table>
				<tr>
					<td class="bullet_text"><img src="./imagenes/widgets/nivel4.png"> Nivel IV Dominio Sobresaliente</td>
					<td class="bullet_text"><img src="./imagenes/widgets/nivel3.png"> Nivel III Dominio Satisfactorio</td>
					<td class="bullet_text"><img src="./imagenes/widgets/nivel2.png"> Nivel II Dominio Básico</td>
					<td class="bullet_text"><img src="./imagenes/widgets/nivel1.png"> Nivel I Dominio Insuficiente</td>
				</tr>
			</table>
		</div>

	</div>		
<!--Fin de página 2-->
<div class="saltopagina"></div>

<!--Inicio de página 3-->
<div class="page_container">
	<div class="header">
			<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right">
	</div>

	<div class="container">
				<h4 class="section_tittle">B. Porcentaje de estudiantes en nivel insuficiente en las escuelas de la misma zona escolar</h4>
	</div>

	<!--Separador de contenido-->
	<div>
		<div class="split">
			<img src="./imagenes/separador.png" style="width: 95%">
		</div>
	</div>
	<br/>
	<div class="left_container">
		El hecho de que un estudiante se encuentre en el nivel insuficiente (o nivel I) es un indicio de que no tiene un dominio 
		básico de los aprendizajes clave al término de la Educación Secundaria y por tanto no es deseable que en una escuela exista 
		una alta proporción de estudiantes con este nivel de dominio mínimo.<br/>
		La siguiente gráfica muestra el porcentaje de alumnos que obtuvieron el mínimo nivel de logro en Lenguaje y Comunicación y 
		Matemáticas, de esta escuela y el resto de las que componen su zona escolar. El propósito no es compararse o medirse entre 
		escuelas de una misma zona sino propiciar un intercambio de experiencias entre centros escolares, especialmente desde aquellas 
		escuelas con mejores resultados y donde hay menores porcentajes de alumnos en nivel Insuficiente en ambas materias evaluadas y 
		que la información sea útil para priorizar los esfuerzos que habrán de llevarse a cabo para asegurar que la cantidad de 
		estudiantes que no tienen un dominio por lo menos básico de los aprendizajes esperados sea cada vez menor.
	</div><br/>

		<div>
			<table>
					<tr>
							<!--td class="elem_center" style="text-align: center;">
								<img src="./imagenes/cuadrantes.png" style="width: 300px;height: 300px;">
							</td-->
							<td class="elem_center" style="text-align: center;">
								<div id="cuadrantes"></div>
							</td>
					</tr>
			</table>
		</div>				

		<div class="text">
			<ul style="font-size:14px;vertical-align: top;">
			<li>
				<span><img src="./imagenes/widgets/cuadro_verde.png">&nbsp;
				<?php echo $txt_1;?>
				</span>
			</li>
			<li>
				<span><img src="./imagenes/widgets/circulo_gris.png">&nbsp;
					<strong>Escuelas en la zona escolar</strong>
				</span>
			</li>
			</ul>
		</div>
		<!--div class="left_container">
		<?php echo $txt_2;?>
		</div-->	

		<div  class="text">
		<ul style="font-size:14px;vertical-align: top;">
		<!--
		<li><?php echo $txt_1;?></li>
		<li><?php echo $txt_2;?></li>
		<li><?php echo $txt_3;?></li>
		<li><?php echo $txt_4;?></li>
		-->
</ul>
</div>
	</div>
	<!--Fin de página 3-->
	<div class="saltopagina"></div>

	<!--Inicio de página 4-->

<div id="lyc"> <!--Inicio seccion lenguaje y comunicacion -->
	<div class="page_container">
		<div class="header">
			<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right">
		</div>			

		<div class="container">
				<h4 class="section_tittle">C. Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN por eje temático</h4>
		</div>	
		<!--Separador de contenido-->
		<div>
			<div class="split">
				<img src="./imagenes/separador.png" style="width: 95%">
			</div>
		</div>
		<br/>
		<div class="reac_container" id="container_manejo_const_txt">
						<h5>Eje temático: Reflexión sobre la lengua</h5>
						<div id="manejo_const_txt"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5">
						<h4 class="sub_tittle">Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h4>
						<div id="react_menor_5"></div>
		</div>
		<div class="reac_container" id="container_txt_arg">
						<h5>Eje temático: Comprensión lectora</h5>
						<div id="txt_arg"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_arg">
						<h4 class="sub_tittle">Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h4>
						<div id="react_menor_5_arg"></div>
		</div>
		
	</div>	
	<!--Fin de página 4-->

	<div class="saltopagina"></div>	
</div> <!--Fin seccion lenguaje y comunicacion -->

<!--Inicio de página 6-->
 
<div id="matematicas"> <!--Inicio seccion matemáticas -->
	<div class="page_container">
		<div class="header">
			<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right">
		</div>			
		<div class="container">
			<h4 class="section_tittle">D. Porcentaje de aciertos en MATEMÁTICAS por eje temático</h4>
		</div>	
		<!--Separador de contenido-->
		<div>
			<div class="split">
				<img src="./imagenes/separador.png" style="width: 95%">
			</div>
		</div>
		<br/>
		<div class="reac_container" id="container_txt_fem">
			<h5>Eje temático: Forma, Espacio y Medida</h5>
			<div id="txt_fem"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_fem">
			<h4 class="sub_tittle">Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h4>
			<div id="react_menor_5_fem"></div>
		</div>

		<div class="reac_container" id="container_txt_mdi">
			<h5>Eje temático: Manejo de la Información</h5>
			<div id="txt_mdi"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_mdi">
			<h4 class="sub_tittle">Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h4>
			<div id="react_menor_5_mdi"></div>
		</div>

	</div>
	<!--Fin de página 6-->
	<div class="saltopagina"></div>	
		<!--Inicio de página 7-->
		<div class="page_container">
			<div class="header">
				<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right">
			</div>			
				<div class="container">
						<h4 class="section_tittle">D. Porcentaje de aciertos en MATEMÁTICAS por eje temático</h4>
				</div>	
				<!--Separador de contenido-->
				<div>
					<div class="split">
						<img src="./imagenes/separador.png" style="width: 95%">
					</div>
				</div>
				<br/>
				<div class="reac_container" id="container_txt_snpa">
								<h5>Eje temático: Sentido numérico y pensamiento algebráico</h5>
								<div id="txt_snpa"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_snpa">
								<h4 class="sub_tittle">Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h4>
								<div id="react_menor_5_snpa"></div>
				</div>

		</div>
	<!--Fin de página 7-->

</div> <!--fin seccion matemáticas -->

<div id="argumentaciones"> <!--Inicio seccion de argumentaciones -->
	<div class="saltopagina"></div>
	<div class="page_container">
		<div class="container">
			<table class="max-width">
				<thead>
				<tr><td colspan="5" style="border:hidden"><img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right"></td></tr>
				<tr><td colspan="5" style="text-align:center" class="td_green">D. ARGUMENTACIONES DE LOS REACTIVOS DE LENGUAJE Y COMUNICACIÓN</td></tr>
				<tr><th class="td_gold" style="width:5%">Reactivo</th><th class="td_gold" style="width:10%">Eje temático</th><th class="td_gold" style="width:10%">Unidad de Evaluación</th><th class="td_gold" style="width:10%">Descriptor</th><th class="td_gold" style="width:65%">Argumentación</th></tr>
				</thead>
				<body>
				<tr><td class="celda_normal">1</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Interpretar el significado de una norma.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se plantea el tema sobre el aprovechamiento escolar. Para identificar la respuesta correcta, el alumno debe comprender la regla e interpretarla para encontrar la opción adecuada.</strong><br/>A. La respuesta es incorrecta porque no refleja lo planteado en el artículo tercero, sino a la seguridad de los alumnos.<br/>C. La respuesta es incorrecta porque no se plantea en el artículo tercero lo relacionado con asesorías académicas.<br/>D. La respuesta es incorrecta porque se restringe a un problema de calificaciones y no de aprovechamiento académico a lo que hace referencia el artículo tercero.</td></tr>
<tr><td class="celda_normal">2</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Elegir la norma que reglamente una situación conflictiva.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque el alumno elige, de entre diversas normas, aquella apropiada para mantener el orden en un espacio social. Para identificar la respuesta, el alumno debe comprender todas las reglas en cuestión y evaluar cuál es la más adecuada.</strong><br/>B. La respuesta es incorrecta porque no responde al tema de la armonía, sino a los días de sesión y el inicio de las reuniones del Comité.<br/>C. La respuesta es incorrecta porque esta opción hace referencia al uso de aparatos electrónicos con el fin de que no interrumpan la reunión, pero ello no necesariamente genera conflicto o desorden.<br/>D. La respuesta es incorrecta porque la norma 10 se refiere a sanciones por incumplimiento, mas no armonía.</td></tr>
<tr><td class="celda_normal">3</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar el derecho que se establece en una norma.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se resuelve acertadamente la solicitud de la pregunta: identificar una obligación. Para ello, el alumno debe identificar el tipo de obligación solicitada.</strong><br/>A. La respuesta es incorrecta porque la obligación que resalta la norma 2 no es elaborar carteles y folletos, sino divulgar los acuerdos del comité.<br/>B. La respuesta es incorrecta porque la obligación 2 no pretende establecer obligaciones, sino divulgar acuerdos.<br/>C. La respuesta es incorrecta porque no responde a la identificación de una obligación general vertida en el reglamento.</td></tr>
<tr><td class="celda_normal">4</td><td class="celda_normal">Reflexión sobre la lengua</td><td class="celda_normal">Reflexión sintáctica y morfosintáctica </td><td class="celda_normal">Identificar la estructura gramatical (verbal) usada para redactar una norma.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se presenta la opción adecuada que preserva la estructura gramatical usada para la redacción del reglamento en cuestión. Para responder, el alumno debe ir más allá del contenido de la regla, pues todas las opciones proponen más o menos el mismo contenido. En este caso, el alumno debe elegir la redacción adecuada de acuerdo con estilo empleado en el resto del documento.</strong><br/>A. La respuesta es incorrecta porque no emplea una redacción adecuada a la usada habitualmente en la redacción de un reglamento.<br/>B. La respuesta es incorrecta porque la respuesta está construida como un hecho y no como una regla que debe acatarse.<br/>D. La respuesta es incorrecta porque rompe con el acuerdo del contenido del reglamento, además de que no preserva el estilo verbal empleado en el resto del documento. </td></tr>
<tr><td class="celda_normal">5</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar el propósito de un guion de entrevista.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella el alumno debe leer la propuesta para un guion de entrevista e interpretar a quién se puede entrevistar de acuerdo con la información biográfica planteada. Para que el alumno localice la respuesta, debe interpretar la información ofrecida y vincular los posibles temas con la persona entrevistada.</strong><br/>B. La respuesta es incorrecta porque la interpretación es errónea en cuanto a la profesión del entrevistado.<br/>C. La respuesta es incorrecta porque la interpretación es errónea respecto del cargo que desempeña el entrevistado.<br/>D. La respuesta es incorrecta porque la interpretación del cargo y profesión desempeñados por el entrevistado son erróneas.</td></tr>
<tr><td class="celda_normal">6</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Reconstruir la trama de una obra teatral.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se resume el contenido global de la escena: los dueños de un negocio esperan a una empleada para entrevistarla y luego contratarla. Para ello, el alumno debe leer y comprender los sucesos narrados e integrarlos. </strong><br/>A. La respuesta es incorrecta porque contiene sólo se presenta parte de la trama, no la global: la riña entre esposos.<br/>C. La respuesta es incorrecta porque desarrolla una historia que es interrumpida por una persona, es decir se presenta una serie de hechos que no se relacionan con el total de la trama.<br/>D. La respuesta es incorrecta porque es anecdótica y no se relaciona con lo solicitado con la consigna. </td></tr>
<tr><td class="celda_normal">7</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar el ambiente de un fragmento de una obra de teatro.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque recrea el ambiente adecuado de la escena. Para ello, el alumno debe leer las acotaciones y diálogos, no en el sentido literal, sino interpretar que se trata de una obra cómica.</strong><br/>A. La respuesta es incorrecta porque el ambiente no es melancólico, sino divertido. De esta forma, la selección de este distractor indica una interpretación equivocada del espacio teatral y de los parlamentos.<br/>C. La respuesta es incorrecta porque no hay elementos para hacer una interpretación de la escena como algo grotesco.<br/>D. La respuesta es incorrecta porque indicaría que el lector no está interpretando correctamente la universalidad de los personajes y del ambiente que les rodea.</td></tr>
<tr><td class="celda_normal">8</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar las acciones de los personajes de acuerdo a la época en la que sucede la historia.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se establece correctamente la relación causal de las acciones de los personajes. Para ello, el alumno debe identificar las acciones de cada uno y relacionarlas lógicamente con la opción adecuada.</strong><br/>A. La respuesta es incorrecta porque expresa el comportamiento de un personaje y no una relación entre personajes, como se solicita.<br/>B. La respuesta es incorrecta porque los personajes secundarios hacen una sobreinterpretación de las acciones del personaje principal, aspecto no solicitado en la pregunta.<br/>C. La respuesta es incorrecta porque los personajes secundarios evalúan sólo los gustos culinarios del personaje principal; sin embargo, la pregunta hace énfasis en el tema de la contratación de Retobona en términos en general.  </td></tr>
<tr><td class="celda_normal">9</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Identificar las circunstancias sociales de la época.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella el lector crea un distanciamiento crítico respecto del texto e identifica el tipo de relación laboral establecida en una sociedad distinta a la suya. Para ello, el alumno debe comprender el texto y después hacer un juicio de él.</strong><br/>A. La respuesta es incorrecta porque el alumno, aunque comprende el tema central de la escena, no es capaz de hacer un distanciamiento crítico de la misma, sino que se queda en la literalidad de la escena.<br/>B. La respuesta es incorrecta porque se presenta una interpretación que no se deriva del contenido de la escena.<br/>C. La respuesta es incorrecta porque se presenta un hecho social que se distancia de la temática real de la escena.</td></tr>
<tr><td class="celda_normal">10</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Valorar las estrategias discursivas utilizadas en el debate.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se presenta la posición del participante respecto de una problemática dentro de un debate, la cual está sustentada en hechos verificables dentro del texto: toxicidad y desintegración del material contaminante. Para ello, el estudiante debe identificar y comprender el tema de discusión, posteriormente, la posición de cada participante y comprender los argumentos que ofrecen estos.</strong><br/>B. La respuesta es incorrecta porque, aunque se presenta el posicionamiento correcto del participante, no se brindan los apoyos que dan validez a sus puntos de vista, aspecto solicitado en la pregunta.<br/>C. La respuesta es incorrecta porque no presta relevancia al punto de vista de los participantes, lo que hace inválida esta opción.<br/>D. La respuesta es incorrecta porque parte de una generalización no autorizada a partir del texto: no es verdad que todos los ecologistas nieguen el uso de la energía nuclear.</td></tr>
<tr><td class="celda_normal">11</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Evaluar las secuencias argumentativas de todo el debate.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se resume la justificación empleada por un participante para fundamentar su punto de vista en un debate. Para identificar la respuesta correcta, el lector debe hacer un seguimiento del discurso pronunciado por el participante y sintetizarlo: la energía nuclear es benéfica si es manejada correctamente.</strong><br/>A. La respuesta es incorrecta porque contiene una opinión no sustentada.<br/>C. La respuesta es incorrecta porque presenta un posicionamiento ante el tema energético, pero no contiene los apoyos requeridos para establecer una argumentación válida.<br/>D.  La respuesta es incorrecta porque es contraria a los planteamientos vertidos por los participantes.</td></tr>
<tr><td class="celda_normal">12</td><td class="celda_normal">Reflexión sobre la lengua</td><td class="celda_normal">Reflexión semántica</td><td class="celda_normal">Seleccionar las conexiones lógicas que organizan un argumento.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque contiene los dos conectores o frases discursivas que estructuran el argumento lógicamente: "además" y "así que".</strong><br/>A. La respuesta es incorrecta porque contienen palabras que no tienen una función argumental dentro del texto, sino cohesiva en términos generales.<br/>C. La respuesta es incorrecta porque contiene palabras cuya función es de carácter cohesivo y sintáctico más no argumentativo.<br/>D. La respuesta es incorrecta porque si bien una palabra funciona como conector para una estructura argumentativa, la otra tiene una función cohesiva en términos generales.</td></tr>
<tr><td class="celda_normal">13</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Identificar opiniones y argumentos empleados por un participante en un debate.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque presenta la posición de un participante en un debate y la justificación que apoya su punto de vista. Para identificar la respuesta correcta, el lector debe evaluar la perspectiva del panelista y los argumentos empleados: no ha sido posible manejar adecuadamente material peligroso en ningún país.</strong><br/>A. La respuesta es incorrecta porque presenta un punto de vista que no está sustentado, tal como se solicita en la pregunta.<br/>B. La respuesta es incorrecta porque presenta una interpretación que alterada el argumento original del participante (Mailler).<br/>D. La respuesta es incorrecta porque niega lo afirmado por los participantes.</td></tr>
<tr><td class="celda_normal">14</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Reconocer el género periodístico de una nota frente a otra.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se presentan los nombres adecuados de los textos periodísticos presentados: noticia y artículo de opinión. Para ello, el alumno debe leerlos y evaluar sus características textuales para dar la respuesta correcta.</strong><br/>B. La respuesta es incorrecta porque las características de un reportaje no incluyen la opinión del autor proporcionada en el texto 1. Además, se confunde la crónica con la noticia ya que no diferencian las estructurales de ambos textos.<br/>C. La respuesta es incorrecta porque, aunque el primer texto puede incluir opiniones, el segundo texto (una crónica) difiere del texto noticioso en cuanto al tipo de información que proporciona, así como su orden y temporalidad.<br/>D. La respuesta es incorrecta porque el reportaje no incluye las opiniones ni argumentos del articulista; asimismo, la noticia no trae opiniones.</td></tr>
<tr><td class="celda_normal">15</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Establecer diferencia de forma de un mismo hecho en dos periódicos.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque señala que el primer texto presenta hechos, en tanto el segundo expone opiniones. Para ello, el alumno debe leer la información y reflexionar sobre la manera de enunciarla con respecto al tipo de texto presentado.</strong><br/>A. La respuesta es incorrecta porque el texto noticioso no pretende hacer reflexionar a los lectores, sino informarlos; por el contrario, el artículo de opinión no se enfoca en informar sino en llevar a los lectores a la reflexión.<br/>C. La respuesta es incorrecta porque los propósitos de los textos están invertidos en la respuesta: artículo de opinión, reflexionar; noticia, informar.<br/>D. La respuesta es incorrecta porque se retoman diferencias secundarias y equivocadas entre los dos textos presentados.</td></tr>
<tr><td class="celda_normal">16</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Establecer semejanzas y diferencias de contenido de un mismo hecho en dos periódicos.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque ella contiene los temas comunes tratados en ambos textos, así como sus diferencias. Para ello, el alumno debe leer e identificar que el tema común del artículo es la discriminación que sufren los inmigrantes mexicanos, así como también detectar que la diferencia entre el texto 1 y el 2 es que muchos trabajadores son eficientes.</strong><br/>B. La respuesta es incorrecta porque no retoma los temas de semejanza, sino particularidades de cada texto, como el asunto de la delincuencia, que sólo pertenece al texto 1.<br/>C. La respuesta es incorrecta porque sólo en un texto se destacan las particularidades de las notas y no sus semejanzas, como la fecha en la que entrará en vigor la ley, dicho en la nota 1.<br/>D. La respuesta es incorrecta porque los vínculos entre semejanzas y diferencias de las temáticas no son globales ni correctos.</td></tr>
<tr><td class="celda_normal">17</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Distinguir entre hechos y opiniones en un texto periodístico.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque indica una opinión sobre los mexicanos. El alumno debe leer e identificar opiniones, hechos y descripciones y elegir, con base en esa distinción, la respuesta correcta. </strong><br/>B. La respuesta es incorrecta porque se hace una descripción fisonómica de los mexicanos, de la cual no se opina nada.<br/>C. La respuesta es incorrecta porque es un hecho, es decir, colaboraron en 1986 en algo.<br/>D. La respuesta es incorrecta porque es un sentimiento de los mexicanos, no una opinión del articulista.</td></tr>
<tr><td class="celda_normal">18</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Elegir la pregunta que sirvió de base para construir la gráfica.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se presentan dos preguntas que dan origen a la investigación, cuyos resultados se traducen en una gráfica. Ésta muestra los resultados de la indagación de dos aspectos a través de dos gráficas: horas dedicadas a ver televisión (gráfica 1) y horarios (gráfica 2). Para elegir la respuesta correcta debe comprenderse ambos temas e interpretar su representación a través de algunas preguntas posibles.</strong><br/>B. La respuesta es incorrecta porque las preguntas no corresponden con los temas representados en la gráfica. Ambas preguntas hacen referencia a gustos.<br/>C. La respuesta es incorrecta porque la primera pregunta versa sobre el tema de medios de comunicación, en tanto la segunda sobre hábitos de estudio.<br/>D. La respuesta es incorrecta porque la primera pregunta versa sobre el tema de medios de comunicación, en tanto la segunda sobre opiniones de temáticas televisivas. </td></tr>
<tr><td class="celda_normal">19</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Elegir los datos adecuados para la organización de una tabla a partir de la lectura de una gráfica.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se plantean los datos que complementan una tabla. Para ello, el alumno debe evaluar la información y luego interpretar cuáles datos no fueron consignados en una tabla. </strong><br/>A. La respuesta es incorrecta porque 15.8% corresponde a "En los cuatro horarios" de la gráfica de la derecha que se refiere a horarios para ver televisión, más no al porcentaje de encuestados que ve la televisión más de 6 horas. Además, el otro valor expresado en esta opción corresponde con una etiqueta ya proporcionada en la tabla.<br/>C. La respuesta es incorrecta porque el horario de uso indicado (4 a 7 pm) corresponde a la gráfica de la derecha, y se pregunta el dato de la gráfica izquierda. Además, esta opción plantea datos que no corresponden con el orden específico solicitado en la tabla.<br/>D. La respuesta es incorrecta porque ambos datos presentados en la opción ya están incorporados en la tabla y corresponden con información distinta a la solicitada.</td></tr>
<tr><td class="celda_normal">20</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar la información que añade un dato específico a una gráfica.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se interpreta el sentido de un dato que aparece en la gráfica (los encuestados que ven la televisión entre 7 y 9 en la gráfica de la derecha). Para ello, el alumno debe leer el dato e interpretar su significado a la luz de la información global integrada en las gráficas.</strong><br/>A. La respuesta es incorrecta porque sostiene que los entrevistados ven la televisión por más de tres horas, aspecto que no corresponde con el dato solicitado en la base del reactivo.<br/>C. La respuesta es incorrecta porque el horario indicado (2 a 4 horas) corresponde a un dato distinto al solicitado en la pregunta.<br/>D. La respuesta es incorrecta porque la relación entre el dato y la etiqueta que lo representa es incorrecta. Ésta última hace referencia al número de horas dedicadas a ver televisión.</td></tr>
<tr><td class="celda_normal">21</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Integrar en un enunciado el tema del  ensayo. </td><td class="celda_normal"><strong>A. La respuesta es correcta porque presenta el tema global el ensayo en cuestión: el derecho de la niñez a opinar y decidir. Para ello, el alumno sólo debe comprender de qué se habla en el texto.</strong><br/>B. La respuesta es incorrecta porque no corresponde al tema global del ensayo y sólo recupera una idea secundaria.<br/>C. La respuesta es incorrecta porque tergiversa la información del ensayo.<br/>D. La respuesta es incorrecta porque plantea lo opuesto al ensayo.</td></tr>
<tr><td class="celda_normal">22</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Identificar el propósito del autor de un ensayo.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se presenta el propósito del ensayo, que es promover la idea de que los niños y jóvenes tienen derecho a pensar y decidir libremente. Para ello, el alumno debe identificar el planteamiento central de este texto.</strong><br/>A. La respuesta es incorrecta porque es contraria al propósito original del texto, ya que refuerza la idea de que sólo los padres deben opinar y tomar decisiones.<br/>B. La respuesta es incorrecta porque presenta una explicación de las acciones de los padres, lo cual no forma parte del contenido del ensayo.<br/>D. La respuesta es incorrecta porque omite el propósito de convencer a una audiencia es particular: los padres de familia.</td></tr>
<tr><td class="celda_normal">23</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar punto de vista del autor de un ensayo.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se presenta la opinión del escritor respecto al derecho de los niños y los jóvenes sobre opinar y decidir. El alumno necesita comprender el tema global, relacionar las ideas del autor en torno a un tema e identificar su punto de vista.</strong><br/>A. La respuesta es incorrecta porque contiene una sobreinterpretación derivada del ensayo: el autor no afirma que los niños deben influir en la opinión de los padres, sino que los padres deben cambiar su mentalidad por sí mismos.<br/>B. La respuesta es incorrecta porque contiene una interpretación contraria de la planteada por el autor.<br/>C. La respuesta es incorrecta porque se refiere a las costumbres y tradiciones (un hecho) y no al punto de vista del autor.</td></tr>
<tr><td class="celda_normal">25</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Sintetizar la conclusión del autor del ensayo. </td><td class="celda_normal"><strong>A. La respuesta es correcta porque integra la conclusión a la que llega el autor en el ensayo. Para ello, el alumno debe leer el texto, comprender los argumentos dados por el autor e interpretarlos para poder identificar la conclusión a la que llega el autor en torno a una temática.</strong><br/>B. La respuesta es incorrecta porque presenta una conclusión que denota una interpretación equivocada del planteamiento que hace el autor sobre el tema.<br/>C. La respuesta es incorrecta porque presenta una conclusión contraria a la del autor del ensayo.<br/>D. La respuesta es incorrecta porque presenta un argumento falso y no una conclusión. Además, denota problemas de interpretación del lector acerca de la información presentada en el ensayo.</td></tr>
<tr><td class="celda_normal">26</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar la secuencia argumentativa en un ensayo.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se hace referencia a la secuencia argumentativa del autor. Para ello, el alumno debe identificar la progresión lógica de la información, así como los puntos de vista del autor.</strong><br/>A. La respuesta es incorrecta porque la secuencia argumentativa contiene interpretaciones erróneas acerca del contenido del ensayo y del punto de vista del autor. Tal es el caso de la aseveración que los padres deben seguir decidiendo por sus hijos.<br/>B. La respuesta es incorrecta porque la secuencia argumentativa plantea posturas que son contrarias a la del autor. Por ejemplo, la aseveración: La opinión de la gente menor no tiene ninguna importancia...<br/>C. La respuesta es incorrecta porque la secuencia argumentativa refuerza la idea de que los niños y jóvenes deben opinar y decidir hasta la edad adulta. Este planteamiento es cuestionado por el autor del ensayo.</td></tr>
<tr><td class="celda_normal">27</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Seleccionar el diálogo adecuado para un personaje a partir de una biografía.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque, a través de un diálogo, se recrean las acciones de un personaje histórico presentado en un texto biográfico. Para ello, el alumno debe leer el texto, comprenderlo e interpretar el diálogo que recupere y sea pertinente con las características del personaje.</strong><br/>A. La respuesta es incorrecta porque el contenido del diálogo no corresponde con las características del personaje histórico presentado en el texto. Por el contrario, apela a una persona despreocupada por los demás, aspecto que es equivocado.<br/>B. La respuesta es incorrecta porque de acuerdo con lo planteado en el texto, el personaje no estaba interesado por los estudios en sí mismos, sino manifestaba un interés por ayudar a los demás.<br/>D. La respuesta es incorrecta porque el personaje presentado habla de sí de forma vanidosa, lo cual no se aprecia en el contenido del texto.</td></tr>
<tr><td class="celda_normal">29</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Seleccionar, a partir de un texto narrativo, acotaciones para la representación del espacio en una obra teatral.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se presenta el escenario pertinente con el contenido de la biografía. Para ello, el alumno debe analizar el contenido de todos los párrafos, ubicar los espacios físicos referidos y decidir cuál sería el más adecuado.</strong><br/>A. La respuesta es incorrecta porque lo esencial del personaje es conocer otras culturas, por lo que la referencia a un personaje solitario que denota esta opción, no tiene sentido en la lógica del personaje biografiado.<br/>B. La respuesta es incorrecta porque este escenario refleja un espacio poco común en la vida del personaje.<br/>D. La respuesta es incorrecta porque refleja un espacio científico y no antropológico como en el que se desenvolvía el personaje.</td></tr>
<tr><td class="celda_normal">30</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Seleccionar, a partir de un texto narrativo, acotaciones para la representación de un actor en una obra teatral.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se establecen las características psicológicas del personaje. Para ello, el alumno debe comprender la vida del personaje, los sucesos que le afectaron, así como sus intereses para luego traducirlos en una caracterización nueva o reinterpretada de su personalidad.</strong><br/>B. La respuesta es incorrecta porque hay una interpretación parcial del personaje, si bien era culta, también era una apasionada de la antropología.<br/>C. La respuesta es incorrecta porque omite una caracterísitica central del personaje biografiado que es la pasión por el trabajo.<br/>D. La respuesta es incorrecta porque la descripción no corresponde con las características del personaje biografiado.</td></tr>
<tr><td class="celda_normal">31</td><td class="celda_normal">Reflexión sobre la lengua</td><td class="celda_normal">Reflexión semántica</td><td class="celda_normal">Elegir los signos de puntuación que reflejan estados de ánimo de los personajes.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se establece la relación semántica entre una expresión y su posible interpretación como gozo o alegría. El alumno debe leer la expresión y asociarla a un sentimiento, para ello debe considerar los signos de admiración que se incluyen en la frase.</strong><br/>C. La respuesta es incorrecta porque la lectura semántica de la palabra no se ajusta a la expuesta en el diálogo: ahí no hay angustia.<br/>D. La respuesta es incorrecta porque la lectura semántica de la palabra no se ajusta a la expuesta en el diálogo: no hay tranquilidad.<br/>A. La respuesta es correcta porque en ella se ingresa una posible pregunta, la cual puede darle continuidad a la entrevista. Para ello, el alumno debe comprender el desarrollo y tópicos de la entrevista y evaluar cuál de las preguntas es la mejor.</td></tr>
<tr><td class="celda_normal">32</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Elegir la pregunta que permita reorientar la información de acuerdo con el propósito de la entrevista (cuando el entrevistado se desvíe del tema).</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se ingresa una posible pregunta, la cual puede darle continuidad a la entrevista. Para ello, el alumno debe comprender el desarrollo y tópicos de la entrevista y evaluar cuál de las preguntas es la mejor.</strong><br/>B. La respuesta es incorrecta porque al pregunta plantea un nuevo tópico (su punto de vista sobre la educación en el extranjero), pero no sobre la trayectoria de la persona entrevistada.<br/>C. La respuesta es incorrecta porque la pregunta hace referencia a una recomendación para los estudiantes y de esta manera rompe con la progresión lógica de la entrevista.<br/>D. La respuesta es incorrecta porque no da continuidad a la estructura lógica de la entrevista.</td></tr>
<tr><td class="celda_normal">33</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Identificar la descripción que caracterice al entrevistado.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se indica la parte del texto donde se encuentra una descripción. Para ello, el alumno debe analizar las secciones del texto (inicio, desarrollo y cierre) y percatarse en cuál de ellas se describe a la persona entrevistada.</strong><br/>A. La respuesta es incorrecta porque, por las características del texto, en la entrevista no existe un nudo en el que se describa a ningún personaje.<br/>B. La respuesta es incorrecta porque en el cuerpo de la entrevista no existen elementos de descripción que proporcione características de la persona entrevistada.<br/>D. La respuesta es incorrecta porque la descripción que se plantea en la conclusión se refiere al espacio físico donde se llevó a cabo la entrevista, más no a la descripción de la entrevistada. </td></tr>
<tr><td class="celda_normal">34</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Identificar la situación comunicativa  en la que se desarrolló la entrevista.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se establecen las dos actitudes con las que participan los sujetos en la entrevista (entrevistada y entrevistador): empatía y disposición.</strong><br/>A. La respuesta es incorrecta porque establece dos actitudes que no corresponden con las mostradas por la entrevistada: cuestionamiento e inconformidad.<br/>C. La respuesta es incorrecta porque establece una actitud que no corresponde con la mostrada ni por la entrevistada ni por el entrevistador: ambos fueron reflexivos, aunque no creativos.<br/>D. La respuesta es incorrecta porque establece una actitud contraria a la mostrada por Julieta Fierro durante la entrevista: falta de dosposición. </td></tr>
<tr><td class="celda_normal">36</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Seleccionar de un listado de preguntas la pertinente a un propósito específico.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque contiene una pregunta que da continuidad al tema de la encuesta y cumple con su propósito: recabar información sobre los medios de comunicación que utilizan los estudiantes. Para ello, el alumno debe comprender el sentido global del texto, entender el propósito de la encuesta y evaluar l aopción que podría sumarse lógicamente al conjunto de preguntas ya establecidas.</strong><br/>B. La respuesta es incorrecta porque plantea una pregunta que, si bien se liga de manera general con la temática de la encuesta, no cumple con su propósito (recabar información sobre los medios utilizados por los estudiantes).<br/>C. La respuesta es incorrecta porque plantea una pregunta que indaga el impacto que un medio de comunicación puede tener, pero que no aporta información que ayude a cumplir con el propósito de la encuesta.<br/>D. La respuesta es incorrecta porque indaga una valoración sobre la información de un medio de comunicación particular (el Internet), pero que no aporta información que ayude a cumplir con el propósito de la encuesta. </td></tr>
<tr><td class="celda_normal">37</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Identificar preguntas pertinentes y no pertinentes de acuerdo al objetivo de una encuesta.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se presenta la pregunta menos pertinente para la encuesta. Para ello, el alumno debe comprender el sentido global de la encuesta y evaluar, de entre todas las opciones, aquella pregunta que se aleja del propósito de la encuesta: recabar información sobre los medios de comunicación que utilizan los estudiantes. </strong><br/>A. La respuesta es incorrecta porque la pregunta contribuye a saber sobre los medios de comunicación que utilizan los estudiantes y, por tanto, cumple con el propósito de la encuesta.<br/>B. La respuesta es incorrecta porque la pregunta arroja información sobre los medios de comunicación que utilizan los estudiantes y, por tanto, cumple con el propósito de la encuesta.<br/>C. La respuesta es incorrecta porque la pregunta amplia la información sobre los medios de comunicación que utilizan los estudiantes y, por tanto, cumple con el propósito de la encuesta.</td></tr>
<tr><td class="celda_normal">38</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Seleccionar la secuencia correcta de un listado de preguntas de acuerdo con el propósito de la encuesta.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se establece el orden temático en el que está dividido la encuesta. Para ello, el alumno debe hacer un análisis tanto del contenido de las preguntas como del orden en el que se encuentran y decidir la respuesta que agrupa correctamente los subtemas.</strong><br/>B. La respuesta es incorrecta porque en ella se establece un orden distinto de los subtemas presentados en la encuesta: Datos personales y luego frecuencia, lo que es incorrecto.<br/>C. La respuesta es incorrecta porque en ella se presenta un orden distinto de los subtemas presentados en la encuesta: Datos personales y luego medios en educación.<br/>D. La respuesta es incorrecta porque el tercer subtema no corresponde a los medios de educación, sino a la frecuencia de uso.</td></tr>
<tr><td class="celda_normal">39</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Seleccionar la pregunta que permite obtener  información específica.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se encuentran las preguntas que aportan información referente a los medios de comunicación que los estudiantes utilizan para apoyar su educación.</strong><br/>A. La respuesta es incorrecta porque una de las preguntas del inciso responde al tiempo de navegación en Internet (pregunta 4); por lo que  no permite conocer los medios de comunicación que utilizan los estudiantes como apoyo a su educación.<br/>B. La respuesta es incorrecta porque una de las preguntas del inciso responde al tiempo usado para ver la televisión (pregunta 5); por lo que no permite conocer qué medios de comunicación utilizan los estudiantes como apoyo a su educación.<br/>C. La respuesta es incorrecta porque una de las preguntas del inciso responde al tiempo que se dedica a la lectura (pregunta 6); por lo que no permite conocer qué medios de comunicación utilizan los estudiantes como apoyo a su educación.  </td></tr>
<tr><td class="celda_normal">40</td><td class="celda_normal">Reflexión sobre la lengua</td><td class="celda_normal">Reflexión semántica</td><td class="celda_normal">Identificar el lenguaje figurado en un poema vanguardista.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se presenta el verso que emplea un lenguaje figurado. Para ello, el alumno debe identificar la opción en la que se expresa una idea u objeto en términos de otro objeto, pero que apela a su semejanza: la ola en semejanza al movimiento de lejanía del sujeto amado. </strong><br/>A. La respuesta es incorrecta porque el verso presenta una oposición de ideas, utilizando el lenguaje literal.<br/>B. La respuesta es incorrecta porque alude a la literalidad de un hecho: insiste en su desgracia.<br/>C. La respuesta es incorrecta porque alude a la condicionalidad de un hecho: aquello que pudiera no ser.</td></tr>
<tr><td class="celda_normal">41</td><td class="celda_normal">Reflexión sobre la lengua</td><td class="celda_normal">Reflexión semántica</td><td class="celda_normal">Interpretar el lenguaje figurado en un poema vanguardista.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque la imagen representa un elemento anímico real del sujeto lírico mediante un elemento imaginario: el dolor como una garza triste. Para ubicar la respuesta correcta es fundamental que el alumno sepa que el lenguaje poético es metafórico, no literal.</strong><br/>A. La respuesta es incorrecta porque se hace una interpretación literal de la metáfora: una garza triste.<br/>B. La respuesta es incorrecta porque  hace una interpretación errónea de la metáfora: la garza no es imagen de los suspiros del poeta.<br/>D. La respuesta es incorrecta porque hace una interpretación errónea de la metáfora: la garza no es la terquedad humana.</td></tr>
<tr><td class="celda_normal">42</td><td class="celda_normal">Fuentes de información </td><td class="celda_normal">Conocimiento </td><td class="celda_normal">Identificar el movimiento vanguardista al que pertenece un poema.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se nombra al movimiento poético en el que se inscribe el poema. Para ello, el alumno debe conocer que el Modernismo retoma las formas clásicas, como el soneto, por ejemplo, y que hace un uso extenso de los recursos retóricos. </strong><br/>A. La respuesta es incorrecta porque el poema no se ajusta a los parámetros estilísticos de la poesía ultraísta, la cual intenta romper con la tradición métrica y retórica antecedente.<br/>B. La respuesta es incorrecta porque el poema no se ajusta a los parámetros formales y conceptuales de la poesía realista donde hay una tendencia a la reproducción de los hechos o las cosas tal y como son.<br/>D. La respuesta es incorrecta porque el poema no se ajusta a los parámetros estilísticos ni temáticos de la poesía creacionista donde el poeta se ubica como un Dios y no como un hombre.</td></tr>
<tr><td class="celda_normal">44</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Identificar el propósito comunicativo del autor en un artículo de opinión.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se expresa el propósito comunicativo del autor: hacer reflexionar al lector sobre el trabajo periodístico en la actualidad. Para ello, el alumno debe reconocer que el texto presentado es un articulo de opinión y recuperar el propósito comunicativo característico de este tipo de textos. </strong><br/>A. La respuesta es incorrecta porque el propósito comunicativo de un artículo de opinión no es informar sino plantear la visión del autor sobre un tema particular. <br/>B. La respuesta es incorrecta porque el propósito comunicativo de un artículo de opinión no es convencer al lector sino plantear la visión del autor sobre un tema particular.<br/>D. La respuesta es incorrecta porque el propósito comunicativo de un artículo de opinión no pretende cuestionar a los lectores acerca de sus opiniones. </td></tr>
<tr><td class="celda_normal">45</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Identificar el punto de vista expresado por el autor en el artículo de opinión.</td><td class="celda_normal"><strong>D. La respuesta es correcta porque en ella se presenta el punto de vista de quien escribe el texto. Para ello, el alumno debe identificar el sentido global del texto, distinguir entre hechos y opiniones e identificar la postura de la autora respecto al tema planteado en el artículo. </strong><br/>A. La respuesta es incorrecta porque en ella se presenta un punto de vista contrario al que la autora presenta en su artículo. Irene Lozano cuestiona el aspecto mercantil del periodismo actual más no sugiere una mentalidad empresarial.<br/>B. La respuesta es incorrecta porque responde a cuestionamientos que generalizan el artículo y que no centran la discusión en el tema que la autora propone: reflexionar sobre el actual periodismo.<br/>C. La respuesta es incorrecta porque en ella se presenta, a manera de cita, el punto de vista de Pulitzer más no el de Irene Lozano.</td></tr>
<tr><td class="celda_normal">46</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Identificar el argumento de mayor  peso que utiliza el autor  para apoyar su punto de vista.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se presenta el argumento central alrededor del cual la autora estructura el artículo (la prensa corrupta produce baja calidad de información). Para ello, el alumno debe comprender el tema central del artículo, reconocer el punto de vista de la autora e identificar los diferentes argumentos que se presentan el texto para, después, elegir aquel que sea el más relevante.  </strong><br/>A. La respuesta es incorrecta porque en ella se presenta una idea que no forma parte del tema central del artículo ni de los argumentos utilizados por la autora.<br/>C. La respuesta es incorrecta porque en ella se presenta un punto de vista respecto a los medios masivos de comunicación y que no forma parte de los argumentos utilizados por la autora.<br/>D. La respuesta es incorrecta porque en ella se presenta información explicita contenida en el articulo, pero que no atañe al tema central del artículo.</td></tr>
<tr><td class="celda_normal">47</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una interpretación </td><td class="celda_normal">Reconocer la conclusión que deriva el autor después de su planteamiento</td><td class="celda_normal"><strong>D. La respuesta es correcta porque contiene la conclusión a la llega la autora y la cual engloba la información del artículo (el periodismo no puede centrarse en las ganancias económicas). Para ello, el alumno debe leer el texto, comprender los argumentos dados por la autora e interpretarlos en una conclusión.</strong><br/>A. La respuesta es incorrecta porque presenta una conclusión que no toma en cuenta el planteamiento real de la autora.<br/>B. La respuesta es incorrecta porque presenta una idea particular del texto y no resume el tema global del articulo.<br/>C. La respuesta es incorrecta porque presenta una opinión que no coincide con el tema expuesto en el texto.</td></tr>
<tr><td class="celda_normal">48</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Reconocer características de personajes míticos.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque en ella se presentan las características de los personajes que aparecen en los dos textos. Para ello, el alumno debe comprender globalmente las historias y reconocer sus semejanzas y diferencias, incluyendo las de sus personajes. </strong><br/>A. La respuesta es incorrecta porque contiene una característica que corresponde únicamente al dios del mito 1: el cuerpo de animal.<br/>C. La respuesta es incorrecta porque en ella se presentan características que no se les puede atribuir a ninguno de los dos dioses: volubles y sensibles.<br/>D. La respuesta es incorrecta porque se contiene una característica que no se ve reflejada en ninguno de los personajes: capaces de alegrase o aburrirse.</td></tr>
<tr><td class="celda_normal">49</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Desarrollo de una comprensión global</td><td class="celda_normal">Reconocer los hechos recurrentes en dos mitos con el mismo tema.</td><td class="celda_normal"><strong>A. La respuesta es correcta porque en ella se presentan los dos hechos que ocurren en ambos mitos: la creación del mundo y la de los seres vivos. Para ello, el alumno debe comprender globalmente ambos textos e identificar sus coincidencias narrativas.</strong><br/>B. La respuesta es incorrecta porque la semejanza entre los mitos no se encuentra en las características fisonómicas de los dioses, ya que estos son diferentes en cada mito.<br/>C. La respuesta es incorrecta porque en ninguno de los dos mitos se describe las características geológicas del mundo antes de su creación.<br/>D. La respuesta es incorrecta porque ésta hace referencia al tiempo narrativo y no a las semejanzas temáticas de ambos mitos.</td></tr>
<tr><td class="celda_normal">50</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Evaluación crítica del texto</td><td class="celda_normal">Identificar los valores que representan dos mitos con el mismo tema.</td><td class="celda_normal"><strong>C. La respuesta es correcta porque en ella se presenta el valor que se menciona al final del primer texto. Para ello, el alumno debe comprender el texto, identificar el dato solicitado y elegir, de entre varias opciones de respuesta, el valor que represente mejor el suceso solicitado en la consigna.</strong><br/>A. La respuesta es incorrecta porque refiere a un valor distinto al mencionado en el primer texto.<br/>B. La respuesta es incorrecta porque, aunque la opción refiere al valor social mencionado al final del primer texto, la razón es errónea.  <br/>D. La respuesta es incorrecta porque refiere a un valor distinto al mencionado en el primer texto.</td></tr>
<tr><td class="celda_normal">51</td><td class="celda_normal">Comprensión lectora</td><td class="celda_normal">Análisis del contenido y de la estructura</td><td class="celda_normal">Identificar las formas de tratar un mismo tema en dos relatos míticos de culturas diferentes.</td><td class="celda_normal"><strong>B. La respuesta es correcta porque contiene los aspectos narrativos ordenados tal y como ocurren en cada mito. Para ello, el alumno debe identificar la secuencia de los hechos presentados en cada uno de los textos.</strong><br/>A. La respuesta es incorrecta porque no respeta el orden de los acontecimientos ocurridos en ambos mitos.<br/>C. La respuesta es incorrecta porque no respeta el orden de los acontecimientos ocurridos en ambos mitos.<br/>D. La respuesta es incorrecta porque altera los acontecimientos ocurridos en ambos mitos.</td></tr>				
				</body>
			</table>
		</div>

		<div class="saltopagina"></div>
		<div class="container">
			<table class="max-width">
				<thead>
				<tr><td colspan="5" style="border:hidden"><img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right"></td></tr>
				<tr><td colspan="5" style="text-align:center" class="td_green">D. ARGUMENTACIONES DE LOS REACTIVOS DE MATEMÁTICAS</td></tr>
				<tr><th class="td_gold" style="width:7%">Reactivo</th><th class="td_gold" style="width:10%">Eje temático</th><th class="td_gold" style="width:10%">Unidad de Evaluación</th><th class="td_gold" style="width:10%">Descriptor</th><th class="td_gold" style="width:63%">Argumentación</th></tr>
				</thead>
				<body>
				<tr><td class="celda_normal">1</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Identificar la figura geométrica que sirve como modelo para recubrir el plano.  </td><td class="celda_normal"><strong>D.  Es la respuesta correcta porque el estudiante identifica el polígono que permite hacer el recubrimiento del plano que se muestra sin dejar espacios.</strong><br/>A., B. y C. La respuesta es incorrecta porque el estudiante identifica polígonos que permiten hacer el recubrimiento del plano pero que no coinciden con el que se muestra.</td></tr>
<tr><td class="celda_normal">2</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Medida</td><td class="celda_normal">Resolver problemas que impliquen calcular el perímetro o área del círculo o alguno de sus elementos (radio o diámetro.)</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante resuelve el problema considerando la fórmula del área del círculo.</strong><br/>A. La respuesta es incorrecta porque el estudiante usa la fórmula del perímetro de una circunferencia y ubica el punto decimal de manera errónea.   <br/>C. La respuesta es incorrecta porque el estudiante considera la fórmula del perímetro de una circunferencia.<br/>D. La respuesta es incorrecta porque el estudiante aplica de manera parcial la fórmula para obtener el  área del círculo porque no involucra el radio al cuadrado. </td></tr>
<tr><td class="celda_normal">3</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Resolver problemas empleando las propiedades de la mediatriz de un segmento y la bisectriz de un ángulo.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante reconoce que los trazos generados por intersección de las mediatrices, circuncentro, es el punto que se ubica a la misma distancia de las tres tiendas.</strong><br/>A. B.  La respuesta es incorrecta porque el estudiante combina propiedades de la mediatriz de un segmento y bisectriz de un ángulo.<br/>C. La respuesta es incorrecta porque el estudiante aplica las propiedades de la bisectriz en vez de la mediatriz de un triangulo.</td></tr>
<tr><td class="celda_normal">4</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Resolver problemas que involucren la semejanza de triángulos.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante aplica correctamente la proporcionalidad y obtiene la razón de semejanza (2), con el cuál se calcula la medida del lado faltante que es 10 m.</strong><br/>B. La respuesta es incorrecta porque el estudiante establece una correctamente la  proporcionalidad pero considera inadecuadamente uno de los elementos geométricos propuestos. La base del triangulo mayor  5.8 m (5 m + 0.8)<br/>C. La respuesta es incorrecta porque el alumno establece correctamente la proporcionalidad pero considera inadecuadamente uno de los elementos geométricos de la figuras. La base del triangulo mayor  6.6 m (5 m + 1.6)<br/>D. La respuesta es incorrecta porque el estudiante establece correctamente la proporcionalidad pero considera inadecuadamente uno de los elementos geométricos de la figura. La altura del triangulo menor  2.4 m (1.6 m + 0.8)</td></tr>
<tr><td class="celda_normal">5</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Identificar las posibilidades de construcción (existencia y unicidad) de triángulos.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante aplica correctamente la propiedad de existencia de un triángulo. (25 m - 10m) < distancia entre el taller y la toma principal de agua < (25 m + 10 m)</strong><br/>A. La respuesta es incorrecta porque el estudiante interpreta y usa inadecuadamente la propiedad de existencia de un triángulo. 10 m < distancia entre el taller y la toma principal de agua < (25 m -10m)<br/>C. La respuesta es incorrecta porque el estudiante aplica erróneamente la propiedad de existencia de un triángulo. Usa  una formula que es incorrecta.  La distancia faltante es mayor a la suma de las longitudes dadas (25 m+ 10 m)<br/>D. La respuesta es incorrecta porque el estudiante aplica erróneamente la propiedad de existencia de un triángulo. Toma uno de los datos proporcionados para generar una respuesta. </td></tr>
<tr><td class="celda_normal">7</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Medida</td><td class="celda_normal">Identificar las secciones que se obtienen al cortar un cilindro o un cono recto con un plano.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante identifica el polígono que se forma (triangulo isósceles) al cortar un cono por un plano perpendicular a la base del cono.</strong><br/>A. La respuesta es incorrecta porque el estudiante identifica la forma geométrica (elipse) al cortar un cono por un plano paralelo a la base del cono.<br/>C. La respuesta es incorrecta porque el estudiante identifica la forma geométrica (parábola) al cortar el cono por un plano oblicuo a la base del cono.<br/>D. La respuesta es incorrecta porque el estudiante identifica la forma geométrica (triángulo rectángulo) al cortar parcialmente un cono por un plano perpendicular a la base del cono.</td></tr>
<tr><td class="celda_normal">8</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Identificar las posibilidades de construcción (existencia y unicidad) de triángulos.</td><td class="celda_normal"><strong>A. Es la respuesta correcta  porque el estudiante aplica correctamente la propiedad de existencia de un triángulo. (7 m - 3 m)< medida del tercer lado <(7m + 3m)</strong><br/>B. La respuesta es incorrecta porque el estudiante interpreta y usa inadecuadamente la propiedad de existencia de un triángulo. 3 m< medida del tercer lado <(7m - 3m)<br/>C. La respuesta es incorrecta porque el estudiante aplica erróneamente la propiedad de existencia de un triángulo. La distancia faltante es mayor a la suma de las longitudes dadas (7 m +3 m)<br/>D. La respuesta es incorrecta porque el estudiante aplica erróneamente la propiedad de existencia de un triángulo. Toma uno de los datos proporcionados para generar una respuesta. </td></tr>
<tr><td class="celda_normal">9</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Nociones de probabilidad</td><td class="celda_normal">Calcular la probabilidad teórica de un evento simple.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante identifica la fracción que representa la probabilidad de un evento que no es mutuamente excluyente. Corresponde al número de casos favorables entre el número de casos posibles.</strong><br/>A. La respuesta es incorrecta porque el estudiante identifica la fracción que representa la probabilidad de uno de los eventos.  La probabilidad de extraer la ficha "O"<br/>B. La respuesta es incorrecta porque el estudiante identifica la fracción que representa la probabilidad de otro de los eventos. La probabilidad de obtener la cara "6" del dado.<br/>D. La respuesta es incorrecta porque el estudiante identifica la fracción que representa la probabilidad de dos eventos mutuamente excluyentes. </td></tr>
<tr><td class="celda_normal">10</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Encontrar el factor inverso en una relación de proporcionalidad.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante establece la relación correcta entre los datos para determinar el factor de proporcionalidad inversa.</strong><br/>A. La respuesta es incorrecta porque el estudiante considera uno de los datos proporcionados en la base del reactivo. Considera que los 2/5 son el factor de proporcionalidad inverso.<br/>C. La respuesta es incorrecta porque el alumno utiliza una estrategia aditiva. Considera que el factor de proporcionalidad inversa es (2/5 + 2).<br/>D. La respuesta es incorrecta porque el estudiante confunde el factor inverso con la razón externa. Obtiene la altura de la figura original y la considera con factor de proporcionalidad inverso.</td></tr>
<tr><td class="celda_normal">11</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Nociones de probabilidad</td><td class="celda_normal">Resolver problemas de conteo.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante aplica la regla del producto sin repetición. 5 × 4 = 20 dígitos de 2 cifras distintas.</strong><br/>A. La respuesta es incorrecta porque el estudiante combina de dos a dos de los elementos del conjunto. Por ejemplo {34, 45, 56, 67,73}<br/>B. Es incorrecta porque el estudiante emplea la regla del producto con repetición. 5 × 5 =25 dígitos de 2 cifras.<br/>C. La respuesta es incorrecta porque el estudiante toma un elemento del conjunto. Por ejemplo, {34, 35, 36, 37}</td></tr>
<tr><td class="celda_normal">12</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Identificar la relación entre la pendiente y la razón de cambio.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante reconoce la gráfica de una función lineal cuya ordenada al origen son los 100 km (Comienzo del trayecto) y la pendiente es 80 km por cada hora (velocidad constante).</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función no lineal que tiene cantidades similares a las descritas, pero es curva debido a que la variación no es constante.<br/>C. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función constante paralela al eje x que comienza en la ordenada al origen de la función original, 100 km que es el comienzo del trayecto y no hay variación.<br/>D. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función lineal en donde se invierten las variables de la función original.</td></tr>
<tr><td class="celda_normal">13</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Identificar las representaciones (gráfica, tabla y expresión algebraica) que correspondan a una misma situación de proporcionalidad directa.</td><td class="celda_normal"><strong>A.  Es la respuesta correcta porque el estudiante identifica la representación algebraica de una relación de proporcionalidad directa dada en una representación gráfica.</strong><br/>B. La respuesta es incorrecta porque el estudiante identifica la representación tabular que no corresponde con la representación gráfica del fenómeno propuesto en el problema.<br/>C. La respuesta es incorrecta porque el estudiante identifica la representación algebraica que corresponde a una relación de proporcionalidad directa con factor inverso del propuesto en el problema.<br/>D. La respuesta es incorrecta porque el estudiante identifica la representación tabular que corresponde a una relación de proporcionalidad directa cuyo factor de proporcionalidad es un múltiplo del factor propuesto en el problema.</td></tr>
<tr><td class="celda_normal">14</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Medida</td><td class="celda_normal">Identificar la cantidad de aumento o disminución de volumen al cambiar alguna de las dimensiones de los cuerpos geométricos</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque que el estudiante identifica la variación del volumen de los cilindros rectos cuando la medida de los radios es la misma y las alturas están en razón 3:1.</strong><br/>B. La respuesta es incorrecta porque el estudiante interpreta la variación del volumen de los cilindros rectos como la diferencia de las alturas.<br/>C. La respuesta es incorrecta porque el estudiante interpreta la variación del volumen de los cilindros rectos como el inverso de la diferencia de las alturas.<br/>D. La respuesta es incorrecta porque el estudiante considera uno de los elementos geométricos que se proporcionan para establecer la variación.</td></tr>
<tr><td class="celda_normal">15</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen divisiones de números fraccionarios.</td><td class="celda_normal"><strong>D. Es la respuesta correcta  porque el estudiante establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la división de fracciones. Además, interpreta el resultado para determinar el número de bolsas llenas.</strong><br/>A. La respuesta es incorrecta porque el estudiante no establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la multiplicación de fracciones.<br/>B. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema y utiliza el algoritmo de la suma de fracciones.<br/>C. La respuesta es incorrecta porque el estudiante establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la división de fracciones, pero sólo considera la parte entera de la fracción mixta.</td></tr>
<tr><td class="celda_normal">16</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen multiplicaciones de números fraccionarios</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la multiplicación de fracciones.                                                       </strong><br/>B. La respuesta es incorrecta porque el estudiante no establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la división de fracciones.<br/>C. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema y utiliza el algoritmo de la suma de fracciones.<br/>D. La respuesta es incorrecta porque el estudiante establece la relación correcta entre los datos del problema y utiliza el algoritmo de la multiplicación de fracciones pero tiene un error de cálculo de  "olvido de la llevada" en el producto de los numeradores.</td></tr>
<tr><td class="celda_normal">17</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos con números fraccionarios con distinto denominador.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante establece adecuadamente la relación entre los datos del problema y aplica la suma y resta de fracciones.</strong><br/>A. La respuesta es incorrecta porque el estudiante establece la relación correcta entre los datos del problema, pero aplica la suma y resta de fracciones como la suma de naturales. Opera numerador con numerador y denominador con denominador por separado.<br/>B. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema, sólo suma las fracciones dadas, pero no resta a la unidad el resultado de la suma.<br/>C. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema, sólo suma las fracciones dadas como números naturales.</td></tr>
<tr><td class="celda_normal">18</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Resolver ecuaciones de primer grado de la forma: ax + bx + c = dx + ex +f y con paréntesis en uno o en ambos miembros de la ecuación, utilizando coeficientes enteros o fraccionarios.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante reconoce el procedimiento que resuelve la ecuación utilizando la simplificación de expresiones algebraicas y las propiedades de la igualdad.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación pero emplea una inadecuada simplificación de las expresiones algebraicas.                               <br/>B. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que presenta errores en las propiedades de la igualdad y de leyes de los signos en la suma.<br/>D. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que incluye errores relacionados con las propiedades de la igualdad.</td></tr>
<tr><td class="celda_normal">19</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen divisiones de números fraccionarios.</td><td class="celda_normal"><strong>C. Es la respuesta correcta  porque el estudiante establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la división de fracciones. Además, interpreta el resultado para determinar el número de vasos necesarios.</strong><br/>A. La respuesta es incorrecta porque el estudiante establece adecuadamente la relación entre los datos del problema y pero multiplica los numeradores por el denominador entre el denominador.<br/>B. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema y utiliza el algoritmo de la suma de fracciones.<br/>D. La respuesta es incorrecta porque el estudiante no establece adecuadamente la relación entre los datos del problema y utiliza el algoritmo de la multiplicación de fracciones.</td></tr>
<tr><td class="celda_normal">20</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Resolver ecuaciones de primer grado de la forma: ax + bx + c = dx + ex +f y con paréntesis en uno o en ambos miembros de la ecuación, utilizando coeficientes enteros o fraccionarios.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante toma la información icónica representada en la figura de la balanza y la transforma en lenguaje algebraico para resolver una ecuación utilizando la simplificación de expresiones algebraicas y las propiedades de la igualdad.</strong><br/>B. La respuesta es incorrecta porque el estudiante plantea una ecuación a partir de la información que se presenta en uno de los extremos de la balanza.                <br/>C. La respuesta es incorrecta porque el estudiante considera que el valor de la incógnita esta determinada por su coeficiente y plantea una expresión basada en la información de uno de los extremos de la balanza.                   <br/>D. La respuesta es incorrecta porque el estudiante plantea la ecuación correctamente pero comete un error relacionados con las propiedades de la igualdad.    </td></tr>
<tr><td class="celda_normal">21</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen multiplicación de números decimales.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante aplica el algoritmo de la multiplicación de números decimales y coloca el punto decimal donde corresponde.</strong><br/>A. La respuesta es incorrecta porque el estudiante aplica el algoritmo de la multiplicación de números decimales y coloca el punto decimal donde no corresponde.<br/>C. La respuesta es incorrecta porque el estudiante obtiene el producto omitiendo una cifra, la que corresponde al cero de la parte decimal del multiplicador y coloca el punto decimal donde no corresponde.<br/>D. La respuesta es incorrecta porque el estudiante obtiene el producto omitiendo una cifra, la que corresponde al cero de la parte decimal del multiplicador y coloca el punto decimal donde corresponde.</td></tr>
<tr><td class="celda_normal">23</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Traducir al lenguaje natural el significado de fórmulas geométricas o viceversa.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante identifica la correspondencia entre la representación algebraica y el enunciado en lenguaje natural.</strong><br/>B. La respuesta es incorrecta porque el estudiante traduce de manera incorrecta una de las operaciones involucradas en la representación algebraica. Confunde la división con el producto.<br/>C. La respuesta es incorrecta porque el estudiante traduce de manera incorrecta una de las operaciones involucradas en la representación algebraica. Confunde el producto con la suma.<br/>D.  La respuesta es incorrecta porque el estudiante traduce de manera incorrecta la expresión algebraica considerando el producto como la aplicación de la propiedad distributiva.</td></tr>
<tr><td class="celda_normal">24</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Números y Sistemas de numeración</td><td class="celda_normal">Ubicar en la recta numérica números decimales dados dos puntos cualesquiera.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante utiliza las propiedades de densidad y orden de los números racionales para comparar y ubicar en la recta numérica los números decimales.</strong><br/>A. La respuesta es incorrecta porque el estudiante mantiene el orden de los números decimales pero su ubicación de algunos en la recta numérica es incorrecta.<br/>B. La respuesta es incorrecta porque el estudiante no coloca en orden algunos de los números decimales en la recta numérica.<br/>C. La respuesta es incorrecta porque el estudiante mantiene el orden de los números decimales pero la  ubicación de algunos en la recta numérica es incorrecta.</td></tr>
<tr><td class="celda_normal">25</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos con números decimales.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante establece adecuadamente la relación entre los datos del problema y aplica los algoritmos de la suma y resta de decimales.</strong><br/>A. La respuesta es incorrecta porque el estudiante no establece la relación adecuada entre los datos del problema.                                  <br/>B. La respuesta es incorrecta porque el estudiante establece una relación correcta entre los datos del problema pero comete un error de olvido de la llevada en la suma.<br/>D. La respuesta es incorrecta porque el estudiante establece relaciones adecuadas entre los datos del problema pero omite en uno de los números decimales una cifra y no respeta su valor posicional; además resta a la cifra mayor la menor sin importar si esta en el minuendo o en el sustraendo.</td></tr>
<tr><td class="celda_normal">27</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Calcular la suma de los ángulos interiores de cualquier polígono. </td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante determina el número de lados que tiene el polígono aplicando la fórmula  Suma de ángulos = 180° (n - 2).     </strong><br/>A. La respuesta es incorrecta porque el estudiante aplica incorrectamente otra fórmula. Suma de ángulos=  180°(n)<br/>B. La respuesta es incorrecta porque el estudiante aplica incorrectamente la formula Suma de ángulos = 180° (n - 2), pero el resultado lo disminuye en uno.<br/>D. La respuesta es incorrecta porque el estudiante aplica incorrectamente la formula. Suma de ángulos = 180° (n - 2), pero al resultado lo aumenta en uno.</td></tr>
<tr><td class="celda_normal">29</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Medida</td><td class="celda_normal"> Explicitación y uso del Teorema de Pitágoras.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante establece la relación entre los datos del problema y aplica el teorema de Pitágoras, realizando el despeje correspondiente para obtener el valor de un cateto.</strong><br/>B. La respuesta es incorrecta porque el estudiante establece la relación entre los datos del problema y aplica el teorema de Pitágoras, realizando el despeje correspondiente para obtener el valor de un cateto pero cambia el signo dentro de la raíz.<br/>C. La respuesta incorrecta porque el estudiante establece la relación entre los datos del problema y aplica el teorema de Pitágoras, realizando el despeje parcial correspondiente para obtener el valor de un cateto omitiendo la raíz cuadrada.<br/>D. La respuesta es incorrecta porque el estudiante establece la relación entre los datos del problema y aplica el teorema de Pitágoras, realizando el despeje parcial correspondiente para obtener el valor de un cateto pero cambia el signo y omite la raíz.</td></tr>
<tr><td class="celda_normal">30</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Resolver problemas que impliquen el uso de relaciones de los ángulos que se forman entre dos rectas paralelas cortadas por una transversal.</td><td class="celda_normal"><strong>B. Es la respuesta es correcta porque el estudiante reconoce la medida del ángulo formado por rectas paralelas y una transversal.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce la medida del ángulo complementario al ángulo dado.<br/>C. La respuesta es incorrecta porque el estudiante reconoce la medida del ángulo, calculando el ángulo adyacente al indicado y tomando como referencia valores de ángulos conocidos ( 120° y 60°).<br/>D. La respuesta es incorrecta porque el estudiante reconoce la medida del ángulo suplementario del ángulo dado.</td></tr>
<tr><td class="celda_normal">31</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Identificar el desarrollo plano de conos y cilindros rectos.</td><td class="celda_normal"><strong>B. Es la respuesta es correcta porque el estudiante reconoce el desarrollo plano que corresponde en dimensiones (altura y diámetro) al cilindro que cumplen con las condiciones adecuadas para generarlo.</strong><br/>A., C. y D. La respuesta es incorrecta porque el estudiante reconoce el desarrollo plano de un cilindro que no corresponde en dimensiones (altura y diámetro) al cilindro que se muestra.</td></tr>
<tr><td class="celda_normal">32</td><td class="celda_normal">Forma, Espacio y Medida</td><td class="celda_normal">Figuras y Cuerpos</td><td class="celda_normal">Identificar figuras simétricas respecto a un eje oblicuo.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante reconoce la figura geométrica que cumple con las propiedades de simetría respecto al eje dado.</strong><br/>B. La respuesta es incorrecta porque el estudiante reconoce la figura geométrica que representa al simétrico de la figura original pero tiene un desplazamiento vertical.<br/>C. La respuesta es incorrecta porque el estudiante reconoce la figura geométrica que representa una traslación horizontal.<br/>D. La respuesta es incorrecta porque el estudiante reconoce la figura geométrica que representa al simétrico de la figura original pero tiene un desplazamiento vertical y horizontal. </td></tr>
<tr><td class="celda_normal">33</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal"> Resolver problemas de proporcionalidad directa en los que se apliquen sucesivamente dos factores constantes de proporcionalidad.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante establece la relación entre los datos del problema,  aplica la sucesión de factores para obtener la medida final.</strong><br/>A. La respuesta es incorrecta porque el estudiante establece una relación inadecuada de los datos, pues suma los factores con la medida de partida.<br/>B. La respuesta es incorrecta porque el estudiante establece la relación parcial entre los datos del problema,  aplica uno de los factores para obtener la medida final.<br/>C. La respuesta es incorrecta porque el estudiante establece una relación inadecuada de los datos, pues suma los dos factores y el resultado se multiplica por la medida de partida.</td></tr>
<tr><td class="celda_normal">34</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Análisis y representación de datos</td><td class="celda_normal">Resolver problemas que impliquen la interpretación de información representada en gráficas de barras o circulares.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante relaciona los datos del problema para calcular el porcentaje de las partes que integran cada categoría de la gráfica presentada.</strong><br/>A. La respuesta es incorrecta porque el estudiante calcula los porcentajes de los porcentajes presentados en la gráfica.<br/>B. La respuesta es incorrecta porque el estudiante considera los porcentajes dados en la base del reactivo.<br/>D. La respuesta es incorrecta porque el estudiante considera las frecuencias absolutas de las categorías.</td></tr>
<tr><td class="celda_normal">35</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Identificar la relación de un fenómeno con su representación gráfica formada por segmentos de recta y curvas. </td><td class="celda_normal"><strong>B.  Es la respuesta correcta porque el estudiante identifica la gráfica que representa la variación de del fenómeno que se muestra en la base del reactivo.</strong><br/>A. La respuesta es incorrecta porque el estudiante identifica la gráfica que presenta intervalos de variaciones cambiadas con respecto al fenómeno que se muestra en la base del reactivo.<br/>C. La respuesta es incorrecta porque el estudiante identifica la gráfica que es simétrica a la respuesta correcta.<br/>D. La respuesta es incorrecta porque el estudiante identifica la gráfica que describe el comportamiento del fenómeno, pero se representan de manera discontinua.</td></tr>
<tr><td class="celda_normal">36</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Resolver problemas que impliquen una relación inversamente proporcional entre dos conjuntos de cantidades.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante reconoce la representación tabular de una situación de proporcionalidad inversa.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce la representación tabular de una situación de proporcionalidad directa.<br/>B. La respuesta es incorrecta porque el estudiante reconoce la representación tabular de una situación decreciente que emplea una estrategia aditiva.<br/>C. La respuesta es incorrecta porque el estudiante reconoce la representación tabular de una situación creciente que emplea una estrategia aditiva.</td></tr>
<tr><td class="celda_normal">37</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Análisis y representación de datos</td><td class="celda_normal">Resolver problemas que impliquen la interpretación de información representada en gráficas de barras o circulares.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante reconoce la representación tabular que corresponde a la gráfica dada.</strong><br/>B. La respuesta es incorrecta porque el estudiante interpreta el porcentaje de la gráfica como la frecuencia absoluta.<br/>C. La respuesta es incorrecta porque el estudiante hace una lectura incorrecta de los porcentajes presentados en la gráfica de barras.<br/>D. La respuesta es incorrecta porque el estudiante hace una lectura incorrecta de los porcentajes presentados en la gráfica y los interpreta como la frecuencia absoluta.</td></tr>
<tr><td class="celda_normal">38</td><td class="celda_normal">Manejo de la información</td><td class="celda_normal">Proporcionalidad y funciones</td><td class="celda_normal">Identificar la relación entre la pendiente y la razón de cambio.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante reconoce la gráfica de una función lineal cuya ordenada al origen son los 110 kg y la pendiente es 2 kg.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función no lineal que tiene cantidades similares a las descritas, pero es curva debido a que la variación no es constante.<br/>C. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función constante paralela al eje x que comienza en la ordenada al origen de la función original, 110 kg y no hay variación.<br/>D. La respuesta es incorrecta porque el estudiante reconoce la gráfica de una función lineal en donde se invierten las variables de la función original.</td></tr>
<tr><td class="celda_normal">39</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Resolver ecuaciones de primer grado de la forma: ax + bx + c = dx + ex +f y con paréntesis en uno o en ambos miembros de la ecuación, utilizando coeficientes enteros o fraccionarios.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante reconoce el procedimiento que resuelve la ecuación utilizando la simplificación de expresiones algebraicas y las propiedades de la igualdad.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación pero tiene errores en las leyes de los signos.                               <br/>B. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que presenta errores en la propiedad distributiva.<br/>C. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que incluye errores relacionados con las propiedades de la igualdad.</td></tr>
<tr><td class="celda_normal">40</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos con números fraccionarios con distinto denominador.</td><td class="celda_normal"><strong>C. Es la respuesta correcta  porque el estudiante establece la relación adecuada entre los datos del problema y utiliza el algoritmo de resta de fracciones.</strong><br/>A. La respuesta es incorrecta porque el estudiante establece la relación adecuada entre los datos del problema pero resta las fracciones como números naturales. Resta numeradores con numeradores y denominadores con denominadores.<br/>B. La respuesta es incorrecta porque el estudiante no establece la relación adecuada entre los datos del problema, aplica el algoritmo de la división.<br/>D. La respuesta es incorrecta porque el estudiante establece la relación adecuada entre los datos del problema y realiza una invención del algoritmo para la resta.</td></tr>
<tr><td class="celda_normal">41</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos con números fraccionarios y decimales.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante suma los números fraccionarios y decimales, haciendo las transformaciones correspondientes (de decimales a fracciones o de fracciones a decimales).</strong><br/>A. La respuesta es incorrecta porque el estudiante suma los números fraccionarios y decimales pero realiza de forma equivocada la trasformación de uno de los números fraccionarios a decimales colocándole un cero de más.<br/>B. La respuesta es incorrecta porque el estudiante suma los números fraccionarios y decimales pero realiza de forma equivocada la trasformación de uno de los números fraccionarios a decimales colocándole un cero de más.<br/>C. La respuesta es incorrecta porque el estudiante suma los números fraccionarios y decimales, pero realiza de forma equivocada la transformación del número decimal a una fracción decimal.</td></tr>
<tr><td class="celda_normal">42</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos con números fraccionarios con distinto denominador.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante establece adecuadamente la relación entre los datos del problema y aplica la suma y resta de fracciones.</strong><br/>A. La respuesta es incorrecta porque el estudiante establece la relación correcta entre los datos del problema, pero aplica la suma y resta de fracciones como la suma de naturales. Opera numerador con numerador y denominador con denominador por separado.<br/>B. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema, multiplica las fracciones dadas.<br/>D. La respuesta es incorrecta porque el estudiante no establece la relación correcta entre los datos del problema, sólo suma las fracciones dadas como números naturales.</td></tr>
<tr><td class="celda_normal">43</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Resolver problemas que impliquen el uso de un sistema de dos ecuaciones lineales con dos incógnitas.</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante plantea el sistema de ecuaciones que permiten resolver el problema.</strong><br/>B. C. D.  La respuesta es incorrecta porque el estudiante plantea el sistema de ecuaciones que permiten resolver el problema pero se comete errores en el procedimiento relacionados con las leyes de los signos, propiedades de la igualdad, propiedad distributiva, o bien, de calculo (suma, resta, multiplicación o división). </td></tr>
<tr><td class="celda_normal">44</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos que impliquen el uso de números enteros.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante establece la relación adecuada entre los datos del problema y suma y resta las cantidades considerando el contexto del problema. A. La respuesta es incorrecta porque el estudiante considera uno de los datos con el signo inadecuado.</strong><br/>B. La respuesta es incorrecta porque el estudiante no realiza una correcta interpretación de los datos basado en el contexto del problema. Se obtiene de la suma de todos los datos.<br/>D. La respuesta es incorrecta porque el estudiante suma y resta  todos los datos pero sin considerar la interpretación del resultado de acuerdo al contexto del problema.</td></tr>
<tr><td class="celda_normal">45</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen multiplicaciones de números fraccionarios</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante identifica las fracciones cuyo producto da el resultado de la base.</strong><br/>B. La respuesta es incorrecta porque el estudiante utiliza el algoritmo de la división de fracciones para obtener el resultado de la base.<br/>C. La respuesta es incorrecta porque el estudiante utiliza el algoritmo de la división, pero invierte el numerador y denominador.<br/>D. La respuesta es incorrecta porque el estudiante hace una invención de algoritmo, multiplica numerador y denominador de la misma fracción. </td></tr>
<tr><td class="celda_normal">46</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Números y Sistemas de numeración</td><td class="celda_normal">Ubicar en la recta numérica números decimales dados dos puntos cualesquiera.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante utiliza las propiedades de densidad y orden de los números racionales para comparar y ubicar en la recta numérica los números decimales.</strong><br/>A. La respuesta es incorrecta porque el estudiante mantiene el orden de los números decimales pero su ubicación de algunos en la recta numérica es incorrecta.<br/>C. La respuesta es incorrecta porque el estudiante no coloca en orden algunos de los números decimales en la recta numérica.<br/>D. La respuesta es incorrecta porque el estudiante ordena los números de acuerdo con el valor de la cifra correspondiente a los centésimos sin tomar en cuenta los décimos.  </td></tr>
<tr><td class="celda_normal">47</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Patrones y ecuaciones</td><td class="celda_normal">Resolver ecuaciones de primer grado de la forma: ax + bx + c = dx + ex +f y con paréntesis en uno o en ambos miembros de la ecuación, utilizando coeficientes enteros o fraccionarios.</td><td class="celda_normal"><strong>B. Es la respuesta correcta porque el estudiante reconoce el procedimiento que resuelve la ecuación utilizando la simplificación de expresiones algebraicas y las propiedades de la igualdad.</strong><br/>A. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que presenta errores en la propiedad distributiva.                             <br/>C. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación que incluye errores relacionados con las propiedades de la igualdad.<br/>D. La respuesta es incorrecta porque el estudiante reconoce el procedimiento que resuelve la ecuación pero tiene errores en las leyes de los signos.  </td></tr>
<tr><td class="celda_normal">48</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas que impliquen el cálculo de la raíz cuadrada</td><td class="celda_normal"><strong>A. Es la respuesta correcta porque el estudiante aplica el algoritmo para el cálculo de la raíz cuadrada; identificando la raíz cuadrada entera y el residuo.</strong><br/>B. La respuesta es incorrecta porque el estudiante considera la raíz cuadrada como la división entre 4, además comete errores de cálculo en el procedimiento.<br/>C. La respuesta es incorrecta porque el estudiante considera la raíz cuadrada como la división entre 3, además comete errores de cálculo en el procedimiento.<br/>D. La respuesta es incorrecta porque el estudiante considera la raíz cuadrada como la división entre 2.</td></tr>
<tr><td class="celda_normal">49</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas multiplicativos</td><td class="celda_normal">Resolver problemas multiplicativos con números enteros.</td><td class="celda_normal"><strong>D. Es la respuesta correcta porque el estudiante identifica la operación y los factores que dan como  resultado el valor dado en la base.</strong><br/>A.  La respuesta es incorrecta porque el estudiante identifica la operación pero los factores no dan como resultado el valor dado en la base.<br/>B.  La respuesta es incorrecta porque el estudiante no identifica ni la operación ni los factores que dan como resultado el valor dado en la base.<br/>C.  La respuesta es incorrecta porque el estudiante identifica los factores que dan como resultado el valor dado en la base pero no la operación.</td></tr>
<tr><td class="celda_normal">50</td><td class="celda_normal">Sentido numérico y pensamiento algebraico.</td><td class="celda_normal">Problemas aditivos</td><td class="celda_normal">Resolver problemas aditivos que impliquen el uso de números enteros.</td><td class="celda_normal"><strong>C. Es la respuesta correcta porque el estudiante establece la relación adecuada entre los datos del problema y suma y resta las cantidades considerando el contexto del problema.</strong><br/>A. La respuesta es incorrecta porque el estudiante considera uno de los datos con el signo inadecuado y comete un error en la suma de "olvido de la llevada".<br/>B. La respuesta es incorrecta porque el estudiante no realiza una correcta interpretación de los datos basado en el contexto del problema. Invierte los signos.<br/>D. La respuesta es incorrecta porque el estudiante suma y resta  todos los datos pero sin considerar la interpretación del resultado de acuerdo al contexto del problema.</td></tr>
</body>
			</table>
		</div>
	</div>
</div> <!--fin seccion  de argumentaciones -->

</body>
</html>
<?php pg_close($db);?>
