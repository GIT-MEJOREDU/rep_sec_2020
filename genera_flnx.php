<?php
	include("./lib/escuelaslib.php");
	require_once('./lib/pdf/mpdf.php');
	$arrv = valida_variable(strtolower(escapa($vin = $_REQUEST['va'])));
	#echo "A: $arrv";
	if(is_array($arrv)){
		$vent = $arrv['vent'];
		$tipo = $arrv['tipo'];
		#echo "V: $vent CMD: $tipo<br/>";
		switch($tipo){
			case 1:
				#conecta a la base
				#$db = dbc();
				#$qw = "select n_entidad, clavecct_zona, n_clavecct, clave_renapo from edo_zonas_send where clavecct_zona = '$vent'";
				#$res = mysqli_query($db,$qw);
				#$hay = mysqli_num_rows($res);
				#$url = "analisis.websire.inee.edu.mx:9191/reporte_escuelas/zona.php?va=$vent";
				#$html= file_get_contents($url);
				#$mpdf = new mPDF ('c', 'Letter');
				#$mpdf->img_dpi = 96;
				#$mpdf->writeHTML($html);
				#$nombre = "$vent.pdf";
				#$mpdf->Output("$nombre", 'I');
				#dbd($db);
			break;
			case 2:
				$db = dbc();
				
				#Se obtiene el numero de pdf's por generar.
				$qw_cont = <<<EOD
				SELECT count(*) as regs
				FROM "reportesEscuelaBatch"."bitacoraReportesEscuelas"
				WHERE "cClaveEntidadFederativa" = '$vent'
				AND "iGenerado" is NULL
EOD;
				$res_cont = pg_query($db,$qw_cont);
				#numero de reportes pdf por generar
				$reg = pg_fetch_assoc($res_cont);
				$num_regs = $reg['regs']; 
				echo "numero de registros: ".$num_regs;

				#$qw = "select ident, ESCUELA, NIVEL, CLAVE_RENAPO, CV_CCT, GENERADO, count(*) as faltan from escuelas_dgiai where CLAVE_RENAPO = '$vent' and generado = 0 order by ident asc limit 1";
				##Consulta para probar el proceso batch
				##se va a generar una tabla para registrar los reportes generados.
				##esta tabla estará en la misma base de datos, pero en esquema diferente
					$qw = <<<EOD
					SELECT "iPkBitacoraReporteEscuela", "iFkEntidadFederativa", "iFkCicloEscolar",
					"iFkNivelEscolar", "iFkTurnoEscolar", "iFkExtensionEmsEscolar",
					"cClaveCentroTrabajo", "cCiloEscolar", "cNombreNivelEscolar",
					"cNombreExtensionEscolar", "cClaveEntidadFederativa", "cAbreviaturaEntidadFederativa",
					"cClaveRENAPO", "iGenerado", "cNombreReporte", "tFechaImpresion"
					FROM "reportesEscuelaBatch"."bitacoraReportesEscuelas"
					WHERE "cClaveEntidadFederativa" = '$vent'
					and "iGenerado" is NULL
					order by "iPkBitacoraReporteEscuela" asc limit 1
EOD;

				echo "V: $vent<br/>";
				echo "Q: $qw";
				$res = pg_query($db,$qw);
				$hay = pg_num_rows($res);
				$row = pg_fetch_array($res);
				$idBitacora = $row['iPkBitacoraReporteEscuela'];
				$clavecct = $row['cClaveCentroTrabajo'];
				$cve_ent = $vent;
				$ident = $row['cClaveEntidadFederativa'];
				#$faltan = $row['faltan'];
				$nivel = "ems";
				$cct_turno = $row['iFkTurnoEscolar'];
				$extension = $row['iFkExtensionEmsEscolar'];
				$ciclo = $row['iFkCicloEscolar'];
				$cveRenapo = $row['cClaveRENAPO'];
				$par_ems = "$clavecct"."$cct_turno"."$extension";

				echo "parametro: ".$par_ems;
				#if($nivel == null or $nivel == "PRIM"){
				#	$nivel = "PRIM";
				#}elseif($nivel == "SEC"){
				#	$nivel = "SEC";
				#}elseif($nivel == "SEC2015"){
				#	$nivel = "SEC2015";
				#}elseif($nivel == "PRIM2018"){
				#	$nivel = "SEC2018";
				#}
				#echo "Q: $qw;";
				#echo "Renapo: $clave_renapo<br/>";
				#echo "Id: $ident<br/>";
				#if(file_exists("./imagenes/graf/$nivel/1/".$clavecct_zona."_1.jpg")){
				#	$url = "http://sirelab.websire.inee.edu.mx/zonas/index.php?va=$clavecct_zona&ni=$nivel";
				#	echo "$i -> $renapo -> $url -> ";
				#	$html= file_get_contents($url);
				#	$mpdf = new mPDF ('c', 'Letter');
				#	$mpdf->img_dpi = 96;
				#	$mpdf->writeHTML($html);
				#	$nombre = "./salida/".$nivel."/".$clavecct_zona.".pdf";
				#	echo "$nombre";
				#	$mpdf->Output($nombre, 'F');
				#	echo " OK - Faltan: $faltan<br/>";
				#}else{
				#	echo "Zona: $clavecct_zona no tiene imágenes generadas<br>";
				#	echo "F: $faltan<br/>";
				#}
				$randy = rand();
				shell_exec("google-chrome --headless --dump-dom http://localhost:9090/reportes_ems/test.php?va=$par_ems > ./salida/$randy$par_ems.php");
				$html= file_get_contents("./salida/$randy$par_ems.php");
				$mpdf = new mPDF ('c', 'Letter');
				$mpdf->SetHTMLFooter ('
				<div style="font-size: 13px; font-weight: bold; text-align: right; color: #333e75; vertical-align: bottom;padding-right: 50px;">
					<span>Página - {PAGENO} de {nbpg}</span><br/>
					<span >
						Para más información sobre los resultados de tu escuela visita <a href="https://www.inee.edu.mx/index.php/sire-inee">www.inee.edu.mx/index.php/sire-inee</a>
					</span>
				</div>'
			);
				$mpdf->img_dpi = 96;
				$mpdf->writeHTML($html);
				$name = "$cveRenapo"."_".$par_ems.".pdf";
				$path_name ="./salida/ems/$name";
				echo "nombre: ".$path_name;
				shell_exec("rm ./salida/$randy$par_ems.php");
				$mpdf->Output("$path_name", 'F');
				echo " OK - Faltan: $num_regs<br/>";
				
				#$qw = "update escuelas_dgiai set generado = 1 where ident = $ident";
				$qw_upd = <<<EOD
					update "reportesEscuelaBatch"."bitacoraReportesEscuelas" set "iGenerado"=1, "cNombreReporte"='$name', "tFechaImpresion"=CURRENT_TIMESTAMP
					where "iPkBitacoraReporteEscuela"=$idBitacora
EOD;
				#echo "QW: $qw<br/>";
				$res = pg_query($db,$qw_upd);
				dbd($db);
				$num_regs--;
				##En caso de que existan mas pdf's sin generar, se ejecuta nuevamente el llamado
				if($num_regs > 0){
					# el redirecionamiento '> /dev/null 2>&1 &'
					# es para redireccionar la salida del comendo
					# y no tener que esperar a la respuesta del comando.
					# asi no se llena de procesos iniciados y se termina el proceso actual que realiza el llamado a la ejecución del sigueinte proceso.
					shell_exec("curl localhost:9090/reportes_ems/genera_flnx.php?va=$vent > /dev/null 2>&1 &");
				}else{
					echo "Generación de PDF terminada";
				}
			break;
		}
	}else{
		echo "Error";
	}
?>