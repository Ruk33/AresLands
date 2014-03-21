<h2>Reporte de batalla</h2>

<?php

$character = $battle->get_attacker();
$winner = $battle->get_winner();
$loser = $battle->get_loser();
$rewards = $battle->get_reward_log();
$log = $battle->get_log();

?>

<img src="{{ URL::base() }}/img/characters/{{ $character->race }}_{{ $character->gender }}_
			@if ( $character->id == $winner->id)
			win
			@else
			lose
			@endif
			.png" width="180px" height="181px" class="pull-right" alt="">

@if ( count($rewards) > 0 )
<h2>Recompensas</h2>
<ul class="unstyled">
	@foreach ( $rewards as $reward )
	<li>{{ $reward }}</li>
	@endforeach
</ul>
@endif

<h2>Informacion</h2>
<ul class="unstyled">
	<li>Vida inicial de {{ $winner->name }}: {{ $battle->get_initial_life_of($winner) }}</li>
	<li>Vida inicial de {{ $loser->name }}: {{ $battle->get_initial_life_of($loser) }}</li>
</ul>

<ul class="unstyled">
	<li>Daño realizado por {{ $winner->name }}: {{ $battle->get_damage_done_by($winner) }}</li>
	<li>Daño realizado por {{ $loser->name }}: {{ $battle->get_damage_done_by($loser) }}</li>
</ul>

<h2>Desarrollo de la pelea</h2>
@foreach ( $log as $message )
<p>{{ $message }}</p>
@endforeach