<h2>Lista de torneos</h2>

<table style="width: 98%;" class="tournament-table">
	<thead>
		<tr>
			<td><i class="icon-info-sign" style="vertical-align: -1px;"></i> Nombre del torneo</td>
		</tr>
	</thead>

	<tbody>
	@foreach ( $tournaments as $tournament )
		<tr>
			<td><a href="{{ URL::to('authenticated/tournaments/' . $tournament->id) }}">{{ $tournament->name }}</a></td>
		</tr>
	@endforeach
	</tbody>
</table>