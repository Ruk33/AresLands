<ul class="breadcrumb">
	<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
	<li><a href="{{ URL::to('admin/quest') }}">Misiones</a> <span class="divider">/</span></li>
	<li class="active">Crear/editar mision</li>
</ul>

<style>
	.tooltip {
		position: fixed;
		z-index: 10000;
	}
</style>

<div class="row">

@if ( $quest->exists )
<h2>Editar mision</h2>
@else
<h2>Crear quest</h2>
@endif

{{ Form::open('admin/quest') }}

{{ Form::hidden('questId', $quest->id ) }}

@if ( $quest->exists )
	<div class="alert alert-error">
		<h3 class="text-center">Borrar mision</h3>
		<p>
			<strong>Nota:</strong> Esto no solo borrará la misión, sino que también borrará el progreso de los personajes que tengan aceptada y/o hayan completado dicha mision.
		</p>

		<div class="text-center">
			<a href="{{ URL::to('admin/quest/' . $quest->id . '/delete') }}" onclick="return confirm('¿Seguro?');" class="btn btn-danger">Borrar mision</a>
		</div>
	</div>
@endif

<div>
<label>name</label>
{{ Form::text('name', Input::old('name', $quest->name), array('class' => 'input-block-level')) }}
</div>

<div>
<label>description</label>
{{ Form::textarea('description', Input::old('description', $quest->description), array('id' => 'description')) }}
</div>

<h4>Nivel mínimo y máximo</h4>
<div>
<label>min_level</label>
{{ Form::number('min_level', Input::old('min_level', $quest->min_level)) }}
</div>

<div>
<label>max_level</label>
{{ Form::number('max_level', Input::old('max_level', $quest->max_level)) }}
</div>

<h4>¿Es repetible?</h4>
<div>
repeatable {{ Form::checkbox('repeatable', null, Input::old('repeatable', $quest->repeatable)) }}
</div>

<h4>Repetible luego de... (en segundos)</h4>
<div>
<label>repeatable_after</label>
{{ Form::number('repeatable_after', Input::old('repeatable_after', $quest->repeatable_after)) }}
</div>

<h4>¿Diaria?</h4>
<div>
daily {{ Form::checkbox('daily', null, Input::old('daily', $quest->daily)) }}
</div>

<h4>Razas y géneros que pueden realizar la misión</h4>
<?php $races = array('dwarf', 'elf', 'drow', 'human'); ?>

@foreach ( $races as $race )
	<div>
	<label>{{ $race }}</label>
	{{ Form::select($race, array('none' => 'ninguno', 'male' => 'masculino', 'female' => 'femenino', 'both' => 'ambos'), Input::old($race, ( $quest->exists ) ? $quest->$race : 'both')) }}
	</div>
@endforeach

<h4>Misión requerida</h4>
<div>
	<label>complete_required</label>
	{{ Form::select('complete_required', array('ninguna', 'misiones' => Quest::lists('name', 'id')), Input::old('complete_required', $quest->complete_required)) }}
</div>

<h4>NPCs con los que se debe interactuar</h4>
<ul class="inline text-center">
	@foreach ( $npcs as $npc )
	<li class="text-center clan-member-link" style="vertical-align: top; padding: 5px; margin-bottom: 10px; border: 1px solid #644e46;">
		<label for="{{ $npc->id }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $npc->get_text_for_tooltip() }}">
			<div class="box box-box-64-gold">
				<img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="">
			</div>
		</label>
		<div>{{ Form::select("action[$npc->id]", array('nada', 'kill' => 'derrotar', 'talk' => 'hablar'), Input::old("action[$npc->id]", ( isset($actions[$npc->id]) ) ? $actions[$npc->id] : null), array('style' => 'width: 65px;')) }}</div>
		<div>{{ Form::number("actionAmount[$npc->id]", Input::old("actionAmount[$npc->id]", ( isset($actionAmount[$npc->id]) ) ? $actionAmount[$npc->id] : 0), array('style' => 'width: 50px;', 'data-toggle' => 'tooltip', 'data-original-title' => 'Cantidad de veces que debe repetirse la accion')) }}</div>
	</li>
	@endforeach
</ul>

<h4>Recompensas</h4>
<ul class="inline text-center">
@foreach ( $items as $item )
	<li class="text-center clan-member-link" style="vertical-align: top; padding: 5px; margin-bottom: 10px; border: 1px solid #644e46;">
	<label for="{{ $item->id }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $item->get_text_for_tooltip() }}">
		<div class="box box-box-64-blue">
			<img src="{{ URL::base() }}/img/icons/items/{{ $item->id }}.png" alt="">
		</div>
	</label>
	<div>{{ Form::number("rewardsAmount[$item->id]", Input::old("rewardsAmount[$item->id]", ( isset($rewards[$item->id]) ) ? $rewards[$item->id] : 0), array('style' => 'width: 50px;', 'data-toggle' => 'tooltip', 'data-original-title' => 'Cantidad')) }}</div>
	{{ Form::checkbox('rewards[]', $item->id, Input::old('rewards[]', ( isset($rewards[$item->id]) ) ? 1 : 0)) }}
	</li>
@endforeach
</ul>

<div class="text-center">
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