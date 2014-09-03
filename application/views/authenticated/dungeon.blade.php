@if ($dungeon)
@if ( Session::has("error") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>¡Alto ahi!</h4>
        <p>{{ Session::get("error") }}</p>
    </div>
</div>
@endif

<div class="row">
    <h2 class="dungeon-name text-center">Mazmorra, {{ $dungeon->zone->name }}</h2>
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
@else
<h1>El calabozo para esta zona aún no ha sido contruído. Vuelve en otra ocación.</h1>
@endif