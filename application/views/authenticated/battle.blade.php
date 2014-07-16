<div class="battle-box">
    
@if ( Session::has("error") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>¡Alto valiente!</h4>
        <p>{{ Session::get("error") }}</p>
    </div>
</div>
@endif
    
<h2>¡Batallar!</h2>
@if ( Session::has('errorMessage') )
	<div class="alert alert-error text-center">
		{{ Session::get('errorMessage') }}
	</div>
@endif

<p>¿Así que quieres probar suerte con algún contrincante?, pues adelante, elige tu reto.</p>

<div class="row">
    <div class="span12">
        <div class="battle-search-box">
            <h2>Busqueda de personajes</h2>
            <ul class="inline text-center">
                <li class="span4">
                    <div class="thumbnail">
                        <div class="caption">
                            {{ Form::open(URL::to_route("post_authenticated_battle_search")) }}
                                {{ Form::token() }}
                                {{ Form::hidden('search_method', 'name') }}

                                {{ Form::label('character_name', 'Por nombre') }}
                                {{ Form::text('character_name', "", array("class" => "span11")) }}

                                <div class="text-center">
                                    <span class="ui-button button">
                                        <i class="button-icon axe"></i>
                                        <span class="button-content">
                                            {{ Form::submit('Buscar por nombre', array('class' => 'ui-button ui-input-button')) }}
                                        </span>
                                    </span>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </li>

                <li class="span4">
                    <div class="thumbnail">
                        <div class="caption">
                            {{ Form::open(URL::to_route("post_authenticated_battle_search")) }}
                                {{ Form::token() }}
                                {{ Form::hidden('search_method', 'random') }}

                                {{ Form::label('race_label', 'Raza') }}
                                {{ Form::select('race', array('dwarf,human,elf,drow' => 'Cualquiera', 'dwarf' => 'Enano', 'human' => 'Humano', 'drow' => 'Drow', 'elf' => 'Elfo'), null, array("class" => "span11")) }}

                                {{ Form::label('level_label', 'Nivel') }}
                                {{ Form::select('operation', array('=' => 'Exactamente', '>' => 'Mayor que', '<' => 'Menor que'), null, array("class" => "span11")) }}
                                {{ Form::number('level', $character->level, array('min' => '1', "class" => "span11")) }}

                                <div class="text-center">
                                    <span class="ui-button button">
                                        <i class="button-icon thunder"></i>
                                        <span class="button-content">
                                            {{ Form::submit('Buscar aleatoriamente', array('class' => 'ui-button ui-input-button')) }}
                                        </span>
                                    </span>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </li>

                <li class="span4">
                    <div class="thumbnail">
                        <div class="caption">
                            {{ Form::open(URL::to_route("post_authenticated_battle_search")) }}
                                {{ Form::token() }}
                                {{ Form::hidden('search_method', 'group') }}

                                {{ Form::label('clan', 'Grupo') }}
                                {{ Form::select('clan', Clan::lists('name', 'id'), null, array("class" => "span11")) }}

                                <div class="text-center">
                                    <span class="ui-button button">
                                        <i class="button-icon dagger"></i>
                                        <span class="button-content">
                                            {{ Form::submit('Buscar en grupo', array('class' => 'ui-button ui-input-button')) }}
                                        </span>
                                    </span>
                                </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="clearfix"></div>

@if ( count($monsters) > 0 )
<h2 style="margin-top: 50px;">Monstruos</h2>
<div class="row">
    <div class="span12">
        <ul class="thumbnails battle-monsters-content">
            @foreach ( $monsters as $monster )
            <li class="thumbnail monster-box">
                <div class="quest-reward-item pull-left">
                    <img src="{{ $monster->get_image_path() }}" />
                </div>

                <div class="level">nivel {{ $monster->level }}</div>
                {{ Form::open(URL::to_route("post_authenticated_battle_monster")) }}
                    {{ Form::token() }}
                    {{ Form::hidden("monster_id", $monster->id) }}
                    
                    {{ Form::submit($monster->name, array("class" => $monster->get_color_class($character) . " ui-button ui-input-button")) }}
                {{ Form::close() }}
                
                <div class="clearfix"></div>
                
                <div class="life-bar">
                    <div class="life-text">
                        @if ( $monster->get_color_class($character) == 'level-very-high' )
                            <span data-toggle="tooltip" data-original-title="Necesitas mas nivel">???</span>
                        @else
                            {{ number_format($monster->life, 0) }} puntos de vida
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
</div>