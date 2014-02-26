<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>AresLands - ¡Una nueva aventura esta por comenzar!, ¿estas listo?</title>
	<meta name="viewport" content="width=device-width">

	<style>
		html, body {
			height: 100%;
		}

		body {
			font-family: "Arial";
			background-color: #0d0000;
			color: white;
			margin: 0;
			padding: 0;
			background-image: url('{{ URL::base() }}/img/door-background.jpg');
			background-repeat: no-repeat;
			background-position: center center;
		}

		a, a:active, a:visited {
			color: #DA7111;
		}

		a:hover {
			color: #F3B32B;
		}

		.container {
			text-align: center;
			padding-top: 20%;
			height: 180px;
		}

		div, span {
			text-shadow: black 0 0 5px, black 0 0 5px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div style="margin-bottom: 20px;"><img src="{{ URL::base() }}/img/logo.png" alt="" /></div>
		<span style="font-size: 10px; letter-spacing: 6px;">UNA NUEVA AVENTURA COMIENZA EN</span>
		<div id="timer" style="font-size: 48px; text-transform: uppercase;"></div>
	</div>

	<script src="{{ URL::base() }}/js/vendor/jquery-1.9.1.min.js"></script>
	<script src="{{ URL::base() }}/js/libs/jquery.countdown.min.js"></script>
	<script>
		$(document).ready(function() {
			var time = {{ $grandOpeningDate->getTimeStamp() - time() }};
			var date = new Date();

			date.setSeconds(date.getSeconds() + time);

			$('#timer').countdown({
				until: date,
				layout: '{dnn} dias {hnn}:{mnn}:{snn}',
				expiryText: '<a href="" onclick="location.reload();">¡Comenzar!</a>'
			});
		});
	</script>
</body>
</html>