<div class="row orb-content">
    <div class="dialog-box">
		<h2>Torneos</h2>
		<p>
			<i>
                ¡Solo los mejores grupos se enlistan en estos torneos!.
            </i>
		</p>
		<p><a href="{{ URL::to('authenticated/allTournaments') }}">Ver torneos anteriores</a></p>
	</div>

	@if ( $tournament )
	@if ( $canRegisterClan )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/registerClanInTournament/' . $tournament->id) }}" class="normal-button">Anota a tu grupo orgulloso lider</a>
		</div>
	@endif
	@if ( $canUnRegisterClan )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/unregisterClanFromTournament/' . $tournament->id) }}" class="normal-button">Salir del torneo</a>
		</div>
	@endif
	@if ( $canReclaimMvpReward )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/claimTournamentMvpReward/' . $tournament->id) }}" class="normal-button">Reclama tu recompensa MVP</a>
		</div>
	@endif
	@if ( $canReclaimClanLiderReward )
		<div class="text-center" style="margin-bottom: 25px;">
			<a href="{{ URL::to('authenticated/claimTournamentClanLeaderReward/' . $tournament->id) }}" class="normal-button">Reclama tu recompensa lider</a>
		</div>
	@endif
	<table class="table table-striped brown-table">
		<thead>
			<tr>
				<th colspan="2"><i class="icon-info-sign icon-white" style="vertical-align: -1px;"></i> Información del torneo</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Nombre del torneo</td>
				<td>{{ $tournament->name }}</td>
			</tr>
			
			<tr>
				<td>Ganador</td>
				<td>{{ ( $tournament->clan_winner_id ) ? $tournament->winner->get_link() : 'Nadie' }}</td>
			</tr>

			<tr>
				<td>Permite el uso de pociones</td>
				<td>{{ ( $tournament->allow_potions ) ? 'Si' : 'No' }}</td>
			</tr>

			<tr>
				<td>Fecha de inicio</td>
				<td>{{ date('d-m-Y H:i:s', $tournament->starts_at) }}</td>
			</tr>

			<tr>
				<td>Fecha final</td>
				<td>{{ date('d-m-Y H:i:s', $tournament->ends_at) }}</td>
			</tr>

			<tr>
				<td>Duracion</td>
				<td>{{ date('z \d\í\a\(\s\) H:i:s', $tournament->ends_at - $tournament->starts_at) }}</td>
			</tr>

			<tr>
				<td>Obligatorio para todos los grupos</td>
				<td>{{ ( $tournament->all_clans ) ? 'Si' : 'No' }}</td>
			</tr>

			<tr>
				<td>Numero minimo de miembros por grupo</td>
				<td>{{ $tournament->min_members }}</td>
			</tr>

			<tr>
				<td>MVP</td>
				<td>{{ ( $tournament->mvp_id ) ? $tournament->mvp->get_link() : 'Nadie' }}</td>
			</tr>

			<tr>
				<td>Luchas totales</td>
				<td>{{ $tournament->battle_counter }}</td>
			</tr>

			<tr>
				<td>Jugadores totales</td>
				<td>{{ $tournament->get_registered_characters_count() }}</td>
			</tr>

			<tr>
				<td>Pociones magicas usadas</td>
				<td>{{ $tournament->potion_counter }}</td>
			</tr>

			<tr>
				<td>Pociones de vida usadas</td>
				<td>{{ $tournament->life_potion_counter }}</td>
			</tr>

			<tr>
				<td data-toggle="tooltip" data-original-title="Varia segun actividad">Recompensa de monedas para todos</td>
				<td>{{ Item::get_divided_coins($tournament->coin_reward)['text'] }}</td>
			</tr>

			<tr>
				<td data-toggle="tooltip" data-original-title="Varia segun actividad">Recompensa monedas para MVP</td>
				<td>{{ Item::get_divided_coins($tournament->mvp_coin_reward)['text'] }}</td>
			</tr>

			<tr>
				<td>Recompensa cofres para MVP</td>
				<td>
					<div class="quest-reward-item" data-toggle="tooltip" data-original-title="{{ Item::find(Config::get('game.chest_item_id'))->get_text_for_tooltip() }}<p>Cantidad: 2</p>">
						<img src="{{ URL::base() }}/img/icons/items/{{ Config::get('game.chest_item_id') }}.png" />
					</div>
				</td>
			</tr>

			<tr>
				<td>Objeto recompensa para lider de clan</td>
				<td>
					<div class="quest-reward-item" data-toggle="tooltip" data-original-title="{{ $tournament->reward_item->get_text_for_tooltip() }}<p>Cantidad: {{ $tournament->item_reward_amount }}</p>">
						<img src="{{ URL::base() }}/img/icons/items/{{ $tournament->item_reward }}.png" />
					</div>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table table-striped brown-table">
		<thead>
			<tr>
				<th>#</th>
				<th><i class="icon-flag icon-white" style="vertical-align: -1px;"></i> Grupo</th>
                <th>
                    <div class="text-center">
                        <i class="icon-signal icon-white" style="vertical-align: -1px;"></i> % de victorias
                    </div>
                </th>
				<th>
                    <div class="text-center">
                        <i class="icon-fire icon-white" style="vertical-align: -1px;"></i> Victorias
                    </div>
                </th>
				<th>
                    <div class="text-center">
                        <i class="icon-asterisk icon-white" style="vertical-align: -1px;"></i> Derrotas
                    </div>
                </th>
				<th></th>
			</tr>
		</thead>

		<tbody>
			<?php $n = 1; ?>
			@foreach ( $registeredClans as $registeredClan )
			<tr>
				<td>{{ $n++; }}</td>
				<td>{{ ( $registeredClan->disqualified ) ? '<s data-toggle="tooltip" data-original-title="Descalificado">' . $registeredClan->clan->get_link() . '</s>' : $registeredClan->clan->get_link() }}</td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_victory_percentage($tournament->id, $registeredClan->clan->id) }}%
                    </div>
                </td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_victories($tournament->id, $registeredClan->clan->id) }}
                    </div>
                </td>
				<td>
                    <div class="text-center">
                        {{ TournamentClanScore::get_defeats($tournament->id, $registeredClan->clan->id) }}
                    </div>
                </td>
				<td style="font-size: 12px; text-transform: uppercase;" class="text-center"><a href="#">Res. grupo</a> - <a href="#">Res. individual</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@else
		<div style="margin-top: 50px;">
			<h1 class="text-center">Sin torneos... aún...</h1>
		</div>
	@endif
</div>