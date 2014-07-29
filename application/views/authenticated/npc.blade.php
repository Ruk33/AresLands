@if ( Session::has("error") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>{{ $npc->name }}</h4>
        <p>{{ Session::get("error") }}</p>
    </div>
</div>
@endif

@if ( Session::has('success') )
	<div class="alert alert-success span11" style="margin-top: 20px;">
        <b>{{ $npc->name }}</b>:
		{{ Session::get('success') }}
	</div>
@endif

<h2>
    <img src="{{ $npc->get_image_path() }}" />
    <span style="padding: 10px;">{{ $npc->name }}</span>
</h2>

<div style="margin-left: 90px; font-size: 12px; font-family: Arial;">{{ $npc->dialog }}</div>

<div class="clearfix"></div>

@if ( count($repeatableQuests) == 0 && count($rewardQuests) == 0 && count($startedQuests) == 0 && count($quests) == 0 && count($merchandises) == 0 )
	<h4 class="text-center" style="margin-top: 100px;">
        Por el momento, no tengo nada para ti. Vuelve en otra ocación.
    </h4>
@else
	@if ( count($quests) > 0 || count($repeatableQuests) > 0 )
		<h2 style="margin-top: 50px;">Misiones disponibles</h2>

        <div class="row">
        <table class="table table-striped brown-table">
            <thead>
                <tr>
                    <th class="span3">Nombre</th>
                    <th class="span2">Requiere</th>
                    <th class="span3"><div class="text-center">Objetivos</div></th>
                    <th class="span2"><div class="text-center">Recompensas</div></th>
                    <th class="span2">Repetible en</th>
                </tr>
            </thead>

            <tbody>
                @foreach ( array_merge($quests, $repeatableQuests) as $quest )
                <tr>
                    <td>
                        @if ( $quest->daily )
                            <div class="pull-left label label-info" data-toggle="tooltip" data-original-title="Mision diaria" style="margin-right: 5px;">D</div>
                        @endif
                        <div data-toggle="tooltip" data-original-title="{{ $quest->description }}">
                            @if ( $quest->can_character_accept_quest($character) )
                                {{ Form::open(URL::to_route("post_authenticated_quest_accept")) }}
                                    {{ Form::token() }}
                                    {{ Form::hidden("id", $quest->id) }}

                                    {{ Form::submit($quest->name, array("class" => "ui-button ui-input-button")) }}
                                {{ Form::close() }}
                            @else
                                {{ $quest->name }}
                            @endif
                        </div>
                    </td>
                    <td>
                        @if ( $quest->complete_required )
                            <div data-toggle="tooltip" data-original-title="Mision otorgada por <span class='positive'>{{ $quest->required_quest->npcs()->first_or_empty()->name }}</span> en nivel {{ $quest->required_quest->min_level }}">
                                {{ $quest->required_quest->name }}
                            </div>
                        @else
                            --
                        @endif
                    </td>
                    <td>
                        <div class="text-center">
                            {{ $quest->get_actions_for_view() }}
                        </div>
                    </td>
                    <td>
                        <div class="text-center">
                            {{ $quest->get_rewards_for_view() }}
                        </div>
                    </td>
                    <td>
                        @if ( $quest->repeatable )
                            @if ( isset($quest->repeatable_at) )
                                {{ Carbon\Carbon::createFromTimestamp($quest->repeatable_at)->toTimeString() }}
                            @else
                                {{ Carbon\Carbon::now()->setTime(0, 0, $quest->repeatable_after)->toTimeString() }}
                            @endif
                        @else
                            --
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
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
				{{ Form::open(URL::to_route("post_authenticated_npc_buy")) }}
					{{ Form::token() }}
                    {{ Form::hidden('random_merchandise', ( $merchandise instanceof NpcRandomMerchandise ) ? 1 : 0) }}
					{{ Form::hidden('id', $merchandise->id) }}
					
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