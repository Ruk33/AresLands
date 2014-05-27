<div class="row">

    <div class="dialog-box" style="margin-left: 20px; margin-top: 30px; width: 690px;">
        <div style="margin-left: 150px;">
            <h2>{{ $npc->name }}</h2>
            <p>{{ $npc->dialog }}</p>
        </div>
    </div>

    <div style="position: absolute; top: 0; left: 0;">
        <img src="{{ URL::base() }}/img/npcs/{{ $npc->id }}.jpg" alt="" width="150px" height="193px" class="img-rounded" style="border: 2px solid #160500; box-shadow: black 0 0 5px;">
    </div>
    
</div>

<div class="clearfix"></div>

@if ( Session::has('buyed') )
	<div class="alert alert-success span11" style="margin-top: 20px;">
        <b>{{ $npc->name }}</b>:
		{{ Session::get('buyed') }}
	</div>
@endif

@if ( count($repeatableQuests) == 0 && count($rewardQuests) == 0 && count($startedQuests) == 0 && count($quests) == 0 && count($merchandises) == 0 )
	<h4 class="text-center" style="margin-top: 100px;">Por el momento, no tengo nada para ti. Vuelve en otra ocación.</h4>
@else
	@if ( count($repeatableQuests) > 0 )
		<h2 style="margin-top: 20px;">Misiones repetibles</h2>

		@foreach ( $repeatableQuests as $repeatableQuest )
			<div class="dark-box span11" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $repeatableQuest->id }}">
				@if ( $repeatableQuest->daily )
					<div class="pull-right label label-warning">DIARIA</div>
				@endif
				<h5>{{ $repeatableQuest->name }} (<small>Disponible en: <span class='timer' data-endtime='{{ $repeatableQuest->repeatable_at - time() }}'></span></small>)</h5>
				
                <div id="{{ $repeatableQuest->id }}" class="collapse">
                    <p><small>{{ $repeatableQuest->description }}</small></p>
                
                    @if ( $deblockQuest = $repeatableQuest->get_deblock_quest()->first() )
                    <div class="clan-member-link text-center" style="text-transform: uppercase; font-size: 12px; color: orange;">
                        Al completarla, desbloquearás la misión: <b>{{ $deblockQuest->name }}</b>
                    </div>
                    @endif

                    <div style="margin-top: 20px; margin-bottom: 100px;">
                        <div class="span6 text-center">
                            <b>Objetivo(s)</b>
                            {{ $repeatableQuest->get_actions_for_view() }}
                        </div>
                        <div class="span6 text-center">
                            <b>Recompensa(s)</b>
                            {{ $repeatableQuest->get_rewards_for_view() }}
                        </div>
                    </div>
                </div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($rewardQuests) > 0 )
		<h2 style="margin-top: 20px;">Misiones completadas, ¡pide tu recompensa!</h2>

		@foreach ( $rewardQuests as $rewardQuest )
			<div class="dark-box span11 {{ $rewardQuest->get_css_class($character) }}" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $rewardQuest->id }}">
				@if ( $rewardQuest->daily )
					<div class="pull-right label label-warning">DIARIA</div>
				@endif
				<h5>{{ $rewardQuest->name }}</h5>

				<div id="{{ $rewardQuest->id }}" class="collapse">
                    <p><small>{{ $rewardQuest->description }}</small></p>
                    
                    @if ( $deblockQuest = $rewardQuest->get_deblock_quest()->first() )
                    <div class="clan-member-link text-center" style="text-transform: uppercase; font-size: 12px; color: orange;">
                        Al completarla, desbloquearás la misión: <b>{{ $deblockQuest->name }}</b>
                    </div>
                    @endif
                    
                    <div style="margin-top: 20px; margin-bottom: 100px;">
                        <div class="span6 text-center">
                            <b>Objetivo(s)</b>
                            {{ $rewardQuest->get_actions_for_view() }}
                        </div>
                        <div class="span6 text-center">
                            <b>Recompensa(s)</b>
                            {{ $rewardQuest->get_rewards_for_view() }}
                        </div>
                    </div>
					<div class="text-center" style="margin-top: 20px;">
						<a href="{{ URL::to('authenticated/rewardFromQuest/' . $rewardQuest->id) }}" class="normal-button">Obtener recompensa</a>
					</div>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($startedQuests) > 0 )
		<h2 style="margin-top: 20px;">Misiones aceptadas</h2>

		@foreach ( $startedQuests as $startedQuest )
			<div class="dark-box span11 {{ $startedQuest->get_css_class($character) }}" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $startedQuest->id }}">
				@if ( $startedQuest->daily )
					<div class="pull-right label label-warning">DIARIA</div>
				@endif
				<h5>{{ $startedQuest->name }}</h5>

				<div id="{{ $startedQuest->id }}" class="collapse">
                    <p><small>{{ $startedQuest->description }}</small></p>
                    
                    @if ( $deblockQuest = $startedQuest->get_deblock_quest()->first() )
                    <div class="clan-member-link text-center" style="text-transform: uppercase; font-size: 12px; color: orange;">
                        Al completarla, desbloquearás la misión: <b>{{ $deblockQuest->name }}</b>
                    </div>
                    @endif
                    
                    <div style="margin-top: 20px; margin-bottom: 100px;">
                        <div class="span6 text-center">
                            <b>Objetivo(s)</b>
                            {{ $startedQuest->get_actions_for_view() }}
                        </div>
                        <div class="span6 text-center">
                            <b>Recompensa(s)</b>
                            {{ $startedQuest->get_rewards_for_view() }}
                        </div>
                    </div>

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
		<h2 style="margin-top: 20px;">Misiones disponibles</h2>

		@foreach ( $quests as $quest )
			<div class="dark-box span11 {{ $quest->get_css_class($character) }}" style="cursor: pointer; margin-bottom: 5px;" data-toggle="collapse" data-target="#{{ $quest->id }}">
				@if ( $quest->daily )
					<div class="pull-right label label-warning">DIARIA</div>
				@endif
                <h5>{{ $quest->name }}</h5>

				<div id="{{ $quest->id }}" class="collapse">
                    <p><small>{{ $quest->description }}</small></p>
                    
                    @if ( $deblockQuest = $quest->get_deblock_quest()->first() )
                    <div class="clan-member-link text-center" style="text-transform: uppercase; font-size: 12px; color: orange;">
                        Al completarla, desbloquearás la misión: <b>{{ $deblockQuest->name }}</b>
                    </div>
                    @endif
                    
                    <div style="margin-top: 20px; margin-bottom: 100px;">
                        <div class="span6 text-center">
                            <b>Objetivo(s)</b>
                            {{ $quest->get_actions_for_view() }}
                        </div>
                        <div class="span6 text-center">
                            <b>Recompensa(s)</b>
                            {{ $quest->get_rewards_for_view() }}
                        </div>
                    </div>
                    
                    <div class="text-center" style="margin-top: 20px;">
						<a href="{{ URL::to('authenticated/acceptQuest/' . $quest->id) }}" class="normal-button">Aceptar misión</a>
					</div>
				</div>
			</div>
		@endforeach
	@endif

	<div class="clearfix"></div>

	@if ( count($merchandises) > 0 )
		<h2 style="margin-top: 50px;">Mercancías</h2>

		<ul class="inline" ng-controller="Item">
		@foreach ( $merchandises as $merchandise )
			@if ( $merchandise->item->type == 'mercenary' )
				@if ( $merchandise->item->zone_to_explore && $merchandise->item->time_to_appear && $character->exploring_times()->where('zone_id', '=', $merchandise->item->zone_to_explore)->where('time', '>=', $merchandise->item->time_to_appear)->take(1)->count() == 0 )
					<li class="text-center" style="vertical-align: top; padding: 10px;" data-toggle="tooltip" data-original-title="Bloqueado, necesitas explorar mas para que este mercenario se te habilite">
						<div class="box box-box-64-gray grayEffect">
							<img src="{{ $merchandise->item->get_image_path() }}" width="80px" height="80px">
						</div>
					</li>
					<?php continue; ?>
				@endif
			@endif
			<li class="text-center" style="vertical-align: top; padding: 10px;" item-tooltip-with-price item-id="{{ $merchandise->item_id }}" item-price="{{ $merchandise->price_copper }}">
			@if ( $characterCoinsCount >= $merchandise->price_copper )
				{{ Form::open('authenticated/buyMerchandise', 'POST') }}
					{{ Form::token() }}
                    {{ Form::hidden('random_merchandise', ( $merchandise instanceof NpcRandomMerchandise ) ? 1 : 0) }}
					{{ Form::hidden('merchandise_id', $merchandise->id) }}
					
					<div class="box box-box-64-gray">
						<img src="{{ $merchandise->item->get_image_path() }}" width="80px" height="80px">
					</div>

					<div>
					@if ( $merchandise->item->stackable )
                        <div data-toggle="tooltip" data-original-title="Podes comprar {{ number_format($characterCoinsCount / $merchandise->price_copper, 0, ',', '.') }}">
                        {{ Form::number('amount', 0, array('max' => number_format($characterCoinsCount / $merchandise->price_copper, 0, '', ''), 'style' => 'width: 50px;')) }}
                        </div>
					@endif
					</div>

					<div>
					{{ Form::submit('Comprar', array('class' => 'btn btn-warning', 'style' => 'font-size: 10px;')) }}
					</div>

				{{ Form::close() }}
			@else
				<div class="box box-box-64-gray">
					<img src="{{ $merchandise->item->get_image_path() }}" width="80px" height="80px">
				</div>
				<div class="btn disabled" style="font-size: 10px;" data-toggle="tooltip" data-title="No tienes suficientes monedas">Comprar</div>
			@endif
			</li>
		@endforeach
		</ul>
	@endif
@endif