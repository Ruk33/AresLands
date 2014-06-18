@if ( $rank == 'kingOfTheHill' )
	<h2>Rey de la colina</h2>
@elseif ( $rank == 'pvp' )
	<h2>Ranking Jugador contra Jugador</h2>
@elseif ( $rank == 'clan' )
	<h2>Ranking Grupos</h2>
@endif

<div class="row">
<ul class="inline text-center span11" style="margin-top: 20px;">
	<li>
		<a href="{{ URL::to('authenticated/ranking/pvp') }}" class="ui-button button">
			<i class="button-icon dagger"></i>
			<span class="button-content">
				PVP
			</span>
		</a>
	</li>
	<li>
		<a href="{{ URL::to('authenticated/ranking/kingOfTheHill') }}" class="ui-button button">
			<i class="button-icon axe"></i>
			<span class="button-content">
				Rey de la colina
			</span>
		</a>
	</li>
	<li>
		<a href="{{ URL::to('authenticated/ranking/clan') }}" class="ui-button button">
			<i class="button-icon fire"></i>
			<span class="button-content">
				Grupos
			</span>
		</a>
	</li>
</ul>

@if ( $rank != 'kingOfTheHill' )
<div class="text-center" style="margin-top: 100px;">
	{{ $elements->links() }}
</div>
@endif

<table class="table table-striped brown-table">
	<thead>
		<tr>
			<th width="20px">#</th>
			@if ( $rank != 'clan' )
			<th width="50px">Raza</th>
			@endif
			<th>Nombre</th>
			@if ( $rank != 'clan' )
			<th>Grupo</th>
			@endif
			@if ( $rank == 'pvp' )
			<th width="150px">Puntos de PVP</th>
			@endif
			@if ( $rank == 'kingOfHill' )
			<th width="50px">Grupo</th>
			<th width="100px">Nivel</th>
            <th>Dias invicto</th>
			@endif
			@if ( $rank == 'clan' )
			<th>Puntos</th>
			@endif
		</tr>
	</thead>

	<tbody>
		<?php $index = $elements->per_page * ($elements->page - 1); ?>
		@foreach ( $elements->results as $element )
		<tr>
			<td>{{ ++$index }}</td>
			@if ( $rank != 'clan' )
			<td>
				<div class="icon-race-30 icon-race-30-{{ $element->race }}_{{ $element->gender }}"></div>
			</td>
			@endif

			<td>
				@if ( $index == 1 )
					<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
				@elseif ( $index == 2 )
					<img src="{{ URL::base() }}/img/icons/crown-silver-icon.png" alt="">
				@elseif ( $index == 3 )
					<img src="{{ URL::base() }}/img/icons/crown-bronze-icon.png" alt="">
				@endif
				@if ( $rank == 'clan' )
					@if ( $element->clan )
					{{ $element->clan->get_link() }}
					@endif
				@else
				{{ $element->get_link() }}
				@endif
			</td>
			@if ( $rank != 'clan' )
			<td>
				@if ( $element->clan_id )
				{{ $element->clan->get_link() }}
				@else
				Sin grupo
				@endif
			</td>
			@endif
			@if ( $rank == 'pvp' )
			<td>{{ $element->pvp_points }}</td>
			@endif
			@if ( $rank == 'xp' )
			<td>{{ $element->level }}</td>
			<td>{{ $element->xp }}</td>
			@endif
			@if ( $rank == 'clan' )
			<td>{{ $element->points }}</td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>

<div class="text-center">
	{{ $elements->links() }}
</div>
</div>