<ul class="inline">
	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $winner->race }}_{{ $winner->gender }}_win.png" alt="" width="180px" height="181px">
			<h3>{{ $winner->name }}</h3>
		</div>
	</li>

	<li style="vertical-align: 100px; width: 175px;">
		<p class="text-center" style="font-family: georgia; font-size: 32px;">contra</p>
	</li>

	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $loser->race }}_{{ $loser->gender }}_lose.png" alt="" width="180px" height="181px">
			<h3>{{ $loser->name }}</h3>
		</div>
	</li>
</ul>

<h2>Desarrollo de la pelea</h2>
<p>{{ $message }}</p>