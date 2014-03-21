<h2>Mazmorras individuales</h2>
<p></p>

<div class="row">
	@foreach ( $dungeons as $dungeon )
		<div class="span3">
			<img src="{{ URL::base() }}/img/dungeons/{{ $dungeon->image }}" alt=""/>
		</div>
	@endforeach
</div>