<div class="row">
	<ul class="breadcrumb">
		<li><a href="{{ URL::to('admin') }}">Panel de administracion</a> <span class="divider">/</span></li>
		<li><a href="{{ URL::to_route('get_admin_dungeon_index') }}">Dungeons</a> <span class="divider">/</span></li>
		<li class="active">
			@if ( $dungeon->exists )
				Editar dungeon
			@else
				Crear dungeon
			@endif
		</li>
	</ul>

	@if ( $dungeon->exists )
		<h1>Editar dungeon</h1>
		{{ Form::open(URL::to_route("post_admin_dungeon_edit"), "POST", array("class" => "form-horizontal")) }}
	@else
		<h1>Crear dungeon</h1>
		{{ Form::open(URL::to_route("post_admin_dungeon_create"), "POST", array("class" => "form-horizontal")) }}
	@endif
	
		{{ Form::hidden("id", $dungeon->id) }}

		<div class="control-group text-center">
			{{ Form::submit(( $dungeon->exists ) ? "Editar" : "Crear", array("class" => "btn btn-primary btn-large")) }}
		</div>

		<div class="control-group">
			{{ Form::label("name", "Nombre del dungeon", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::text("name", Input::old("name", $dungeon->name), array("class" => "span11")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("only_clan", "¿Solamente para grupos?", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("only_clan", array("No", "Si"), Input::old("only_clan", (string) $dungeon->only_clan), array("class" => "span11", "autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("rest_time", "Duracion de la mazmorra", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::text("rest_time", Input::old("rest_time", $dungeon->rest_time), array("class" => "span11")) }}
				<span class="help-block">
					<p>Tiempo (en segundos) que deben esperar luego de hacer una mazmorra.</p>
					<div>
						Formula recomendada:<br>
						<code>
							(nivel minimo + cantidad de bichos * (nivel bichos / cantidad de bichos) * solo grupo) * 6
						</code>
						<p>solo grupo = 1 (no), 2 (si)</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("zone_id", "Zona", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("zone_id", $zones, Input::old("zone_id", (string) $dungeon->zone_id), array("class" => "span11", "autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("min_level", "Nivel minimo", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("min_level", Input::old("min_level", $dungeon->min_level), array("class" => "span11")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("show_monsters_stats", "¿Mostrar atributos de los monstruos?", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("show_monsters_stats", array("No", "Si"), Input::old("show_monsters_stats", (string) $dungeon->show_monsters_stats), array("class" => "span11", "autocomplete" => "off")) }}
				<span class="help-block">Poner "No" para que los atributos se muestren SOLO cuando se derrote al monstruo.</span>
			</div>
		</div>

		<hr>

		<h1>Monstruos</h1>
		<div class="span11">
			<p>No elegir mas de cinco (5) para evitar deformar el diseño de la página.</p>
		</div>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Nombre</th>
					<th>Zona</th>
					<th>Nivel</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $monsters as $monster )
				<tr>
					<td class="span1">
						<div class="text-center">
							{{ Form::checkbox("monsters[]", $monster->id, Input::old("monsters[$monster->id]", isset($monstersInDungeon[$monster->id]))) }}
						</div>
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}">
							{{ $monster->name }}
						</div>
					</td>
					<td>
						{{ $monster->zone->name }}
					</td>
					<td>
						Nivel {{ $monster->level }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<hr>

		<h1>Recompensas por terminar el dungeon</h1>
		<div class="span11">
			<p>Esta(s) recomenza(s) solamente se da(n) una vez en cada nivel de dificultad.</p>
			<p><b>Ejemplo:</b> Un jugador termina la mazmorra en nivel iniciante. Si no ha completado ya la mazmorra en esta dificultad, se le da recompensa.</p>
			<p>Se recuerda además que cada monstruo dará sus propias recompensas (haya o no completado la mazmorra).</p>
			<p class="alert alert-info">
				Formula recomendada para las monedas (cobre):<br>
				<code>(nivel minimo * 4 * (nivel bichos / cantidad de bichos) * 2 + duracion mazmorra) * 10</code>
			</p>
		</div>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Nombre</th>
					<th><div class="text-center">Chance de obtener</div></th>
					<th><div class="text-center">Cantidad</div></th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $items as $item )
				<tr>
					<td class="span1">
						<div class="text-center">
							{{ Form::checkbox("rewards[]", $item->id, Input::old("rewards[$item->id]", isset($rewardsDungeon[$item->id]))) }}
						</div>
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $item->get_text_for_tooltip() }}">
							{{ $item->name }}
						</div>
					</td>
					<td>
						<div class="text-center">
							{{ Form::number("chance[$item->id]", Input::old("chance[$item->id]", isset($rewardsDungeon[$item->id]) ? $rewardsDungeon[$item->id]["chance"] : 0), array("class" => "span4")) }}%
						</div>
					</td>
					<td>
						<div class="text-center">
							{{ Form::number("amount[$item->id]", Input::old("amount[$item->id]", isset($rewardsDungeon[$item->id]) ? $rewardsDungeon[$item->id]["amount"] : 0), array("class" => "span6")) }}
						</div>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<div class="control-group text-center">
			{{ Form::submit(( $dungeon->exists ) ? "Editar" : "Crear", array("class" => "btn btn-primary btn-large")) }}
		</div>

	{{ Form::close() }}
</div>