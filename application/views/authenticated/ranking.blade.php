<h2>Ranking</h2>

<div class="span11">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>#</th>
				<th>Raza</th>
				<th>Nombre</th>
				<th>Puntos de PVP</th>
			</tr>
		</thead>

		<tbody>
			<?php $index = $characters->page; ?>
			@foreach ( $characters->results as $character )
			<tr>
				<td>{{ $index++ }}</td>
				<td><img src="{{ URL::base() }}/img/icons/race/{{ $character->race }}_{{ $character->gender }}.jpg" alt=""></td>
				<td>
					@if ( $index - 1 == 1 )
						<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
					@elseif ( $index - 1 == 2 )
						<img src="{{ URL::base() }}/img/icons/crown-silver-icon.png" alt="">
					@elseif ( $index - 1 == 3 )
						<img src="{{ URL::base() }}/img/icons/crown-bronze-icon.png" alt="">
					@endif
					{{ $character->get_link() }}
				</td>
				<td>{{ $character->pvp_points }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	{{ $characters->links() }}
</div>