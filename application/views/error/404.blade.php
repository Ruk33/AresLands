<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>AresLands - Busca un mapa, ¡nos perdimos!</title>
	<meta name="viewport" content="width=device-width">
	
	<style>
		html, body {
			height: 100%;
		}
	
		body {
			font-family: "Arial";
			background-color: #131111;
			color: white;
			margin: 0;
			padding: 0;
			box-shadow: inset 0 0 100px black;
		}
		
		a, a:active, a:visited {
			color: #DA7111;
		}
		
		a:hover {
			color: #F3B32B;
		}
		
		.container {
			text-align: center;
			padding-top: 13%;
		}
	</style>
</head>
<body>
	<div class="container">
		<img src="{{ URL::base() }}/img/logo.png" alt="" />
		<h2>No conozco estas tierras, ¿estamos perdidos?</h2>
		<p>Creo que estamos perdidos, mejor volvamos al <a href="{{ URL::base() }}">inicio</a></p>
	</div>
</body>
</html>