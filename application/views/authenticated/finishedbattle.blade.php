<ul class="inline">
	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $character_two->race }}_{{ $character_two->gender }}_
			@if ( $character_two->id == $winner->id)
			win
			@else
			lose
			@endif
			.png" alt="" width="180px" height="181px">

			<h3>{{ $character_two->name }}</h3>
		</div>
	</li>

	<li style="vertical-align: 100px; width: 175px;">
		<p class="text-center" style="font-family: georgia; font-size: 32px;">contra</p>
	</li>

	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $character_one->race }}_{{ $character_one->gender }}_
			@if ( $character_one->id == $winner->id)
			win
			@else
			lose
			@endif
			.png" alt="" width="180px" height="181px">

			<h3>{{ $character_one->name }}</h3>
		</div>
	</li>
</ul>

<h2>Desarrollo de la pelea</h2>
<p>{{ $message }}</p>