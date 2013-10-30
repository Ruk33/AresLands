<?php $editMode = isset($npc); ?>

<ul class="breadcrumb">
	<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
	<li><a href="{{ URL::to('admin/npc') }}">Npcs</a> <span class="divider">/</span></li>
	<li class="active">Crear/editar mision</li>
</ul>

@if ( $editMode )
<h2>Editar npc</h2>

<center><img src="{{ URL::base() }}/img/icons/npcs/{{ $npc->id }}.png" alt="" /></center>
@else
<h2>Crear npc</h2>
@endif

{{ Form::open('admin/npc') }}

{{ Form::hidden('npcId', ( $editMode ) ? $npc->id : 0 ) }}

@if ( $editMode )
	<div class="alert alert-error">
		<h3 class="text-center">Borrar npc</h3>

		<div class="text-center">
			<a href="{{ URL::to('admin/npc/' . $npc->id . '/delete') }}" onclick="return confirm('¿Seguro?');" class="btn btn-danger">Borrar npc</a>
		</div>
	</div>
@endif

<div>
<label>name</label>
{{ Form::text('name', Input::old('name', ( $editMode ) ? $npc->name : ''), array('class' => 'input-block-level')) }}
</div>

<div>
<label>dialog</label>
{{ Form::textarea('dialog', Input::old('dialog', ( $editMode ) ? $npc->dialog : ''), array('id' => 'dialog')) }}
</div>

<div>
<label>tooltip_dialog</label>
{{ Form::textarea('tooltip_dialog', Input::old('tooltip_dialog', ( $editMode ) ? $npc->tooltip_dialog : ''), array('class' => 'input-block-level')) }}
</div>

<h4>Zona y tiempo para aparecer</h4>
<div>
<label>zone</label>
{{ Form::select('zone_id', array('zonas' => Zone::lists('name', 'id')), Input::old('zone_id', ( $editMode ) ? $npc->zone_id : 0)) }}
</div>

<div>
<label>time_to_appear</label>
{{ Form::number('time_to_appear', Input::old('time_to_appear', ( $editMode ) ? $npc->time_to_appear : 0)) }}
</div>

<h4>Tipo (npc, monstruo, etc.)</h4>
<div>
<label>type</label>
{{ Form::select('type', array('npc' => 'npc', 'monster' => 'monster', 'boss' => 'boss'), Input::old('type', ( $editMode ) ? $npc->type : 'npc')) }}
</div>

<h4>Atributos</h4>
<div>
<label>level</label>
{{ Form::number('level', Input::old('level', ( $editMode ) ? $npc->level : 0)) }}
</div>

<div>
<label>life</label>
{{ Form::number('life', Input::old('life', ( $editMode ) ? $npc->life : 0)) }}
</div>

<div>
<label>stat_strength</label>
{{ Form::number('stat_strength', Input::old('stat_strength', ( $editMode ) ? $npc->stat_strength : 0)) }}
</div>

<div>
<label>stat_dexterity</label>
{{ Form::number('stat_dexterity', Input::old('stat_dexterity', ( $editMode ) ? $npc->stat_dexterity : 0)) }}
</div>

<div>
<label>stat_resistance</label>
{{ Form::number('stat_resistance', Input::old('stat_resistance', ( $editMode ) ? $npc->stat_resistance : 0)) }}
</div>

<div>
<label>stat_magic</label>
{{ Form::number('stat_magic', Input::old('stat_magic', ( $editMode ) ? $npc->stat_magic : 0)) }}
</div>

<div>
<label>stat_magic_skill</label>
{{ Form::number('stat_magic_skill', Input::old('stat_magic_skill', ( $editMode ) ? $npc->stat_magic_skill : 0)) }}
</div>

<div>
<label>stat_magic_resistance</label>
{{ Form::number('stat_magic_resistance', Input::old('stat_magic_resistance', ( $editMode ) ? $npc->stat_magic_resistance : 0)) }}
</div>

<h4>Armas/escudos</h4>
<div>
<label>lhand</label>
{{ Form::select('lhand', array(0 => 'ninguna', 'armas/escudos' => Item::where_not_in('type', array('none', 'mercenary', 'etc', 'arrow', 'potion'))->lists('name', 'id')), Input::old('lhand', ( $editMode ) ? $npc->lhand : 0)) }}
</div>

<div>
<label>rhand</label>
{{ Form::select('rhand', array(0 => 'ninguna', 'armas/escudos' => Item::where_not_in('type', array('none', 'mercenary', 'etc', 'arrow', 'potion'))->lists('name', 'id')), Input::old('rhand', ( $editMode ) ? $npc->rhand : 0)) }}
</div>

<h4>Defensas</h4>
<div>
<label>p_defense</label>
{{ Form::number('p_defense', Input::old('p_defense', ( $editMode ) ? $npc->p_defense : 0)) }}
</div>

<div>
<label>m_defense</label>
{{ Form::number('m_defense', Input::old('m_defense', ( $editMode ) ? $npc->m_defense : 0)) }}
</div>

<h4>Experiencia</h4>
<div>
<label>xp</label>
{{ Form::number('xp', Input::old('xp', ( $editMode ) ? $npc->xp : 0)) }}
</div>

<h4>Mercancias (solo si es de tipo npc)</h4>
<ul class="inline">
@foreach ( $items as $item )
	<li class="text-center" style="vertical-align: top; padding: 5px; margin-bottom: 10px;" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{{ $item->get_text_for_tooltip() }}">
	<label for="{{ $item->id }}">
		<div class="inventory-item">
			<img src="{{ URL::base() }}/img/icons/items/{{ $item->id }}.png" alt="">
		</div>
	</label>
	<div>{{ Form::number("merchandisesPrice[$item->id]", Input::old("merchandisesPrice[$item->id]", ( $editMode && isset($merchandisesPrice[$item->id]) ) ? $merchandisesPrice[$item->id] : 0), array('style' => 'width: 50px;', 'data-toggle' => 'tooltip', 'data-original-title' => 'Precio')) }}</div>
	{{ Form::checkbox('merchandises[]', $item->id, Input::old('merchandises[]', ( $editMode && isset($merchandisesPrice[$item->id]) ) ? 1 : 0)) }}
	</li>
@endforeach
</ul>

<h4>Misiones</h4>
<ul class="inline">
	@foreach ( $quests as $quest )
	<li class="clan-member-link">
		{{ Form::checkbox('quests[]', $quest->id, Input::old('quests[]', ( $editMode && isset($npcQuests[$quest->id]) ) ? 1 : 0)) }}
		<a href="{{ URL::to('admin/quest/' . $quest->id . '/edit') }}" target="__blank">{{ $quest->name }}</a>
	</li>
	@endforeach
</ul>

<div class="text-center">
	{{ Form::submit(( $editMode ) ? 'Editar npc' : 'Crear npc', array('class' => 'btn btn-large btn-primary')) }}
</div>

{{ Form::close() }}

<script src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>

<script>
	CKEDITOR.replace('dialog', {
		language: 'es',
		disableObjectResizing: true,
		extraPlugins: '',
		removePlugins: '',
		toolbar: null,
		scayt_autoStartup: true,
		scayt_sLang: 'es_ES'
	});
</script>