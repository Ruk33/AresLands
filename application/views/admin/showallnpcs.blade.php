<ul class="breadcrumb">
	<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
	<li class="active">Npcs</li>
</ul>

<a href="{{ URL::to('admin/npc/0/create') }}" class="btn btn-primary">Crear npc</a>

<ul>
@foreach ( $npcs as $npc )
	<li>
		(ID: {{ $npc->id }}) <a href="{{ URL::to('admin/npc/' . $npc->id . '/edit') }}">{{ $npc->name }} ({{ $npc->type }})</a>
	</li>
@endforeach
</ul>