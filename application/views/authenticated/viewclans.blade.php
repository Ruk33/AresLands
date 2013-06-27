@if ( $character->clan_id == 0 )
	<h2>Crear grupo</h2>
	<a href="{{ URL::to('authenticated/createClan') }}" class="btn btn-primary">Crear grupo</a>
@endif

<h2>Grupos</h2>

<div class="span11">
	@if ( count($clans) > 0 )
		<table class="table table-hover">
			<thead>
				<tr>
					<td>Nombre del grupo</td>
					<td>Lider</td>
				</tr>
			</thead>

			<tbody>
			@foreach ( $clans as $clan )
				<tr>
					<td><a href="{{ URL::to('authenticated/clan/' . $clan->id) }}">{{{ $clan->name }}}</a></td>
					<td><a href="{{ URL::to('authenticated/character/' . $clan->lider->name) }}">{{ $clan->lider->name }}</a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@else
		<p>No hay grupos...</p>
	@endif
</div>