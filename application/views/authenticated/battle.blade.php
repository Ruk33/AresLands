<h2>¡Batallar!</h2>
<p>¿Así que quieres probar suerte con algún contrincante?</p>

<ul class="thumbnails" style="margin-left: -18px;">
	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Buscar por nombre</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'name') }}

					{{ Form::label('name_label', 'Nombre') }}
					{{ Form::text('character_name') }}

					<div>
					{{ Form::submit('¡Buscar!', ['class' => 'btn btn-primary']) }}
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>

	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Buscar aleatoriamente</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'random') }}

					{{ Form::label('race_label', 'Raza') }}
					{{ Form::select('race', [ 'any' => 'Cualquiera', 'dwarf' => 'Enano', 'human' => 'Humano', 'drow' => 'Drow', 'elf' => 'Elfo' ]) }}

					{{ Form::label('level_label', 'Nivel') }}
					{{ Form::select('operation', [ 'exactly' => 'Exactamente', 'greaterThan' => 'Mayor que', 'lowerThan' => 'Menor que' ]) }}
					{{ Form::number('level', null, ['min' => '1']) }}

					<div>
					{{ Form::submit('¡Buscar!', ['class' => 'btn btn-primary']) }}
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>

	<li class="span4">
		<div class="thumbnail">
			<div class="caption">
				<h2>Buscar en grupo</h2>
				{{ Form::open() }}
					{{ Form::hidden('search_method', 'group') }}

					{{ Form::label('group_label', 'Grupo') }}
					{{ Form::select('group_name', []) }}

					<div>
					{{ Form::submit('¡Buscar!', ['class' => 'btn btn-primary']) }}
					</div>
				{{ Form::close() }}
			</div>
		</div>
	</li>
</ul>