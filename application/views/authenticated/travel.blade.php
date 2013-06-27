<h2>Viajar</h2>

@if ( $error )
	<p>{{ $error }}</p>
@endif

<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, enim, delectus praesentium cum minima iste amet harum rem debitis illo pariatur assumenda explicabo illum deleniti impedit officia quae labore voluptatum!</p>

<ul class="thumbnails">
	@foreach ( $zones as $zone )
		<li class="span4">
			<div class="thumbnail">
				<div class="caption">
					<img src="/img/zones/{{ $zone->id }}.jpg" alt="" class="text-center">

					<h4>{{ $zone->name }}</h4>
					<p>{{ $zone->description }}</p>

					<a href="{{ URL::to('authenticated/travel/' . $zone->id) }}">Viajar</a>
				</div>
			</div>
		</li>
	@endforeach
</ul>