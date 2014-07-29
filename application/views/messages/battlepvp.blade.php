<h2>Reporte de batalla</h2>

<?php

	$winner = $battle->get_winner();
	$loser = $battle->get_loser();
	$pair = $battle->get_pair();
	$stolenOrb = $battle->get_stolen_orb();
	$log = $battle->get_log();
    $attackerLog = $battle->get_unit_log($battle->get_attacker());
    $targetLog = $battle->get_unit_log($battle->get_target());

?>

<ul class="inline">
	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $winner->race }}_{{ $winner->gender }}_win.png" alt="" width="180px" height="181px">
			<h1>{{ $winner->name }}</h1>
		</div>
	</li>

	<li style="vertical-align: 100px; width: 175px;">
		<p class="text-center" style="font-family: georgia; font-size: 32px;">vs</p>
	</li>

	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $loser->race }}_{{ $loser->gender }}_lose.png" alt="" width="180px" height="181px">
			<h1>{{ $loser->name }}</h1>
		</div>
	</li>
</ul>

@if ( $stolenOrb )
<h2>¡Orbe robado!</h2>
<p>{{ $winner->name }} ha robado a {{ $loser->name }} el orbe {{ $stolenOrb->name }}</p>
@endif

<table class="table table-striped brown-table">
    <thead>
        <tr>
            <th class="span3">Basico</th>
            <th class="span4"><div class="text-center">{{ $battle->get_attacker()->name }}</div></th>
            <th class="span4"><div class="text-center">{{ $battle->get_target()->name }}</div></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>Ganador</b></td>
            <td>
                <div class="text-center">
                @if ($battle->get_attacker()->id == $battle->get_winner()->id)
                    <i class="icon-ok icon-white"></i>
                @else
                    <i class="icon-remove icon-white"></i>
                @endif
                </div>
            </td>
            <td>
                <div class="text-center">
                @if ($battle->get_target()->id == $battle->get_winner()->id)
                    <i class="icon-ok icon-white"></i>
                @else
                    <i class="icon-remove icon-white"></i>
                @endif
                </div>
            </td>
        </tr>
        
        <tr>
            <td><b>Recompensas</b></td>
            <td><div class="text-center">{{ $battle->get_rewards_for_view($battle->get_attacker()) }}</div></td>
            <td><div class="text-center">{{ $battle->get_rewards_for_view($battle->get_target()) }}</div></td>
        </tr>
        
        <tr>
            <td><b>Vida inicial</b></td>
            <td><div class="text-center">{{ (int) $battle->get_initial_life_of($battle->get_attacker()) }}</div></td>
            <td><div class="text-center">{{ (int) $battle->get_initial_life_of($battle->get_target()) }}</div></td>
        </tr>
        
        <tr>
            <td><b>Daño realizado</b></td>
            <td>
                <div class="text-center">
                    {{ $battle->get_damage_done_by($battle->get_attacker()) }}
                    @if ($battle->get_pair())
                        <span data-toggle="tooltip" data-original-title="Pareja">
                            +{{ $battle->get_damage_done_by($battle->get_pair()) }}
                        </span>
                    @endif
                </div>
            </td>
            <td><div class="text-center">{{ $battle->get_damage_done_by($battle->get_target()) }}</div></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th class="span3">Desarrollo</th>
            <th class="span4"><div class="text-center">{{ $battle->get_attacker()->name }}</div></th>
            <th class="span4"><div class="text-center">{{ $battle->get_target()->name }}</div></th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 0, $max = count($attackerLog); $i < $max; $i++) 
        <tr>
            <td>
                @if ($attackerLog[$i]['magical'])
                <div class="magical-attack">Golpe magico</div>
                @else
                <div class="physical-attack">Golpe fisico</div>
                @endif
            </td>
            <td><div class="text-center">{{ $attackerLog[$i]['message'] }}</div></td>
            <td><div class="text-center">{{ $targetLog[$i]['message'] }}</div></td>
        </tr>
        @endfor
    </tbody>
</table>