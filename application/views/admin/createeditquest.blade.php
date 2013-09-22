<?php $editMode = isset($quest); ?>

<ul class="breadcrumb">
	<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
	<li><a href="{{ URL::to('admin/quest') }}">Misiones</a> <span class="divider">/</span></li>
	<li class="active">Crear/editar mision</li>
</ul>

@if ( $editMode )
<h2>Editar mision</h2>
@else
<h2>Crear quest</h2>
@endif

{{ Form::open('admin/quest') }}

{{ Form::hidden('questId', ( $editMode ) ? $quest->id : 0 ) }}

@if ( $editMode )
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
<label>class_name</label>
{{ Form::text('class_name', Input::old('class_name', ( $editMode ) ? $quest->class_name : 'Quest_'), array('class' => 'input-block-level')) }}
</div>

<div>
<label>name</label>
{{ Form::text('name', Input::old('name', ( $editMode ) ? $quest->name : ''), array('class' => 'input-block-level')) }}
</div>

<div>
<label>description</label>
{{ Form::textarea('description', Input::old('description', ( $editMode ) ? $quest->description : ''), array('id' => 'description')) }}
</div>

<h4>Nivel mínimo y máximo</h4>
<div>
<label>min_level</label>
{{ Form::number('min_level', Input::old('min_level', ( $editMode ) ? $quest->min_level : 0)) }}
</div>

<div>
<label>max_level</label>
{{ Form::number('max_level', Input::old('max_level', ( $editMode ) ? $quest->max_level : 0)) }}
</div>

<h4>¿Es repetible?</h4>
<div>
repeatable {{ Form::checkbox('repeatable', null, Input::old('repeatable', ( $editMode ) ? $quest->repeatable : 0)) }}
</div>

<h4>Repetible luego de... (en segundos)</h4>
<div>
<label>repeatable_after</label>
{{ Form::number('repeatable_after', Input::old('repeatable_after', ( $editMode ) ? $quest->repeatable_after : 0)) }}
</div>

<h4>¿Diaria?</h4>
<div>
daily {{ Form::checkbox('daily', null, Input::old('daily', ( $editMode ) ? $quest->daily : 0)) }}
</div>

<h4>Razas y géneros que pueden realizar la misión</h4>
<?php $races = array('dwarf', 'elf', 'drow', 'human'); ?>

@foreach ( $races as $race )
	<div>
	<label>{{ $race }}</label>
	{{ Form::select($race, array('none' => 'ninguno', 'male' => 'masculino', 'female' => 'femenino', 'both' => 'ambos'), Input::old($race, ( $editMode ) ? $quest->$race : 'both')) }}
	</div>
@endforeach

<h4>Misión requerida</h4>
<div>
	<label>complete_required</label>
	{{ Form::select('complete_required', array('ninguna', 'misiones' => Quest::lists('name', 'id')), Input::old('complete_required', ( $editMode ) ? $quest->complete_required : 0)) }}
</div>

<?php $events = array('acceptQuest', 'npcTalk', 'pveBattle', 'pveBattleWin', 'equipItem', 'unequipItem'); ?>

<h4>Eventos</h4>
@foreach ( $events as $event )
	<div>
	{{ Form::checkbox('events[]', $event, Input::old($event, ( $editMode && isset($triggers[$event]) ) ? 1 : 0)) }} {{ $event }}
	</div>
@endforeach

<h4>Recompensas</h4>
<ul class="inline">
@foreach ( $items as $item )
	<li class="text-center" style="vertical-align: top; padding: 5px; margin-bottom: 10px;">
	<label for="{{ $item->id }}">
		<div class="inventory-item">
			<img src="{{ URL::base() }}/img/icons/items/{{ $item->id }}.png" alt="" data-toggle="tooltip" data-placement="top" data-original-title="{{ $item->get_text_for_tooltip() }}">
		</div>
	</label>
	<div>{{ Form::number("rewardsAmount[$item->id]", Input::old("rewardsAmount[$item->id]", ( $editMode && isset($rewards[$item->id]) ) ? $rewards[$item->id] : 0), array('style' => 'width: 50px;', 'data-toggle' => 'tooltip', 'data-original-title' => 'Cantidad')) }}</div>
	{{ Form::checkbox('rewards[]', $item->id, Input::old('rewards[]', ( $editMode && isset($rewards[$item->id]) ) ? 1 : 0)) }}
	</li>
@endforeach
</ul>

<div class="text-center">
	{{ Form::submit(( $editMode ) ? 'Editar mision' : 'Crear mision', array('class' => 'btn btn-large btn-primary')) }}
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