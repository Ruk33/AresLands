<h2>Ranking</h2>

<div class="span11">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Raza</th>
				<th>Nombre</th>
				<th>Grupo</th>
				<th>Puntos de PVP</th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $characters as $character )
			<tr>
				<td><img src="/img/icons/race/{{ $character->race }}_{{ $character->gender }}.jpg" alt=""></td>
				<td>{{ $character->get_link() }}</td>
				<td>
					@if ( $character->clan )
						{{ $character->clan->get_link() }}
					@else
						Sin grupo
					@endif
				</td>
				<td>{{ $character->pvp_points }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>