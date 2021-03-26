<?php 
	include("./lib/escuelaslib.php");
	#Ent = 20
	#Mpio = 1041
	#conecta a la base
	$arrv = valida_variable(strtoupper(escapa($_REQUEST['va'])));

	if(is_array($arrv)){
		#si es válida la variable CCT
		$escuela = substr($arrv['vent'],0,10);
		$turno = substr($arrv['vent'],10,1);
		#$extension = substr($arrv['vent'],11);
		$cicloEscolar = 19;

		$numPaginas = 11;

		$arr_renapo = array('AGUASCALIENTES'=>'as',
		'BAJA CALIFORNIA'=>'bc',
		'BAJA CALIFORNIA SUR'=>'bs',
		'CAMPECHE'=>'cc',
		'COAHUILA DE ZARAGOZA'=>'cl',
		'COLIMA'=>'cm',
		'CHIAPAS'=>'cs',
		'CHIHUAHUA'=>'ch',
		'CIUDAD DE MEXICO'=>'df',
		'DURANGO'=>'dg',
		'GUANAJUATO'=>'gt',
		'GUERRERO'=>'gr',
		'HIDALGO'=>'hg',
		'JALISCO'=>'jc',
		'MEXICO'=>'mc',
		'MICHOACAN DE OCAMPO'=>'mn',
		'MORELOS'=>'ms',
		'NAYARIT'=>'nt',
		'NUEVO LEON'=>'nl',
		'OAXACA'=>'oc',
		'PUEBLA'=>'pl',
		'QUERETARO'=>'qt',
		'QUINTANA ROO'=>'qr',
		'SAN LUIS POTOSI'=>'sp',
		'SINALOA'=>'sl',
		'SONORA'=>'sr',
		'TABASCO'=>'tc',
		'TAMAULIPAS'=>'ts',
		'TLAXCALA'=>'tl',
		'VERACRUZ DE IGNACIO DE LA LLAVE'=>'vz',
		'YUCATAN'=>'yn',
		'ZACATECAS'=>'zs');

		#Se obtiene conexion a BD
		$db = dbc();

		#se obtienen datos generales de la escuela, para el encabezado
		#consulta de datos generales
		$qw = <<<EOD
		SELECT ct."cNombreCentroTrabajo", te."cNombreTurnoEscolar", 
		gm."cNombreGradoMarginacionPlanea", ef."cNombreEntidad",
		m."cNombreMunicipio", ze."cClaveZonaEscolar"
		FROM hechos."hechosReporteIneeSecundaria" AS h
		FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."gradosMarginacionPlanea" AS gm ON gm."iPkGradoMarginacionPlanea" = h."iFkGradoMarginacionPlanea"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
		FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio"=h."iFkMunicipio"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"= '$turno' AND h."iFkCicloEscolar"= '$cicloEscolar'
EOD;

	$res = pg_query($db, $qw);
	#Se asignan valores de datos generales de la escuela, para mostrarlas en encabezado del reporte
	$row = pg_fetch_assoc($res);
	$nom_cct = $row['cNombreCentroTrabajo'];
	$cve_cct = $escuela;
	$gdo_marginacion = $row['cNombreGradoMarginacionPlanea'];
	$nom_turno = $row['cNombreTurnoEscolar'];
	#$nom_ext = $row['cNombreExtensionEms'];
	#$iPk_ext = $row['iPkExtensionEms'];
	#$cve_plantel = $row['cClavePlantel'];
	#$nom_subsistema = $row['cNombreSubsistemaEms'];
	$nom_entidad = $row['cNombreEntidad'];
	$nom_mun = $row['cNombreMunicipio'];
	$cve_zona_e = $row['cClaveZonaEscolar'];
	#$iPk_entidad = $row['iPkEntidadFederativa'];
	#$iPk_subs = $row['iPkSubsistemaEms'];
	$ciclo1 = 17;
	$ciclo2 = 19;

#Consulta comparativo ciclos de PLANEA Lenguaje y Comunicacion
$qw_compara_LyC = <<<EOD
	SELECT ce."cCicloEscolar" AS "CicloEscolar", CAST(h."dPorcentAlumnsEscNvlLgrILyC" AS NUMERIC (5,2)) AS "I_Insuficiente", CAST(h."dPorcentAlumnsEscNvlLgrIILyC" AS NUMERIC (5,2)) AS "II_Elemental", CAST(h."dPorcentAlumnsEscNvlLgrIIILyC" AS NUMERIC (5,2)) AS "III_Bueno", CAST(h."dPorcentAlumnsEscNvlLgrIVLyC" AS NUMERIC (5,2)) AS "IV_Excelente"
	FROM hechos."hechosReporteIneeSecundaria" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"= '$turno' AND h."iFkCicloEscolar" IN (17,19);
EOD;
$res_compara_LyC = pg_query($db, $qw_compara_LyC);

#Consulta comparativo ciclos de PLANEA Matemáticas
$qw_compara_mat = <<<EOD
	SELECT  ce."cCicloEscolar" AS "CicloEscolar", CAST(h."dPorcentAlumnsEscNvlLgrIMat" AS NUMERIC (5,2)) AS "I_Insuficiente", CAST(h."dPorcentAlumnsEscNvlLgrIIMat" AS NUMERIC (5,2)) AS "II_Elemental", CAST(h."dPorcentAlumnsEscNvlLgrIIIMat" AS NUMERIC (5,2)) AS "III_Bueno", CAST(h."dPorcentAlumnsEscNvlLgrIVMat" AS NUMERIC (5,2)) AS "IV_Excelente"
	FROM hechos."hechosReporteIneeSecundaria" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN (17,19)
EOD;
$res_compara_mat = pg_query($db, $qw_compara_mat);


#Consulta de Comparativo con las escuelas de la entidad y el mismo subsistema
#---- Limite min y max de lengua y comunicación ----
$qw_limits_lyc = <<<EOD
SELECT  CASE WHEN h."dPuntajePromedioMinimoGpoCompLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioMinimoGpoCompLyC" END AS "dPuntajePromedioMinimoGpoCompLyC" ,
CASE WHEN h."dPuntajePromedioMaximoGpoCompLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioMaximoGpoCompLyC" END AS "dPuntajePromedioMaximoGpoCompLyC" 
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
EOD;
$res_limits_lyc = pg_query($db, $qw_limits_lyc);

#---- Escuela promedio en el mismo subsistema ----
$qw_prom_subs = <<<EOD
SELECT  CASE WHEN h."dPuntajePromedioEscGpoCompLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscGpoCompLyC" END AS "dPuntajePromedioEscGpoCompLyC" ,
		CASE WHEN h."dPuntajePromedioEscGpoCompMat"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscGpoCompMat" END AS "dPuntajePromedioEscGpoCompMat" 
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
EOD;
$res_prom_subs = pg_query($db, $qw_prom_subs);

#---- Mi escuela ----
$qw_escuela_subs = <<<EOD
SELECT  CASE WHEN h."dPuntajePromedioEscLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscLyC" END AS "dPuntajePromedioEscLyC" ,
		CASE WHEN h."dPuntajePromedioEscMat"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEscMat" END AS "dPuntajePromedioEscMat" 
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
EOD;
$res_escuela_subs = pg_query($db, $qw_escuela_subs);

#---- Escuelas en el mismo subsistema, misma entidad y con el mismo grado de marginación ----
$qw_ent_subs = <<<EOD
SELECT  CASE WHEN hh."dPuntajePromedioEscLyC" = -9999 THEN -0.01 ELSE hh."dPuntajePromedioEscLyC" END  AS "dPuntajePromedioEscLyC",
		CASE WHEN hh."dPuntajePromedioEscMat" = -9999 THEN -0.01 ELSE hh."dPuntajePromedioEscMat" END  AS "dPuntajePromedioEscMat"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" = h."iFkAgrupador"
FULL OUTER JOIN hechos."hechosReporteEmsInee" AS hh ON h."iFkAgrupador" = hh."iFkAgrupador"
WHERE hh."iFkCentroTrabajo" <> h."iFkCentroTrabajo"
	AND ct."cClaveCentroTrabajo"='01PBH3291R'
	AND te."iPkTurnoEscolar"='1'
	AND ex."iPkExtensionEms"='399'
	AND h."iFkCicloEscolar"='19'
ORDER BY hh."dPuntajePromedioEscLyC",hh."dPuntajePromedioEscMat";	
EOD;
$res_ent_subs = pg_query($db, $qw_ent_subs);

#---- Promedio de la entidad ----
$qw_ent_prom = <<<EOD
SELECT  CASE WHEN h."dPuntajePromedioEstatalLyC"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEstatalLyC" END AS "dPuntajePromedioEstatalLyC" ,
		CASE WHEN h."dPuntajePromedioEstatalMat"  = -9999 THEN -0.01 ELSE "dPuntajePromedioEstatalMat" END AS "dPuntajePromedioEstatalMat" 
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms".agrupadores AS agru ON agru."iPkAgrupador" =h."iFkAgrupador"
WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19';
EOD;
$res_ent_prom = pg_query($db, $qw_ent_prom);

#---- Textos ----
$qw_ent_texto = <<<EOD
SELECT "iNumeroCuadrante", "cMensajeEje1", "cMensajeEje2"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesPlaneaEms"."subsistemasEms" AS ss ON ss."iPkSubsistemaEms" = h."iFkSubsistemaEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."cuadrantesEscuela" AS cu ON cu."iPkCuadranteEscuela" = h."iFkCuadranteEscuela"
WHERE ct."cClaveCentroTrabajo"='01PBH3291R' AND te."iPkTurnoEscolar"='1' AND ex."iPkExtensionEms"='399' AND h."iFkCicloEscolar"='19'
ORDER BY ct."cClaveCentroTrabajo";
EOD;
$res_ent_texto = pg_query($db, $qw_ent_texto);
$row_ent_texto = pg_fetch_assoc($res_ent_texto);
#--Textos para el cuadrante.
$txt1_2 = $row_ent_texto['cMensajeEje1'];
$txt3_4 = $row_ent_texto['cMensajeEje2'];
$pos1 = strpos($txt1_2, ".");
$txt_1 = trim(substr ($txt1_2 , 0, $pos1+1));
$txt_1 = "<strong>Las líneas de este texto se deben modificar</strong> " . $txt_1;
$txt_2 = trim(substr ($txt1_2 , $pos1+2));
$pos1 = strpos($txt3_4, ".");
$txt_3 = trim(substr ($txt3_4 , 0, $pos1+1));
$txt_4 = trim(substr ($txt3_4 , $pos1+2));
//echo "texto1:".$txt_1;
//echo "texto2:".$txt_2;
//echo "texto3:".$txt_3;
//echo "texto4:".$txt_4;

/**
 * Inicio de Bloque de consultas para
 * Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN Unidad diagnóstica
*/

#---- Aspecto de evaluación: Manejo y construcción de la información ----
//Manejo y construcción de textos
$evaluacion = "Comprensión lectora";
$qw_manejo_txts = <<<EOD
SELECT rlc."cUnidadEvaluacion", CAST(SUM(rlc."dPorcentsAlumnsAcertReactivo")/COUNT(rlc."dPorcentsAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = '$cicloEscolar'
AND rlc."cUnidadEvaluacion" = 'Reflexión sobre la lengua'
GROUP BY rlc."cUnidadEvaluacion";
EOD;
$res_manejo_txts = pg_query($db, $qw_manejo_txts);

#-- Resultados por temas y reactivos - LyC --
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5 = <<<EOD
	SELECT *
	FROM (
		SELECT CONCAT(rlc."cContenidoTematico",' Reactivo #',rlc."cNumeroReactivo"), CAST(rlc."dPorcentsAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje
		FROM hechos."hechosReporteIneeSecundaria" AS h
		FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = $cicloEscolar AND rlc."cUnidadEvaluacion"= 'Reflexión sobre la lengua'
		ORDER BY rlc."cUnidadEvaluacion",Porcentaje ASC
		LIMIT 5) AS nTable
	ORDER BY Porcentaje DESC;
EOD;
$res_react_menor_5 = pg_query($db, $qw_react_menor_5);

#---- Eje temático: Comprensión lectora
$qw_txt_argumentativo = <<<EOD
SELECT rlc."cUnidadEvaluacion", CAST(SUM(rlc."dPorcentsAlumnsAcertReactivo")/COUNT(rlc."dPorcentsAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = '$cicloEscolar'
AND rlc."cUnidadEvaluacion" = 'Comprensión lectora'
GROUP BY rlc."cUnidadEvaluacion";
EOD;

$res_txt_argumentativo = pg_query($db, $qw_txt_argumentativo);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_arg = <<<EOD
SELECT *
FROM (
	SELECT CONCAT(rlc."cContenidoTematico",' Reactivo #',rlc."cNumeroReactivo"), CAST(rlc."dPorcentsAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje
	FROM hechos."hechosReporteIneeSecundaria" AS h
	FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" = $cicloEscolar AND rlc."cUnidadEvaluacion"= 'Comprensión lectora'
	ORDER BY rlc."cUnidadEvaluacion",Porcentaje ASC
	LIMIT 5) AS nTable
ORDER BY Porcentaje DESC;
EOD;
$res_react_menor_5_arg = pg_query($db, $qw_react_menor_5_arg);

#Unidad diagnóstica: Texto expositivo
#---- Aspecto de evaluación: Texto expositivo ----
$qw_txt_expositivo = <<<EOD
SELECT 	rlc."cAspectoEvaluacion", 
		CASE WHEN h."dPorcentajeAspecto3LyC" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto3LyC" END  AS "dPorcentajeAspecto3LyC"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rlc."cAspectoEvaluacion" = 'Texto Expositivo' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto3LyC" >= 0
GROUP BY rlc."cAspectoEvaluacion", h."dPorcentajeAspecto3LyC"
ORDER BY rlc."cAspectoEvaluacion";
EOD;
$res_txt_expositivo = pg_query($db, $qw_txt_expositivo);

#Unidad diagnóstica: Texto expositivo
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_exp = <<<EOD
SELECT *
FROM (SELECT 	CONCAT(rlc."cEspecificacion",' Reactivo #',rlc."iNumeroReactivo"), 
				CASE WHEN rlc."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rlc."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" = '$iPk_ext' AND h."iFkCicloEscolar" = '$cicloEscolar' AND rlc."cAspectoEvaluacion" = 'Texto Expositivo'
AND rlc."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rlc."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;
$res_react_menor_5_exp = pg_query($db, $qw_react_menor_5_exp);

#Unidad diagnóstica: Texto literario
#---- Aspecto de evaluación: Texto literario ----
$qw_txt_literario = <<<EOD
SELECT 	rlc."cAspectoEvaluacion", 
		CASE WHEN h."dPorcentajeAspecto4LyC" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto4LyC" END  AS "dPorcentajeAspecto4LyC"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rlc."cAspectoEvaluacion" = 'Texto Literario' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto4LyC" >= 0
GROUP BY rlc."cAspectoEvaluacion", h."dPorcentajeAspecto4LyC"
ORDER BY rlc."cAspectoEvaluacion";
EOD;
$res_txt_literario = pg_query($db, $qw_txt_literario);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_lit = <<<EOD
SELECT *
FROM (SELECT CONCAT(rlc."cEspecificacion",' Reactivo #',rlc."iNumeroReactivo"), 
			 CASE WHEN rlc."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rlc."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" = '$iPk_ext' AND h."iFkCicloEscolar" = '$cicloEscolar' AND rlc."cAspectoEvaluacion" = 'Texto Literario'
AND rlc."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rlc."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;
$res_react_menor_5_lit = pg_query($db, $qw_react_menor_5_lit);
/**
 * Fin de Bloque de consultas para
 * Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN Unidad diagnóstica
*/


/**
 * Inicio de Bloque de consultas para
 * Porcentaje de aciertos en MATEMÁTICAS Unidad diagnóstica
*/
#Unidad diagnóstica: Cambios y relaciones
$qw_cyr = <<<EOD
SELECT rm."cAspectoEvaluacion",
	   CASE WHEN h."dPorcentajeAspecto1Mat" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto1Mat" END  AS "dPorcentajeAspecto1Mat"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" = '$iPk_ext' AND rm."cAspectoEvaluacion" = 'Cambios y relaciones' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto1Mat" >= 0
GROUP BY rm."cAspectoEvaluacion", h."dPorcentajeAspecto1Mat"
ORDER BY rm."cAspectoEvaluacion";
EOD;
$res_cyr = pg_query($db, $qw_cyr);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_cyr = <<<EOD
SELECT *
FROM (SELECT CONCAT(rm."cEspecificacion",' Reactivo #',rm."iNumeroReactivo"), 
			 CASE WHEN rm."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND rm."cAspectoEvaluacion"= 'Cambios y relaciones'
AND rm."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rm."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;
$res_react_menor_5_cyr = pg_query($db, $qw_react_menor_5_cyr);

#Unidad diagnóstica: Manejo de la información
$qw_mdi = <<<EOD
SELECT rm."cUnidadEvaluacion", CAST(SUM(rm."dporcentAlumnsAcertReactivo")/COUNT(rm."dporcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND rm."cUnidadEvaluacion"= 'Manejo de la Información'
GROUP BY rm."cUnidadEvaluacion";
EOD;
$res_mdi = pg_query($db, $qw_mdi);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_mdi = <<<EOD
SELECT *
FROM (
SELECT CONCAT(rm."cContenidoTematico",' Reactivo #',rm."cNumeroReactivo"), CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND rm."cUnidadEvaluacion"= 'Manejo de la Información'
ORDER BY rm."cUnidadEvaluacion","Porcentaje" ASC
LIMIT 5) AS nTable
ORDER BY "Porcentaje" DESC;
EOD;
$res_react_menor_5_mdi = pg_query($db, $qw_react_menor_5_mdi);

#Unidad diagnóstica: Sentido numérico y pensamiento algebraico
#AQUI
$qw_snpa = <<<EOD
SELECT rm."cUnidadEvaluacion", CAST(SUM(rm."dporcentAlumnsAcertReactivo")/COUNT(rm."dporcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND rm."cUnidadEvaluacion"= 'Sentido numérico y pensamiento algebraico'
GROUP BY rm."cUnidadEvaluacion";
EOD;
$res_snpa = pg_query($db, $qw_snpa);

#--Sentido numérico y pensamiento algebraico
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_snpa = <<<EOD
SELECT *
FROM (
SELECT CONCAT(rm."cContenidoTematico",' Reactivo #',rm."cNumeroReactivo"), CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND rm."cUnidadEvaluacion"= 'Sentido numérico y pensamiento algebraico'
ORDER BY rm."cUnidadEvaluacion","Porcentaje" ASC
LIMIT 5) AS nTable
ORDER BY "Porcentaje" DESC;
EOD;
$res_react_menor_5_snpa = pg_query($db, $qw_react_menor_5_snpa);

#Unidad diagnóstica: Forma, espacio y medida
#---- Aspecto de evaluación: Forma, Espacio y Medida ----
$qw_fem = <<<EOD
SELECT rm."cUnidadEvaluacion", CAST(SUM(rm."dporcentAlumnsAcertReactivo")/COUNT(rm."dporcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"= '$cicloEscolar' 
AND rm."cUnidadEvaluacion"= 'Forma Espacio y Medida'
GROUP BY rm."cUnidadEvaluacion";
EOD;
$res_fem = pg_query($db, $qw_fem);

#--Sentido Forma espacio y medida
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_fem = <<<EOD
SELECT *
FROM (
SELECT CONCAT(rm."cContenidoTematico",' Reactivo #',rm."cNumeroReactivo"), CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS "Porcentaje"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19 
AND rm."cUnidadEvaluacion"= 'Forma Espacio y Medida'
ORDER BY rm."cUnidadEvaluacion","Porcentaje" ASC
LIMIT 5) AS nTable
ORDER BY "Porcentaje" DESC;
EOD;
$res_react_menor_5_fem = pg_query($db, $qw_react_menor_5_fem);

/**
 * Fin de Bloque de consultas para
 * Porcentaje de aciertos en MATEMÁTICAS Unidad diagnóstica
*/

/**
 * Inicio de Bloque de consultas de
 * Contexto y expectativas de los alumnos
*/

#---- Trabaja actualmente ----
$qw_ta = <<<EOD
SELECT co."cPregunta",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=4 AND co."iNumeroEscala"=1
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_ta = pg_query($db, $qw_ta);

#---- Tipo de escuela, por sostenimiento, en que los alumnos estudiaron el último año de secundaria ----
$qw_tes = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=57
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_tes = pg_query($db, $qw_tes);

#---- Tipo de escuela, por tipo de servicio, en que los alumnos estudiaron el último año de secundaria ----
$qw_tets = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=58
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_tets = pg_query($db, $qw_tets);

#---- Nivel máximo de estudios que esperan alcanzar ----
$qw_nme = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=8
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_nme = pg_query($db, $qw_nme);

/**
 * Fin de Bloque de consultas de
 * Contexto y expectativas de los alumnos
*/

/**
 * Inicio de Bloque de consultas de
 * Entorno escolar
 * Frecuencia con la que el último maestro de español (literatura, lectura, redacción, etcétera) que tuvieron los alumnos realiza o realizaba las siguientes actividades:
*/
#---Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje
$qw_esp_act1 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=68
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esp_act1 = pg_query($db, $qw_esp_act1);

#-- Relaciona sus conocimientos previos con los nuevos --
$qw_esp_act2 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=69
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esp_act2 = pg_query($db, $qw_esp_act2);

#-- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
$qw_esp_act3 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=70
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esp_act3 = pg_query($db, $qw_esp_act3);

#-- Ayuda a los alumnos que tienen dificultades con algún tema --
$qw_esp_act4 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=71
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esp_act4 = pg_query($db, $qw_esp_act4);

#-- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
$qw_esp_act5 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=72
AND co."dPorcentajeAlumnosEscuela" >= 0
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esp_act5 = pg_query($db, $qw_esp_act5);
/**
 * Fin de Bloque de consultas de
 * Entorno escolar
*/
/**
 * Inicio de Bloque de consultas de
 * Entorno escolar
 * Frecuencia con la que el último maestro de matemáticas que tuvieron los alumnos realiza o realizaba las siguientes actividades:
*/
#---Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje
$qw_mate_act1 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=81
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_mate_act1 = pg_query($db, $qw_mate_act1);

#-- Relaciona sus conocimientos previos con los nuevos --
$qw_mate_act2 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=82
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_mate_act2 = pg_query($db, $qw_mate_act2);

#-- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
$qw_mate_act3 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=83
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_mate_act3 = pg_query($db, $qw_mate_act3);

#-- Ayuda a los alumnos que tienen dificultades con algún tema --
$qw_mate_act4 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=84
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_mate_act4 = pg_query($db, $qw_mate_act4);

#-- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
$qw_mate_act5 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'AND co."iNumeroPregunta"=85
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_mate_act5 = pg_query($db, $qw_mate_act5);
/**
 * Fin de Bloque de consultas de
 * Entorno escolar Matemáticas
*/

/**
 * Inicio de Bloque de consultas de
 * Clima y convivencia escolar
*/
#---- ¿Consideras a tu escuela un lugar seguro? ----
$qw_esc_segura = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta"=95
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_segura = pg_query($db, $qw_esc_segura);


#---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
#-- Entre estudiantes --
$qw_esc_opin1 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 104
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_opin1 = pg_query($db, $qw_esc_opin1);

#---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
#-- Entre estudiantes y docentes --
$qw_esc_opin2 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 105
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_opin2 = pg_query($db, $qw_esc_opin2);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Insultan, ofenden o ridiculizan a sus compañeros --
$qw_esc_clima_convg4 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 96
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg4 = pg_query($db, $qw_esc_clima_convg4);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#- Destruyen el mobiliario o dañan las instalaciones --
$qw_esc_clima_convg5 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 97
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg5 = pg_query($db, $qw_esc_clima_convg5);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Llevan armas (navajas, cuchillos, pistolas) --
$qw_esc_clima_convg6 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 99
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg6 = pg_query($db, $qw_esc_clima_convg6);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Se roban las pertenencias de los estudiantes --
$qw_esc_clima_convg7 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 100
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg7 = pg_query($db, $qw_esc_clima_convg7);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Lesionan o lastiman a otros estudiantes --
$qw_esc_clima_convg8 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 102
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg8 = pg_query($db, $qw_esc_clima_convg8);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Golpean o empujan a otros estudiantes --
$qw_esc_clima_convg9 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 101
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg9 = pg_query($db, $qw_esc_clima_convg9);

#---- Frecuencia con la que los estudiantes de la escuela: ----
#-- Consumen droga --
$qw_esc_clima_convg10 = <<<EOD
SELECT co."cPregunta",co."cEscala",CASE WHEN co."dPorcentajeAlumnosEscuela" = -9999 THEN -0.01 ELSE co."dPorcentajeAlumnosEscuela" END AS "dPorcentajeAlumnosEscuela"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."escalasContexto" AS co ON co."iPkEscalaContexto" = h."iFkEscalaContexto"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND co."iNumeroPregunta" = 98
GROUP BY co."cPregunta",co."cEscala",co."dPorcentajeAlumnosEscuela",co."iNumeroEscala"
ORDER BY co."iNumeroEscala";
EOD;
$res_esc_clima_convg10 = pg_query($db, $qw_esc_clima_convg10);
/**
 * Fin de Bloque de consultas de
 * Clima y convivencia escolar
*/

/**
 * Inicio de Bloque de consultas de Estadistica básica pro zona escolar
 * 
*/

#---- Matrícula escolar  Gráfica----
$qw_matricula_escolar = <<<EOD
WITH ntable AS(
SELECT y."Ciclo Escolar", u.* 
FROM(
SELECT ce."cCicloEscolar" AS "Ciclo Escolar",
    CASE WHEN h."iMatriculaPrimerGrado" = -9999 THEN -0.01 ELSE h."iMatriculaPrimerGrado" END  AS "1ro", 
    CASE WHEN h."iMatriculaSegundoGrado" =-9999 THEN -0.01 ELSE h."iMatriculaSegundoGrado" END AS "2do", 
    CASE WHEN h."iMatriculaTercerGrado" = -9999 THEN -0.01 ELSE h."iMatriculaTercerGrado" END  AS "3ro",
	CASE WHEN h."iMatriculaCuartoGrado" = -9999 THEN -0.01 ELSE h."iMatriculaCuartoGrado" END  AS "4to",
	CASE WHEN h."iMatriculaQuintoGrado" = -9999 THEN -0.01 ELSE h."iMatriculaQuintoGrado" END  AS "5to",
	CASE WHEN h."iMatriculaTotal"		= -9999 THEN -0.01 ELSE h."iMatriculaTotal" END AS "Total"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo"  AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND ce."iPkCicloEscolar" IN (17,18,19)
ORDER BY ce."cCicloEscolar") AS y
CROSS JOIN UNNEST(
array['1ro','2do','3ro','4to','5to','Total'],
array[y."1ro",y."2do",y."3ro",y."4to",y."5to",y."Total"]
)WITH ORDINALITY AS u(grado,valor,sort_no)
)
SELECT grado AS "Grado",
	max(CASE WHEN "Ciclo Escolar"='2014/2015' THEN valor END) AS "2014/2015",
	max(CASE WHEN "Ciclo Escolar"='2015/2016' THEN valor END) AS "2015/2016",
	max(CASE WHEN "Ciclo Escolar"='2016/2017' THEN valor END) AS "2016/2017"
FROM ntable
GROUP BY grado,sort_no
ORDER BY sort_no;
EOD;
$res_matricula_escolar = pg_query($db, $qw_matricula_escolar);

#---- Matriculación oportuna ----
$qw_matricula_oportuna = <<<EOD
SELECT CASE WHEN h."dTasaMatriculacionOportuna" = -9999 THEN -0.01 ELSE CAST(h."dTasaMatriculacionOportuna" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
$res_matricula_oportuna = pg_query($db, $qw_matricula_oportuna);


#---- Alumnos en edad idónea y extraedead ligera ----
$qw_matricula_ido_ext = <<<EOD
SELECT CASE WHEN h."dPorcentajeAlmnsRegularNoExtraEdad" = -9999 THEN -0.01 ELSE CAST(h."dPorcentajeAlmnsRegularNoExtraEdad" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
$res_matricula_ido_ext = pg_query($db, $qw_matricula_ido_ext);

#---- Aprobación al final del ciclo escolar ----
$qw_matricula_apro_ciclo = <<<EOD
SELECT CASE WHEN h."dTasaAprobacion" = -9999 THEN -0.01 ELSE CAST(h."dTasaAprobacion" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
$res_matricula_apro_ciclo = pg_query($db, $qw_matricula_apro_ciclo);

#---- Aprobación despues del periodo de regularización ----
$qw_matricula_apro_reg = <<<EOD
SELECT CASE WHEN h."dTasaAprobacionRegularizacion" = -9999 THEN -0.01 ELSE CAST(h."dTasaAprobacionRegularizacion" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
$res_matricula_apro_reg = pg_query($db, $qw_matricula_apro_reg);

#---- Retención intracurricular ----
$qw_matricula_reten = <<<EOD
SELECT CASE WHEN h."dTasaRetencion" = -9999 THEN -0.01 ELSE CAST(h."dTasaRetencion" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
EOD;
$res_matricula_reten = pg_query($db, $qw_matricula_reten);
/**
 * Fin de Bloque de consultas de Estadistica básica pro zona escolar
 * 
*/



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
			font-family: arial,verdana;
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
		
		.td_green{
			background-color: #beeae4;
			color: #212449;
			font-weight: bold;
			border-style: solid;
			border-color: #212449;
			border-top-width: 1px;
			border-left-width: 0px;
			border-bottom-width: 1px;
			border-right-width: 0px;
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
			background-color: #FB4F57;
		}

		.td_bg_yellow{
			background-color: #FDD16C;
		}

		.td_bg_green{
			background-color: #6ACB9C;
		}

		.td_bg_blue{
			background-color: #90B0D9;
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
			font-size: 12px;
			text-align: justify;
			margin: 0 auto;
			width: 82%;
			padding: 0.2% 0;
		}
		
		.td_top{
			vertical-align:top;
			color: #333e75;
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
		}
		.left_container {
			width: 100%;
    		text-align: left;
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
			color: #333e75;
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
		}
		.google-visualization-table-td {
			text-align: center !important;
		}
	</style>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
google.charts.load('current', {packages: ['corechart']});    
google.charts.load('current', {'packages':['bar']});
google.charts.load('current', {'packages':['table']});
google.charts.setOnLoadCallback(drawChart);
google.charts.setOnLoadCallback(drawTable);


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
					echo "['".$row_comp_lyc['CicloEscolar']."',".
					$row_comp_lyc['I_Insuficiente']*(-1).",'".$txtstyle_I."',".$row_comp_lyc['II_Elemental'].",'".$txtstyle_II."',".
					$row_comp_lyc['III_Bueno'].",'".$txtstyle_III."',".$row_comp_lyc['IV_Excelente'].",'".$txtstyle_IV."'],";
				}
				?>
]);

	var options_comp_nacional = {
	tooltip: {trigger: 'none'},
	title: 'Lenguaje y Comunicación',
	hAxis: {
		textPosition: 'none',
		gridlines: {color: 'transparent'}},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 11, }},
  chartArea: {left: 300,right: 0,top: 20,bottom: 0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
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
					echo "['".$row_comp_mat['CicloEscolar']."',".
					$val_I*(-1).",'".$txtstyle_I."',".$val_II.",'".$txtstyle_II."',".
					$val_III.",'".$txtstyle_III."',".$val_IV.",'".$txtstyle_IV."'],";
				}
				?>
]);

	var options_comp_nacional_mat = {
	tooltip: {trigger: 'none'},
	title: 'Matemáticas',
	hAxis: {
		textPosition: 'none',
		gridlines: {color: 'transparent'}},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 11, }},
  chartArea: {left: 300,right: 0,top: 20,bottom: 0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
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
		while($row_prom_subs = pg_fetch_assoc($res_prom_subs)){
			echo "[".$row_prom_subs['dPuntajePromedioEscGpoCompLyC'].",".$row_prom_subs['dPuntajePromedioEscGpoCompMat'].",'point { size: 1; shape-type: circle; fill-color: #000000;}'],";
		}
		while($row_ent_subs = pg_fetch_assoc($res_ent_subs)){
			echo "[".$row_ent_subs['dPuntajePromedioEscLyC'].",".$row_ent_subs['dPuntajePromedioEscMat'].",'point { size: 6; shape-type: circle; fill-color: #006600;}'],";
		}
		while($row_escuela_subs = pg_fetch_assoc($res_escuela_subs)){
			echo "[".$row_escuela_subs['dPuntajePromedioEscLyC'].",".$row_escuela_subs['dPuntajePromedioEscMat'].",'point { size: 12; shape-type: star; fill-color: #0000cc;}'],";
		}	
		while($row_ent_prom = pg_fetch_assoc($res_ent_prom)){
			echo "[".$row_ent_prom['dPuntajePromedioEstatalLyC'].",".$row_ent_prom['dPuntajePromedioEstatalMat'].",'point { size: 12; shape-type: triangle; fill-color: #808080;}'],";
		}	
	?>
]);

//$row_limits_lyc = pg_fetch_assoc(res_limits_lyc);
$minlyc = 400;
$maxlyc = 800;

var options_comp = {
	chart:{
		title: 'Puntajes promedio PLANEA 2017',
		subtitle: 'Mi escuela en mi zona escolar'
		},
	legend: 'none',
	height: 350,
	width: 350,
	crosshair: {trigger: 'both', orientation: 'both', color: 'gray'},//crosshair: {trigger: 'selection', orientation: 'both', color: 'gray'},
	vAxis: {gridlines: {color: 'transparent'}, title: '% de alumnos en nivel Insuficiente en MAT'},
	hAxis: {gridlines: {color: 'transparent'}, title: '% de alumnos en nivel Insuficiente en LyC'}
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
				$color = "#FB4F57";
			}elseif($row_manejo_txts['porcentaje']>40 && $row_manejo_txts['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_react_menor_5['porcentaje']>40 && $row_react_menor_5['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_txt_argumentativo['porcentaje']>40 && $row_txt_argumentativo['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_react_menor_5_arg['porcentaje']>40 && $row_react_menor_5_arg['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_mdi['Porcentaje']>40 && $row_mdi['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_react_menor_5_mdi['Porcentaje']>40 && $row_react_menor_5_mdi['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_snpa['Porcentaje']>40 && $row_snpa['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_react_menor_5_snpa['Porcentaje']>40 && $row_react_menor_5_snpa['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_fem['Porcentaje']>40 && $row_fem['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
				$color = "#FB4F57";
			}elseif($row_react_menor_5_fem['Porcentaje']>40 && $row_react_menor_5_fem['Porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
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
	  0:{color: '#90faa0'}
},
  annotations:{
	  textStyle: {fontSize: 10}
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
								Nivel de:
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
								<?php echo $cve_cct;?>
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
								La <strong>MEJOREDU</strong> concibe a la educación como un derecho de todos los niños, niñas, adolescentes y 
								jóvenes que implica asegurarles el acceso, tránsito y permanencia a los centros escolares, así como un aprendizaje 
								pertinente, significativo y relevante. La valoración de este aprendizaje está articulada en una relación en donde 
								las evidencias de la evaluación sirvan a los centros escolares y a las autoridades educativas a generar orientaciones 
								que permitan a los alumnos aprender más y mejor.<br/>
								Es por ello que con el propósito de ayudar a las escuelas a identificar 
								<strong>las fortalezas y oportunidades respecto al aprendizaje de los estudiantes y generar orientaciones que 
								promuevan y faciliten procesos de mejora a través de identificar sus necesidades, retos y avances en los logros 
								alcanzados,</strong> se presenta este reporte escolar con información de la última aplicación de la prueba PLANEA. 
								Este reporte se generó para todas las escuelas secundarias que participaron en la aplicación de la prueba en 2019 e 
								integra información sistematizada de logro educativo de cada centro escolar, así como información más detallada 
								sobre aquellos temas que los resultados sugieren que los alumnos no dominan por completo.<br/>
								<br/>
								<strong>Los resultados presentados en este reporte reflejan en forma confiable el logro que obtuvo el conjunto de 
								los alumnos del último grado en cada secundaria, aun cuando en algunas escuelas sólo se aplicó a una muestra de sus 
								estudiantes.</strong> Así mismo, los datos que se ofrecen deben verse como el resultado acumulado del proceso de 
								aprendizaje de los estudiantes de la escuela a lo largo de los seis años de primaria y los tres de secundaria, pues 
								reflejan el trabajo y esfuerzo de todo el equipo docente, además de las condiciones particulares de los estudiantes.
								<br/>
								<br/>
								En la lógica de la mejora continua, se espera que estos reportes sean uno de los múltiples apoyos que ayuden a los 
								centros escolares a emprender procesos que propicien los ajustes o cambios que se requieren en la práctica para 
								satisfacer sus necesidades, afrontar los retos y sostener o acrecentar los avances logrados en el aprendizaje de los 
								alumnos. <strong>Se sugiere que, con base en esta información, en el conocimiento de las condiciones escolares y en 
								el marco de una reflexión colectiva, el personal docente y directivo de cada escuela defina las acciones a seguir.</strong> 
								Es importante considerar que, para fortalecer los aprendizajes y habilidades que se identifiquen como susceptibles de 
								mejora, será necesario reforzar no sólo las acciones en el sexto grado, sino también en los grados previos en los que 
								se aporten las bases académicas para alcanzar las que mide PLANEA.
								</td>
							</tr>
						</table>
			  </div>

				<!--Separador de contenido-->
				<div>
						<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
						</div>
				</div>

				<div>
					<table class="max-width">
						<tr>
							<td rowspan="6" class="td_top left">¿Qué contiene?</th>
							<td class="td_just"><strong>A. </strong> El <strong>porcentaje de alumnos en cada uno de los niveles de logro</strong>,
							 tanto en Lenguaje y Comunicación como en Matemáticas en 2017 en las aplicaciones de Planea 2015, 2017 y 2019.</th>
						</tr>
							<tr>
								<td class="td_just">
								<strong>B.</strong> El <strong>comparativo de los resultados de 2017 con los de 2015</strong> así como con los de escuelas 
								similares, escuelas de su entidad y las escuelas de todo el país. Hay que tomar en cuenta que en algunas escuelas sólo 
								se cuenta con información para 2017.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>C.</strong> Una <strong>comparación del puntaje promedio obtenido por la escuela con el promedio de las escuelas 
								de su misma zona escolar</strong>.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>D.</strong> Las <strong>prioridades de atención académica</strong>, con base en los reactivos en los que los estudiantes 
								obtuvieron menor porcentaje de aciertos por cada eje temático, en los dos campos de conocimiento evaluados</strong>.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>E.</strong> La <strong>información sobre el contexto escolar, docentes, grupos y alumnos</strong> por grado escolar, 
								así como los porcentajes de aprobación y, de alumnos en edad idónea o un año más por encima de la idónea de la escuela, como 
								una referencia general.
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
				<h4 class="section_tittle">Resultados de la escuela por niveles de logro en Planea 2015, 2017 y 2019</h4>
			</div>
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

		<!--Separador de contenido-->
			<div class="split">
				<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
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
		  
	</div>		
<!--Fin de página 2-->
<div class="saltopagina"></div>

<!--Inicio de página 3-->
<div class="page_container">
	<div class="header">
			<img src="./imagenes/cabeceras/cabeza_00.png" class="elem_center" style="align:right">
	</div>

	<div class="container">
				<h4 class="section_tittle">Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN por eje temático</h4>
	</div>

	<div class="split">
					<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
	</div>
	<div class="left_container">
		El hecho de que un estudiante se encuentre en el nivel insuficiente (o nivel I) es un indicio de que no tiene un dominio 
		básico de los aprendizajes clave al término de la Educación Secundaria y por tanto no es deseable que en una escuela exista 
		una alta proporción de estudiantes con este nivel de dominio mínimo.
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
				<span><img src="./imagenes/widgets/mi_escuela.png">&nbsp;
					<strong>Esta Escuela tiene <span style="color:#7CCD7C";>menos de la mitad</span> de los alumnos en nivel Insuficiente en Lenguaje y Comunicación (LyC) y 
					Matemáticas (MAT)</strong>.
				</span>
			</li>
			<li>
				<span><img src="./imagenes/widgets/escuela_zona.png">&nbsp;
					<strong>Escuelas en la zona escolar</strong>
				</span>
			</li>
			</ul>
		</div>
		<div class="left_container">
			En la zona escolar hay 5 escuelas que tienen más de 50% de sus alumnos en nivel Insuficiente en 
			Lenguaje y Comunicación (LyC) y Matemáticas (MAT).<br/>
			Tomando en cuenta el porcentaje de alumnos en nivel Insuficiente en ambas materias, esta escuela se 
			encuentra entre el 30% de las escuelas con las menores proporciones de estudiantes en nivel 
			Insuficiente en el estado.
		</div>	

		<div class="split">
					<img src="./imagenes/separador.png">
	  </div>
		<div  class="text">
		<ul style="font-size:14px;vertical-align: top;">
		<li><?php echo $txt_1;?></li>
		<li><?php echo $txt_2;?></li>
		<li><?php echo $txt_3;?></li>
		<li><?php echo $txt_4;?></li>
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
				<h4 class="section_tittle">Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN por eje temático</h4>
		</div>	
		<div class="split">
					<img src="./imagenes/separador.png" style="width: 95%">
	  	</div>
		<div class="reac_container" id="container_manejo_const_txt">
						<h5>Eje temático: Reflexión sobre la lengua</h5>
						<div id="manejo_const_txt"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5">
						<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
						<div id="react_menor_5"></div>
		</div>
		<div class="reac_container" id="container_txt_arg">
						<h5>Eje temático: Comprensión lectora</h5>
						<div id="txt_arg"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_arg">
						<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
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
			<h4 class="section_tittle">Porcentaje de aciertos en MATEMÁTICAS por eje temático</h4>
		</div>	
		<div class="split">
			<img src="./imagenes/separador.png" style="width: 95%">
		</div>
		<div class="reac_container" id="container_txt_fem">
			<h5>Eje temático: Forma, Espacio y Medida</h5>
			<div id="txt_fem"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_fem">
			<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
			<div id="react_menor_5_fem"></div>
		</div>

		<div class="reac_container" id="container_txt_mdi">
			<h5>Eje temático: Manejo de la Información</h5>
			<div id="txt_mdi"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_mdi">
			<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
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
						<h4 class="section_tittle">Porcentaje de aciertos en MATEMÁTICAS por eje temático</h4>
				</div>	
				<div class="split">
							<img src="./imagenes/separador.png" style="width: 95%">
				</div>
				<div class="reac_container" id="container_txt_snpa">
								<h5>Eje temático: Sentido numérico y pensamiento algebráico</h5>
								<div id="txt_snpa"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_snpa">
								<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
								<div id="react_menor_5_snpa"></div>
				</div>

		</div>
	<!--Fin de página 7-->

</div> <!--fin seccion matemáticas -->

</body>
</html>
<?php pg_close($db);?>
