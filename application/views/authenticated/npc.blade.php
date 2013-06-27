<h2>{{ $npc->name }}</h2>
<p>{{ $npc->dialog }}</p>

@if ( count($quests) > 0 )
	<h2>Misiones</h2>

	@foreach ( $quests as $key => $quest )
		<div class="dark-box" data-toggle="collapse" data-target="#{{ $quest->id }}">
			<strong>{{ $quest->name }}</strong>
			<div class="pull-right">Recompenza(s): {{ $quest->get_reward_for_view() }}</div>
		</div>
		<div id="{{ $quest->id }}" class="collapse">
			<p>{{ $quest->description }}</p>
			<p>
				@if ( isset($rewardQuests[$key]) )
					<a href="{{ URL::to('authenticated/rewardFromQuest/' . $quest->id) }}">Obtener recompensa</a>
				@else
					<a href="{{ URL::to('authenticated/acceptQuest/' . $quest->id) }}">Aceptar la misión</a>
				@endif
			</p>
		</div>
	@endforeach
@endif

@if ( count($merchandises) > 0 )
	<h2>Mercancías</h2>
	
	<ul class="inline">
	@foreach ( $merchandises as $merchandise )
		<li style="display: table-cell; vertical-align: top; padding: 10px;">
		{{ Form::open('authenticated/buyMerchandise', 'POST') }}

			{{ Form::hidden('merchandise_id', $merchandise->id) }}
			
			
			<div class="inventory-item">
				<img src="/img/icons/inventory/items/{{ $merchandise->item_id }}.png" alt="" data-toggle="tooltip" data-placement="top" data-original-title="{{ $merchandise->item->get_text_for_tooltip() }}<p><b>Precio</b>: {{ $merchandise->price_copper }}</p>">
			</div>
			
			<div>
			@if ( $merchandise->item->stackable )
				<?php

				for ( $i = 1, $max = @($characterCoinsCount / $merchandise->price_copper), $amount = []; $i <= $max; $i++ )
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

				{{ Form::select('amount', $amount, null, ['style' => 'width: 64px;']) }}
			@endif
			</div>
	
			<div>
			{{ Form::submit('Comprar', ['class' => 'btn btn-warning', 'style' => 'font-size: 10px;']) }}
			</div>

		{{ Form::close() }}
		</li>
	@endforeach
	</ul>
@endif