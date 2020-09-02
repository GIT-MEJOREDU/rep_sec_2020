<?php
require_once('./lib/pdf/mpdf.php');
include("./lib/escuelaslib.php");
$vin = $_REQUEST['va'];
$arrv = valida_variable(strtoupper(escapa($vin)));
#print_r($arrv);
if(is_array($arrv)){
	$cct_turno = $arrv['vent'];
	$tipo = $arrv['tipo'];
	shell_exec("google-chrome --headless --dump-dom http://localhost:9090/reportes_ems/test_img.php?va=$cct_turno > ./salida/$cct_turno.php");
	$html= file_get_contents("./salida/$cct_turno.php");
	shell_exec("rm ./salida/$cct_turno.php");
	if(preg_match('/"cuadrante" src="data:image\/png;base64,(.+)"/',$html,$data)){
		$data = base64_decode($data[1]);
		header('Content-Type: image/png');
		echo "$data";
	}else{
		$path = "./imagenes/";
		$name = "sin_subsistema.png";
		$el_archivo = "sin_subsistema.png";
		$fname = $path."$el_archivo";
		$tipo = "image/png";
		#echo "$fname";
		$fp = fopen($fname,"r");
		header ("Content-Disposition: inline;");
	header ("Content-type: $tipo");
	header("Content-Transfer-Encoding: binary");
	header('Content-Length: '. filesize($fname));
	$r=0;
	while($r < filesize($fname)){
		echo fread($fp,8192);
		$r += 8192;
	}
	ob_end_flush();
	}
	#$mpdf = new mPDF ('c', 'Letter');
	#$mpdf->img_dpi = 96;
	#$mpdf->writeHTML($html);
	#$nombre = "$cct_turno.pdf";
	#$mpdf->Output("$nombre", 'I');
}else{
	echo "Error: VA";
}
?>
