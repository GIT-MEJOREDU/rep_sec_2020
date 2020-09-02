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
				$nivel = "";
				$qw = "select ident, ESCUELA, CLAVE_RENAPO, CV_CCT, GENERADO, count(*) as faltan from escuelas_dgiai_sec where CLAVE_RENAPO = '$vent' and generado = 0 order by ident asc limit 1";
				echo "V: $vent<br/>";
				echo "Q: $qw";
				$res = mysqli_query($db,$qw);
				$hay = mysqli_num_rows($res);
				$row = mysqli_fetch_array($res);
				$clavecct_zona = $row['CV_CCT'];
				if($clavecct_zona == '-99'){
					$clavecct_zona = "SIN_ZONA";
				}
				$renapo = $vent;
				$ident = $row['ident'];
				$faltan = $row['faltan'];
				$nivel = "secundaria";
				$cct_turno = $row['ESCUELA'];
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
				shell_exec("google-chrome --headless --dump-dom http://localhost:9090/reporte_sec/contexto.php?va=$cct_turno > ./salida/$randy$cct_turno.php");
				$html= file_get_contents("./salida/$randy$cct_turno.php");
				$mpdf = new mPDF ('c', 'Letter');
				$mpdf->img_dpi = 96;
				$mpdf->writeHTML($html);
				$nombre = "./salida/$nivel/$renapo"."_".$clavecct_zona."_".$cct_turno.".pdf";
				echo "$nombre";
				shell_exec("rm ./salida/$randy$cct_turno.php");
				$mpdf->Output("$nombre", 'F');
				echo " OK - Faltan: $faltan<br/>";
				$qw = "update escuelas_dgiai_sec set generado = 1 where ident = $ident";
				#echo "QW: $qw<br/>";
				$res = mysqli_query($db,$qw);
				dbd($db);
				if($faltan - 1 >= 0){
?>
<html>
	<head>
		<title>
		
		</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script type="text/javascript">
			function delayer(){
				document.forma.submit();
			}
		</script>
	</head>
	<body onLoad="setTimeout('delayer()', 300)">
		<form name="forma" method="post" action="<?php echo $PHP_SELF?>">
			<input type="hidden" name="va" value="<?php echo $vent?>">
		</form>
	</body>
</html>
<?php
				}else{
					echo "Generación de PDF terminada";
				}
			break;
		}
	}else{
		echo "Error";
	}
?>
