<h1>Generador - Objetos</h1>

{{ Form::open() }}
	{{ Form::token() }}

	{{ Form::label("to_who", "¿Para quien?") }}
	@foreach ( array(Item::WARRIOR => "Guerrero", Item::WIZARD => "Mago", Item::MIXED => "Mixto") as $toWho => $name)
		{{ Form::checkbox("to_who[]", $toWho) }} {{ $name }} <br>
	@endforeach

	{{ Form::label("type", "Tipo") }}
	
	@foreach ( array("blunt" => "Mazas", "sword" => "Espadas", "bow" => "Arcos", "dagger" => "Dagas", "staff" => "Palos magicos", "shield" => "Escudos", "potion" => "Pociones", "mercenary" => "Mercenarios") as $type => $name )
		{{ Form::checkbox("type[]", $type) }} {{ $name }} <br>
	@endforeach

	{{ Form::label("level", "Nivel") }}
	{{ Form::text("level") }}
	<small>Separar por comas (",") en caso de querer generar para varios niveles</small>

	{{ Form::label("amount", "Cantidad") }}
	{{ Form::number("amount") }}

	<div>
	{{ Form::submit("Generar", array("class" => "btn btn-primary")) }}
	</div>
{{ Form::close() }}