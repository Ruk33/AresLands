<h2>¡Batallar!</h2>
@if ( Session::has('errorMessage') )
	<div class="alert alert-error text-center">
		{{ Session::get('errorMessage') }}
	</div>
@endif

<p>¿Así que quieres probar suerte con algún contrincante?, pues adelante, elige tu reto.</p>

<h2 style="margin-bottom: -25px; margin-top: 50px;">Personajes</h2>
<ul style="margin-left: -15px;">
	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h4>Por nombre</h4>
				{{ Form::open() }}
					{{ Form::token() }}
					{{ Form::hidden('search_method', 'name') }}

					{{ Form::label('character_name', 'Nombre') }}
					{{ Form::text('character_name') }}

					<div class="text-center">
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
				<h4>Aleatoriamente</h4>
				{{ Form::open() }}
					{{ Form::token() }}
					{{ Form::hidden('search_method', 'random') }}

					{{ Form::label('race_label', 'Raza') }}
					{{ Form::select('race', array('any' => 'Cualquiera', 'dwarf' => 'Enano', 'human' => 'Humano', 'drow' => 'Drow', 'elf' => 'Elfo')) }}

					{{ Form::label('level_label', 'Nivel') }}
					{{ Form::select('operation', array('exactly' => 'Exactamente', 'greaterThan' => 'Mayor que', 'lowerThan' => 'Menor que')) }}
					{{ Form::number('level', $character->level, array('min' => '1')) }}

					<div class="text-center">
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
				<h4>En grupo</h4>
				{{ Form::open() }}
					{{ Form::token() }}
					{{ Form::hidden('search_method', 'group') }}

					{{ Form::label('clan', 'Grupo') }}
					{{ Form::select('clan', Clan::lists('name', 'id')) }}

					<div class="text-center">
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
<h2 style="margin-top: 50px;">Monstruos</h2>
<ul class="inline battle-monsters-content" style="margin-left: -7px;">
	@foreach ( $monsters as $monster )
	<li class="text-center clan-member-link" style="width: 30%; vertical-align: top; margin-bottom: 15px; border: 1px solid #322924; box-shadow: black 0 0 5px;" data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}">
		<img src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" alt="" width="32px" height="32px" class="monster-image">
		
		{{ Form::open(URL::to('authenticated/toBattleMonster')) }}
			{{ Form::token() }}
			{{ Form::hidden('monster_id', $monster->id) }}
			
			{{ Form::submit($monster->name, array('class' => 'btn btn-link ' . $monster->get_color_class($character))) }}
		{{ Form::close() }}
	</li>
	@endforeach
</ul>
@endif