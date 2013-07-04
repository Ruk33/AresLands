<h2>Viajar</h2>

<div class="span11">
	@if ( $error )
		<div class="alert alert-error">
			<strong>¡Alto viajero!</strong>
			<p>{{ $error }}</p>
		</div>
	@endif

	<p>
		Te encuentras con varios caminos, sientes gran intriga, ¿cuál elegir?.
	</p>

	<p>
		<b>{{ $character->name }}:</b> Si viajo, compraré provisiones. Calculo que gastaré {{ Config::get('game.travel_cost') }} <img src="/img/copper.gif" alt="">
	</p>

	<ul class="thumbnails">
		@foreach ( $cities as $city )
			<li class="span4">
				<div class="thumbnail">
					<div class="caption">
						<img src="{{ URL::base() }}/img/zones/{{ $city->id }}.jpg" alt="" class="text-center">

						<h4>{{ $city->name }}</h4>
						<p>{{ $city->description }}</p>

						<a href="{{ URL::to('authenticated/travel/' . $city->id) }}" class="btn btn-primary">Viajar a la ciudad</a>

						@if ( count($city->villages) > 0 )
						<div style="margin-top: 15px;">
						<strong>Villas</strong>
						<ul>
							@foreach ( $city->villages as $village )
							<li><a href="{{ URL::to('authenticated/travel/' . $village->id) }}">{{ $village->name }}</a></li>
							@endforeach
						</ul>
						</div>
						@endif

						@if ( count($city->farm_zones) > 0 )
						<div style="margin-top: 15px;">
						<strong>Zonas de farmeo</strong>
						<ul>
							@foreach ( $city->farm_zones as $farm_zones )
							<li><a href="{{ URL::to('authenticated/travel/' . $farm_zones->id) }}">{{ $farm_zones->name }}</a></li>
							@endforeach
						</ul>
						</div>
						@endif
					</div>
				</div>
			</li>
		@endforeach
	</ul>
</div>