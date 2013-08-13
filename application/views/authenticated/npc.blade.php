<div class="pull-left">
	<img src="{{ URL::base() }}/img/npcs/{{ $npc->id }}.jpg" alt="" width="150px" height="193px">
</div>

<div style="margin-left: 175px;">
	<h2>{{ $npc->name }}</h2>
	<p>{{ $npc->dialog }}</p>
</div>

<div class="clearfix"></div>

@if ( count($quests) > 0 || count($rewardQuests) > 0 || count($startedQuests) > 0 )
	
	@if ( count($rewardQuests) > 0 )
		<h2>Misiones completadas, ¡pide tu recompensa!</h2>

		@foreach ( $rewardQuests as $rewardQuest )
			<div class="dark-box span11" style="cursor: pointer;" data-toggle="collapse" data-target="#{{ $rewardQuest->id }}">
				<strong style="line-height: 60px;">{{ $rewardQuest->name }}</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $rewardQuest->get_reward_for_view() }}
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
			<div class="dark-box span11" style="cursor: pointer;" data-toggle="collapse" data-target="#{{ $startedQuest['quest']->id }}">
				<strong style="line-height: 60px;">{{ $startedQuest['quest']->name }}</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $startedQuest['quest']->get_reward_for_view() }}
				</div>
				
				<div id="{{ $startedQuest['quest']->id }}" class="collapse">
					<p>{{ $startedQuest['quest']->description }}</p>

					<strong>Progreso</strong>
					<p>{{ $startedQuest['characterQuest']->get_progress_for_view() }}</p>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($quests) > 0 )
		<h2>Misiones disponibles</h2>

		@foreach ( $quests as $quest )
			<div class="dark-box span11" style="cursor: pointer;" data-toggle="collapse" data-target="#{{ $quest->id }}">
				<strong style="line-height: 60px;">{{ $quest->name }}</strong>
				<div class="pull-right">
					<small>Recompensa(s)</small>
					{{ $quest->get_reward_for_view() }}
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

@endif

@if ( count($merchandises) > 0 )
	<h2>Mercancías</h2>
	
	<ul class="inline" ng-controller="Item">
	@foreach ( $merchandises as $merchandise )
		<li class="text-center" style="vertical-align: top; padding: 10px;">
		@if ( $characterCoinsCount >= $merchandise->price_copper )
			{{ Form::open('authenticated/buyMerchandise', 'POST') }}

				{{ Form::hidden('merchandise_id', $merchandise->id) }}
				
				<div class="inventory-item">
					<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" ng-mouseover="onMouseOver({{ $merchandise->item_id }})" dynamic-tooltip="item[{{ $merchandise->item_id }}]" ng-init="price[{{ $merchandise->item_id }}] = {{ $merchandise->price_copper }}" width="80px" height="80px">
				</div>
				
				<div>
				@if ( $merchandise->item->stackable )
					<?php

					for ( $i = 1, $max = @($characterCoinsCount / $merchandise->price_copper), $amount = array(); $i <= $max; $i++ )
					{
						if ( $i > 25 )
						{
							$i += 4;
						}

						if ( $i > 50 )
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
				<!--<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" alt="" ng-mouseover="onMouseOver({{ $merchandise->item_id }})" data-toggle="tooltip" data-placement="top" data-original-title="[[ item[{{ $merchandise->item_id }}] ]]<p>Precio: {{ $merchandise->price_copper }}</p>">-->
				<img src="{{ URL::base() }}/img/icons/items/{{ $merchandise->item_id }}.png" ng-mouseover="onMouseOver({{ $merchandise->item_id }})" dynamic-tooltip="item[{{ $merchandise->item_id }}]" ng-init="price[{{ $merchandise->item_id }}] = {{ $merchandise->price_copper }}" width="80px" height="80px">
			</div>
			<div class="btn disabled" style="font-size: 10px;" data-toggle="tooltip" data-title="No tienes suficientes monedas">Comprar</div>
		@endif
		</li>
	@endforeach
	</ul>
@endif