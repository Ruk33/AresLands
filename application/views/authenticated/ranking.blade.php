<div class="ranking-box">

<div class="row">
    <div class="span12" style="padding: 15px;">
        <ul class="inline pull-right">
            <li>
                <a href="{{ URL::to_route("get_authenticated_ranking_index", array("pvp")) }}" class="ui-button button">
                    <i class="button-icon dagger"></i>
                    <span class="button-content">
                        PVP
                    </span>
                </a>
            </li>
            <li>
                <span data-toggle="tooltip" data-original-title="¡En camino!" class="ui-button button">
                    <i class="button-icon axe"></i>
                    <span class="button-content">
                        Rey de la colina
                    </span>
                </span>
            </li>
            <li>
                <a href="{{ URL::to_route("get_authenticated_ranking_index", array("clan")) }}" class="ui-button button">
                    <i class="button-icon fire"></i>
                    <span class="button-content">
                        Grupos
                    </span>
                </a>
            </li>
        </ul>

        @if ( $rank == 'kingOfTheHill' )
            <h2>Rey de la colina</h2>
        @elseif ( $rank == 'pvp' )
            <h2>Ranking Jugador contra Jugador</h2>
        @elseif ( $rank == 'clan' )
            <h2>Ranking Grupos</h2>
        @endif
    </div>

<table class="table table-striped brown-table">
	<thead>
		<tr>
            <th width="20px"><div class="text-center">#</div></th>
			@if ( $rank != 'clan' )
            <th width="50px"><div class="text-center">Raza</div></th>
			@endif
			<th>Nombre</th>
			@if ( $rank != 'clan' )
			<th>Grupo</th>
			@endif
			@if ( $rank == 'pvp' )
            <th width="150px"><div class="text-center">Puntos de PVP</div></th>
			@endif
			@if ( $rank == 'kingOfHill' )
			<th width="50px">Grupo</th>
            <th width="100px"><div class="text-center">Nivel</div></th>
            <th>Dias invicto</th>
			@endif
			@if ( $rank == 'clan' )
            <th><div class="text-center">Puntos</div></th>
			@endif
		</tr>
	</thead>

	<tbody>
		<?php 
        
        $index = 0;
        
        if ($pagination) {
            $index = $pagination->per_page * ($pagination->page - 1); 
        }
        
        ?>
		@foreach ( $elements as $element )
		<tr>
            <td><div class="text-center">{{ ++$index }}</div></td>
			@if ( $rank != 'clan' )
			<td>
                <div style="margin: 0 auto;" class="icon-race-30 icon-race-30-{{ $element->race }}_{{ $element->gender }}"></div>
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
            <td><div class="text-center"><b>{{ $element->pvp_points }}</b></div></td>
			@endif
			@if ( $rank == 'xp' )
            <td><div class="text-center">{{ $element->level }}</div></td>
            <td><div class="text-center">{{ $element->xp }}</div></td>
			@endif
			@if ( $rank == 'clan' )
            <td><div class="text-center"><b>{{ $element->points }}</b></div></td>
			@endif
		</tr>
		@endforeach
	</tbody>
</table>

@if ($pagination)
<div class="text-center">
	{{ $pagination->links() }}
</div>
@endif

</div>

</div>