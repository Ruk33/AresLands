<h2>Reporte de batalla</h2>

<?php

	$winner = $battle->get_winner();
	$loser = $battle->get_loser();
    $attackerLog = $battle->get_unit_log($battle->get_attacker());
    $targetLog = $battle->get_unit_log($battle->get_target());
    $characterImg = URL::base() . "/img/characters/{$battle->get_attacker()->race}_{$battle->get_attacker()->gender}_";
    
    if ($battle->get_attacker()->id == $winner->id) {
        $characterImg .= "win.png";
    } else {
        $characterImg .= "lose.png";
    }
    
?>

<div class="text-center">
<img src="{{ $characterImg }}" width="180px" height="181px" alt="">
</div>

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