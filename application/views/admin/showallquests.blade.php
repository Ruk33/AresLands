<ul class="breadcrumb">
	<li><a href="{{ URL::to('admin/index') }}">Panel de administración</a> <span class="divider">/</span></li>
	<li class="active">Misiones</li>
</ul>

<a href="{{ URL::to('admin/quest/0/create') }}" class="btn btn-primary">Crear mision</a>

<ul>
@foreach ( $quests as $quest )
	<li>
		(ID: {{ $quest->id }}) <a href="{{ URL::to('admin/quest/' . $quest->id . '/edit') }}">{{ $quest->name }}</a>
	</li>
@endforeach
</ul>