<div class="span11">
	<ul class="thumbnails">
		<li class="span5 text-center">
			<div class="thumbnail text-center">
				<img src="{{ URL::base() }}/img/characters/{{ $character_two->race }}_{{ $character_two->gender }}_
				@if ( $character_two->id == $winner->id)
				win
				@else
				lose
				@endif
				.png" alt="">

				<h3>{{ $character_two->name }}</h3>
			</div>
		</li>

		<li class="span1" style="margin-top: 75px;">
			<p class="text-center" style="font-family: georgia; font-size: 32px;">vs</p>
		</li>

		<li class="span5">
			<div class="thumbnail text-center">
				<img src="{{ URL::base() }}/img/characters/{{ $character_one->race }}_{{ $character_one->gender }}_
				@if ( $character_one->id == $winner->id)
				win
				@else
				lose
				@endif
				.png" alt="">

				<h3>{{ $character_one->name }}</h3>
			</div>
		</li>
	</ul>

	<h2>Desarrollo de la pelea</h2>
	<p>{{ $message }}</p>
</div>