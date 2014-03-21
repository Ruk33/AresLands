<h2>Reporte de batalla</h2>

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

<h2>Informacion</h2>
<ul class="unstyled">
	<li>Daño realizado por {{ $winner->name }}: {{ $damageByWinner }}</li>
	<li>Daño realizado por {{ $loser->name }}: {{ $damageByLoser }}</li>
	@if ( $pair )
	<li>Daño realizado por {{ $pair->name }}: {{ $damageByPair }}</li>
	@endif
</ul>

<h2>Desarrollo de la pelea</h2>
@foreach ( $log as $message )
	<p>{{ $message }}</p>
@endforeach