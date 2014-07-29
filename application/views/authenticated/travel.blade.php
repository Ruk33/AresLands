@if ( Session::has("error") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>¡Alto viajero!</h4>
        <p>{{ Session::get("error") }}</p>
    </div>
</div>
@endif

<h2>Viajar</h2>

<p>
    Caminas, caminas y sigues caminando... notas que el suelo comienza a 
    cambiar, observas detenidamente los alrededores y te das cuenta de que hay 
    varios caminos para seguir. Todavía consigues ver borrosamente a 
    {{ $character->zone->name }} a tus espaldas, pero estos caminos ya te 
    alejarán mucho, ¿decides continuar?.
</p>

<p>
    <b>{{ $character->name }} piensa:</b> 
    si decido continuar, gastaré {{ Config::get('game.travel_cost') }} 
    <i class="coin coin-copper" style="display: inline-block;"></i> monedas en
    provisiones.
</p>

<div class="row">
    <ul class="thumbnails" style="margin-left: -10px;">
        @foreach ( $zones as $zone )
        <li class="thumbnail">
            <div class="travel-zone-box">
                {{ Form::open(URL::to_route("post_authenticated_action_travel")) }}
                    <img class="image" src="{{ URL::base() }}/img/zones/32/{{ $zone->id }}.png" alt="">

                    {{ Form::token() }}
                    {{ Form::hidden("id", $zone->id) }}
                    
                    {{ Form::submit($zone->name, array("class" => "ui-button input-ui-button name")) }}
                {{ Form::close() }}

                <p class="description">{{ $zone->description }}</p>

                <div class="explored-time">
                    <span class="explored-time-label">Tiempo explorado</span><br>
                    <span class="explored-time-text">
                    @if ( isset($exploringTime[$zone->id]) && $exploringTime[$zone->id] > 0 )
                        {{ date('z \d\í\a\(\s\) H:i:s', $exploringTime[$zone->id]) }}
                    @else
                        Sin explorar
                    @endif
                    </span>
                </p>
            </div>
        </li>
        @endforeach
    </ul>
</div>