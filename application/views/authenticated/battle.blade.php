<h2>¡Batallar!</h2>

<div class="span11">

	@if ( Session::has('errorMessage') )
		<div class="alert alert-error">
			{{ Session::get('errorMessage') }}
		</div>
	@endif

<p>¿Así que quieres probar suerte con algún contrincante?</p>

<ul class="thumbnails" style="margin-left: -18px;">
	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Por nombre</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'name') }}

					{{ Form::label('name_label', 'Nombre') }}
					{{ Form::text('character_name') }}

					<div>
					{{ Form::submit('¡Buscar por nombre!', array('class' => 'normal-button', 'style' => 'width: 222px;')) }}
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
					{{ Form::submit('¡Buscar aleatoriamente!', array('class' => 'normal-button', 'style' => 'width: 222px;')) }}
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

					{{ Form::label('group_label', 'Grupo') }}
					{{ Form::select('clan', Clan::lists('name', 'id')) }}

					<div>
					{{ Form::submit('¡Buscar en grupo!', array('class' => 'normal-button', 'style' => 'width: 222px;')) }}
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>
</ul>
</div>