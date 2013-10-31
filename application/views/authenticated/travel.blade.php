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

	<ul class="inline text-center">
		@foreach ( $zones as $zone )
		<li class="clan-member-link text-left" style="width: 45%; vertical-align: top;">
			<a href="{{ URL::to('authenticated/travel/' . $zone->id) }}" onclick="return confirm('¿Seguro que quieres viajar a {{ $zone->name }}?');">
			<ul class="inline">
				<li style="vertical-align: top; width: 32px;">
					<img src="{{ URL::base() }}/img/zones/32/{{ $zone->id }}.png" alt="">
				</li>
				<li style="width: 250px;">
					<strong>{{ $zone->name }}</strong>
					<p style="font-size: 12px;">{{ $zone->description }}</p>
					<p><strong>Tiempo explorado:</strong><br>
					@if ( isset($exploringTime[$zone->id]) && $exploringTime[$zone->id] > 0 )
					{{ date('z \d\í\a\(\s\) H:i:s', $exploringTime[$zone->id]) }}
					@else
					00 días 00:00:00
					@endif</p>
				</li>
			</ul>
			</a>
		</li>
		@endforeach
	</ul>
</div>