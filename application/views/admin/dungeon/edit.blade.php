<div class="row">
	<h1>Editar dungeon</h1>

	{{ Form::open(URL::to_route("post_admin_dungeon_edit"), "POST", array("class" => "form-horizontal")) }}
	
		{{ Form::hidden("id", $dungeon->id) }}

		<div class="control-group">
			{{ Form::label("name", "Nombre del dungeon", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::text("name", $dungeon->name, array("class" => "span11")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("only_clan", "¿Solamente para grupos?", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("only_clan", array("No", "Si"), (string) $dungeon->only_clan, array("class" => "span11", "autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("rest_time", "Duracion de la mazmorra", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::text("rest_time", $dungeon->rest_time, array("class" => "span11")) }}
				<span class="help-block">Tiempo (en segundos) que deben esperar luego de hacer una mazmorra.</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("zone_id", "Zona", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("zone_id", $zones, (string) $dungeon->zone_id, array("class" => "span11", "autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("min_level", "Nivel minimo", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("min_level", $dungeon->min_level, array("class" => "span11")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("show_monsters_stats", "¿Mostrar atributos de los monstruos?", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("show_monsters_stats", array("No", "Si"), (string) $dungeon->show_monsters_stats, array("class" => "span11", "autocomplete" => "off")) }}
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
					<th>Nivel</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $monsters as $monster )
				<tr>
					<td>
						{{ Form::checkbox("monsters[]", $monster->id, isset($monstersInDungeon[$monster->id])) }}
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}">
							{{ $monster->name }}
						</div>
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
		</div>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Nombre</th>
					<th>Chance de obtener</th>
					<th>Cantidad</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $items as $item )
				<tr>
					<td>
						{{ Form::checkbox("rewards[]", $item->id, isset($rewardsDungeon[$item->id])) }}
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $item->get_text_for_tooltip() }}">
							{{ $item->name }}
						</div>
					</td>
					<td>
						{{ Form::number("chance[]", isset($rewardsDungeon[$item->id]) ? $rewardsDungeon[$item->id]["chance"] : 0, array("class" => "span3")) }}%
					</td>
					<td>
						{{ Form::number("amount[]", isset($rewardsDungeon[$item->id]) ? $rewardsDungeon[$item->id]["amount"] : 0, array("class" => "span3")) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<div class="control-group text-center">
			{{ Form::submit("Editar", array("class" => "btn btn-primary btn-large")) }}
		</div>

	{{ Form::close() }}
</div>