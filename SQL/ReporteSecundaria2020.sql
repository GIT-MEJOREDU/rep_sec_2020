----####  Busqueda avanzada ####----   
---- Entidad ----

SELECT "cNombreEntidad","iPkEntidadFederativa"
FROM "dimensionesReporteSecundaria"."entidadesFederativas"
ORDER BY "cNombreEntidad"

---- Municipio -----

SELECT m."cNombreMunicipio", m."iPkMunicipio"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio"=h."iFkMunicipio"
WHERE h."iFkEntidadFederativa"=30 AND h."iFkCicloEscolar"=19
GROUP BY m."cNombreMunicipio", m."iPkMunicipio"
ORDER BY m."cNombreMunicipio";

---- CCT y Turno ----

SELECT CONCAT( SUBSTRING(CONCAT(ct."cNombreCentroTrabajo",'                           '),1,30), ' - ',te."cNombreTurnoEscolar",' - ',ct."cClaveCentroTrabajo") AS CCT
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio"=h."iFkMunicipio"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE h."iFkEntidadFederativa"=30 AND h."iFkMunicipio" = 1834 AND h."iFkCicloEscolar"=19
ORDER BY ct."cNombreCentroTrabajo"

---- CCT ----

SELECT ct."cClaveCentroTrabajo"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio" = h."iFkMunicipio"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
WHERE h."iFkEntidadFederativa" = 30 AND h."iFkMunicipio" = 1834 AND h."iFkCicloEscolar" = 19
GROUP BY ct."cClaveCentroTrabajo"
ORDER BY ct."cClaveCentroTrabajo"

---- Filtro avanzado: Turno ----

SELECT te."cNombreTurnoEscolar"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio" = h."iFkMunicipio"
FULL OUTER JOIN "dimensionesReporteSecundaria".localidades AS l On l."iPkLocalidad" = h."iFkLocalidad"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE h."iFkEntidadFederativa" = 30 AND h."iFkMunicipio" = 1834 AND ct."cClaveCentroTrabajo"='30DTV0214R' AND h."iFkCicloEscolar" = 19
GROUP BY te."cNombreTurnoEscolar"
ORDER BY te."cNombreTurnoEscolar"

----#### Filtros de busqueda ####----
---- CCT ----

SELECT ct."cClaveCentroTrabajo"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Turno ----

SELECT te."cNombreTurnoEscolar"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND h."iFkCicloEscolar"=19

----#### Información General ####----

---- Nombre del CCT ----

SELECT ct."cNombreCentroTrabajo"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Entidad ----

SELECT ef."cNombreEntidad"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Clave ----

SELECT ct."cClaveCentroTrabajo"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Turno ----

SELECT te."cNombreTurnoEscolar"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Nivel de marginación ----

SELECT gm."cNombreGradoMarginacionPlanea"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."gradosMarginacionPlanea" AS gm ON gm."iPkGradoMarginacionPlanea" = h."iFkGradoMarginacionPlanea"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Municipio ----

SELECT m."cNombreMunicipio"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria".municipios AS m ON m."iPkMunicipio"=h."iFkMunicipio"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

---- Zona escolar ----

SELECT ze."cClaveZonaEscolar"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19

----#### Comparativos 2015, 2017 y 2019 ####----
---- Lenguaje y comunicación ----

SELECT ce."cCicloEscolar" AS "CicloEscolar", CAST(h."dPorcentAlumnsEscNvlLgrILyC" AS NUMERIC (5,2)) AS "I Insuficiente", CAST(h."dPorcentAlumnsEscNvlLgrIILyC" AS NUMERIC (5,2)) AS "II Elemental", CAST(h."dPorcentAlumnsEscNvlLgrIIILyC" AS NUMERIC (5,2)) AS "III Bueno", CAST(h."dPorcentAlumnsEscNvlLgrIVLyC" AS NUMERIC (5,2)) AS "IV Excelente"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar" IN (17,19)

---- Matemáticas ----

SELECT  ce."cCicloEscolar" AS "CicloEscolar", CAST(h."dPorcentAlumnsEscNvlLgrIMat" AS NUMERIC (5,2)) AS "I Insuficiente", CAST(h."dPorcentAlumnsEscNvlLgrIIMat" AS NUMERIC (5,2)) AS "II Elemental", CAST(h."dPorcentAlumnsEscNvlLgrIIIMat" AS NUMERIC (5,2)) AS "III Bueno", CAST(h."dPorcentAlumnsEscNvlLgrIVMat" AS NUMERIC (5,2)) AS "IV Excelente"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar" IN (17,19)


------Cuadrante LYC-Mat---------


----#### Comparativo con las escuelas de la entidad y del mismo subsistema ####----
---- Limite min = 0 y max = 100 de lengua y comunicación ----

---- Limite min = 0 y max = 100 de matemáticas ----

---- Media 50 para Mat y LyC

---- Promedio de Mi escuela ----

SELECT t h."dPorcentAlumnsEscNvlLgrIMat", h."dPorcentAlumnsEscNvlLgrILyC"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar" = 19;


---- Promedio de escuelas en la zona escolar ----

SELECT Select h."dPorcentAlumnsEscNvlLgrIMat", h."dPorcentAlumnsEscNvlLgrILyC"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."zonasEscolares" AS ze ON ze."iPkZonaEscolar" = h."iFkZonaEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
WHERE ef."iPkEntidadFederativa" = 30 AND h."iFkCicloEscolar" = 19 AND ze."cClaveZonaEscolar"= '30FTV0077C'
ORDER BY ct."cClaveCentroTrabajo"

---- Promedio de la entidad ----

SELECT DISTINCT h."dPctjAlmsTdsEscEstNvlLgrIMat", h."dPrctjAlmsTdsEscEstNvlLgrILyC"
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"



---- Textos ----

--SELECT "iNumeroCuadrante", "cMensajeEje1", "cMensajeEje2"
--FROM hechos."hechosReporteEmsInee" AS h
--FULL OUTER JOIN "dimensionesPlaneaEms"."entidadesFederativas" AS ef ON ef."iPkEntidadFederativa" = h."iFkEntidadFederativa"
--FULL OUTER JOIN "dimensionesPlaneaEms"."subsistemasEms" AS ss ON ss."iPkSubsistemaEms" = h."iFkSubsistemaEms"
--FULL OUTER JOIN "dimensionesPlaneaEms"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
--FULL OUTER JOIN "dimensionesPlaneaEms"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
--FULL OUTER JOIN "dimensionesPlaneaEms"."extensionesEms" AS ex ON ex."iPkExtensionEms" = h."iFkExtensionEms"
--FULL OUTER JOIN "dimensionesPlaneaEms"."ciclosEscolares" AS ce ON ce."iPkCicloEscolar" = h."iFkCicloEscolar"
--FULL OUTER JOIN "dimensionesPlaneaEms"."cuadrantesEscuela" AS cu ON cu."iPkCuadranteEscuela" = h."iFkCuadranteEscuela"
--WHERE ct."cClaveCentroTrabajo"='30ETH0494X' AND te."iPkTurnoEscolar"=1 AND ex."iPkExtensionEms" = 6434 AND h."iFkCicloEscolar" = 19
--ORDER BY ct."cClaveCentroTrabajo"




----#### Resultados por temas y reactivos - LyC ####----

---- Eje temático ----

SELECT rlc."cUnidadEvaluacion", CAST(SUM(rlc."dPorcentsAlumnsAcertReactivo")/COUNT(rlc."dPorcentsAlumnsAcertReactivo") AS NUMERIC (5,2)) AS Porcentaje
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar" = 19
GROUP BY rlc."cUnidadEvaluacion"
ORDER BY rlc."cUnidadEvaluacion",Porcentaje

---- Contenidos Tematicos ----

SELECT CONCAT(rlc."cContenidoTematico",' Reactivo #',rlc."cNumeroReactivo"), CAST(rlc."dPorcentsAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosLyC" AS rlc ON rlc."iPkResultadoLyC" = h."iFkResultadoLyC"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar" = 19 AND rlc."cUnidadEvaluacion"= 'Comprensión lectora'
ORDER BY rlc."cUnidadEvaluacion",Porcentaje DESC

----#### Resultados por temas y reactivos - Mat ####----
---- Eje temático ----

SELECT rm."cUnidadEvaluacion", CAST(SUM(rm."dporcentAlumnsAcertReactivo")/COUNT(rm."dporcentAlumnsAcertReactivo") AS NUMERIC (5,2)) AS Porcentaje
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19
GROUP BY rm."cUnidadEvaluacion"
ORDER BY rm."cUnidadEvaluacion",Porcentaje

---- Contenidos Tematicos ----

SELECT CONCAT(rm."cContenidoTematico",' Reactivo #',rm."cNumeroReactivo"), CAST(rm."dporcentAlumnsAcertReactivo" AS NUMERIC (5,2)) AS Porcentaje
FROM hechos."hechosReporteIneeSecundaria" AS h
FULL OUTER JOIN "dimensionesReporteSecundaria"."centrosTrabajo" AS ct ON ct."iPkCentroTrabajo" = h."iFkCentroTrabajo"
FULL OUTER JOIN "dimensionesReporteSecundaria"."turnosEscolares" AS te ON te."iPkTurnoEscolar" = h."iFkTurnoEscolar"
FULL OUTER JOIN "dimensionesReporteSecundaria"."resultadosMat" AS rm ON rm."iPkResultadoMat" = h."iFkResultadoMat"
WHERE ct."cClaveCentroTrabajo"='30DTV0214R' AND te."cNombreTurnoEscolar"='MATUTINO' AND h."iFkCicloEscolar"=19 AND rm."cUnidadEvaluacion"= 'Forma Espacio y Medida'
ORDER BY rm."cUnidadEvaluacion",Porcentaje DESC