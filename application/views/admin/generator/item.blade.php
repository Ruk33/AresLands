<h1>Generador - Objetos</h1>

{{ Form::open() }}
	{{ Form::token() }}

	{{ Form::label("to_who", "¿Para quien?") }}
	{{ Form::select("to_who", array("warrior" => "Guerrero", "magician" => "Mago", "neutral" => "Neutro")) }}

	{{ Form::label("type", "Tipo") }}
	{{ Form::select("type", array("blunt" => "Mazas", "sword" => "Espadas", "bow" => "Arcos", "dagger" => "Dagas", "staff" => "Palos magicos", "shield" => "Escudos", "potion" => "Pociones", "mercenary" => "Mercenarios")) }}

	{{ Form::label("level", "Nivel") }}
	{{ Form::number("level") }}

	{{ Form::label("amount", "Cantidad") }}
	{{ Form::number("amount") }}

	<div>
	{{ Form::submit("Generar", array("class" => "btn btn-primary")) }}
	</div>
{{ Form::close() }}