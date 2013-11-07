<div class="pull-left">
	<img src="{{ URL::base() }}/img/npcs/{{ $npc->id }}.jpg" alt="" width="150px" height="193px" style="border: 2px solid #160500;">
</div>

<div style="margin-left: 175px;">
	<h2>{{ $npc->name }}</h2>
	<p>{{ $npc->dialog }}</p>
</div>

<div class="clearfix"></div>

@if ( count($repeatableQuests) == 0 && count($rewardQuests) == 0 && count($startedQuests) == 0 && count($quests) == 0 && count($merchandises) == 0 )
	<h4 class="text-center" style="margin-top: 100px;">Por el momento, no tengo nada para ti. Vuelve en otra ocación.</h4>
@else
	@if ( count($repeatableQuests) > 0 )
		<h2>Misiones repetibles</h2>

		@foreach ( $repeatableQuests as $repeatableQuest )
			<div class="dark-box span11" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $repeatableQuest->id }}">
				@if ( $repeatableQuest->daily )
					<span class="label label-warning">DIARIA</span>
				@endif
				<strong style="line-height: 60px;">{{ $repeatableQuest->name }} (<small>Disponible en: <span class='timer' data-endtime='{{ $repeatableQuest->repeatable_at - time() }}'></span></small>)</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $repeatableQuest->get_rewards_for_view() }}
				</div>

				<div id="{{ $repeatableQuest->id }}" class="collapse">
					<p>{{ $repeatableQuest->description }}</p>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($rewardQuests) > 0 )
		<h2>Misiones completadas, ¡pide tu recompensa!</h2>

		@foreach ( $rewardQuests as $rewardQuest )
			<div class="dark-box span11" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $rewardQuest->id }}">
				@if ( $rewardQuest->daily )
					<span class="label label-warning">DIARIA</span>
				@endif
				<strong style="line-height: 60px;">{{ $rewardQuest->name }}</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $rewardQuest->get_rewards_for_view() }}
				</div>

				<div id="{{ $rewardQuest->id }}" class="collapse">
					<p>{{ $rewardQuest->description }}</p>
					<p>
						<a href="{{ URL::to('authenticated/rewardFromQuest/' . $rewardQuest->id) }}">Obtener recompensa</a>
					</p>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($startedQuests) > 0 )
		<h2>Misiones aceptadas</h2>

		@foreach ( $startedQuests as $startedQuest )
			<div class="dark-box span11" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $startedQuest->id }}">
				@if ( $startedQuest->daily )
					<span class="label label-warning">DIARIA</span>
				@endif
				<strong style="line-height: 60px;">{{ $startedQuest->name }}</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $startedQuest->get_rewards_for_view() }}
				</div>

				<div id="{{ $startedQuest->id }}" class="collapse">
					<p>{{ $startedQuest->description }}</p>

					@if ( $progress = $character->get_progress_for_view($startedQuest) )
						<strong>Tu progreso</strong>
						<p>{{ $progress }}</p>
					@endif
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($quests) > 0 )
		<h2>Misiones disponibles</h2>

		@foreach ( $quests as $quest )
			<div class="dark-box span11" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $quest->id }}">
				@if ( $quest->daily )
					<span class="label label-warning">DIARIA</span>
				@endif
				<strong style="line-height: 60px;">{{ $quest->name }}</strong>

				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $quest->get_rewards_for_view() }}
				</div>

				<div id="{{ $quest->id }}" class="collapse">
					<p>{{ $quest->description }}</p>
					<p>
						<a href="{{ URL::to('authenticated/acceptQuest/' . $quest->id) }}" class="normal-button">Aceptar misión</a>
					</p>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($merchandises) > 0 )
		<h2 style="margin-top: 50px;">Mercancías</h2>

		<ul class="inline" ng-controller="Item">
		@foreach ( $merchandises as $merchandise )
			@if ( $merchandise->type == 'mercenary' )
				@if ( $merchandise->zone_to_explore && $merchandise->time_to_appear && $character->exploring_times()->where('zone_id', '=', $merchandise->zone_to_explore)->where('time', '>=', $merchandise->time_to_appear)->take(1)->count() == 0 )
					<li class="text-center" style="vertical-align: top; padding: 10px;" data-toggle="tooltip" data-original-title="Bloqueado, necesitas explorar mas para que este mercenario se te habilite">
						<div class="inventory-item grayEffect">
							<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" width="80px" height="80px">
						</div>
					</li>
					<?php continue; ?>
				@endif
			@endif
			<li class="text-center" style="vertical-align: top; padding: 10px;" item-tooltip-with-price item-id="{{ $merchandise->item_id }}" item-price="{{ $merchandise->price_copper }}">
			@if ( $characterCoinsCount >= $merchandise->price_copper )
				{{ Form::open('authenticated/buyMerchandise', 'POST') }}
					{{ Form::token() }}
					{{ Form::hidden('merchandise_id', $merchandise->id) }}

					<div class="inventory-item">
						<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" width="80px" height="80px">
					</div>

					<div>
					@if ( $merchandise->stackable )
						<?php

						for ( $i = 1, $max = @($characterCoinsCount / $merchandise->price_copper), $amount = array(); $i <= $max; $i++ )
						{
							if ( $i > 25 )
							{
								$i += 4;
							}

							if ( $i > 100 )
							{
								break;
							}

							$amount[$i] = $i;
						}

						?>

						{{ Form::select('amount', $amount, null, array('style' => 'width: 64px;')) }}
					@endif
					</div>

					<div>
					{{ Form::submit('Comprar', array('class' => 'btn btn-warning', 'style' => 'font-size: 10px;')) }}
					</div>

				{{ Form::close() }}
			@else
				<div class="inventory-item">
					<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" width="80px" height="80px">
				</div>
				<div class="btn disabled" style="font-size: 10px;" data-toggle="tooltip" data-title="No tienes suficientes monedas">Comprar</div>
			@endif
			</li>
		@endforeach
		</ul>
	@endif
@endif