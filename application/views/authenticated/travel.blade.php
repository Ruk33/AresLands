<h2>Viajar</h2>

<div class="span11">
	@if ( $error )
		<div class="alert alert-error">
			<strong>¡Alto viajero!</strong>
			<p>{{ $error }}</p>
		</div>
	@endif

	<p>
		Te encuentras con varios caminos. Sientes gran intriga, ¿cuál elegir?.
	</p>

	<p>
		<b>{{ $character->name }}:</b> Si viajo, compraré provisiones. Estimo que gastaré {{ Config::get('game.travel_cost') }} <i class="coin coin-copper" style="display: inline-block;"></i>
	</p>

	@foreach ( $lands as $land )
	<div class="clan-member-link">
		<img src="{{ URL::base() }}/img/zones/32/unknown.png" class="text-center" style="margin-right: 5px;" width="32px" height="32px" alt="">
		
		@if ( $character->zone_id == $land->id )
			{{ $land->name }}
		@elseif ( $character->level < $land->min_level )
			<s>{{ $land->name }}</s> (nivel necesario: {{ $land->min_level }})
		@else
			<a href="{{ URL::to('authenticated/travel/' . $land->id) }}">{{ $land->name }}</a>
		@endif

		<p>{{ $land->description }}</p>

		<ul class="unstyled">
			<li><strong>Zonas de farmeo</strong></li>
			@foreach ( $land->farm_zones as $farmZone )
				<li class="dark-box" style="margin-bottom: 10px;">
					@if ( $character->zone_id == $farmZone->id )
						{{ $farmZone->name }}
					@elseif ( $character->level < $farmZone->min_level )
						<s>{{ $farmZone->name }}</s> (nivel necesario: {{ $farmZone->min_level }})
					@else
						<a href="{{ URL::to('authenticated/travel/' . $farmZone->id) }}">{{ $farmZone->name }}</a>
					@endif

					<p>{{ $farmZone->description }}</p>

					<ul class="inline clan-member-link">
						<li><strong>NPCs:</strong></li>
						@foreach ( $farmZone->npcs()->select(array('name', 'zone_id', 'level'))->get() as $npc )
						<li>{{ $npc->name }} ({{ $npc->level }})</li>
						@endforeach
					</ul>
				</li>
			@endforeach
		</ul>

		<ul class="unstyled">
			<li><strong>Ciudades</strong></li>
			@foreach ( $land->cities as $city )
			<li class="dark-box" style="margin-bottom: 10px;">
				<img src="{{ URL::base() }}/img/zones/32/{{ $city->id }}.png" class="text-center" style="margin-right: 5px;" alt="">
				
				@if ( $character->zone_id == $city->id )
					{{ $city->name }}
				@elseif ( $character->level < $city->min_level )
					<s>{{ $city->name }}</s> (nivel necesario: {{ $city->min_level }})
				@else
					<a href="{{ URL::to('authenticated/travel/' . $city->id) }}">{{ $city->name }}</a>
				@endif
				
				<p>{{ $city->description }}</p>

				<ul class="inline clan-member-link">
					<li><strong>NPCs:</strong></li>
					@foreach ( $city->npcs()->select(array('name', 'zone_id', 'level'))->get() as $npc )
					<li style="margin-bottom: 10px;">{{ $npc->name }} ({{ $npc->level }})</li>
					@endforeach
				</ul>

				<!--
				<ul class="unstyled">
					<li><strong>Villas</strong></li>
					@foreach ( $city->villages as $village )
					<li class="dark-box" style="margin-bottom: 10px;">
						@if ( $character->zone_id == $village->id )
							{{ $village->name }}
						@elseif ( $character->level < $village->min_level )
							<s>{{ $village->name }}</s> (nivel necesario: {{ $village->min_level }})
						@else
							<a href="{{ URL::to('authenticated/travel/' . $village->id) }}">{{ $village->name }}</a>
						@endif

						<p>{{ $village->description }}</p>

						<ul class="inline clan-member-link">
							<li><strong>NPCs:</strong></li>
							@foreach ( $village->npcs()->select(array('name', 'zone_id', 'level'))->get() as $npc )
							<li>{{ $npc->name }} ({{ $npc->level }})</li>
							@endforeach
						</ul>
					</li>
					@endforeach
				</ul>
				-->

				<ul class="unstyled">
					<li><strong>Zonas de farmeo</strong></li>
					@foreach ( $city->farm_zones as $farmZone )
					<li class="dark-box" style="margin-bottom: 10px;">
						@if ( $character->zone_id == $farmZone->id )
							{{ $farmZone->name }}
						@elseif ( $character->level < $farmZone->min_level )
							<s>{{ $farmZone->name }}</s> (nivel necesario: {{ $farmZone->min_level }})
						@else
							<a href="{{ URL::to('authenticated/travel/' . $farmZone->id) }}">{{ $farmZone->name }}</a>
						@endif

						<p>{{ $farmZone->description }}</p>

						<ul class="inline clan-member-link">
							<li><strong>NPCs:</strong></li>
							@foreach ( $farmZone->npcs()->select(array('name', 'zone_id', 'level'))->get() as $npc )
							<li>{{ $npc->name }} ({{ $npc->level }})</li>
							@endforeach
						</ul>
					</li>
					@endforeach
				</ul>
			</li>
			@endforeach
		</ul>
	</div>
	@endforeach
</div>