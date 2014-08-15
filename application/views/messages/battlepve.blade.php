<h2>Reporte de batalla</h2>

<div class="text-center">
@if ($winner == $battle->getAttacker())
<img src="{{ URL::base() . "/img/characters/{$battle->getAttacker()->race}_{$battle->getAttacker()->gender}_win.png" }}" width="180px" height="181px" alt="">
@else
<img src="{{ URL::base() . "/img/characters/{$battle->getAttacker()->race}_{$battle->getAttacker()->gender}_lose.png" }}" width="180px" height="181px" alt="">
@endif
</div>

<table class="table table-striped brown-table">
    <thead>
        <tr>
            <th class="span3">Basico</th>
            <th class="span4"><div class="text-center">{{ $battle->getAttacker()->name }}</div></th>
            <th class="span4"><div class="text-center">{{ $battle->getTarget()->name }}</div></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>Ganador</b></td>
            <td>
                <div class="text-center">
                @if ($battle->getAttacker() == $winner)
                    <i class="icon-ok icon-white"></i>
                @else
                    <i class="icon-remove icon-white"></i>
                @endif
                </div>
            </td>
            <td>
                <div class="text-center">
                @if ($battle->getTarget() == $winner)
                    <i class="icon-ok icon-white"></i>
                @else
                    <i class="icon-remove icon-white"></i>
                @endif
                </div>
            </td>
        </tr>
        
        <tr>
            <td><b>Recompensas</b></td>
            <td><div class="text-center">{{ $battle->getAttackerReport()->getRewardsForView() }}</div></td>
            <td><div class="text-center">{{ $battle->getTargetReport()->getRewardsForView() }}</div></td>
        </tr>
        
        <tr>
            <td><b>Vida inicial</b></td>
            <td><div class="text-center">{{ (int) $attacker["initialLife"] }}</div></td>
            <td><div class="text-center">{{ (int) $target["initialLife"] }}</div></td>
        </tr>
        
        <tr>
            <td><b>Daño realizado</b></td>
            <td><div class="text-center">{{ (int) $attacker["damageDone"] }}</div></td>
            <td><div class="text-center">{{ (int) $target["damageDone"] }}</div></td>
        </tr>
        
        <tr>
            <td><b>Daño recibido</b></td>
            <td><div class="text-center">{{ (int) $attacker["damageTaken"] }}</div></td>
            <td><div class="text-center">{{ (int) $target["damageTaken"] }}</div></td>
        </tr>
    </tbody>
</table>

<table class="table table-striped brown-table">
    <thead>
        <tr>
            <th class="span6"><div class="text-center">{{ $battle->getAttacker()->name }}</div></th>
            <th class="span6"><div class="text-center">{{ $battle->getTarget()->name }}</div></th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 0, $max = count($attacker["damageMessages"]); $i < $max; $i++) 
        <tr>
            <td><div class="text-center">{{ $attacker["damageMessages"][$i] }}</div></td>
            <td><div class="text-center">{{ $target["damageMessages"][$i] }}</div></td>
        </tr>
        @endfor
    </tbody>
</table>