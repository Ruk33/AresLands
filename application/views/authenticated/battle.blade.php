<h2>¡Batallar!</h2>

@if ( Session::has('errorMessage') )
	<div class="alert alert-error">
		{{ Session::get('errorMessage') }}
	</div>
@endif

<p>¿Así que quieres probar suerte con algún contrincante?</p>

<h2 style="margin-bottom: -25px;">Personajes</h2>
<ul style="margin-left: -15px;">
	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Por nombre</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'name') }}

					{{ Form::label('character_name', 'Nombre') }}
					{{ Form::text('character_name') }}

					<div>
						<span class="ui-button button">
							<i class="button-icon axe"></i>
							<span class="button-content">
								{{ Form::submit('Buscar por nombre', array('class' => 'ui-button ui-input-button')) }}
							</span>
						</span>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>

	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Aleatoriamente</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'random') }}

					{{ Form::label('race_label', 'Raza') }}
					{{ Form::select('race', array('any' => 'Cualquiera', 'dwarf' => 'Enano', 'human' => 'Humano', 'drow' => 'Drow', 'elf' => 'Elfo')) }}

					{{ Form::label('level_label', 'Nivel') }}
					{{ Form::select('operation', array('exactly' => 'Exactamente', 'greaterThan' => 'Mayor que', 'lowerThan' => 'Menor que')) }}
					{{ Form::number('level', $character->level, array('min' => '1')) }}

					<div>
						<span class="ui-button button">
							<i class="button-icon thunder"></i>
							<span class="button-content">
								{{ Form::submit('Buscar aleatoriamente', array('class' => 'ui-button ui-input-button')) }}
							</span>
						</span>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>

	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>En grupo</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'group') }}

					{{ Form::label('clan', 'Grupo') }}
					{{ Form::select('clan', Clan::lists('name', 'id')) }}

					<div>
						<span class="ui-button button">
							<i class="button-icon dagger"></i>
							<span class="button-content">
								{{ Form::submit('Buscar en grupo', array('class' => 'ui-button ui-input-button')) }}
							</span>
						</span>
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>
</ul>

<div class="clearfix"></div>

@if ( count($monsters) > 0 )
<h2>Monstruos</h2>
<ul class="inline battle-monsters-content" style="margin-left: -7px;">
	@foreach ( $monsters as $monster )
	<li class="text-center clan-member-link" style="width: 30%; vertical-align: top; margin-bottom: 25px;" data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}">
		<img src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" alt="" width="32px" height="32px" class="monster-image">
		
		<a href="{{ URL::to('authenticated/toBattleMonster/' . $monster->id) }}">
		@if ( $monster->level - $character->level > 10 )
		<strong style="color: #F52700;">
		@elseif ( $monster->level - $character->level > 5 )
		<strong style="color: orange;">
		@else
		<strong style="color: white;">
		@endif
		{{ $monster->name }}</strong></a>
	</li>
	@endforeach
</ul>
@endif