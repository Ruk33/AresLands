<h1 class="text-center">Simulacion de {{ $battleSimulator->getBattleSimulatorReport()->getSimulationAmount() }} batallas</h1>

<div class="row">
    <table class="table table-striped brown-table">
        <thead>
            <tr>
                <th>Informacion</th>
                <th>
                    @if ($name = $battleSimulator->getBattleSimulatorReport()->getAttacker()->name)
                        {{ $name }}
                    @else
                        Atacante
                    @endif
                </th>
                <th>
                    @if ($name = $battleSimulator->getBattleSimulatorReport()->getTarget()->name)
                        {{ $name }}
                    @else
                        Objetivo
                    @endif
                </th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>Victorias</td>
                <td>{{ $battleSimulator->getBattleSimulatorReport()->getAttackerVictories() }}</td>
                <td>{{ $battleSimulator->getBattleSimulatorReport()->getTargetVictories() }}</td>
            </tr>
            
            <tr>
                <td>Derrotas</td>
                <td>{{ $battleSimulator->getBattleSimulatorReport()->getAttackerLoses() }}</td>
                <td>{{ $battleSimulator->getBattleSimulatorReport()->getTargetLoses() }}</td>
            </tr>
            
            @foreach ($battleSimulator->getBattleSimulatorReport()->getBattles() as $i => $battle)
            <tr>
                <td>Battalla {{ ++$i }}</td>
                <td>{{ $battle->getAttackerReport()->getDamageDone() }}</td>
                <td>{{ $battle->getTargetReport()->getDamageDone() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<a href="{{ URL::to_route("get_admin_battle_simulator_index") }}">Crear otra simulacion</a>