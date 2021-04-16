<?php

	function dbc(){
		$con = pg_connect("host=postgres12 port=5432 dbname=reportes_escolares user=postgres password=***********") or die('connection failed');
		return $con;
	}
	
	function dbd($db){
		pg_close($db);
	}
	
	function escapa($texto){
		$db = dbc();
		$regreso = pg_escape_string($db,$texto);
		
		dbd($db);
		return $regreso;
	}
	
	function valida_variable($var){
		#echo "VV: $var";
		if(is_string($var) && strlen($var) >= 11){
			#es CCT TURNO
			$regreso = array('tipo'=>1,'vent'=>$var);
		}elseif(((strlen($var) == 2) || (strlen($var) == 3)) && is_string($var)){
			#es generación de pdf por estado/nivel
			$regreso = array('tipo'=>2,'vent'=>$var);
		}else{
			#param inválido
			$regreso = 0;
		}
		return $regreso;
	}

?>
