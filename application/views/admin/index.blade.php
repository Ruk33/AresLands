<h2>Panel de administración</h2>

<ul class="unstyled">
    <li><a href="{{ URL::to('admin/quest/') }}" class="btn btn-primary">Misiones</a></li>
    <li><a href="{{ URL::to_route('get_admin_npc_index') }}" class="btn btn-primary">Npcs</a></li>
    <li><a href="{{ URL::to('admin/tournament') }}" class="btn btn-primary">Ver torneos</a></li>
    <li><a href="{{ URL::to('admin/tournament/create') }}" class="btn btn-primary">Crear torneo</a></li>
    <li><a href="{{ URL::to_route('get_admin_dungeon_index') }}" class="btn btn-primary">Mazmorras</a></li>
    <li><a href="{{ URL::to_route('get_admin_battle_simulator_index') }}" class="btn btn-primary">Simulador de batallas</a></li>
</ul>