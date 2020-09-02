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
	$iPk_entidad = $row['iPkEntidadFederativa'];
	$iPk_subs = $row['iPkSubsistemaEms'];


#Consulta de Comparativo con las escuelas de la entidad y el mismo subsistema


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

}
</script>
</head>
<body>
		<div>
			<table>
					<tr>
							<td class="elem_center" style="text-align: center;">
								<div id="cuadrantes"></div>
							</td>
					</tr>
			</table>
		</div>				
</body>
</html>
<?php pg_close($db);?>