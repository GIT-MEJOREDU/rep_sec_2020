<html>
<head>
<style type="text/css">
	@media all {
		div.saltopagina{
			display: none;
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
		width:100%;
		border:0;
		padding:0;
		border-spacing: 0px;
		border-collapse: collapse;
	}
	
	td{
		font-size: 14px;
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
		font-size: 15px;
		font-weight: bold;
	}
	
	.td_center_gray{
		text-align: center;
		color: #999999;
		font-size: 14px;
		font-weight: bold;
	}
	
	.td_left{
		text-align: left;
	}
	
	.td_right{
		text-align: right;
		color: #333e75;
		font-weight: bold;
	}
	
	.td_just{
		font-size: 12px;
		text-align: justify;
	}
	
	.td_top{
		vertical-align:top;
		color: #333e75;
		font-size: 12px;
		font-weight: bold;
	}
</style>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.load("current", {packages:["bar"]});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      //var data = new google.visualization.DataTable();
      //data.addColumn('number');
      //data.addColumn('number');
      //data.addRow([5, 5]);
      //data.addRow([7, 7]);
      //data.addRow([10, 10]);
      
      var data1 = google.visualization.arrayToDataTable
            ([['', 'Puntaje PLANEA 2018 (Lenguaje y comunicación , Matemáticas)', {'type': 'string', 'role': 'style'}],
              [553, 570, 'point { size: 0; shape-type: circle; fill-color: #000000;}'],
              [540, 550, 'point { size: 10; shape-type: circle; fill-color: #ff0000;}'],
              [575, 575, null],
              [563, 546, null],
              [549, 532, null],
              [559, 532, null],
              [549.2, 576.3, null],
              [562, 540, null]
      ]);

      
      var options1 = {
      	chart: {
      		title: 'Puntajes promedio PLANEA 2018',
      		subtitle: 'Mi escuela en mi zona escolar',
        },
        legend: 'none',
        colors: ['#087037'],
        pointShape: 'circle',
        pointSize: 10,
        crosshair: {
        	trigger: 'selection',
        	orientation: 'both',
        	color: 'gray'
        },
        vAxis: {
        	gridlines: {
        		color: 'transparent'
        	}
        },
        hAxis: {
        	gridlines: {
        		color: 'transparent'
        	}
        }
      };
      var chart1 = new google.visualization.ScatterChart(document.getElementById('g1'));
      chart1.draw(data1, options1);
      chart1.setSelection([{"row":0}]);
      //google.visualization.events.addListener(chart, 'onmouseover', readyHandler);
      //function readyHandler(e){
      //	alert("Evento");
      //	
      //}
      
      var data2 = google.visualization.arrayToDataTable([
        ['Genre', 'Nivel I', 'Nivel II', 'Nivel III', 'Nivel IV', { role: 'annotation' } ],
        ['2015', 16, 24, 20, 40, ''],
        ['2018', 40, 20, 24, 16, '']
      ]);

      var options2 = {
          isStacked: 'percent',
          height: 300,
          legend: {position: 'top', maxLines: 3},
          vAxis: {
            minValue: 0,
            ticks: [0, .3, .6, .9, 1]
          }
        };

      var chart2 = new google.visualization.ColumnChart(document.getElementById('g2'));
      chart2.draw(data2, options2);
      
      var data3 = google.visualization.arrayToDataTable([
        ['Genre', 'Nivel I', 'Nivel II', 'Nivel III', 'Nivel IV', { role: 'annotation' } ],
        ['2015', 16, 24, 20, 40, ''],
        ['2018', 40, 20, 24, 16, '']
      ]);

      var options3 = {
          isStacked: 'percent',
          height: 300,
          legend: {position: 'top', maxLines: 3},
          vAxis: {
            minValue: 0,
            ticks: [0, .3, .6, .9, 1]
          }
        };

      var chart3 = new google.visualization.ColumnChart(document.getElementById('g3'));
      chart3.draw(data3, options3);
        
        var data5 = google.visualization.arrayToDataTable([
		['Parámetro', 'Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
		['Mi escuela', -11.7,'11.7%', 52.9,'52.9%', 26.4,'26.4%',8.8, '8.8%'],
		['Zona escolar', -36.4,'36.4%', 24.9,'24.9%', 17.9,'17.9%',20.5, '20.5%'],
		['Escuelas en el estado', -51,'51%', 18.8,'18.8%', 16.7,'16.7%',13.4, '13.4%'],
		['Todas las escuelas de México', -56.4,'56.4%', 18,'18%', 14.7,'14.7%',10.6, '10.6%']
		]);
		
		var options5 = {
			tooltip: {
				trigger: 'none'
			},
			title: 'Lenguaje y Comunicación',
			hAxis: {
			textPosition: 'none',
        	gridlines: {
        		color: 'transparent'
        	}
          },
          vAxis: {
        	gridlines: {
        		color: 'transparent'
        	}
          },
          bar:{
          	  groupWidth: '80%'
          },
          width: 1500,
          height: 400,
			isStacked:true	  
		};  
		
		// Instantiate and draw the chart.
		var chart5 = new google.visualization.BarChart(document.getElementById('g5'));
		chart5.draw(data5, options5);
        
        
		var data6 = google.visualization.arrayToDataTable([
		['Parámetro', 'Nivel I', {role: 'annotation'}, 'Nivel II', {role: 'annotation'}, 'Nivel III', {role: 'annotation'}, 'Nivel IV', {role: 'annotation'}],
		['Mi escuela', -11.7,'11.7%', 52.9,'52.9%', 26.4,'26.4%',8.8, '8.8%'],
		['Zona escolar', -36.4,'36.4%', 24.9,'24.9%', 17.9,'17.9%',20.5, '20.5%'],
		['Escuelas en el estado', -51,'51%', 18.8,'18.8%', 16.7,'16.7%',13.4, '13.4%'],
		['Todas las escuelas de México', -56.4,'56.4%', 18,'18%', 14.7,'14.7%',10.6, '10.6%']
		]);
		
		var options6 = {
			tooltip: {
				trigger: 'none'
			},
			title: 'Matemáticas',
			hAxis: {
			textPosition: 'none',
        	gridlines: {
        		color: 'transparent'
        	}
          },
          vAxis: {
        	gridlines: {
        		color: 'transparent'
        	}
          },
          bar:{
          	  groupWidth: '80%'
          },
          width: 1500,
          height: 400,
			isStacked:true	  
		};  
		
		// Instantiate and draw the chart.
		var chart6 = new google.visualization.BarChart(document.getElementById('g6'));
		chart6.draw(data6, options6);
        
        
    }
 
  </script>
  </head>
  <body>
<table>
	<tr>
		<td>
			<img src="imagenes/cabeceras/cabeza_<?php echo $row['clave_renapo'];?>.png">
		</td>
	</tr>
	<tr>
		<td>
			<br/>
			<table style="width: 95%">
				<tr>
					<td class="td_green" style="width: 120px;text-align: left;padding-left: 10px;">
						Zona escolar:
					</td>
					<td colspan="3" class="td_data" style="text-align: left;<?php if(strlen($row['n_clavecct'])>55){echo 'font-size: 12px;';}?>">
						<?php echo $row['n_clavecct'];?>
					</td>
				</tr>
				<tr>
					<td class="td_green" style="width: 120px;text-align: left;">
						Clave:
					</td>
					<td class="td_data">
						<?php echo $row['clavecct_zona'];?>
					</td>
					<td class="td_green" style="width: 120px;">
						E. Federativa:
					</td>
					<td class="td_data">
						<?php echo $row['n_entidad'];?>
					</td>
				</tr>
				<tr>
					<td colspan="4" class="td_left">
						<br/>
						<br/>
						<br/>
						<br/>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style="width:95%;">
				<tr>
					<td class="td_top">
						¿Para qué sirve?
					</td>
					<td class="td_just" style="width: 560px;font-size: 14px;">
						El Instituto Nacional para la Evaluación de la Educación y la autoridad educativa estatal ofrecen este reporte para todos los supervisores escolares de las escuelas primarias y secundarias. El propósito es ofrecerles información sistematizada del logro educativo en sus escuelas, como herramienta para identificar las prioridades de atención académica en la zona escolar y contribuir así a una planeación con el mayor sustento posible.<br/>
						<br/>
						Con base en la información que contiene este reporte, se sugiere ampliamente que los equipos de supervisión identifiquen las escuelas y los temas que requieren de mayor apoyo y, en coordinación con los colegiados de cada centro escolar involucrado, las acciones que se deban realizar para su atención. Recordemos que cada escuela necesita una atención diferenciada de acuerdo con sus fortalezas, retos y oportunidades específicas. Nuestros objetivos son que todas las niñas, niños y jóvenes asistan a la escuela; que todos concluyan al menos la educación media superior y que cada estudiante reciba una educación de calidad que le permita dominar los contenidos esenciales de los planes y programas.  
						<br/>
						<br/>
						<br/>
					</td>
				<tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table style="width:95%;">
				<tr>
					<td class="td_top">
						¿Qué contiene?
					</td>
					<td class="td_just" style="width: 560px;font-size: 14px;">
						<span style="font-weight: bold;">A.</span>	Un mapa con el nivel de logro en PLANEA de las escuelas de su zona escolar, a efecto de identificar a las que muestren mayores rezagos, tanto en Lenguaje y Comunicación como en Matemáticas. El mapa muestra como referencia el resultado promedio del estado.<br/>
						<br/>
						<span style="font-weight: bold;">B.</span>	El nivel de logro promedio de la zona escolar, con énfasis en el porcentaje de estudiantes en Nivel I (Insuficiente), cuya disminución se recomienda como prioridad. Asimismo, se ofrece el nivel de logro por cada escuela de la zona, ordenadas de mayor a menor prioridad de atención.<br/>
						<br/>
						<span style="font-weight: bold;">C.</span>	El porcentaje de aciertos en cada uno de los temas que evalúa PLANEA, tanto en el promedio de la zona escolar, como en cada una de las escuelas a su cargo, a fin de aportar información útil para la ruta de mejora escolar y para los procesos de acompañamiento y formación continua.<br/>

					</td>
				<tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="td_right" style="height: 190px;vertical-align: bottom;padding-right: 50px;">
			1 de <?echo $ptot?>
		</td>
	</tr>
</table>
<div class="saltopagina"></div>
    <div id="g1" style="width: 700px; height: 700px;"></div>
    <div class="saltopagina"></div>
    <table>
    	<tr>
    		<th style="width: 600px;">
    			Lenguaje y comunicación
    		</th>
    		<th style="width: 600px;">
    			Matemáticas
    		</th>
    	</tr>
    	<tr>
    		<td id="g2">
    		</td>
    		<td id="g3">
    		</td>
    	</tr>
    </table>
    <div class="saltopagina"></div>
    <div id="g4" style="width: 700px; height: 700px;"></div>
    <div class="saltopagina"></div>
    <div id="g5" style="width: 900px; height: 400px;"></div>
    <div class="saltopagina"></div>
    <div id="g6" style="width: 900px; height: 400px;"></div>
    <div class="saltopagina"></div>
  </body>
</html>