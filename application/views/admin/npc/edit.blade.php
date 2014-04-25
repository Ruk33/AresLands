<div class="row">
	<ul class="breadcrumb">
		<li><a href="{{ URL::to('admin') }}">Panel de administracion</a> <span class="divider">/</span></li>
		<li><a href="{{ URL::to_route('get_admin_npc_index') }}">NPCs</a> <span class="divider">/</span></li>
		<li class="active">
			@if ( $npc->exists )
				Editar npc
			@else
				Crear npc
			@endif
		</li>
	</ul>

	@if ( $npc->exists )
		<h1>Editar npc</h1>
		{{ Form::open(URL::to_route("post_admin_npc_edit"), "POST", array("class" => "form-horizontal")) }}
	@else
		<h1>Crear npc</h1>
		{{ Form::open(URL::to_route("post_admin_npc_create"), "POST", array("class" => "form-horizontal")) }}
	@endif

		{{ Form::hidden("id", $npc->id) }}

		<div class="control-group text-center">
			{{ Form::submit(( $npc->exists ) ? "Editar" : "Crear", array("class" => "btn btn-primary btn-large")) }}
		</div>

		<div class="control-group">
			{{ Form::label("name", "Nombre del NPC", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::text("name", Input::old("name", $npc->name), array("class" => "span12")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("dialog", "Dialogo", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::textarea("dialog", Input::old("dialog", $npc->dialog), array("class" => "span12", "id" => "dialog")) }}
				<span class="help-block">Solo si el NPC es de tipo "Mercader"</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("tooltip_dialog", "Dialogo corto", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::textarea("tooltip_dialog", Input::old("tooltip_dialog", $npc->tooltip_dialog), array("class" => "span12", "id" => "tooltip_dialog")) }}
				<span class="help-block">Solo si el NPC es de tipo "Mercader"</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("zone_id", "Zona", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("zone_id", $zones, Input::old("zone_id", (string) $npc->zone_id), array("autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("level_to_appear", "Nivel en el que aparece", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("level_to_appear", Input::old("level_to_appear", $npc->level_to_appear)) }}
				<span class="help-block">Solo si el NPC es de tipo "Mercader"</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("type", "Tipo", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::select("type", array("npc" => "Mercader", "monster" => "Monstruo"), Input::old("type", $npc->type), array("autocomplete" => "off")) }}
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("level", "Nivel", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("level", Input::old("level", $npc->level)) }}
				<span class="help-block">Solo si el NPC es de tipo "Monstruo"</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("life", "Vida", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("life", Input::old("life", $npc->life)) }}
				<span class="help-block">Solo si el NPC es de tipo "Monstruo"</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_strength", "Fuerza", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_strength", Input::old("stat_strength", $npc->stat_strength)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular fuerza:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 8) * 1.7</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_dexterity", "Destreza", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_dexterity", Input::old("stat_dexterity", $npc->stat_dexterity)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular destreza:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 9) * 2</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_resistance", "Resistencia fisica", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_resistance", Input::old("stat_resistance", $npc->stat_resistance)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular resistencia:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 7) * 1.2</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_magic", "Magia", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_magic", Input::old("stat_magic", $npc->stat_magic)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular magia:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 8) * 1.9</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_magic_skill", "Destreza magica", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_magic_skill", Input::old("stat_magic_skill", $npc->stat_magic_skill)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular destreza magica:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 9) * 2.1</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("stat_magic_resistance", "Resistencia magica", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("stat_magic_resistance", Input::old("stat_magic_resistance", $npc->stat_magic_resistance)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<div class="alert alert-info">
						Formula utilizada para calcular resistencia magica:
						<code>(nivel * rand(warrior, warrior * 2) * especial) + rand(nivel * 5, nivel * 7) * 1.5</code>
						<p>
							Si el npc es guerrero entonces warrior = 1, de lo contrario es 2 <br>
							Si el npc es especial, entonces especial = 2, de lo contrario es 1
						</p>
					</div>
				</span>
			</div>
		</div>

		<div class="control-group">
			{{ Form::label("xp", "Experiencia", array("class" => "control-label")) }}
			<div class="controls">
				{{ Form::number("xp", Input::old("xp", $npc->xp)) }}
				<span class="help-block">
					<p>Solo si el NPC es de tipo "Monstruo"</p>
					<p>Esta es la experiencia aproximada (ya que los rates globales y los del personaje modifican directamente este valor) que dará al personaje que logre derrotarlo</p>
				</span>
			</div>
		</div>

		<hr>

		<h1>Mercancias <small>(solo si el npc es de tipo "Mercader")</small></h1>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Nombre</th>
					<th>Precio (en cobre)</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $items as $item )
				<tr>
					<td class="span1">
						<div class="text-center">
							{{ Form::checkbox("merchandises[]", $item->id, Input::old("merchandises[$item->id]", isset($merchandises[$item->id]))) }}
						</div>
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $item->get_text_for_tooltip() }}">
							{{ $item->name }}
						</div>
					</td>
					<td>
						{{ Form::number("merchandises_prices[$item->id]", Input::old("merchandises_prices[$item->id]", ( isset($merchandises[$item->id]) ) ? $merchandises[$item->id]['price_copper'] : 0 )) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<hr>

		<h1>Misiones <small>(solo si el NPC es de tipo "Mercader")</small></h1>
		
		<ul class="unstyled">
			@foreach ( $quests as $quest )
			<li>
				<div class="clan-member-link">
					<div class="span1 text-center">
						{{ Form::checkbox("quests[]", $quest->id, isset($npc_quests[$quest->id])) }}
					</div>
					<a href="{{ URL::to('admin/quest/' . $quest->id . '/edit') }}" target="_blank">
						{{ $quest->name }}
					</a>
				</div>
			</li>
			@endforeach
		</ul>

		<hr>

		<h1>Drop <small>(solo si el NPC es de tipo "Monstruo")</small></h1>

		<table class="table">
			<thead>
				<tr>
					<th></th>
					<th>Nombre</th>
					<th>Chance</th>
					<th>Cantidad</th>
				</tr>
			</thead>
			<tbody>
				@foreach ( $items as $item )
				<tr>
					<td class="span1">
						<div class="text-center">
							{{ Form::checkbox("drops[]", $item->id, Input::old("drops[$item->id]", isset($drops[$item->id]))) }}
						</div>
					</td>
					<td>
						<div data-toggle="tooltip" data-original-title="{{ $item->get_text_for_tooltip() }}">
							{{ $item->name }}
						</div>
					</td>
					<td>
						{{ Form::number("drop_chance[$item->id]", Input::old("drop_chance[$item->id]", ( isset($drops[$item->id]) ) ? $drops[$item->id]['chance'] : 0 )) }}
					</td>
					<td>
						{{ Form::number("drop_amount[$item->id]", Input::old("drop_amount[$item->id]", ( isset($drops[$item->id]) ) ? $drops[$item->id]['amount'] : 0 )) }}
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

		<div class="control-group text-center">
			{{ Form::submit(( $npc->exists ) ? "Editar" : "Crear", array("class" => "btn btn-primary btn-large")) }}
		</div>
</div>

<script src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>

<script>
	var config = {
		language: 'es',
		disableObjectResizing: true,
		extraPlugins: '',
		removePlugins: '',
		toolbar: null,
		scayt_autoStartup: true,
		scayt_sLang: 'es_ES'
	}

	CKEDITOR.replace('dialog', config);
	CKEDITOR.replace('tooltip_dialog', config);
</script>