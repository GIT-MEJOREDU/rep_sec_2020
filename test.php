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
		$extension = substr($arrv['vent'],11);
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
		SELECT ct."cNombreCentroTrabajo", ct."cClaveCentroTrabajo", te."cNombreTurnoEscolar", ex."cNombreExtensionEms", ex."iPkExtensionEms", pl."cClavePlantel", ss."cNombreSubsistemaEms", ef."cNombreEntidad", ef."iPkEntidadFederativa", ss."iPkSubsistemaEms"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		FULL OUTER JOIN "dimensionesPlaneaEms"."plantelesEms" AS pl ON pl."iPkPlantel" = h."iFkPlantel"
		FULL OUTER JOIN "dimensionesPlaneaEms"."subsistemasEms" AS ss ON ss."iPkSubsistemaEms" = h."iFkSubsistemaEms"
		FULL OUTER JOIN "dimensionesPlaneaEms"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" =h."iFkEntidadFederativa"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar"='$cicloEscolar' AND ex."iPkExtensionEms"=$extension; 
EOD;

	$res = pg_query($db, $qw);
	#Se asignan valores de datos generales de la escuela, para mostrarlas en encabezado del reporte
	$row = pg_fetch_assoc($res);
	$nom_cct = $row['cNombreCentroTrabajo'];
	$cve_cct = $row['cClaveCentroTrabajo'];
	$nom_turno = $row['cNombreTurnoEscolar'];
	$nom_ext = $row['cNombreExtensionEms'];
	$iPk_ext = $row['iPkExtensionEms'];
	$cve_plantel = $row['cClavePlantel'];
	$nom_subsistema = $row['cNombreSubsistemaEms'];
	$nom_entidad = $row['cNombreEntidad'];
	$nom_mun = "MUNICIPIO AAAAAAA AAAAA";
	$nivel = "NIVEL AAAAA AAAA";
	$zona = "ZONA ABC-999-1234";
	$iPk_entidad = $row['iPkEntidadFederativa'];
	$iPk_subs = $row['iPkSubsistemaEms'];


		#Consulta de nivel de logro LyC para el comparativo con Matematicas
		$qw_nivel_logro_LyC = <<<EOD
		SELECT ce."cCicloEscolar" AS "CicloEscolar", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrILyC" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIILyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIILyC" AS NUMERIC (5,2))  END AS "II_Elemental", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIILyC" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIILyC" AS NUMERIC (5,2)) END AS "III_Bueno", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIVLyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVLyC" AS NUMERIC (5,2))  END AS "IV_Excelente"
			FROM hechos."hechosReporteEmsInee" AS h
			FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo"  AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
			FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
			FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
			FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"																																  
			WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
			AND h."dPorcentAlumnsEscNvlLgrILyC"   >= 0;
EOD;
	$res_nl_LyC = pg_query($db, $qw_nivel_logro_LyC);

		#Consulta de nivel de logro Mat, para el comparativo con LyC
		$qw_nivel_logro_Mat = <<<EOD
		SELECT  ce."cCicloEscolar" AS "CicloEscolar", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIMat" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIMat" AS NUMERIC (5,2))  END AS "II_Elemental", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIIMat" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIIMat" AS NUMERIC (5,2)) END AS "III_Bueno", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIVMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVMat" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo"  AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		AND h."dPorcentAlumnsEscNvlLgrIMat"   >= 0;
EOD;

	$res_nl_Mat = pg_query($db, $qw_nivel_logro_Mat);

#Consulta de Comparativo con escuelas promedio de la entidad y del país Lenguaje y Comunicacion
$qw_compara_LyC = <<<EOD
SELECT nTable.Resultado, nTable."I_Insuficiente", nTable."II_Elemental", nTable."III_Bueno",nTable."IV_Excelente" 
FROM  
(
		SELECT 'Nacional' AS Resultado,  
			CASE WHEN h."dPrctjAlmsTdsEscMxNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscMxNvlLgrILyC" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPrctjAlmsTdsEscMxNvlLgrIILyC"  = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscMxNvlLgrIILyC" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPrctjAlmsTdsEscMxNvlLgrIIILyC" = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscMxNvlLgrIIILyC" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPrctjAlmsTdsEscMxNvlLgrIVLyC"  = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscMxNvlLgrIVLyC" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo"  AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'

		UNION
		SELECT 'Estatal' AS Resultado,  
			CASE WHEN h."dPrctjAlmsTdsEscEstNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscEstNvlLgrILyC" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPrctjAlmsTdsEscEstNvlLgrIILyC"  = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscEstNvlLgrIILyC" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPrctjAlmsTdsEscEstNvlLgrIIILyC" = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscEstNvlLgrIIILyC" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPrctjAlmsTdsEscEstNvlLgrIVLyC"  = -9999 THEN -0.01 ELSE CAST(h."dPrctjAlmsTdsEscEstNvlLgrIVLyC" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'

		UNION
		SELECT ct."cEscuelaParecida" AS Resultado,  
			CASE WHEN h."dPorcentAlmsEscParNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrILyC" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIILyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIILyC" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIIILyC" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIIILyC" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIVLyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIVLyC" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		
		UNION
		SELECT 'Escuela' AS Resultado,  
			CASE WHEN h."dPorcentAlumnsEscNvlLgrILyC"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrILyC" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIILyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIILyC" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIILyC" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIILyC" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIVLyC"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVLyC" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		ORDER BY Resultado ) as nTable
WHERE 	"I_Insuficiente" is not null
	OR  "II_Elemental"	is not null
	OR	"III_Bueno"		is not null
	OR "IV_Excelente"	is not null	;
EOD;

$res_compara_LyC = pg_query($db, $qw_compara_LyC);

#Consulta de Comparativo con escuelas promedio de la entidad y del país Matemáticas
$qw_compara_mat = <<<EOD
SELECT nTable.Resultado, nTable."I_Insuficiente", nTable."II_Elemental", nTable."III_Bueno",nTable."IV_Excelente" 
FROM  
(
	SELECT 'Nacional' AS Resultado, 
			CASE WHEN h."dPctjAlmsTdsEscMxNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscMxNvlLgrIMat" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPctjAlmsTdsEscMxNvlLgrIIMat"  = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscMxNvlLgrIIMat" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPctjAlmsTdsEscMxNvlLgrIIIMat" = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscMxNvlLgrIIIMat" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPctjAlmsTdsEscMxNvlLgrIVMat"  = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscMxNvlLgrIVMat" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		UNION
																																  
		SELECT 'Estatal' AS Resultado,
			CASE WHEN h."dPctjAlmsTdsEscEstNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscEstNvlLgrIMat" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPctjAlmsTdsEscEstNvlLgrIIMat"  = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscEstNvlLgrIIMat" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPctjAlmsTdsEscEstNvlLgrIIIMat" = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscEstNvlLgrIIIMat" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPctjAlmsTdsEscEstNvlLgrIVMat"  = -9999 THEN -0.01 ELSE CAST(h."dPctjAlmsTdsEscEstNvlLgrIVMat" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
																																
		UNION
		SELECT ct."cEscuelaParecida" AS Resultado,
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIMat" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIIMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIIMat" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIIIMat" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIIIMat" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPorcentAlmsEscParNvlLgrIVMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlmsEscParNvlLgrIVMat" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		UNION
																																	
		SELECT 'Escuela' AS Resultado,
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIMat"   = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIMat" AS NUMERIC (5,2))   END AS "I_Insuficiente", 
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIMat" AS NUMERIC (5,2))  END AS "II_Elemental",
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIIIMat" = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIIIMat" AS NUMERIC (5,2)) END AS "III_Bueno",
			CASE WHEN h."dPorcentAlumnsEscNvlLgrIVMat"  = -9999 THEN -0.01 ELSE CAST(h."dPorcentAlumnsEscNvlLgrIVMat" AS NUMERIC (5,2))  END AS "IV_Excelente"
		FROM hechos."hechosReporteEmsInee" AS h
		FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
		FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
		FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms"  AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
		WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND h."iFkCicloEscolar" IN ('$cicloEscolar') AND ex."iPkExtensionEms" = '$iPk_ext'
		ORDER BY Resultado ) as nTable
WHERE 	"I_Insuficiente" is not null
	OR  "II_Elemental" is not null
	OR	"III_Bueno" is not null
	OR "IV_Excelente"is not null;
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
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
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
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
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
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
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
	AND ct."cClaveCentroTrabajo"='$escuela'
	AND te."iPkTurnoEscolar"='$turno'
	AND ex."iPkExtensionEms"='$iPk_ext'
	AND h."iFkCicloEscolar"='$cicloEscolar'
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
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar';
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
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms"='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar'
ORDER BY ct."cClaveCentroTrabajo";
EOD;
$res_ent_texto = pg_query($db, $qw_ent_texto);
$row_ent_texto = pg_fetch_assoc($res_ent_texto);
#--Textos para el cuadrante.
$txt1_2 = $row_ent_texto['cMensajeEje1'];
$txt3_4 = $row_ent_texto['cMensajeEje2'];
$pos1 = strpos($txt1_2, ".");
$txt_1 = trim(substr ($txt1_2 , 0, $pos1+1));
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
$qw_manejo_txts = <<<EOD
SELECT rlc."cAspectoEvaluacion", CASE WHEN h."dPorcentajeAspecto1LyC" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto1LyC" END  AS "dPorcentajeAspecto1LyC"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" = '$iPk_ext' AND rlc."cAspectoEvaluacion" = 'Manejo y construcción de la información' AND h."iFkCicloEscolar" = '$cicloEscolar'
AND h."dPorcentajeAspecto1LyC" >= 0
GROUP BY rlc."cAspectoEvaluacion",  CASE WHEN h."dPorcentajeAspecto1LyC" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto1LyC" END
ORDER BY rlc."cAspectoEvaluacion";
EOD;

$res_manejo_txts = pg_query($db, $qw_manejo_txts);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5 = <<<EOD
SELECT *
FROM (
	SELECT 	CONCAT(rlc."cEspecificacion",' Reactivo #',rlc."iNumeroReactivo"), 
			CASE WHEN rlc."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rlc."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
	FROM hechos."hechosReporteEmsInee" AS h
	FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
	FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
	FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
	FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
	WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" = '$iPk_ext' AND h."iFkCicloEscolar" = '$cicloEscolar' AND rlc."cAspectoEvaluacion" = 'Manejo y construcción de la información'
	AND rlc."dporcentAlumnsAcertReactivo" >= 0
	ORDER BY rlc."cAspectoEvaluacion",Porcentaje ASC
	LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;

$res_react_menor_5 = pg_query($db, $qw_react_menor_5);

#---Unidad diagnóstica: Texto argumentativo
#---- Aspecto de evaluación: Texto argumentativo ----
$qw_txt_argumentativo = <<<EOD
SELECT 	rlc."cAspectoEvaluacion", 
		CASE WHEN h."dPorcentajeAspecto2LyC" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto2LyC" END  AS "dPorcentajeAspecto2LyC"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rlc."cAspectoEvaluacion" ='Texto argumentativo' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto2LyC" >= 0
GROUP BY rlc."cAspectoEvaluacion", h."dPorcentajeAspecto2LyC"
ORDER BY rlc."cAspectoEvaluacion";
EOD;
$res_txt_argumentativo = pg_query($db, $qw_txt_argumentativo);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_arg = <<<EOD
SELECT *
FROM (SELECT 	CONCAT(rlc."cEspecificacion",' Reactivo #',rlc."iNumeroReactivo"), 
				CASE WHEN rlc."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rlc."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND h."iFkCicloEscolar" ='$cicloEscolar' AND rlc."cAspectoEvaluacion" = 'Texto argumentativo'
AND rlc."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rlc."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
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
SELECT rm."cAspectoEvaluacion", 
	   CASE WHEN h."dPorcentajeAspecto3Mat" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto3Mat" END  AS "dPorcentajeAspecto3Mat"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rm."cAspectoEvaluacion" = 'Manejo de la información' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto3Mat" >= 0
GROUP BY rm."cAspectoEvaluacion", h."dPorcentajeAspecto3Mat"
ORDER BY rm."cAspectoEvaluacion";
EOD;
$res_mdi = pg_query($db, $qw_mdi);

#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_mdi = <<<EOD
SELECT *
FROM (SELECT CONCAT(rm."cEspecificacion",' Reactivo #',rm."iNumeroReactivo"),
			 CASE WHEN rm."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND rm."cAspectoEvaluacion"= 'Manejo de la información'
AND rm."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rm."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;
$res_react_menor_5_mdi = pg_query($db, $qw_react_menor_5_mdi);

#Unidad diagnóstica: Sentido numérico y pensamiento algebraico
$qw_snpa = <<<EOD
SELECT rm."cAspectoEvaluacion",
	   CASE WHEN h."dPorcentajeAspecto4Mat" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto4Mat" END  AS "dPorcentajeAspecto4Mat"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rm."cAspectoEvaluacion" = 'Sentido numérico y pensamiento algebráico' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto4Mat" >= 0
GROUP BY rm."cAspectoEvaluacion", h."dPorcentajeAspecto4Mat"
ORDER BY rm."cAspectoEvaluacion";
EOD;
$res_snpa = pg_query($db, $qw_snpa);

#--Sentido numérico y pensamiento algebraico
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_snpa = <<<EOD
SELECT *
FROM (SELECT CONCAT(rm."cEspecificacion",' Reactivo #',rm."iNumeroReactivo"), 
			 CASE WHEN rm."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND rm."cAspectoEvaluacion"='Sentido numérico y pensamiento algebráico'
AND rm."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rm."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
EOD;
$res_react_menor_5_snpa = pg_query($db, $qw_react_menor_5_snpa);

#Unidad diagnóstica: Forma, espacio y medida
#---- Aspecto de evaluación: Forma, Espacio y Medida ----
$qw_fem = <<<EOD
SELECT rm."cAspectoEvaluacion", 
	   CASE WHEN h."dPorcentajeAspecto2Mat" = -9999 THEN -0.01 ELSE h."dPorcentajeAspecto2Mat" END  AS "dPorcentajeAspecto2Mat"
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND rm."cAspectoEvaluacion" ='Forma, Espacio y Medida' AND h."iFkCicloEscolar" ='$cicloEscolar'
AND h."dPorcentajeAspecto2Mat" >= 0
GROUP BY rm."cAspectoEvaluacion", h."dPorcentajeAspecto2Mat"
ORDER BY rm."cAspectoEvaluacion";
EOD;
$res_fem = pg_query($db, $qw_fem);

#--Sentido Forma espacio y medida
#-- Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático --
$qw_react_menor_5_fem = <<<EOD
SELECT *
FROM (SELECT CONCAT(rm."cEspecificacion",' Reactivo #',rm."iNumeroReactivo"), 
			 CASE WHEN rm."dporcentAlumnsAcertReactivo" = -9999 THEN -0.01 ELSE CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) END AS Porcentaje
FROM hechos."hechosReporteEmsInee" AS h
FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
FULL OUTER JOIN "dimensionesPlaneaEms"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='$escuela' AND te."iPkTurnoEscolar"='$turno' AND ex."iPkExtensionEms" ='$iPk_ext' AND h."iFkCicloEscolar"='$cicloEscolar' AND rm."cAspectoEvaluacion"= 'Forma, Espacio y Medida'
AND rm."dporcentAlumnsAcertReactivo" >= 0
ORDER BY rm."cAspectoEvaluacion",Porcentaje ASC
LIMIT 5) AS nTable
ORDER BY Porcentaje ASC;
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
* Bloque para generar grafica del comparativo LyC con Matemáticas
*/

	//se hace fetch sobre reesultados de LyC

var data_LyC = google.visualization.arrayToDataTable([
	['Nivel de logro','Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
				//['Lenguaje y Comunicacion', 4.55,'4.55%', 4.55,'4.55%', 45.45,'45.45%', 45.45,'45.45%']
	<?php
					while($row_LyC = pg_fetch_assoc($res_nl_LyC)){
						if($row_LyC['I_Insuficiente']<0){$txtstyle_I="Dato no disponible"; $val_I=0;}else{$txtstyle_I=$row_LyC['I_Insuficiente']."%"; $val_I=$row_LyC['I_Insuficiente'];}
						if($row_LyC['II_Elemental']<0){$txtstyle_II="Dato no disponible"; $val_II=0;}else{$txtstyle_II=$row_LyC['II_Elemental']."%"; $val_II=$row_LyC['II_Elemental'];}
						if($row_LyC['III_Bueno']<0){$txtstyle_III="Dato no disponible"; $val_III=0;}else{$txtstyle_III=$row_LyC['III_Bueno']."%"; $val_III=$row_LyC['III_Bueno'];}
						if($row_LyC['IV_Excelente']<0){$txtstyle_IV="Dato no disponible"; $val_IV=0;}else{$txtstyle_IV=$row_LyC['IV_Excelente']."%"; $val_IV=$row_LyC['IV_Excelente'];}
						echo "['Lenguaje y Comunicación',".$val_I.",'".$txtstyle_I."',".$val_II.",'".$txtstyle_II."',".
						$val_III.",'".$txtstyle_III."',".$val_IV.",'".$txtstyle_IV."'],";
						//echo "['Lenguaje y Comunicación',".$row_LyC['I_Insuficiente'].",'".$row_LyC['I_Insuficiente']."%',".$row_LyC['II_Elemental'].",'".$row_LyC['II_Elemental']."%',".
						//$row_LyC['III_Bueno'].",'".$row_LyC['III_Bueno']."%',".$row_LyC['IV_Excelente'].",'".$row_LyC['IV_Excelente']."%'],";
					}
					?>
]);

	//se hace fetch sobre reesultados de matemáticas

  var data_Mat = google.visualization.arrayToDataTable([
	['Nivel de logro','Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
<?php
					while($row_Mat = pg_fetch_assoc($res_nl_Mat)){
						if($row_Mat['I_Insuficiente']<0){$txtstyle_I="Dato no disponible"; $val_I=0;}else{$txtstyle_I=$row_Mat['I_Insuficiente']."%"; $val_I=$row_Mat['I_Insuficiente'];}
						if($row_Mat['II_Elemental']<0){$txtstyle_II="Dato no disponible"; $val_II=0;}else{$txtstyle_II=$row_Mat['II_Elemental']."%"; $val_II=$row_Mat['II_Elemental'];}
						if($row_Mat['III_Bueno']<0){$txtstyle_III="Dato no disponible"; $val_III=0;}else{$txtstyle_III=$row_Mat['III_Bueno']."%"; $val_III=$row_Mat['III_Bueno'];}
						if($row_Mat['IV_Excelente']<0){$txtstyle_IV="Dato no disponible"; $val_IV=0;}else{$txtstyle_IV=$row_Mat['IV_Excelente']."%"; $val_IV=$row_Mat['IV_Excelente'];}
						echo "['Matemáticas',".$val_I.",'".$txtstyle_I."',".$val_II.",'".$txtstyle_II."',".
						$val_III.",'".$txtstyle_III."',".$val_IV.",'".$txtstyle_IV."'],";
					}
					?>
]);

  var options_fullStacked_LyC = {
		tooltip: {trigger: 'none'},
		//title: 'Resultados por niveles de logro en PLANEA 2017',
isStacked: 'percent',
height: 250,
	  width: 200,
		//title: 'Lenguaje y Comunicación',
	  legend: {position: 'none', maxLines: 3},//legend: {position: 'none', maxLines: 3},
vAxis: {
    minValue: 0,
    ticks: [0, .1, .2, .3, .4, .5, .6, .7, .8, .9, 1]},
	  series:{
	  0:{color: '#FB4F57',},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	chartArea: {
	  top: 10,
	  bottom: 20},
	annotations:{
	  textStyle: {fontSize: 10}
	}
	};

  var options_fullStacked_Mat = {
                tooltip: {trigger: 'none'},
                //title: 'Resultados por niveles de logro en PLANEA 2017',
isStacked: 'percent',
height: 250,
          width: 200,
					//title: 'Matemáticas',
          legend: {position: 'none', maxLines: 3},
vAxis: {
    minValue: 0,
    ticks: [0, .1, .2, .3, .4, .5, .6, .7, .8, .9, 1]},
          series:{
          0:{color: '#FB4F57',},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
        chartArea: {
          top: 10,
          bottom: 20},
        annotations:{
          textStyle: {fontSize: 10}
        }
        };


		var container_LyC = document.getElementById('comp_planea_lyc');
		var chart_LyC = new google.visualization.ColumnChart(container_LyC);
		google.visualization.events.addListener(chart_LyC, 'ready', function () {
			container_LyC.innerHTML = '<img src="' + chart_LyC.getImageURI() + '">';
    });
		chart_LyC.draw(data_LyC, options_fullStacked_LyC);

		<?php if (pg_num_rows ( $res_nl_LyC ) == 0){ ?>
			document.getElementById('comp_planea_lyc').style.display = "none";
			//document.getElementById('manejo_const_txt').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
		<?php } ?>

		var container_Mat1 = document.getElementById('comp_planea_mat');
		var chart_Mat = new google.visualization.ColumnChart(container_Mat1);
		google.visualization.events.addListener(chart_Mat, 'ready', function () {
			container_Mat1.innerHTML = '<img src="' + chart_Mat.getImageURI() + '">';
    });
		chart_Mat.draw(data_Mat, options_fullStacked_Mat);
		
		<?php if (pg_num_rows ( $res_nl_Mat ) == 0){ ?>
			document.getElementById('comp_planea_mat').style.display = "none";
			//document.getElementById('manejo_const_txt').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
		<?php } ?>

/*FIN
* Bloque para generar grafica del comparativo LyC con Matemáticas
*/

/*INICIO
* Bloque para generar grafica del Comparativo con escuelas promedio de la entidad y del país Lenguaje y Comunicacion
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
					echo "['".$row_comp_lyc['resultado']."',".
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
* Bloque para generar grafica del Comparativo con escuelas promedio de la entidad y del país Lenguaje y Comunicacion
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
					echo "['".$row_comp_mat['resultado']."',".
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
	vAxis: {gridlines: {color: 'transparent'}, title: 'Puntaje en Matemáticas'},
	hAxis: {gridlines: {color: 'transparent'}, title: 'Puntaje en Lenguaje y Comunicación'}
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
			if($row_manejo_txts['dPorcentajeAspecto1LyC']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_manejo_txts['dPorcentajeAspecto1LyC']."%";}
			if($row_manejo_txts['dPorcentajeAspecto1LyC']>=0 && $row_manejo_txts['dPorcentajeAspecto1LyC']<=40){
				$color = "#FB4F57";
			}elseif($row_manejo_txts['dPorcentajeAspecto1LyC']>40 && $row_manejo_txts['dPorcentajeAspecto1LyC']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_manejo_txts['cAspectoEvaluacion']."',".$row_manejo_txts['dPorcentajeAspecto1LyC'].",'".$txtstyle."','".$color."'],";
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
			if($row_txt_argumentativo['dPorcentajeAspecto2LyC']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_txt_argumentativo['dPorcentajeAspecto2LyC']."%";}
			if($row_txt_argumentativo['dPorcentajeAspecto2LyC']>0 &&$row_txt_argumentativo['dPorcentajeAspecto2LyC']<=40){
				$color = "#FB4F57";
			}elseif($row_txt_argumentativo['dPorcentajeAspecto2LyC']>40 && $row_txt_argumentativo['dPorcentajeAspecto2LyC']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_txt_argumentativo['cAspectoEvaluacion']."',".$row_txt_argumentativo['dPorcentajeAspecto2LyC'].",'".$txtstyle."','".$color."'],";
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
* Unidad diagnóstica: Texto expositivo
*/
var data_txt_expositivo = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_txt_expositivo = pg_fetch_assoc($res_txt_expositivo)){
			if($row_txt_expositivo['dPorcentajeAspecto3LyC']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_txt_expositivo['dPorcentajeAspecto3LyC']."%";}
			if($row_txt_expositivo['dPorcentajeAspecto3LyC']>0 && $row_txt_expositivo['dPorcentajeAspecto3LyC']<=40){
				$color = "#FB4F57";
			}elseif($row_txt_expositivo['dPorcentajeAspecto3LyC']>40 && $row_txt_expositivo['dPorcentajeAspecto3LyC']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_txt_expositivo['cAspectoEvaluacion']."',".$row_txt_expositivo['dPorcentajeAspecto3LyC'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_txt_expositivo = {
tooltip: {trigger: 'none'},
  title: 'Texto Expositivo',	  
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

var container_txt_expositivo = document.getElementById('txt_exp');
var chart_txt_expositivo = new google.visualization.BarChart(container_txt_expositivo);
google.visualization.events.addListener(chart_txt_expositivo, 'ready', function () {
container_txt_expositivo.innerHTML = '<img src="' + chart_txt_expositivo.getImageURI() + '">';
});
chart_txt_expositivo.draw(data_txt_expositivo, options_txt_expositivo);

<?php
if (pg_num_rows ( $res_txt_expositivo ) == 0){
?>
	//document.getElementById('container_txt_exp').style.visibility = "hidden";
	document.getElementById('container_txt_exp').style.display = "none";
	document.getElementById('txt_exp').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php
}
?>
/*FIN
* Unidad diagnóstica: Texto expositivo
*/

/*INICIO
* "texto expositivo"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_exp = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_exp = pg_fetch_assoc($res_react_menor_5_exp)){
			if($row_react_menor_5_exp['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_exp['porcentaje']."%";}
			if($row_react_menor_5_exp['porcentaje']>0 && $row_react_menor_5_exp['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_exp['porcentaje']>40 && $row_react_menor_5_exp['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_exp['concat']."',".$row_react_menor_5_exp['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_exp = {
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

var container_react_menor_5_exp = document.getElementById('react_menor_5_exp');
var chart_react_menor_5_exp = new google.visualization.BarChart(container_react_menor_5_exp);
google.visualization.events.addListener(chart_react_menor_5_exp, 'ready', function () {
container_react_menor_5_exp.innerHTML = '<img src="' + chart_react_menor_5_exp.getImageURI() + '">';
});
chart_react_menor_5_exp.draw(data_react_menor_5_exp, options_react_menor_5_exp);

<?php if (pg_num_rows ( $res_react_menor_5_arg ) == 0){ ?>
	document.getElementById('container_react_menor_5_exp').style.display = "none";
	document.getElementById('react_menor_5_exp').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* Unidad diagnóstica: Texto literario
*/
var data_txt_literario = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_txt_literario = pg_fetch_assoc($res_txt_literario)){
			if($row_txt_literario['dPorcentajeAspecto4LyC']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_txt_literario['dPorcentajeAspecto4LyC']."%";}
			if($row_txt_literario['dPorcentajeAspecto4LyC']>40 && $row_txt_literario['dPorcentajeAspecto4LyC']<=40){
				$color = "#FB4F57";
			}elseif($row_txt_literario['dPorcentajeAspecto4LyC']>40 && $row_txt_literario['dPorcentajeAspecto4LyC']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_txt_literario['cAspectoEvaluacion']."',".$row_txt_literario['dPorcentajeAspecto4LyC'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_txt_literario = {
tooltip: {trigger: 'none'},
  title: 'Texto Literario',	  
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

var container_txt_literario = document.getElementById('txt_lit');
var chart_txt_literario = new google.visualization.BarChart(container_txt_literario);
google.visualization.events.addListener(chart_txt_literario, 'ready', function () {
container_txt_literario.innerHTML = '<img src="' + chart_txt_literario.getImageURI() + '">';
});
chart_txt_literario.draw(data_txt_literario, options_txt_literario);

<?php if (pg_num_rows ( $res_txt_literario ) == 0){ ?>
	document.getElementById('container_txt_lit').style.display = "none";
	document.getElementById('txt_lit').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Unidad diagnóstica: Texto literario
*/

/*INICIO
* "texto literario"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_lit = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_lit = pg_fetch_assoc($res_react_menor_5_lit)){
			if($row_react_menor_5_lit['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_lit['porcentaje']."%";}
			if($row_react_menor_5_lit['porcentaje']>40 && $row_react_menor_5_lit['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_lit['porcentaje']>40 && $row_react_menor_5_lit['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_lit['concat']."',".$row_react_menor_5_lit['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_lit = {
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

var container_react_menor_5_lit = document.getElementById('react_menor_5_lit');
var chart_react_menor_5_lit = new google.visualization.BarChart(container_react_menor_5_lit);
google.visualization.events.addListener(chart_react_menor_5_lit, 'ready', function () {
container_react_menor_5_lit.innerHTML = '<img src="' + chart_react_menor_5_lit.getImageURI() + '">';
});
chart_react_menor_5_lit.draw(data_react_menor_5_lit, options_react_menor_5_lit);

<?php if (pg_num_rows ( $res_react_menor_5_lit ) == 0){ ?>
	document.getElementById('container_react_menor_5_lit').style.display = "none";
	document.getElementById('react_menor_5_lit').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
	document.getElementById('lyc').style.display = "none";
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* MATEMÁTICAS
* Unidad diagnóstica: Cambios y relaciones
*/
var data_cyr = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_cyr = pg_fetch_assoc($res_cyr)){
			if($row_cyr['dPorcentajeAspecto1Mat']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_cyr['dPorcentajeAspecto1Mat']."%";}
			if($row_cyr['dPorcentajeAspecto1Mat']>0 && $row_cyr['dPorcentajeAspecto1Mat']<=40){
				$color = "#FB4F57";
			}elseif($row_cyr['dPorcentajeAspecto1Mat']>40 && $row_cyr['dPorcentajeAspecto1Mat']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_cyr['cAspectoEvaluacion']."',".$row_cyr['dPorcentajeAspecto1Mat'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_cyr = {
tooltip: {trigger: 'none'},
  title: 'Texto Literario',	  
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

var container_cyr = document.getElementById('txt_cyr');
var chart_cyr = new google.visualization.BarChart(container_cyr);
google.visualization.events.addListener(chart_cyr, 'ready', function () {
container_cyr.innerHTML = '<img src="' + chart_cyr.getImageURI() + '">';
});
chart_cyr.draw(data_cyr, options_cyr);

<?php if (pg_num_rows ( $res_cyr ) == 0){ ?>
	document.getElementById('container_txt_cyr').style.display = "none";
	document.getElementById('txt_cyr').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 70; width: 900 ">Dato No Disponible</div>';
<?php } ?>
/*FIN
* Unidad diagnóstica: Cambios y relaciones
*/

/*INICIO
* "Cambios y relaciones"
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/
var data_react_menor_5_cyr = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_react_menor_5_cyr = pg_fetch_assoc($res_react_menor_5_cyr)){
			if($row_react_menor_5_cyr['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_cyr['porcentaje']."%";}
			if($row_react_menor_5_cyr['porcentaje']>0 && $row_react_menor_5_cyr['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_cyr['porcentaje']>40 && $row_react_menor_5_cyr['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_cyr['concat']."',".$row_react_menor_5_cyr['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_react_menor_5_cyr = {
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

var container_react_menor_5_cyr = document.getElementById('react_menor_5_cyr');
var chart_react_menor_5_cyr = new google.visualization.BarChart(container_react_menor_5_cyr);
google.visualization.events.addListener(chart_react_menor_5_cyr, 'ready', function () {
container_react_menor_5_cyr.innerHTML = '<img src="' + chart_react_menor_5_cyr.getImageURI() + '">';
});
chart_react_menor_5_cyr.draw(data_react_menor_5_cyr, options_react_menor_5_cyr);

<?php if (pg_num_rows ( $res_react_menor_5_cyr ) == 0){ ?>
	document.getElementById('container_react_menor_5_cyr').style.display = "none";
	document.getElementById('react_menor_5_cyr').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 200; width: 1100 ">Dato No Disponible</div>';
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
			if($row_mdi['dPorcentajeAspecto3Mat']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mdi['dPorcentajeAspecto3Mat']."%";}
			if($row_mdi['dPorcentajeAspecto3Mat']>0 && $row_mdi['dPorcentajeAspecto3Mat']<=40){
				$color = "#FB4F57";
			}elseif($row_mdi['dPorcentajeAspecto3Mat']>40 && $row_mdi['dPorcentajeAspecto3Mat']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_mdi['cAspectoEvaluacion']."',".$row_mdi['dPorcentajeAspecto3Mat'].",'".$txtstyle."','".$color."'],";
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
			if($row_react_menor_5_mdi['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_mdi['porcentaje']."%";}
			if($row_react_menor_5_mdi['porcentaje']>0 && $row_react_menor_5_mdi['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_mdi['porcentaje']>40 && $row_react_menor_5_mdi['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_mdi['concat']."',".$row_react_menor_5_mdi['porcentaje'].",'".$txtstyle."','".$color."'],";
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
			if($row_snpa['dPorcentajeAspecto4Mat']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_snpa['dPorcentajeAspecto4Mat']."%";}
			if($row_snpa['dPorcentajeAspecto4Mat']>0 && $row_snpa['dPorcentajeAspecto4Mat']<=40){
				$color = "#FB4F57";
			}elseif($row_snpa['dPorcentajeAspecto4Mat']>40 && $row_snpa['dPorcentajeAspecto4Mat']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_snpa['cAspectoEvaluacion']."',".$row_snpa['dPorcentajeAspecto4Mat'].",'".$txtstyle."','".$color."'],";
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
			if($row_react_menor_5_snpa['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_snpa['porcentaje']."%";}
			if($row_react_menor_5_snpa['porcentaje']>0 && $row_react_menor_5_snpa['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_snpa['porcentaje']>40 && $row_react_menor_5_snpa['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_snpa['concat']."',".$row_react_menor_5_snpa['porcentaje'].",'".$txtstyle."','".$color."'],";
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
			if($row_fem['dPorcentajeAspecto2Mat']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_fem['dPorcentajeAspecto2Mat']."%";}
			if($row_fem['dPorcentajeAspecto2Mat']>0 && $row_fem['dPorcentajeAspecto2Mat']<=40){
				$color = "#FB4F57";
			}elseif($row_fem['dPorcentajeAspecto2Mat']>40 && $row_fem['dPorcentajeAspecto2Mat']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_fem['cAspectoEvaluacion']."',".$row_fem['dPorcentajeAspecto2Mat'].",'".$txtstyle."','".$color."'],";
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
			if($row_react_menor_5_fem['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_react_menor_5_fem['porcentaje']."%";}
			if($row_react_menor_5_fem['porcentaje']>0 && $row_react_menor_5_fem['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_react_menor_5_fem['porcentaje']>40 && $row_react_menor_5_fem['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['".$row_react_menor_5_fem['concat']."',".$row_react_menor_5_fem['porcentaje'].",'".$txtstyle."','".$color."'],";
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
	document.getElementById('matematicas').style.display = "none";
<?php } ?>
/*FIN
* Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático
*/

/*INICIO
* Contexto y expectativas de los alumnos
* Trabaja actualmente
*/
var data_ta = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}],
	<?php
		while($row_ta = pg_fetch_assoc($res_ta)){
			if($row_ta['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_ta['dPorcentajeAlumnosEscuela']."%";}
			//echo "['".$row_ta['cPregunta']."',".$row_ta['dPorcentajeAlumnosEscuela'].",'".$row_ta['dPorcentajeAlumnosEscuela']."%'],";
			echo "['Trabaja actualmente',".$row_ta['dPorcentajeAlumnosEscuela'].",'".$txtstyle."'],";
		}		
	?>
]);

var options_ta = {
tooltip: {trigger: 'none'},
  title: 'Trabaja actualmente',	  
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
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_ta = document.getElementById('txt_ta');
var chart_ta = new google.visualization.BarChart(container_ta);
google.visualization.events.addListener(chart_ta, 'ready', function () {
container_ta.innerHTML = '<img src="' + chart_ta.getImageURI() + '">';
});
chart_ta.draw(data_ta, options_ta);

<?php if (pg_num_rows ( $res_ta ) == 0){ ?>
	document.getElementById('container_txt_ta').style.display = "none";
	document.getElementById('txt_ta').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
	document.getElementById('contexto_escolar').style.display = "none";
<?php } ?>
/*FIN
* Trabaja actualmente
*/

/*INICIO
* Tipo de escuela, por sostenimiento, en que los alumnos estudiaron el último año de secundaria
*/
//res_tes
var data_tes = google.visualization.arrayToDataTable([
	['Pregunta','Publica', {role: 'annotation'}, 'Privada', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_tes ) > 0){
				echo "['',";
				while($row_tes = pg_fetch_assoc($res_tes)){
					if($row_tes['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_tes['dPorcentajeAlumnosEscuela']."%";}
					echo $row_tes['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_tes = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom:20},
  series:{0:{color: '#6ACB9C'},
  1:{color: '#90B0D9'}},
	legend: 'none',
	height: 70,
  width: 1500,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_tes = document.getElementById('comp_tes');
var chart_tes = new google.visualization.BarChart(container_tes);
google.visualization.events.addListener(chart_tes, 'ready', function () {
container_tes.innerHTML = '<img src="' + chart_tes.getImageURI() + '">';
});
chart_tes.draw(data_tes, options_tes);

<?php if (pg_num_rows ( $res_tes ) == 0){ ?>
	document.getElementById('container_comp_tes').style.display = "none";
	document.getElementById('comp_tes').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Tipo de escuela, por sostenimiento, en que los alumnos estudiaron el último año de secundaria
*/

/*INICIO
* Tipo de escuela, por tipo de servicio, en que los alumnos estudiaron el último año de secundaria ----
*/
var data_tets = google.visualization.arrayToDataTable([
	['Pregunta','General o técnica', {role: 'annotation'}, 'Telesecundaria', {role: 'annotation'},'Abierta, para adultos, para trabajadores, Ceneval o INEA', {role: 'annotation'}, 'Comunitaria', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_tets ) > 0){
				echo "['',";
				while($row_tets = pg_fetch_assoc($res_tets)){
					if($row_tets['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_tets['dPorcentajeAlumnosEscuela']."%";}
					echo $row_tets['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_tets = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom:20},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1500,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_tets = document.getElementById('comp_tets');
var chart_tets = new google.visualization.BarChart(container_tets);
google.visualization.events.addListener(chart_tets, 'ready', function () {
container_tets.innerHTML = '<img src="' + chart_tets.getImageURI() + '">';
});
chart_tets.draw(data_tets, options_tets);

<?php if (pg_num_rows ( $res_tets ) == 0){ ?>
	document.getElementById('container_comp_tets').style.display = "none";
	document.getElementById('comp_tets').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Tipo de escuela, por tipo de servicio, en que los alumnos estudiaron el último año de secundaria ----
*/

/*INICIO
* Nivel máximo de estudios que esperan alcanzar ----
*/
var data_nme = google.visualization.arrayToDataTable([
	['Pregunta','Media superior', {role: 'annotation'}, 'Técnico superior', {role: 'annotation'},'Licenciatura', {role: 'annotation'}, 'Posgrado', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_nme ) > 0){
				echo "['',";
				while($row_nme = pg_fetch_assoc($res_nme)){
					if($row_nme['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_nme['dPorcentajeAlumnosEscuela']."%";}
					echo $row_nme['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_nme = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0,
		bottom:20},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1500,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_nme = document.getElementById('comp_nme');
var chart_nme = new google.visualization.BarChart(container_nme);
google.visualization.events.addListener(chart_nme, 'ready', function () {
container_nme.innerHTML = '<img src="' + chart_nme.getImageURI() + '">';
});
chart_nme.draw(data_nme, options_nme);

<?php if (pg_num_rows ( $res_nme ) == 0){ ?>
	document.getElementById('comp_nme').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Nivel máximo de estudios que esperan alcanzar ----
*/

/*INICIO
* Entorno escolar
* --- Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje --
*/
var data_esp_act1 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_esp_act1 ) > 0){
				echo "['Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje',";
				while($row_esp_act1 = pg_fetch_assoc($res_esp_act1)){
					if($row_esp_act1['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esp_act1['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esp_act1['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_esp_act1 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esp_act1 = document.getElementById('comp_esp_act1');
var chart_esp_act1 = new google.visualization.BarChart(container_esp_act1);
google.visualization.events.addListener(chart_esp_act1, 'ready', function () {
container_esp_act1.innerHTML = '<img src="' + chart_esp_act1.getImageURI() + '">';
});
chart_esp_act1.draw(data_esp_act1, options_esp_act1);

<?php if (pg_num_rows ( $res_esp_act1 ) == 0){ ?>
	document.getElementById('comp_esp_act1').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Entorno escolar
* --- Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje --
*/

/*INICIO
* Entorno escolar
* -- Relaciona sus conocimientos previos con los nuevos --
*/
var data_esp_act2 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_esp_act2 ) > 0){
				echo "['Relaciona sus conocimientos previos con los nuevos',";
				while($row_esp_act2 = pg_fetch_assoc($res_esp_act2)){
					if($row_esp_act2['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esp_act2['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esp_act2['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_esp_act2 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esp_act2 = document.getElementById('comp_esp_act2');
var chart_esp_act2 = new google.visualization.BarChart(container_esp_act2);
google.visualization.events.addListener(chart_esp_act2, 'ready', function () {
container_esp_act2.innerHTML = '<img src="' + chart_esp_act2.getImageURI() + '">';
});
chart_esp_act2.draw(data_esp_act2, options_esp_act2);

<?php if (pg_num_rows ( $res_esp_act2 ) == 0){ ?>
	document.getElementById('comp_esp_act2').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Entorno escolar
* -- Relaciona sus conocimientos previos con los nuevos --
*/

/*INICIO
* Entorno escolar
* --- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
*/
var data_esp_act3 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_esp_act3 ) > 0){
				echo "['Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, discutan y formulen preguntas',";
				while($row_esp_act3 = pg_fetch_assoc($res_esp_act3)){
					if($row_esp_act3['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esp_act3['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esp_act3['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_esp_act3 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esp_act3 = document.getElementById('comp_esp_act3');
var chart_esp_act3 = new google.visualization.BarChart(container_esp_act3);
google.visualization.events.addListener(chart_esp_act3, 'ready', function () {
container_esp_act3.innerHTML = '<img src="' + chart_esp_act3.getImageURI() + '">';
});
chart_esp_act3.draw(data_esp_act3, options_esp_act3);

<?php if (pg_num_rows ( $res_esp_act3 ) == 0){ ?>
	document.getElementById('comp_esp_act3').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Entorno escolar
* --- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
*/

/*INICIO
* Entorno escolar
* -- Ayuda a los alumnos que tienen dificultades con algún tema --
*/
var data_esp_act4 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_esp_act4 ) > 0){
				echo "['Ayuda a los alumnos que tienen dificultades con algún tema',";
				while($row_esp_act4 = pg_fetch_assoc($res_esp_act4)){
					if($row_esp_act4['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esp_act4['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esp_act4['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_esp_act4 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esp_act4 = document.getElementById('comp_esp_act4');
var chart_esp_act4 = new google.visualization.BarChart(container_esp_act4);
google.visualization.events.addListener(chart_esp_act4, 'ready', function () {
container_esp_act4.innerHTML = '<img src="' + chart_esp_act4.getImageURI() + '">';
});
chart_esp_act4.draw(data_esp_act4, options_esp_act4);

<?php if (pg_num_rows ( $res_esp_act4 ) == 0){ ?>
	document.getElementById('comp_esp_act4').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Entorno escolar
* -- Ayuda a los alumnos que tienen dificultades con algún tema --
*/
/*INICIO
* Entorno escolar
* -- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
*/
var data_esp_act5 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php if (pg_num_rows ( $res_esp_act5 ) > 0){
				echo "['Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje',";
				while($row_esp_act5 = pg_fetch_assoc($res_esp_act5)){
					if($row_esp_act5['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esp_act5['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esp_act5['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				}?>
]);

	var options_esp_act5 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esp_act5 = document.getElementById('comp_esp_act5');
var chart_esp_act5 = new google.visualization.BarChart(container_esp_act5);
google.visualization.events.addListener(chart_esp_act5, 'ready', function () {
container_esp_act5.innerHTML = '<img src="' + chart_esp_act5.getImageURI() + '">';
});
chart_esp_act5.draw(data_esp_act5, options_esp_act5);

<?php if (pg_num_rows ( $res_esp_act5 ) == 0){ ?>
	document.getElementById('comp_esp_act5').innerHTML = '<div style="margin-left: auto; margin-right: auto; height: 50; width: 900" >Dato No Disponible<sup>1</sup></div>';
<?php } ?>
/*FIN
* Entorno escolar
* -- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
*/

/*INICIO
* Entorno escolar Matemáticas
* --- Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje --
*/
var data_mate_act1 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje',";
				while($row_mate_act1 = pg_fetch_assoc($res_mate_act1)){
					if($row_mate_act1['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mate_act1['dPorcentajeAlumnosEscuela']."%";}
					echo $row_mate_act1['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_mate_act1 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mate_act1 = document.getElementById('comp_mate_act1');
var chart_mate_act1 = new google.visualization.BarChart(container_mate_act1);
google.visualization.events.addListener(chart_mate_act1, 'ready', function () {
container_mate_act1.innerHTML = '<img src="' + chart_mate_act1.getImageURI() + '">';
});
chart_mate_act1.draw(data_mate_act1, options_mate_act1);

/*FIN
* Entorno escolar Matemáticas
* --- Utiliza ejemplos cercanos a la realidad (vida diaria) para ayudar a su aprendizaje --
*/

/*INICIO
* Entorno escolar Matemáticas
* -- Relaciona sus conocimientos previos con los nuevos --
*/
var data_mate_act2 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Relaciona sus conocimientos previos con los nuevos',";
				while($row_mate_act2 = pg_fetch_assoc($res_mate_act2)){
					if($row_mate_act2['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mate_act2['dPorcentajeAlumnosEscuela']."%";}
					echo $row_mate_act2['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_mate_act2 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mate_act2 = document.getElementById('comp_mate_act2');
var chart_mate_act2 = new google.visualization.BarChart(container_mate_act2);
google.visualization.events.addListener(chart_mate_act2, 'ready', function () {
container_mate_act2.innerHTML = '<img src="' + chart_mate_act2.getImageURI() + '">';
});
chart_mate_act2.draw(data_mate_act2, options_mate_act2);

/*FIN
* Entorno escolar Matemáticas
* -- Relaciona sus conocimientos previos con los nuevos --
*/

/*INICIO
* Entorno escolar Matemáticas
* --- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
*/
var data_mate_act3 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, discutan y formulen preguntas',";
				while($row_mate_act3 = pg_fetch_assoc($res_mate_act3)){
					if($row_mate_act3['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mate_act3['dPorcentajeAlumnosEscuela']."%";}
					echo $row_mate_act3['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_mate_act3 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mate_act3 = document.getElementById('comp_mate_act3');
var chart_mate_act3 = new google.visualization.BarChart(container_mate_act3);
google.visualization.events.addListener(chart_mate_act3, 'ready', function () {
container_mate_act3.innerHTML = '<img src="' + chart_mate_act3.getImageURI() + '">';
});
chart_mate_act3.draw(data_mate_act3, options_mate_act3);

/*FIN
* Entorno escolar Matemáticas
* --- Estimula la participación de los alumnos, los anima(n) a que expresen sus opiniones, disc (...) --
*/

	/*INICIO
* Entorno escolar Matemáticas
* -- Ayuda a los alumnos que tienen dificultades con algún tema --
*/
var data_mate_act4 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Ayuda a los alumnos que tienen dificultades con algún tema',";
				while($row_mate_act4 = pg_fetch_assoc($res_mate_act4)){
					if($row_mate_act4['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mate_act4['dPorcentajeAlumnosEscuela']."%";}
					echo $row_mate_act4['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_mate_act4 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
	series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mate_act4 = document.getElementById('comp_mate_act4');
var chart_mate_act4 = new google.visualization.BarChart(container_mate_act4);
google.visualization.events.addListener(chart_mate_act4, 'ready', function () {
container_mate_act4.innerHTML = '<img src="' + chart_mate_act4.getImageURI() + '">';
});
chart_mate_act4.draw(data_mate_act4, options_mate_act4);

/*FIN
* Entorno escolar Matemáticas
* -- Ayuda a los alumnos que tienen dificultades con algún tema --
*/
/*INICIO
* Entorno escolar Matemáticas
* -- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
*/
var data_mate_act5 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje',";
				while($row_mate_act5 = pg_fetch_assoc($res_mate_act5)){
					if($row_mate_act5['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_mate_act5['dPorcentajeAlumnosEscuela']."%";}
					echo $row_mate_act5['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_mate_act5 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 80,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_mate_act5 = document.getElementById('comp_mate_act5');
var chart_mate_act5 = new google.visualization.BarChart(container_mate_act5);
google.visualization.events.addListener(chart_mate_act5, 'ready', function () {
container_mate_act5.innerHTML = '<img src="' + chart_mate_act5.getImageURI() + '">';
});
chart_mate_act5.draw(data_mate_act5, options_mate_act5);

/*FIN
* Entorno escolar Matemáticas
* -- Emplea mapas conceptuales, cuadros sinópticos y esquemas para facilitar el aprendizaje --
*/

/*INICIO
* Clima y convivencia escolar
* -- ¿Consideras a tu escuela un lugar seguro? --
*/
var data_esc_segura = google.visualization.arrayToDataTable([
	['Pregunta','Nada seguro', {role: 'annotation'}, 'Poco seguro', {role: 'annotation'},'Seguro', {role: 'annotation'}, 'Muy seguro', {role: 'annotation'}],
				<?php
				echo "['¿Consideras a tu escuela un lugar seguro?',";
				while($row_esc_segura = pg_fetch_assoc($res_esc_segura)){
					if($row_esc_segura['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_segura['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_segura['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_segura = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_segura = document.getElementById('clima_esc_segura');
var chart_esc_segura = new google.visualization.BarChart(container_esc_segura);
google.visualization.events.addListener(chart_esc_segura, 'ready', function () {
container_esc_segura.innerHTML = '<img src="' + chart_esc_segura.getImageURI() + '">';
});
chart_esc_segura.draw(data_esc_segura, options_esc_segura);

/*FIN
* Clima y convivencia escolar
* -- ¿Consideras a tu escuela un lugar seguro? --
*/

/*INICIO
* Clima y convivencia escolar
* ---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
*-- Entre estudiantes --
*/
var data_esc_opin1 = google.visualization.arrayToDataTable([
	['Pregunta','Mala', {role: 'annotation'}, 'Regular', {role: 'annotation'},'Buena', {role: 'annotation'}, 'Excelente', {role: 'annotation'}],
				<?php
				echo "['Entre estudiantes',";
				while($row_esc_opin1 = pg_fetch_assoc($res_esc_opin1)){
					if($row_esc_opin1['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_opin1['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_opin1['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_opin1 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_opin1 = document.getElementById('clima_esc_opin1');
var chart_esc_opin1 = new google.visualization.BarChart(container_esc_opin1);
google.visualization.events.addListener(chart_esc_opin1, 'ready', function () {
container_esc_opin1.innerHTML = '<img src="' + chart_esc_opin1.getImageURI() + '">';
});
chart_esc_opin1.draw(data_esc_opin1, options_esc_opin1);

/*FIN
* Clima y convivencia escolar
*---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
*-- Entre estudiantes --
*/

/*INICIO
* Clima y convivencia escolar
*---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
*-- Entre estudiantes y docentes --
*/
var data_esc_opin2 = google.visualization.arrayToDataTable([
	['Pregunta','Mala', {role: 'annotation'}, 'Regular', {role: 'annotation'},'Buena', {role: 'annotation'}, 'Excelente', {role: 'annotation'}],
				<?php
				echo "['Entre estudiantes y docentes',";
				while($row_esc_opin2 = pg_fetch_assoc($res_esc_opin2)){
					if($row_esc_opin2['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_opin2['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_opin2['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_opin2 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#FB4F57'},
  1:{color: '#FDD16C'},
  2:{color: '#6ACB9C'},
  3:{color: '#90B0D9'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_opin2 = document.getElementById('clima_esc_opin2');
var chart_esc_opin2 = new google.visualization.BarChart(container_esc_opin2);
google.visualization.events.addListener(chart_esc_opin2, 'ready', function () {
container_esc_opin2.innerHTML = '<img src="' + chart_esc_opin2.getImageURI() + '">';
});
chart_esc_opin2.draw(data_esc_opin2, options_esc_opin2);

/*FIN
* Clima y convivencia escolar
*---- Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar: ----
*-- Entre estudiantes y docentes --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Insultan, ofenden o ridiculizan a sus compañeros --
*/
var data_esc_clima_convg4 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Insultan, ofenden o ridiculizan a sus compañeros',";
				while($row_esc_clima_convg4 = pg_fetch_assoc($res_esc_clima_convg4)){
					if($row_esc_clima_convg4['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg4['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg4['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg4 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg4 = document.getElementById('clima_convg4');
var chart_esc_clima_convg4 = new google.visualization.BarChart(container_esc_clima_convg4);
google.visualization.events.addListener(chart_esc_clima_convg4, 'ready', function () {
container_esc_clima_convg4.innerHTML = '<img src="' + chart_esc_clima_convg4.getImageURI() + '">';
});
chart_esc_clima_convg4.draw(data_esc_clima_convg4, options_esc_clima_convg4);

/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Insultan, ofenden o ridiculizan a sus compañeros --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Destruyen el mobiliario o dañan las instalaciones --
*/
var data_esc_clima_convg5 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Destruyen el mobiliario o dañan las instalaciones',";
				while($row_esc_clima_convg5 = pg_fetch_assoc($res_esc_clima_convg5)){
					if($row_esc_clima_convg5['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg5['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg5['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg5 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg5 = document.getElementById('clima_convg5');
var chart_esc_clima_convg5 = new google.visualization.BarChart(container_esc_clima_convg5);
google.visualization.events.addListener(chart_esc_clima_convg5, 'ready', function () {
container_esc_clima_convg5.innerHTML = '<img src="' + chart_esc_clima_convg5.getImageURI() + '">';
});
chart_esc_clima_convg5.draw(data_esc_clima_convg5, options_esc_clima_convg5);

/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Destruyen el mobiliario o dañan las instalaciones --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Llevan armas (navajas, cuchillos, pistolas) --
*/
var data_esc_clima_convg6 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Llevan armas (navajas, cuchillos, pistolas)',";
				while($row_esc_clima_convg6 = pg_fetch_assoc($res_esc_clima_convg6)){
					if($row_esc_clima_convg6['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg6['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg6['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg6 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg6 = document.getElementById('clima_convg6');
var chart_esc_clima_convg6 = new google.visualization.BarChart(container_esc_clima_convg6);
google.visualization.events.addListener(chart_esc_clima_convg6, 'ready', function () {
container_esc_clima_convg6.innerHTML = '<img src="' + chart_esc_clima_convg6.getImageURI() + '">';
});
chart_esc_clima_convg6.draw(data_esc_clima_convg6, options_esc_clima_convg6);

/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Llevan armas (navajas, cuchillos, pistolas) --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Se roban las pertenencias de los estudiantes --
*/
var data_esc_clima_convg7 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Se roban las pertenencias de los estudiantes',";
				while($row_esc_clima_convg7 = pg_fetch_assoc($res_esc_clima_convg7)){
					if($row_esc_clima_convg7['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg7['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg7['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg7 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg7 = document.getElementById('clima_convg7');
var chart_esc_clima_convg7 = new google.visualization.BarChart(container_esc_clima_convg7);
google.visualization.events.addListener(chart_esc_clima_convg7, 'ready', function () {
container_esc_clima_convg7.innerHTML = '<img src="' + chart_esc_clima_convg7.getImageURI() + '">';
});
chart_esc_clima_convg7.draw(data_esc_clima_convg7, options_esc_clima_convg7);

/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Se roban las pertenencias de los estudiantes --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Lesionan o lastiman a otros estudiantes --
*/
var data_esc_clima_convg8 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Lesionan o lastiman a otros estudiantes',";
				while($row_esc_clima_convg8 = pg_fetch_assoc($res_esc_clima_convg8)){
					if($row_esc_clima_convg8['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg8['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg8['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg8 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg8 = document.getElementById('clima_convg8');
var chart_esc_clima_convg8 = new google.visualization.BarChart(container_esc_clima_convg8);
google.visualization.events.addListener(chart_esc_clima_convg8, 'ready', function () {
container_esc_clima_convg8.innerHTML = '<img src="' + chart_esc_clima_convg8.getImageURI() + '">';
});
chart_esc_clima_convg8.draw(data_esc_clima_convg8, options_esc_clima_convg8);

/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Lesionan o lastiman a otros estudiantes --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Golpean o empujan a otros estudiantes --
*/
var data_esc_clima_convg9 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Golpean o empujan a otros estudiantes',";
				while($row_esc_clima_convg9 = pg_fetch_assoc($res_esc_clima_convg9)){
					if($row_esc_clima_convg9['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg9['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg9['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg9 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg9 = document.getElementById('clima_convg9');
var chart_esc_clima_convg9 = new google.visualization.BarChart(container_esc_clima_convg9);
google.visualization.events.addListener(chart_esc_clima_convg9, 'ready', function () {
container_esc_clima_convg9.innerHTML = '<img src="' + chart_esc_clima_convg9.getImageURI() + '">';
});
chart_esc_clima_convg9.draw(data_esc_clima_convg9, options_esc_clima_convg9);
/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Golpean o empujan a otros estudiantes --
*/

/*INICIO
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Consumen droga --
*/
var data_esc_clima_convg10 = google.visualization.arrayToDataTable([
	['Pregunta','Nunca', {role: 'annotation'}, 'Pocas veces', {role: 'annotation'},'Muchas veces', {role: 'annotation'}, 'Siempre', {role: 'annotation'}],
				<?php
				echo "['Consumen droga',";
				while($row_esc_clima_convg10 = pg_fetch_assoc($res_esc_clima_convg10)){
					if($row_esc_clima_convg10['dPorcentajeAlumnosEscuela']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_esc_clima_convg10['dPorcentajeAlumnosEscuela']."%";}
					echo $row_esc_clima_convg10['dPorcentajeAlumnosEscuela'].",'".$txtstyle."',";
				}
				echo "]";
				?>
]);

	var options_esc_clima_convg10 = {
	tooltip: {trigger: 'none'},
	hAxis: {
		minValue: 0,
		ticks: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]},
	vAxis: {
		gridlines: {color: 'transparent'}},
	bar:{groupWidth: '80%'},
	annotations:{textStyle: {fontSize: 10, }},
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  series:{0:{color: '#90B0D9'},
  1:{color: '#6ACB9C'},
  2:{color: '#FDD16C'},
  3:{color: '#FB4F57'}},
	legend: 'none',
	width: 1100,
	height: 70,
	isStacked:true
};  

// Instantiate and draw the chart.
var container_esc_clima_convg10 = document.getElementById('clima_convg10');
var chart_esc_clima_convg10 = new google.visualization.BarChart(container_esc_clima_convg10);
google.visualization.events.addListener(chart_esc_clima_convg10, 'ready', function () {
container_esc_clima_convg10.innerHTML = '<img src="' + chart_esc_clima_convg10.getImageURI() + '">';
});
chart_esc_clima_convg10.draw(data_esc_clima_convg10, options_esc_clima_convg10);
/*FIN
* Clima y convivencia escolar
*---- Frecuencia con la que los estudiantes de la escuela: ----
*-- Consumen droga --
*/

/*INICIO
* Estadistica básica por zona escolar
*-- Matrícula por grado y ciclo escolar --
*/
	var data = new google.visualization.DataTable();
        data.addColumn('string', 'Grado');
        data.addColumn('string', '2014/2015');
				data.addColumn('string', '2015/2016');
				data.addColumn('string', '2016/2017');
        data.addRows([
					<?php
					while($row_matricula_escolar = pg_fetch_assoc($res_matricula_escolar)){
						if($row_matricula_escolar['2016/2017']<0){$txtstyle1617="Dato no disponible";}else{$txtstyle1617=$row_matricula_escolar['2016/2017'];}
						if($row_matricula_escolar['2015/2016']<0){$txtstyle1516="Dato no disponible";}else{$txtstyle1516=$row_matricula_escolar['2015/2016'];}
						if($row_matricula_escolar['2014/2015']<0){$txtstyle1415="Dato no disponible";}else{$txtstyle1415=$row_matricula_escolar['2014/2015'];}
						echo "['".$row_matricula_escolar['Grado']."','".$txtstyle1415."','".$txtstyle1516."','".$txtstyle1617."'],";
					}
					?>
        ]);

				//var formatter = new google.visualization.BarFormat({width: 120});
				//formatter.format(data, 1); // Apply formatter to second column

				var options_matricula_escolar = 
				{allowHtml: true, 
					showRowNumber: false, 
					width: '800',
					annotations:{
	  				textStyle: {fontSize: 10}
					}
				};

				var container_matricula_escolar = document.getElementById('matricula_grado_esc');

        var chart_matricula_escolar = new google.visualization.Table(container_matricula_escolar);

        chart_matricula_escolar.draw(data, options_matricula_escolar);
/*FIN
* Estadistica básica por zona escolar
*-- Matrícula por grado y ciclo escolar --
*/

/*INICIO
* Estadistica básica por zona escolar
* ---- Matriculación oportuna ----
*/
var data_matricula_oportuna = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_matricula_oportuna = pg_fetch_assoc($res_matricula_oportuna)){
			if($row_matricula_oportuna['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_matricula_oportuna['porcentaje']."%";}
			if($row_matricula_oportuna['porcentaje']>0 && $row_matricula_oportuna['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_matricula_oportuna['porcentaje']>40 && $row_matricula_oportuna['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['Matriculación oportuna',".$row_matricula_oportuna['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_matricula_oportuna = {
tooltip: {trigger: 'none'},
  title: 'Matriculación oportuna',	  
  height: 80,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  hAxis: {
		minValue: 0, ticks: [0, 20, 40, 60, 80, 100]},
  series:{
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_matricula_oportuna = document.getElementById('anvc_esc_g1');
var chart_matricula_oportuna = new google.visualization.BarChart(container_matricula_oportuna);
google.visualization.events.addListener(chart_matricula_oportuna, 'ready', function () {
container_matricula_oportuna.innerHTML = '<img src="' + chart_matricula_oportuna.getImageURI() + '">';
});
chart_matricula_oportuna.draw(data_matricula_oportuna, options_matricula_oportuna);

/*FIN
* Estadistica básica por zona escolar
*---- Matriculación oportuna ----
*/

/*INICIO
* Estadistica básica por zona escolar
* -- Alumnos en edad idónea y extraedead ligera --
*/
var data_matricula_ido_ext = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_matricula_ido_ext = pg_fetch_assoc($res_matricula_ido_ext)){
			if($row_matricula_ido_ext['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_matricula_ido_ext['porcentaje']."%";}
			if($row_matricula_ido_ext['porcentaje']>0 && $row_matricula_ido_ext['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_matricula_ido_ext['porcentaje']>40 && $row_matricula_ido_ext['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['Alumnos en edad idónea y extraedead ligera',".$row_matricula_ido_ext['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_matricula_ido_ext = {
tooltip: {trigger: 'none'},
  title: 'Alumnos en edad idónea y extraedead ligera',	  
  height: 80,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  hAxis: {
		minValue: 0, ticks: [0, 20, 40, 60, 80, 100]},
  series:{
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_matricula_ido_ext = document.getElementById('anvc_esc_g2');
var chart_matricula_ido_ext = new google.visualization.BarChart(container_matricula_ido_ext);
google.visualization.events.addListener(chart_matricula_ido_ext, 'ready', function () {
container_matricula_ido_ext.innerHTML = '<img src="' + chart_matricula_ido_ext.getImageURI() + '">';
});
chart_matricula_ido_ext.draw(data_matricula_ido_ext, options_matricula_ido_ext);

/*FIN
* Estadistica básica por zona escolar
*--- Alumnos en edad idónea y extraedead ligera ----
*/

/*INICIO
* Estadistica básica por zona escolar
* -- Aprobación al final del ciclo escolar --
*/
var data_matricula_apro_ciclo = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_matricula_apro_ciclo = pg_fetch_assoc($res_matricula_apro_ciclo)){
			if($row_matricula_apro_ciclo['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_matricula_apro_ciclo['porcentaje']."%";}
			if($row_matricula_apro_ciclo['porcentaje']>0 && $row_matricula_apro_ciclo['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_matricula_apro_ciclo['porcentaje']>40 && $row_matricula_apro_ciclo['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['Aprobación al final del ciclo escolar',".$row_matricula_apro_ciclo['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_matricula_apro_ciclo = {
tooltip: {trigger: 'none'},
  title: 'Aprobación al final del ciclo escolar',	  
  height: 80,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  hAxis: {
		minValue: 0, ticks: [0, 20, 40, 60, 80, 100]},
  series:{
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_matricula_apro_ciclo = document.getElementById('anvc_esc_g3');
var chart_matricula_apro_ciclo = new google.visualization.BarChart(container_matricula_apro_ciclo);
google.visualization.events.addListener(chart_matricula_apro_ciclo, 'ready', function () {
container_matricula_apro_ciclo.innerHTML = '<img src="' + chart_matricula_apro_ciclo.getImageURI() + '">';
});
chart_matricula_apro_ciclo.draw(data_matricula_apro_ciclo, options_matricula_apro_ciclo);
/*FIN
* Estadistica básica por zona escolar
*-- Aprobación al final del ciclo escolar --
*/

/*INICIO
* Estadistica básica por zona escolar
* -- Aprobación despues del periodo de regularización --
*/
var data_matricula_apro_reg = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_matricula_apro_reg = pg_fetch_assoc($res_matricula_apro_reg)){
			if($row_matricula_apro_reg['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_matricula_apro_reg['porcentaje']."%";}
			if($row_matricula_apro_reg['porcentaje']>0 && $row_matricula_apro_reg['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_matricula_apro_reg['porcentaje']>40 && $row_matricula_apro_reg['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['Aprobación despues del periodo de regularización',".$row_matricula_apro_reg['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_matricula_apro_reg = {
tooltip: {trigger: 'none'},
  title: 'Aprobación despues del periodo de regularización',	  
  height: 80,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  hAxis: {
		minValue: 0, ticks: [0, 20, 40, 60, 80, 100]},
  series:{
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_matricula_apro_reg = document.getElementById('anvc_esc_g4');
var chart_matricula_apro_reg = new google.visualization.BarChart(container_matricula_apro_reg);
google.visualization.events.addListener(chart_matricula_apro_reg, 'ready', function () {
container_matricula_apro_reg.innerHTML = '<img src="' + chart_matricula_apro_reg.getImageURI() + '">';
});
chart_matricula_apro_reg.draw(data_matricula_apro_reg, options_matricula_apro_reg);
/*FIN
* Estadistica básica por zona escolar
*-- Aprobación despues del periodo de regularización --
*/

/*INICIO
* Estadistica básica por zona escolar
* -- Retención intracurricular --
*/
var data_matricula_reten = google.visualization.arrayToDataTable([
	['Eje', 'Nombre', {role: 'annotation'}, { role: 'style' }],
	<?php
		while($row_matricula_reten = pg_fetch_assoc($res_matricula_reten)){
			if($row_matricula_reten['porcentaje']<0){$txtstyle="Dato no disponible";}else{$txtstyle=$row_matricula_reten['porcentaje']."%";}
			if($row_matricula_reten['porcentaje']>0 && $row_matricula_reten['porcentaje']<=40){
				$color = "#FB4F57";
			}elseif($row_matricula_reten['porcentaje']>40 && $row_matricula_reten['porcentaje']<=60){
				$color = "#FDD16C";
			}else{
				$color = "#6ACB9C";
			}
			echo "['Retención intracurricular',".$row_matricula_reten['porcentaje'].",'".$txtstyle."','".$color."'],";
		}		
	?>
]);

var options_matricula_reten = {
tooltip: {trigger: 'none'},
  title: 'Retención intracurricular',	  
  height: 80,
  width: 900,
  legend: 'none',
  chartArea: {
	  left: 500,
	  right: 50,
	  top:0},
  hAxis: {
		minValue: 0, ticks: [0, 20, 40, 60, 80, 100]},
  series:{
	  0:{color: '#FDD16C'}
},
  annotations:{
	  textStyle: {fontSize: 10}
  }
};

var container_matricula_reten = document.getElementById('anvc_esc_g5');
var chart_matricula_reten = new google.visualization.BarChart(container_matricula_reten);
google.visualization.events.addListener(chart_matricula_reten, 'ready', function () {
container_matricula_reten.innerHTML = '<img src="' + chart_matricula_reten.getImageURI() + '">';
});
chart_matricula_reten.draw(data_matricula_reten, options_matricula_reten);
/*FIN
* Estadistica básica por zona escolar
*-- Retención intracurricular --
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
								<?php echo $nivel;?>
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
								<?php echo $zona;?>
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
								<td class="td_just">La <strong>MEJOREDU</strong> concibe a la educación como un derecho de todos los niños, niñas, adolescentes y jóvenes que implica asegurarles el acceso, tránsito y permanencia a los centros escolares, así como un aprendizaje pertinente, significativo y relevante. La valoración de este aprendizaje está articulada en una relación en donde las evidencias de la evaluación sirvan a los centros escolares y a las autoridades educativas a generar orientaciones que permitan a los alumnos aprender más y mejor.<br/>
Es por ello que con el propósito de ayudar a las escuelas a identificar <strong>las fortalezas y oportunidades respecto al aprendizaje de los estudiantes y generar orientaciones que promuevan y faciliten procesos de mejora a través de identificar sus necesidades, retos y avances en los logros alcanzados,</strong> se presenta este reporte escolar con información de la última aplicación de la prueba PLANEA. Este reporte se generó para todas las escuelas secundarias que participaron en la aplicación de la prueba en 2019 e integra información sistematizada de logro educativo de cada centro escolar, así como información más detallada sobre aquellos temas que los resultados sugieren que los alumnos no dominan por completo.<br/>
<br/>
<strong>Los resultados presentados en este reporte reflejan en forma confiable el logro que obtuvo el conjunto de los alumnos del último grado en cada secundaria, aun cuando en algunas escuelas sólo se aplicó a una muestra de sus estudiantes.</strong> Así mismo, los datos que se ofrecen deben verse como el resultado acumulado del proceso de aprendizaje de los estudiantes de la escuela a lo largo de los seis años de primaria y los tres de secundaria, pues reflejan el trabajo y esfuerzo de todo el equipo docente, además de las condiciones particulares de los estudiantes.
<br/>
<br/>
En la lógica de la mejora continua, se espera que estos reportes sean uno de los múltiples apoyos que ayuden a los centros escolares a emprender procesos que propicien los ajustes o cambios que se requieren en la práctica para satisfacer sus necesidades, afrontar los retos y sostener o acrecentar los avances logrados en el aprendizaje de los alumnos. <strong>Se sugiere que, con base en esta información, en el conocimiento de las condiciones escolares y en el marco de una reflexión colectiva, el personal docente y directivo de cada escuela defina las acciones a seguir.</strong> Es importante considerar que, para fortalecer los aprendizajes y habilidades que se identifiquen como susceptibles de mejora, será necesario reforzar no sólo las acciones en el sexto grado, sino también en los grados previos en los que se aporten las bases académicas para alcanzar las que mide PLANEA.
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
							<td class="td_just"><strong>A.</strong> El <strong>porcentaje de alumnos en cada uno de los niveles de logro</strong>, tanto en Lenguaje y Comunicación como en Matemáticas en 2017.</th>
						</tr>
							<tr>
								<td class="td_just">
								<strong>B.</strong> El <strong>comparativo de los resultados</strong> con escuelas similares, en la entidad, en el mismo subsistema y con el promedio de todas las escuelas del país.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>C.</strong> Una <strong>comparación del puntaje promedio obtenido por el centro escolar con el promedio de todas las escuelas de su mismo subsistema en la entidad</strong>.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>D.</strong> Las <strong>prioridades de atención académica</strong>, con base en los reactivos en los que los estudiantes obtuvieron menor porcentaje de aciertos por cada eje temático, en los dos campos de conocimiento evaluados</strong>.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
								<strong>E.</strong> La información sobre el <strong>contexto y expectativas de los alumno y del entorno y clima escolar</strong> derivada del cuestionario que fue aplicado a los alumnos que participaron en la prueba.
							  </td>
							</tr>
							<tr>
							  <td class="td_just">
									<strong>F.</strong> La <strong>información sobre docentes, grupos y alumnos</strong> por grado escolar, así como los porcentajes de aprobación y, de alumnos en edad idónea o un año más por encima de la idónea de la escuela , como una referencia general.
							  </td>
							</tr>
						</table>
			  		</div>		
						<br/>
						<div class="large_text">
								<p class="large_text">
											Usted podrá encontrar información complementaria, que incluye los resultados de su escuela en cada uno de los reactivos y las especificaciones técnicas de cada pregunta, en el portal del INEE (<a  href="http://www.inee.edu.mx">www.inee.edu.mx</a>). Sírvase hacernos llegar sus comentarios y sugerencias al correo <a href='mailto: apoyosupervisores@inee.edu.mx'>apoyosupervisores@inee.edu.mx</a>
								</p>
						</div>	

	</div>
		<!--Fin de página 1-->
	<div class="saltopagina"></div>


	<!--Inicio de página 2-->
	<div class="page_container">

	<!--Inicio del encabezado de página-->	
			<div class="header">
						<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$row['cNombreEntidad']];?>.png" class="elem_center">
			</div>
	<!--Fini del encabezado de página-->	
			<div class="container">
				<h4 class="section_tittle">Prioridades de atención académica</h4>
			</div>

		<div class="first_graph_container">
			<table>
					<tr>
							<td>
								<div id="comp_planea_lyc"></div>
							</td>
							<td>
								<div id="comp_planea_mat"></div>
							</td>
					</tr>
			</table>
		</div>				
			
		<div class=" container bullet_container">
			<span class="bullet_text"><img src="./imagenes/widgets/nivel4.png">&nbsp;Nivel IV Dominio sobresaliente</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel3.png">&nbsp;Nivel III Dominio satisfactorio</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel2.png">&nbsp;Nivel II Dominio básico</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel1.png">&nbsp;Nivel I Dominio insuficiente</span>
		</div>
		
		
		<div class="container">
			<h4 class="section_tittle">Comparativo con escuelas promedio de la entidad y del país</h4>
		</div>

		<!--Separador de contenido-->
			<div class="split">
				<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
			</div>
		
		<div>
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
			
				
				<div class=" container bullet_container">
							<span class="bullet_text"><img src="./imagenes/widgets/nivel4.png">&nbsp;Nivel IV Dominio sobresaliente</span>
							<span class="bullet_text"><img src="./imagenes/widgets/nivel3.png">&nbsp;Nivel III Dominio satisfactorio</span>
							<span class="bullet_text"><img src="./imagenes/widgets/nivel2.png">&nbsp;Nivel II Dominio básico</span>
							<span class="bullet_text"><img src="./imagenes/widgets/nivel1.png">&nbsp;Nivel I Dominio insuficiente</span>
				</div>
		</div>
		  
	</div>		
<!--Fin de página 2-->
<div class="saltopagina"></div>



<!--Inicio de página 3-->
<div class="page_container">
	<div class="header">
			<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$row['cNombreEntidad']];?>.png" class="elem_center">
	</div>

	<div class="container">
				<h4 class="section_tittle">Comparativo con las escuelas de la entidad y del mismo subsistema</h4>
	</div>

	<div class="split">
					<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
	</div>
		
		<div class="text">
				<ul style="font-size:14px;vertical-align: top;">
					<li>
						La línea vertical dentro de la gráfica representa el promedio en lenguaje y comunicación de las escuelas del mismo subsistema.
					</li>
					<li>
					  La línea horizontal dentro de la gráfica representa el promedio en matemáticas de las escuelas del mismo subsistema.
					</li>
					<li>
					  La intersección de ambas líneas representa el promedio de las escuelas del mismo subsistema ambas asignaturas.
					</li>
				</ul>
		</div>

		<div>
			<table>
					<tr>
							<td class="elem_center" style="text-align: center;">
								<img src="./imagenes/cuadrantes.png" style="width: 300px;height: 300px;">
							</td>
							<td class="elem_center" style="text-align: center;">
								<div id="cuadrantes"></div>
							</td>
					</tr>
			</table>
		</div>				

		<div class="container bullet_container">
						<span class="bullet_text"><img src="./imagenes/widgets/mi_escuela.png">&nbsp;Mi escuela</span>
						<span class="bullet_text"><img src="./imagenes/widgets/escuela_zona.png">&nbsp;Escuela del subsistema</span>
						<span class="bullet_text"><img src="./imagenes/widgets/prom_estatal.png">&nbsp;Promedio estatal</span>
		</div>	
		<div class="split">
					<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png">
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
						<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$row['cNombreEntidad']];?>.png" class="elem_center">
		</div>			

		<div class="container">
				<h4 class="section_tittle">Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN</h4>
		</div>	
		<div class="split">
					<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
	  </div>
		<div class="reac_container" id="container_manejo_const_txt">
						<h5>Aspecto de evaluación: Manejo y construcción de la información</h5>
						<div id="manejo_const_txt"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5">
						<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
						<div id="react_menor_5"></div>
		</div>
		<div class="reac_container" id="container_txt_arg">
						<h5>Aspecto de evaluación: Texto argumentativo</h5>
						<div id="txt_arg"></div>
		</div>
		<div class="reac_container" id="container_react_menor_5_arg">
						<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
						<div id="react_menor_5_arg"></div>
		</div>
		
	</div>	
	<!--Fin de página 4-->
	<div class="saltopagina"></div>		

	<!--Inicio de página 5-->
		<div class="page_container">
			<div class="header">
				<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$nom_entidad];?>.png" class="elem_center" style="height: 100px">
			</div>			

				<div class="container">
						<h4 class="section_tittle">Porcentaje de aciertos en LENGUAJE Y COMUNICACIÓN</h4>
				</div>	
				<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
				</div>
				<div class="reac_container" id="container_txt_exp">
								<h5>Aspecto de evaluación: Texto expositivo</h5>
								<div id="txt_exp" style="margin-left: auto; margin-right: auto;"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_exp">
								<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
								<div id="react_menor_5_exp" style="margin-left: auto; margin-right: auto;"></div>
				</div>
				<div class="reac_container" id="container_txt_lit">
								<h5>Aspecto de evaluación: Texto literario</h5>
								<div id="txt_lit" style="margin-left: auto; margin-right: auto;"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_lit">
								<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
								<div id="react_menor_5_lit" style="margin-left: auto; margin-right: auto;"></div>
				</div>

		</div>
	<!--Fin de página 5-->
	<div class="saltopagina"></div>	
</div> <!--Fin seccion lenguaje y comunicacion -->

<!--Inicio de página 6-->
 
<div id="matematicas"> <!--Inicio seccion matemáticas -->
	<div class="page_container">
		<div class="header">
							<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$row['cNombreEntidad']];?>.png" class="elem_center">
			</div>			

			<div class="container">
					<h4 class="section_tittle">Porcentaje de aciertos en MATEMÁTICAS</h4>
			</div>	
			<div class="split">
						<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
			</div>
			<div class="reac_container" id="container_txt_cyr">
							<h5>Aspecto de evaluación: Cambios y relaciones</h5>
							<div id="txt_cyr" style="margin-left: auto; margin-right: auto;"></div>
			</div>
			<div class="reac_container" id="container_react_menor_5_cyr">
							<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
							<div id="react_menor_5_cyr"></div>
			</div>
			<div class="reac_container" id="container_txt_mdi">
							<h5>Aspecto de evaluación: Manejo de la información</h5>
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
								<img src="./imagenes/cabeceras/cabeza_<?php echo $arr_renapo[$row['cNombreEntidad']];?>.png" class="elem_center">
				</div>			

				<div class="container">
						<h4 class="section_tittle">Porcentaje de aciertos en MATEMÁTICAS</h4>
				</div>	
				<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
				</div>
				<div class="reac_container" id="container_txt_snpa">
								<h5>Aspecto de evaluación: Sentido numérico y pensamiento algebráico</h5>
								<div id="txt_snpa"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_snpa">
								<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
								<div id="react_menor_5_snpa"></div>
				</div>
				<div class="reac_container" id="container_txt_fem">
								<h5>Aspecto de evaluación: Forma, Espacio y Medida</h5>
								<div id="txt_fem"></div>
				</div>
				<div class="reac_container" id="container_react_menor_5_fem">
								<h5>Especificaciones de los 5 reactivos con la menor cantidad de aciertos en este eje temático</h5>
								<div id="react_menor_5_fem"></div>
				</div>

		</div>
	<!--Fin de página 7-->
	<div class="saltopagina"></div>	

</div> <!--fin seccion matemáticas -->

<!--Inicio de página 8-->	

<div id="contexto_escolar"> <!--Inicio seccion contexto escolar  -->
<div class="page_container">
		<div class="header">
						<img src="./imagenes/cabeceras/contexto/cabeza_<?php echo  $arr_renapo[$nom_entidad];?>.png" class="elem_center">
		</div>
		<div class="container">
						<h4 class="section_tittle">Información del contexto escolar</h4>
				</div>						

		<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
		</div>
		<div class="reac_container" id="container_txt_ta">
								<h5>Porcentaje de alumnos que trabajan (tomando en cuenta el trabajo asalariado o sin pago y trabajo en un negocio familiar)</h5>
								<div id="txt_ta"></div>
		</div>
		<div class="reac_container" id="container_comp_tes">
								<h5>Tipo de escuela, por sostenimiento, en que los alumnos estudiaron el último año de secundaria</h5>
								<div id="comp_tes"></div>
			<div class="container bullet_container">
				<span class="bullet_text"><img src="./imagenes/widgets/nivel3.png">&nbsp;Pública</span>
				<span class="bullet_text"><img src="./imagenes/widgets/nivel4.png">&nbsp;Privada</span>
			</div>
		</div>
		<br>
		<div class="reac_container" id="container_comp_tets">
								<!--
								<h5>Tipo de escuela, por tipo de servicio, en que los alumnos estudiaron el último año de secundaria</h5>
								-->
								<div id="comp_tets"></div>
			<div class="container bullet_container">
				<span class="bullet_text"><img src="./imagenes/widgets/nivel1.png">&nbsp;General o técnica</span>
				<span class="bullet_text"><img src="./imagenes/widgets/nivel2.png">&nbsp;Telesecundaria</span>
				<span class="bullet_text"><img src="./imagenes/widgets/nivel3.png">&nbsp;Abierta, para adultos, para trabajadores, Ceneval o INEA</span>
				<span class="bullet_text"><img src="./imagenes/widgets/nivel4.png">&nbsp;Comunitaria</span>
			</div>
		</div>
		<div class="reac_container">
								<h5>Nivel máximo de estudios que esperan alcanzar</h5>
								<div id="comp_nme"></div>
		</div>
		<div class="container bullet_container">
			<span class="bullet_text"><img src="./imagenes/widgets/nivel1.png">&nbsp;Media superior</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel2.png">&nbsp;Técnico superior</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel3.png">&nbsp;Licenciatura</span>
			<span class="bullet_text"><img src="./imagenes/widgets/nivel4.png">&nbsp;Posgrado</span>
		</div>		
		
</div>
<!--Fin de página 8-->	
<div class="saltopagina"></div>

<!--Inicio de página 9-->	
<div class="page_container">
<div class="header">
						<img src="./imagenes/cabeceras/contexto/cabeza_<?php echo  $arr_renapo[$nom_entidad];?>.png" class="elem_center">
		</div>
		<div class="container">
						<h4 class="section_tittle">Entorno escolar</h4>
				</div>						
				<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
		</div>
		<div class="reac_container">
								<h5>Frecuencia con la que el último maestro de español (literatura, lectura, redacción, etcétera) que tuvieron los alumnos realiza o realizaba las siguientes actividades:</h5>
		</div>
		<div class="reac_container">
					<div id="comp_esp_act1"></div>
		</div>
		<div class="reac_container">
					<div id="comp_esp_act2"></div>
		</div>
		<div class="reac_container">
					<div id="comp_esp_act3"></div>
		</div>
		<div class="reac_container">
					<div id="comp_esp_act4"></div>
		</div>
		<div class="reac_container">
					<div id="comp_esp_act5"></div>
		</div>
		
		<div class="container bullet_container">
			<span><img src="./imagenes/widgets/nivel1.png">&nbsp;Nunca</span>
			<span><img src="./imagenes/widgets/nivel2.png">&nbsp;Pocas veces</span>
			<span><img src="./imagenes/widgets/nivel3.png">&nbsp;Muchas veces</span>
			<span><img src="./imagenes/widgets/nivel4.png">&nbsp;Siempre</span>
		</div>
		<div class="reac_container">
								<h5>Frecuencia con la que el último maestro de matemáticas que tuvieron los alumnos realiza o realizaba las siguientes actividades:</h5>
							
		</div>
		<div class="reac_container">
					<div id="comp_mate_act1"></div>
		</div>
		<div class="reac_container">
					<div id="comp_mate_act2"></div>
		</div>
		<div class="reac_container">
					<div id="comp_mate_act3"></div>
		</div>
		<div class="reac_container">
					<div id="comp_mate_act4"></div>
		</div>
		<div class="reac_container">
					<div id="comp_mate_act5"></div>
		</div>
		<div class="container bullet_container">
			<span><img src="./imagenes/widgets/nivel1.png">&nbsp;Nunca</span>
			<span><img src="./imagenes/widgets/nivel2.png">&nbsp;Pocas veces</span>
			<span><img src="./imagenes/widgets/nivel3.png">&nbsp;Muchas veces</span>
			<span><img src="./imagenes/widgets/nivel4.png">&nbsp;Siempre</span>
		</div>
		
</div>
<!--Fin de página 9-->	
<div class="saltopagina"></div>

<!--Inicio de página 10-->	
<div class="page_container">
		<div class="header">
						<img src="./imagenes/cabeceras/contexto/cabeza_<?php echo $arr_renapo[$nom_entidad];?>.png" class="elem_center">
		</div>
		<div class="container">
						<h4 class="section_tittle">Clima y convivencia escolar</h4>
		</div>						
		<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
		</div>
		<div class="reac_container">
								<div id="clima_esc_segura"></div>
		</div>
		
		<div class="container bullet_container">
			<span><img src="./imagenes/widgets/nivel1.png">&nbsp;Nada seguro</span>
			<span><img src="./imagenes/widgets/nivel2.png">&nbsp;Poco seguro</span>
			<span><img src="./imagenes/widgets/nivel3.png">&nbsp;Seguro</span>
			<span><img src="./imagenes/widgets/nivel4.png">&nbsp;Muy seguro</span>
		</div>
		<div class="reac_container">
								<h5>Opinión de los alumnos sobre las relaciones entre los miembros de la comunidad escolar</h5>
								<div id="clima_esc_opin1"></div>
								<div id="clima_esc_opin2"></div>
		</div>
		<div class="container bullet_container">
			<span><img src="./imagenes/widgets/nivel1.png">&nbsp;Mala</span>
			<span><img src="./imagenes/widgets/nivel2.png">&nbsp;Regular</span>
			<span><img src="./imagenes/widgets/nivel3.png">&nbsp;Buena</span>
			<span><img src="./imagenes/widgets/nivel4.png">&nbsp;Excelente</span>
		</div>
		<div class="reac_container">
								<h5>Frecuencia con la que los estudiantes de la escuela</h5>
								<div id="clima_convg4"></div>
								<div id="clima_convg5"></div>
								<div id="clima_convg6"></div>
								<div id="clima_convg7"></div>
								<div id="clima_convg8"></div>
								<div id="clima_convg9"></div>
								<div id="clima_convg10"></div>
		</div>
		<div class="container bullet_container">
			<span><img src="./imagenes/widgets/nivel4.png">&nbsp;Nunca</span>
			<span><img src="./imagenes/widgets/nivel3.png">&nbsp;Pocas veces</span>
			<span><img src="./imagenes/widgets/nivel2.png">&nbsp;Muchas veces</span>
			<span><img src="./imagenes/widgets/nivel1.png">&nbsp;Siempre</span>
		</div>
			
</div>
<!--Fin de página 10-->	
<div class="saltopagina"></div>

</div> <!--Fin seccion contexto escolar -->

<!--Inicio de página 11-->	
<div class="page_container">
	<div class="header">
							<img src="./imagenes/cabeceras/matricula/cabeza_<?php echo $arr_renapo[$nom_entidad];?>.png" class="elem_center">
		</div>

	<div class="container">
						<h4 class="section_tittle">Matrícula por grado y ciclo escolar</h4>
		</div>						
		<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
		</div>
		<br>
		<div>
			<div id="matricula_grado_esc"></div>
		</div>
		<br/>
			<div class="reac_container">
								<h5 class="section_tittle">Avance escolar</h5>
			</div>
			<div class="split">
							<img src="http://analisis.websire.inee.edu.mx:9191/reporte_sec/imagenes/separador.png" style="width: 95%">
		  </div>
			<div class="reac_container">
						<div id="anvc_esc_g1"></div>
			</div>
			<div class="reac_container">
						<div id="anvc_esc_g2"></div>
			</div>
			<div class="reac_container">
						<div id="anvc_esc_g3"></div>
			</div>
			<div class="reac_container">
						<div id="anvc_esc_g4"></div>
			</div>
			<div class="reac_container">
						<div id="anvc_esc_g5"></div>
			</div>				
			<div>
				<table>
								<tr>
									<td class="td_right" colspan="2">Definiciones</td>
								</tr>
								<tr>
									<td class="right">
										<strong>Matriculación oportuna:</strong>Porcentaje de alumnos matriculados en primer grado con 15 años de edad.
									</td>
								</tr>
								<tr>
									<td class="right">
										<strong>Alumnos en edad idónea y extraedad ligera:</strong>Porcentaje de alumnos que cursan el grado correspondiente a su edad o un grado menos.
									</td>
								</tr>
								<tr>
									<td class="right">
										<strong>Aprobación al final del ciclo escolar:</strong> Porcentaje de alumnos que acreditan el grado cursado al finalizar ciclo escolar.
									</td>
								</tr>
								<tr>
									<td class="right">
										<strong>Aprobación después del periodo de regularización:</strong>Porcentaje de alumnos que acreditan el grado al finalizar el ciclo escolar o después del periodo de regularización.
									</td>
								</tr>
								<tr>
									<td class="right">
									<strong>Retención intracurricular:</strong>Porcentaje de alumnos matriculados al final del ciclo escolar respecto de la matrícula al inicio del ciclo. Puede tomar valores por encima de 100% debido a alumnos que se incorporan en el transcurso del ciclo escolar.
									</td>
								</tr>
				</table>
			</div>

		</div>
		<!--div id="alumn_por_grado" style="margin-left: auto; margin-right: auto;"></div-->
		</div>
 </div>
</body>
</html>
<?php pg_close($db);?>
