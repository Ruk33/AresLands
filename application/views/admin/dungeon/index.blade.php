<h1>Dungeons</h1>

<a href="{{ URL::to_route('get_admin_dungeon_create') }}">Crear nuevo dungeon</a>

<div class="row">
<ul class="unstyled">
	@foreach ( $dungeons as $dungeon )
	<li class="clan-member-link">
		<span>{{ $dungeon->name }}</span>
		<span>(<a href="{{ URL::to_route('get_admin_dungeon_edit', array($dungeon->id)) }}">Modificar</a>)</span>
		<span class="pull-right">
			<a href="{{ URL::to_route("get_admin_dungeon_delete", array($dungeon->id)) }}">Borrar</a>
		</span>
	</li>
	@endforeach
</ul>
</div>