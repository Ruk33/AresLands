@if ($dungeon)
    @if ( Session::has("error") )
    <div class="clearfix row">
        <div class="alert alert-error no-border-radius span12">
            <h4>¡Alto ahi!</h4>
            <p>{{ Session::get("error") }}</p>
        </div>
    </div>
    @endif
    
    @if ($firstTime)
        <div class="row">
            <div class="dungeon-first-time-box" style="margin-top: 33px;">
                <div class="dungeon-first-time-box-content">
                    <h2>¡Bienvenido al Portal Oscuro!</h2>
                    <p>
                        Parece que ésta es tu primera vez en estos lugares. 
                        Debo advertirte, si decides proceder, 
                        ¡hazlo con mucho cuidado!.
                    </p>
                    
                    <p>
                        Reciéntemente un grupo de brujos de Ares lograron con éxito 
                        abrir un portal oscuro hacia una prisión donde antiguos
                        y poderosos males fueron encerrados. El portal no durará 
                        mucho, ¡por eso necesitamos tu ayuda para detenerlos y que 
                        no vuelvan a hacer de las suyas!.
                    </p>
                    
                    <p>
                        Los adversarios contra los que deberás batallar no se 
                        comparan en absoluto a nada contra lo que te hayas enfrentado 
                        anteriormente. Pero si logras derrotarlos, serás enormemente 
                        recompensado pues se sabe que éstas criaturas portan objetos 
                        de calidad legendaria.
                    </p>
                    
                    <p>
                        Si decides continuar, buena suerte valiente... 
                        la necesitarás.
                    </p>
                    
                    <div class="dungeon-button block-center">
                        <a href="{{ URL::to_route("get_authenticated_dungeon_index") }}">Proceder</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="red-flag block-center">
                <h2 class="text-center">Portal oscuro</h2>
                <h4 class="dungeon-name text-center">{{ $dungeon->zone->name }}</h4>
            </div>
            <div class="dungeon-level-container">
                <div class="dungeon-level-label text-center">Niveles</div>
                <ul class="inline text-center">
                    @foreach ($dungeon->levels as $dungeonLevel)
                    <li data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $dungeonLevel->target->name }}">
                        @if ($dungeon->has_character_completed_level($character, $dungeonLevel))
                            <img src="{{ $dungeonLevel->target->get_image_path() }}" class="grayEffect" />
                        @else
                            <img src="{{ $dungeonLevel->target->get_image_path() }}" />
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="dungeon-container">
                <div class="dungeon-image-container">
                    <img src="{{ $actualDungeonLevel->get_image_path() }}" />
                </div>
                <div class="dungeon-info-container">
                    <div class="dungeon-info-content">
                        @if ($actualDungeonLevel->has_requirements())
                        <div class="pull-right dungeon-info-requirements">
                            <span>Requiere</span>
                            {{ $actualDungeonLevel->get_requirements_for_view() }}
                        </div>
                        @endif
                        <div class="dungeon-info-text">
                            <h2>{{ $actualDungeonLevel->target->name }}</h2>
                            <p>
                                @if ($dialog = $actualDungeonLevel->target->dialog)
                                    {{ $dialog }}
                                @else
                                    ¿Te atreves a enfrentarme?
                                @endif
                            </p>
                        </div>
                        <div class="dungeon-info-button-container">
                            @if ($actualDungeonLevel->is_against_king())
                            <div class="dungeon-king-since pull-right">
                                <div class="text-right">Rey desde</div>
                                {{ Carbon\Carbon::createFromTimestamp($dungeon->king_since)->toDateTimeString() }}
                            </div>
                            @endif
                            @if ($dungeon->is_character_king($character))
                            <div class="dungeon-welcome-king">
                                <div class="dungeon-button">
                                    @if ($character->gender == "male")
                                    ¡Bienvenido rey!
                                    @else
                                    ¡Bienvenida reina!
                                    @endif
                                </div>
                            </div>
                            @else
                            <ul class="inline text-center">
                                @if ($dungeon->has_character_cd($character))
                                <li>
                                    <div class="dungeon-button">
                                        Disponible en 
                                        <span class="timer" data-endtime="{{ $dungeon->get_character_cd($character) }}">
                                            --:--:--
                                        </span>
                                    </div>
                                </li>
                                @endif
                                <li>
                                    <div class="dungeon-button">
                                        {{ Form::open(URL::to_route("post_authenticated_dungeon_index")) }}
                                            {{ Form::hidden("dungeon_id", $dungeon->id) }}
                                            {{ Form::submit("Batallar") }}
                                            @if ($dungeon->has_character_cd($character))
                                            <span>(10 IronCoins)</span>
                                            @endif
                                        {{ Form::close() }}
                                    </div>
                                </li>
                            </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="dungeon-level-container" style="margin-top: 200px;">
        <h5 class="text-center" style="padding-top: 32px; color: white; text-shadow: 0 0 5px black;">
            Esta zona está segura, aún no han abierto ningún portal oscuro... aún...
        </h5>
    </div>
@endif