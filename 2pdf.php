<?php
require_once('./lib/pdf/mpdf.php');
include("./lib/escuelaslib.php");
$vin = $_REQUEST['va'];
$arrv = valida_variable(strtoupper(escapa($vin)));
#print_r($arrv);
if(is_array($arrv)){
	$cct_turno = $arrv['vent'];
	$tipo = $arrv['tipo'];
	$randy = rand();
	shell_exec("google-chrome --headless --dump-dom --no-sandbox http://localhost/rep_sec_2020/test.php?va=$cct_turno > ./salida/$randy$cct_turno.php");
	$html= file_get_contents("./salida/$randy$cct_turno.php");
	$mpdf = new mPDF ('c', 'Letter');
	$mpdf->SetHTMLFooter ('
		<div style="font-size: 13px; font-weight: bold; text-align: right; color: #333e75; vertical-align: bottom;padding-right: 50px;">
			<span>Página - {PAGENO} de {nbpg}</span><br/>
			<!--span >
				Para más información sobre los resultados de tu escuela visita <a href="https://www.gob.mx/mejoredu">https://www.gob.mx/mejoredu</a>
			</span-->
		</div>'
	);
	$mpdf->img_dpi = 96;
	$mpdf->writeHTML($html);
	$nombre = "$cct_turno.pdf";
	shell_exec("rm ./salida/$randy$cct_turno.php");
	$mpdf->Output("$nombre", 'I');
}else{
	echo "Error variable de entrada no válida: VA";
}
?>
