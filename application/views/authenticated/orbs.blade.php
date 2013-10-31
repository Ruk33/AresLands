<div class="orb-content">
	<div style="margin-left: 200px; color: rgb(162, 158, 147);">
		<h2>Orbes</h2>
		<p>
			<i>
				Orbes... antiguas y poderosas piedras cuyo núcleo reciéntemente ha despertado nuevamente.
				Se dice que quien posea estos raros artilugios obtendrá regalos de extrañas formas.
			</i>
		</p>
		<p>Si quieres hacerte con uno de estos extraños objetos, ¡deberás atacar a su poseedor y vencerlo!.</p>
	</div>

	<ul class="inline text-center">
		@foreach ( $orbs as $orb )
			<li style="vertical-align: top; width: 230px; margin-bottom: 25px;">
				<h2>{{ $orb->name }}</h2>
				
				@if ( $character->level > $orb->max_level || $character->level < $orb->min_level )
				<img class="grayEffect" src="{{ URL::base() }}/img/icons/orbs/{{ $orb->id }}.png" width="150px" height="150px" data-toggle="tooltip" data-title="<h6>{{ $orb->name }}</h6><p class='negative'>No cumples con los requisitos</p><p class='text-left'>{{ $orb->description }}</p><ul class='unstyled'><li class='positive'><b>Recompensas que otorga este orbe:</b></li><li><strong>Monedas:</strong> {{ $orb->coins }}</li><li><strong>Puntos:</strong> {{ $orb->points }}</li></ul><ul class='unstyled'><li class='negative'><b>Requerimientos:</b></li><li><strong>Nivel mínimo:</strong> {{ $orb->min_level }}</li><li><b>Nivel máximo:</b> {{ $orb->max_level }}</li></ul>">
				@else
				<img src="{{ URL::base() }}/img/icons/orbs/{{ $orb->id }}.png" width="150px" height="150px" data-toggle="tooltip" data-title="<h6>{{ $orb->name }}</h6><p class='text-left'>{{ $orb->description }}</p><ul class='unstyled'><li class='positive'><b>Recompensas que otorga este orbe:</b></li><li><strong>Monedas:</strong> {{ $orb->coins }}</li><li><strong>Puntos:</strong> {{ $orb->points }}</li></ul><ul class='unstyled'><li class='negative'><b>Requerimientos:</b></li><li><strong>Nivel mínimo:</strong> {{ $orb->min_level }}</li><li><b>Nivel máximo:</b> {{ $orb->max_level }}</li></ul>">
				@endif
				
				<p>
					<strong>Poseedor:</strong> 
					@if ( $orb->owner_character ) 
						{{ $orb->owner()->select(array('name'))->first()->get_link() }} 
					@else 
						Nadie 
					@endif
				</p>
				
				@if ( $orb->last_attack_time && $orb->last_attacker )
				<p>
					<strong>Último ataque:</strong> 
					{{ date('H:i:s d/m/y', $orb->last_attack_time) }} <br>
					por {{ $orb->attacker()->select(array('name'))->first()->get_link() }}
				</p>
				@endif
			</li>
		@endforeach
	</ul>
</div>