<h2>Reporte de batalla</h2>

<?php

	$winner = $battle->get_winner();
	$loser = $battle->get_loser();
	$pair = $battle->get_pair();
	$rewards = $battle->get_reward_log();
	$stolenOrb = $battle->get_stolen_orb();
	$log = $battle->get_log();

?>

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

@if ( count($rewards) > 0 )
<h2>Recompensas</h2>
<ul class="unstyled">
	@foreach ( $rewards as $reward )
	<li>{{ $reward }}</li>
	@endforeach
</ul>
@endif

@if ( $stolenOrb )
<h2>¡Orbe robado!</h2>
<p>{{ $winner->name }} ha robado a {{ $loser->name }} el orbe {{ $stolenOrb->name }}</p>
@endif

<h2>Informacion</h2>
<ul class="unstyled">
	<li>Vida inicial de {{ $winner->name }}: {{ $battle->get_initial_life_of($winner) }}</li>
	<li>Vida inicial de {{ $loser->name }}: {{ $battle->get_initial_life_of($loser) }}</li>
</ul>

<ul class="unstyled">
	<li>Daño realizado por {{ $winner->name }}: {{ $battle->get_damage_done_by($winner) }}</li>
	<li>Daño realizado por {{ $loser->name }}: {{ $battle->get_damage_done_by($loser) }}</li>
	@if ( $pair )
	<li>Daño realizado por {{ $pair->name }}: {{ $battle->get_damage_done_by($pair) }}</li>
	@endif
</ul>

<h2>Desarrollo de la pelea</h2>
@foreach ( $log as $message )
	<p>{{ $message }}</p>
@endforeach