<h2>Desarrollo de la pelea</h2>
<img src="{{ URL::base() }}/img/characters/{{ $character->race }}_{{ $character->gender }}_
			@if ( $character->id == $winner->id)
			win
			@else
			lose
			@endif
			.png" width="180px" height="181px" class="pull-right" alt="">
<p>{{ $message }}</p>