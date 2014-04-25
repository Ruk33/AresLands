<div class="row">

	<ul class="breadcrumb">
		<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
		<li><a href="{{ URL::to('admin/quest') }}">Misiones</a> <span class="divider">/</span></li>
		<li class="active">Crear/editar mision</li>
	</ul>

	@if ( $quest->exists )
	<h1>Editar mision</h1>
	@else
	<h1>Crear quest</h1>
	@endif

	{{ Form::open('admin/quest', 'POST', array("class" => "form-horizontal")) }}

	{{ Form::hidden('questId', $quest->id ) }}

	@if ( $quest->exists )
		<div class="alert alert-warning">
			<h1>Borrar mision</h1>
			<p>
				<strong>Nota:</strong> Esto no solo borrará la misión, sino que también borrará el progreso de los personajes que tengan aceptada y/o hayan completado dicha mision.
			</p>

			<div class="text-center">
				<a href="{{ URL::to('admin/quest/' . $quest->id . '/delete') }}" onclick="return confirm('¿Seguro?');" class="btn btn-danger">Borrar mision</a>
			</div>
		</div>
	@endif

	<div class="control-group text-center">
		{{ Form::submit(( $quest->exists ) ? 'Editar mision' : 'Crear mision', array('class' => 'btn btn-large btn-primary')) }}
	</div>

	<div class="control-group">
		{{ Form::label("name", "Nombre de la mision", array("class" => "control-label")) }}
		<div class="controls">
			{{ Form::text('name', Input::old('name', $quest->name), array('class' => 'input-block-level')) }}
		</div>
	</div>

	<div class="control-group">
		{{ Form::label("description", "Descripcion", array("class" => "control-label")) }}
		<div class="controls">
			{{ Form::textarea('description', Input::old('description', $quest->description), array('id' => 'description')) }}
		</div>
	</div>

	<div class="control-group">
		{{ Form::label("min_level", "Nivel minimo", array("class" => "control-label")) }}
		<div class="controls">
			{{ Form::number('min_level', Input::old('min_level', $quest->min_level)) }}
		</div>
	</div>

	<div class="control-group">
		{{ Form::label("max_level", "Nivel maximo", array("class" => "control-label")) }}
		<div class="controls">
			{{ Form::number('max_level', Input::old('max_level', $quest->max_level)) }}
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">Repetible</div>
		<div class="controls">
			{{ Form::checkbox('repeatable', null, Input::old('repeatable', $quest->repeatable)) }}
		</div>
	</div>

	<div class="control-group">
		{{ Form::label("repeatable_after", "Repetible luego de", array('class' => 'control-label')) }}
		<div class="controls">
			{{ Form::number('repeatable_after', Input::old('repeatable_after', $quest->repeatable_after)) }} segundos
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">Diaria</div>
		<div class="controls">
			{{ Form::checkbox('daily', null, Input::old('daily', $quest->daily)) }}
		</div>
	</div>

	<?php $races = array('dwarf', 'elf', 'drow', 'human'); ?>

	@foreach ( $races as $race )
		<div class="control-group">
			{{ Form::label($race, ucfirst($race), array('class' => 'control-label')) }}
			<div class="controls">
				{{ Form::select($race, array('none' => 'Ninguno', 'male' => 'Masculino', 'female' => 'Femenino', 'both' => 'Ambos'), Input::old($race, ( $quest->exists ) ? $quest->$race : 'both')) }}
				tiene(n) permitido hacer la mision
			</div>
		</div>
	@endforeach

	<div class="control-group">
		{{ Form::label("complete_required", "Mision requerida", array('class' => 'control-label')) }}
		<div class="controls">
			{{ Form::select('complete_required', array('Ninguna', 'Misiones' => Quest::lists('name', 'id')), Input::old('complete_required', $quest->complete_required)) }}
		</div>
	</div>

	<hr>

	<h1>NPCs con los que se debe interactuar</h1>
	<table class="table">
		<thead>
			<tr>
				<th>Nombre</th>
				<th>Nivel</th>
				<th>Accion requerida</th>
				<th><div class="text-center">Numero de veces a repetir la accion</div></th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $npcs as $npc )
			<tr data-toggle="tooltip" data-original-title="{{ $npc->get_text_for_tooltip() }}">
				<td>
					{{ $npc->name }}
				</td>
				<td>Nivel {{ $npc->level }}</td>
				<td>
					{{ Form::select("action[$npc->id]", array('Nada', 'kill' => 'Derrotar', 'talk' => 'Hablar'), Input::old("action[$npc->id]", ( isset($actions[$npc->id]) ) ? $actions[$npc->id] : null), array('class' => 'span8')) }}
				</td>
				<td>
					<div class="text-center">
						{{ Form::number("actionAmount[$npc->id]", Input::old("actionAmount[$npc->id]", ( isset($actionAmount[$npc->id]) ) ? $actionAmount[$npc->id] : 0), array('class' => 'span3')) }}
					</div>
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<hr>

	<h1>Recompensas</h1>

	<table class="table">
		<thead>
			<tr>
				<th></th>
				<th>Nombre</th>
				<th>Cantidad</th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $items as $item )
			<tr data-toggle="tooltip" data-original-title="{{ $item->get_text_for_tooltip() }}">
				<td>
					<div class="span1">
						{{ Form::checkbox('rewards[]', $item->id, Input::old('rewards[]', ( isset($rewards[$item->id]) ) ? 1 : 0)) }}
					</div>
				</td>
				<td>{{ $item->name }}</td>
				<td>{{ Form::number("rewardsAmount[$item->id]", Input::old("rewardsAmount[$item->id]", ( isset($rewards[$item->id]) ) ? $rewards[$item->id] : 0)) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="control-group text-center">
		{{ Form::submit(( $quest->exists ) ? 'Editar mision' : 'Crear mision', array('class' => 'btn btn-large btn-primary')) }}
	</div>

	{{ Form::close() }}

	<script src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>

	<script>
		CKEDITOR.replace('description', {
			language: 'es',
			disableObjectResizing: true,
			extraPlugins: '',
			removePlugins: '',
			toolbar: null,
			scayt_autoStartup: true,
			scayt_sLang: 'es_ES'
		});
	</script>

</div>