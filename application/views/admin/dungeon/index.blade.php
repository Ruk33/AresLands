<div class="row">
	<div class="span12">
		<ul class="breadcrumb">
			<li><a href="{{ URL::to('admin') }}">Panel de administracion</a> <span class="divider">/</span></li>
			<li class="active">Dungeons</li>
		</ul>

		<h1>Dungeons</h1>

		<a href="{{ URL::to_route('get_admin_dungeon_create') }}">Crear nuevo dungeon</a>

		<ul class="unstyled">
			@foreach ( $dungeons as $dungeon )
			<li class="clan-member-link">
				<span>{{ $dungeon->name }}</span>
				<span>(<a href="{{ URL::to_route('get_admin_dungeon_edit', array($dungeon->id)) }}">Modificar</a>)</span>
				<span class="pull-right">
					<a onclick="return confirm('¿Seguro que queres borrar la mazmorra {{ $dungeon->name }}?');" href="{{ URL::to_route("get_admin_dungeon_delete", array($dungeon->id)) }}">Borrar</a>
				</span>
			</li>
			@endforeach
		</ul>
	</div>
</div>