<h2>Reporte de batalla</h2>

<ul class="inline">
	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $winner->race }}_{{ $winner->gender }}_win.png" alt="" width="180px" height="181px">
            <h1>{{ $winner->get_link() }}</h1>
		</div>
	</li>

	<li style="vertical-align: 100px; width: 175px;">
		<p class="text-center" style="font-family: georgia; font-size: 32px;">contra</p>
	</li>

	<li style="width: 250px;">
		<div class="thumbnail text-center">
			<img src="{{ URL::base() }}/img/characters/{{ $loser->race }}_{{ $loser->gender }}_lose.png" alt="" width="180px" height="181px">
            <h1>{{ $loser->get_link() }}</h1>
		</div>
	</li>
</ul>

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
                @if ($battle->getAttacker()->id == $winner->id)
                    <i class="icon-ok icon-white"></i>
                @else
                    <i class="icon-remove icon-white"></i>
                @endif
                </div>
            </td>
            <td>
                <div class="text-center">
                @if ($battle->getTarget()->id == $winner->id)
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
            <td>
                <div class="text-center">
                    {{ (int) $attacker["damageDone"] }}
                    @if ($battle->getPair())
                        <span data-toggle="tooltip" data-original-title="Pareja">
                            +{{ (int) $battle->getPairReport()->getDamageDone() }}
                        </span>
                    @endif
                </div>
            </td>
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