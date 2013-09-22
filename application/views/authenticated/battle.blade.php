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
<ul class="inline battle-monsters-content" style="margin-left: 20px;">
	@foreach ( $monsters as $monster )
	<li class="text-center" style="width: 150px; vertical-align: top; margin-bottom: 25px;">
		@if ( $monster->level - $character->level > 10 )
		<h4 style="color: #F52700;">
		@elseif ( $monster->level - $character->level > 5 )
		<h4 style="color: orange;">
		@else
		<h4 style="color: white;">
		@endif
		{{ $monster->name }}</h4>

		<img src="{{ URL::base() }}/img/npcs/{{ $monster->id }}.jpg" alt="" width="128px" height="128px">

		<p>{{ $monster->dialog }}</p>

		<a href="{{ URL::to('authenticated/toBattleMonster/' . $monster->id) }}" class="btn btn-warning" style="color: white;">Atacar</a>
	</li>
	@endforeach
</ul>
@endif