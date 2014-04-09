<h2>Calabozos</h2>

<div class="row text-center">
	<ul class="unstyled">
		@foreach ( $dungeons as $dungeon )
		<li>
			<div class="span12 dungeon">
				<div class="dungeon-container">
					<div class="span6">	
						{{ Form::open(URL::to('authenticated/dungeon')) }}
							{{ Form::token() }}
							{{ Form::hidden('id', $dungeon->id) }}
							{{ Form::submit($dungeon->name, array('class' => 'ui-button ui-input-button')) }}
						{{ Form::close() }}
						<span class="dungeon-average-level">Nivel promedio: {{ $dungeon->get_average_level() }}</span>
					</div>
					<div class="span6">
						<div class="dungeon-well">
							<ul class="inline">
								@foreach ( $dungeon->monsters()->order_by('level', 'asc')->get() as $monster )
								<li>
									@if ( $dungeon->can_character_see_stats_of_monster($character, $monster) )
										@if ( $dungeon->character_has_defeated_monster($character, $monster) )
										<img src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}<br><span class='positive'>DERROTADO</span>" class="monster-image grayEffect">
										@else
										<img src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}" class="monster-image">
										@endif
									@else
										<img src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" data-toggle="tooltip" data-original-title="Aun no puedes ver la informacion de este mounstruo. ¡Primero debes derrotarlo!" class="monster-image">
									@endif
								</li>
								@endforeach
							</ul>
						</div>
					</div>

					<div style="margin-left: 50px; padding-top: 75px;">
						<div class="row">
							<div class="span4">
								<div class="progress progress-dungeon">
									<div class="text progress-text-dungeon">Tu progreso en dificultad novato</div>
									<div class="bar bar-dungeon-noob" style="width: {{ $dungeon->get_progress_percent_of($character, Dungeon::NOOB_LEVEL) }}%"></div>
								</div>
							</div>

							<div class="span4">
								<div class="progress progress-dungeon">
									<div class="text progress-text-dungeon">Tu progreso en dificultad normal</div>
									<div class="bar bar-dungeon-normal" style="width: {{ $dungeon->get_progress_percent_of($character, Dungeon::NORMAL_LEVEL) }}%"></div>
								</div>
							</div>

							<div class="span4">
								<div class="progress progress-dungeon">
									<div class="text progress-text-dungeon">Tu progreso en dificultad experto</div>
									<div class="bar bar-dungeon-expert" style="width: {{ $dungeon->get_progress_percent_of($character, Dungeon::EXPERT_LEVEL) }}%"></div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="span12">
								<div class="progress progress-dungeon">
									<div class="text progress-text-dungeon">Tu progreso en dificultad ELITE</div>
									<div class="bar bar-dungeon-elite" style="width: {{ $dungeon->get_progress_percent_of($character, Dungeon::ELITE_LEVEL) }}%"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</li>
		@endforeach
	</ul>
</div>