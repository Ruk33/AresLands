<div style="margin-bottom: 50px;">
	<h2>Talentos</h2>
	<p>Los talentos son habilidades especiales que puedes lanzarte a ti mismo, a los miembros de tu grupo o inclusive a otros jugadores.</p>
	<p>Cada 5 niveles, 1 punto se te es otorgado para que aprendas estas habilidades. Recuerda que solamente puedes aprender 8.</p>
	<p>Para lanzar una habilidad a un personaje (o a ti mismo), simplemente buscalo por el <a href="{{ URL::to('authenticated/ranking') }}" target="_blank">ranking</a> y te aparecerán las habilidades que puedas conjurar.</p>
</div>

<div style="margin-bottom: 50px;">
	<h2>Tus puntos</h2>
	<strong>Tus puntos de talentos:</strong> {{ $character->talent_points }}
</div>

<div style="margin-bottom: 50px;">
	<h2>Raciales</h2>
	<p>Estas son las habilidades que obtienes por tu raza</p>

	<ul class="thumbnails">
	@foreach ( $racials as $skill )
		<li class="span4">
			<div class="clan-member-link thumbnail" style="height: 250px;">
				<div class="text-center">
					<img src="{{ $skill->get_image_path() }}" />
				</div>
				<div class="caption" style="height: 150px;">
					<div class="text-center" style="margin-bottom: 10px;">
						<strong style="color: white;">{{ $skill->name }}</strong>
					</div>
					<p style="color: gold; font-size: 12px;">{{ $skill->description }}</p>
				</div>

				<div class="text-center">
					@if ( $character->has_talent($skill) )
						<?php $talent = $character->talents()->where('skill_id', '=', $skill->id)->first(); ?>
						@if ( $character->can_use_talent($talent) )
							¡Listo para usar!
						@else
							<div class="timer" data-endtime="{{ $talent->usable_at - time() }}"></div>
						@endif
					@else
						@if ( $character->can_learn_talent($skill) )
							{{ Form::open(URL::to_route("post_authenticated_talent_learn")) }}
								{{ Form::token() }}
								{{ Form::hidden('id', $skill->id) }}

								{{ Form::submit('Aprender', array('class' => 'btn btn-link')) }}
							{{ Form::close() }}
						@endif
					@endif
				</div>
			</div>
		</li>
	@endforeach
	</ul>
</div>

<h2>Caracteristicas</h2>
<p>Estas son las habilidades que destrabaste al elegir tus caracteristicas</p>

@foreach ( $talents as $name => $characteristic )
	<strong>{{ $name }}</strong>
	<ul class="inline" style="margin-bottom: 180px;">
	@foreach ( $characteristic as $skill )
		<li class="span6">
			<div class="alert-container">
				<img src="{{ URL::base() }}/img/icons/skills/{{ $skill->id }}.png" class="pull-left" />
				<div style="margin-left: 75px;">
					<strong style="color: white;">{{ $skill->name }}</strong>
					<div style="font-size: 13px;">
						<p><span style="color: gold;">{{ $skill->description }}</span><br>
						@if ( $skill->duration > 0 )
							Duracion: {{ $skill->duration }} minuto(s)</p>
						@endif
					</div>
				</div>
				
				<div style="position: absolute; bottom: 5px; right: 10px;">
					@if ( $character->has_talent($skill) )
						<?php $talent = $character->talents()->where('skill_id', '=', $skill->id)->first(); ?>
						@if ( $character->can_use_talent($talent) )
							Aprendida
						@else
							<div class="timer" data-endtime="{{ $talent->usable_at - time() }}"></div>
						@endif
					@else
						@if ( $character->can_learn_talent($skill) )
							{{ Form::open(URL::to_route("post_authenticated_talent_learn")) }}
								{{ Form::token() }}
								{{ Form::hidden('id', $skill->id) }}

								{{ Form::submit('Aprender', array('class' => 'btn btn-link')) }}
							{{ Form::close() }}
						@endif
					@endif
				</div>
			</div>
		</li>
	@endforeach
	</ul>
@endforeach