<div class="dungeon-king-congratulations-box block-center">
    <div class="dungeon-king-congratulations-box-content">
        <div style="margin-left: 60px;">
            <h2>¿Con qué raza comenzarás tu aventura?</h2>
        </div>
        <ul class="inline character-creation-race-list text-center">
            <li data-toggle="popover" data-trigger="hover" 
                data-content="
                <div class='character-creation-race-tooltip'>
                    <div class='span3'>
                        <img src='{{ URL::base() }}/img/dwarf-both.png' />
                    </div>
                    <div class='span4'>
                        <h2>Enanos</h2>
                        <em class='positive'>Raza pura</em>
                        <p>Los enanos son maestros en el combate cuerpo a cuerpo y evitarán a toda costa el mágico, puesto que para éste último son pésimos.</p>
                        <p>
                            <b>Estadísticas iniciales:</b>
                            <ul>
                                <li><b>Vida máxima inicial:</b> 500</li>
                                <li><b>Fuerza:</b> 28</li>
                                <li><b>Destreza:</b> 9</li>
                                <li><b>Resistencia:</b> 13</li>
                                <li><b>Magia:</b> 0</li>
                                <li><b>Destreza magica:</b> 0</li>
                                <li><b>Contraconjuro:</b> 0</li>
                            </ul>
                        </p>
                    </div>
                </div>" 
                data-placement="bottom">
                <a href="{{ URL::to('charactercreation/create/dwarf') }}" class="character-creation-race-icon character-creation-race-icon-dwarf"></a>
            </li>
            
            <li data-toggle="popover" data-trigger="hover" 
                data-content=
                "<div class='character-creation-race-tooltip'>
                    <div class='span3'>
                        <img src='{{ URL::base() }}/img/human-both.png' />
                    </div>
                    <div class='span4'>
                        <h2>Humanos</h2>
                        <em class='positive'>Raza mixta</em>
                        <p>Los humanos tienen grandes destrezas tanto en el combate físico como mágico.</p>
                        <p>
                            <b>Estadísticas iniciales:</b>
                            <ul>
                                <li><b>Vida máxima inicial:</b> 400</li>
                                <li><b>Fuerza:</b> 22</li>
                                <li><b>Destreza:</b> 10</li>
                                <li><b>Resistencia:</b> 0</li>
                                <li><b>Magia:</b> 10</li>
                                <li><b>Destreza magica:</b> 8</li>
                                <li><b>Contraconjuro:</b> 0</li>
                            </ul>
                        </p>
                    </div>
                </div>" 
                data-placement="bottom">
                <a href="{{ URL::to('charactercreation/create/human') }}" class="character-creation-race-icon character-creation-race-icon-human"></a>
            </li>
            
            <li data-toggle="popover" data-trigger="hover" 
                data-content=
                "<div class='character-creation-race-tooltip'>
                    <div class='span3'>
                        <img src='{{ URL::base() }}/img/elf-both.png' />
                    </div>
                    <div class='span4'>
                        <h2>Elfos</h2>
                        <em class='positive'>Raza mixta</em>
                        <p>Los elfos son tan buenos tanto en combates físicos como mágicos, <br>pero tienen una ligera preferencia hacia la magia.</p>
                        <p>
                            <b>Estadísticas iniciales:</b>
                            <ul>
                                <li><b>Vida máxima inicial:</b> 300</li>
                                <li><b>Fuerza:</b> 12</li>
                                <li><b>Destreza:</b> 8</li>
                                <li><b>Resistencia:</b> 3</li>
                                <li><b>Magia:</b> 17</li>
                                <li><b>Destreza magica:</b> 8</li>
                                <li><b>Contraconjuro:</b> 2</li>
                            </ul>
                        </p>
                    </div>
                </div>" 
                data-placement="bottom">
                <a href="{{ URL::to('charactercreation/create/elf') }}" class="character-creation-race-icon character-creation-race-icon-elf"></a>
            </li>
            
            <li data-toggle="popover" data-trigger="hover" 
                data-content=
                "<div class='character-creation-race-tooltip'>
                    <div class='span3'>
                        <img src='{{ URL::base() }}/img/drow-both.png' />
                    </div>
                    <div class='span4'>
                        <h2>Drows</h2>
                        <em class='positive'>Raza pura</em>
                        <p>Los Drows tienen una excelente destreza en los combates mágicos, pero dejan mucho que desear en los físicos.</p>
                        <p>
                            <b>Estadísticas iniciales:</b>
                            <ul>
                                <li><b>Vida máxima inicial:</b> 150</li>
                                <li><b>Fuerza:</b> 0</li>
                                <li><b>Destreza:</b> 0</li>
                                <li><b>Resistencia:</b> 0</li>
                                <li><b>Magia:</b> 37</li>
                                <li><b>Destreza magica:</b> 13</li>
                                <li><b>Contraconjuro:</b> 0</li>
                            </ul>
                        </p>
                    </div>
                </div>" 
                data-placement="bottom">
                <a href="{{ URL::to('charactercreation/create/drow') }}" class="character-creation-race-icon character-creation-race-icon-drow"></a>
            </li>
        </ul>
    </div>
</div>