<h2>Ranking</h2>

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
		<a href="{{ URL::to('authenticated/ranking/xp') }}" class="ui-button button">
			<i class="button-icon axe"></i>
			<span class="button-content">
				Experiencia
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
			@if ( $rank == 'xp' )
			<th width="50px">Nivel</th>
			<th width="100px">Experiencia</th>
			@endif
			@if ( $rank == 'clan' )
			<th>Puntos</th>
			@endif
		</tr>
	</thead>

	<tbody>
		<?php $index = 0; ?>
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

{{ $elements->links() }}
</div>