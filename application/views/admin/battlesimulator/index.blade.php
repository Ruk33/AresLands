<ul class="inline">
    <li><a href="{{ URL::to('admin') }}">Panel de administracion</a></li>
    <li><span class="divider">/</span></li>
    <li class="active">Simulador de batallas</li>
</ul>

<h1>Simulador de batallas</h1>
<i>Porque probar las batallas manualmente es muy mainstream</i>

<div style="padding: 25px;">
    {{ Form::open(URL::to_route("post_admin_battle_simulator_execute")) }}

    <div class="row">
        <div class="span6">
            {{ $battleSimulator->getAmountInput() }}
        </div>

        <div class="span6">
            {{ $battleSimulator->getBattleTypeInput() }}
        </div>
    </div>

    <div class="row" style="margin-top: 25px;">
        <div class="row alert alert-info text-center">
            <b>Formula de experiencia (puntos de atributo):</b> 
            <code>10 * nivel * (nivel + 1) / 2</code>.
            <br>
            <small>Gracias Nicolas Ignacio Gomez (muZk).</small>
        </div>
        
        <div class="row alert alert-info text-center">
            <b>Formula para la vida:</b> 
            <code>40 * nivel * (nivel + 1) / 2 - 40</code>. 
            <br>
            <small>Se recuerda que algunas razas comienzan con vidas diferentes, asi 
            que al resultado de esta formula se le debe sumar la vida inicial.</small>
        </div>
        
        <div class="row">
            <div class="span6">
                <h2>Atacante <small>(generalmente personaje)</small></h2>
                {{ $battleSimulator->getUnitInputs(BattleSimulator::ATTACKER_INPUT) }}
            </div>

            <div class="span6">
                <h2>Objetivo <small>(generalmente monstruo)</small></h2>
                {{ $battleSimulator->getUnitInputs(BattleSimulator::TARGET_INPUT) }}
            </div>
        </div>
    </div>

    <div class="text-center" style="margin-top: 25px;">
        {{ $battleSimulator->getSubmitInput() }}
    </div>

    {{ Form::close() }}
</div>