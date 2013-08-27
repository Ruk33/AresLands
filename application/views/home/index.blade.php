<div style="width: 940px; height: 200px; background-image: url('/img/p.jpg'); margin-left: -16px; margin-top: -15px; margin-bottom: 25px; border-bottom: 1px solid rgb(37, 37, 37);">
	
	<div style="margin-left: 30px; padding-top: 10px; width: 450px; color: white;">
		<h2>El renacimiento de Tierras de Leyenda</h2>
		<p>Un antiguo y poderoso mal ha despertado... un mal del que ni siquiera los mas antiguos tenían conocimiento. Consigo, demonios y otras oscuras criaturas se han levantado. Una gran batalla se aproxima; nuestro futuro es incierto...</p>
	</div>

</div>

<h4>¡Bienvenido a AresLands forastero/a!</h4>

<p><b>AresLands</b> es un juego de navegador <b>completamente gratuito</b>, al que puedes <b>jugar sin necesidad de instalar nada</b>. Su ambiente es épico, y deberás guiar, equipar, completar misiones, entrenar a tu personaje, derrotar a tus enemigos y mucho más, ¡para ser el mejor!. Recuerda que jugarás junto a otros jugadores que tendrán la misma meta, ¡no debes dejarlos superarte!.</p>

<div class="text-center">
	<a href="{{ Config::get('game.registration_url') }}" class="ui-button button" style="margin-top: 20px;">
		<i class="button-icon document"></i>
		<span class="button-content">Registrate y juega gratis</span>
	</a>

	<a href="{{ Config::get('game.login_url') }}" class="ui-button button" style="margin-top: 20px;">
		<i class="button-icon document"></i>
		<span class="button-content">Si ya tienes cuenta, ingresa</span>
	</a>
</div>

<div style="margin-top: 75px;">
<div style="width: 940px; height: 200px; background-image: url('/img/p1.png'); margin-left: -16px; margin-top: -15px; border-bottom: 1px solid rgb(37, 37, 37);"></div>
<h3 class="text-center" style="color: white;">Cuatro poderozas razas para elegir</h3>

<ul class="inline" style="margin-top: 10px;">
	<li style="vertical-align: top;">
		<div class="clan-member-link" style="width: 400px; height: 150px;">
			<div class="icon-race-30 icon-race-30-dwarf_male pull-left" style="margin-right: 10px;"></div>
			<h4 style="margin-top: 5px; color: white;">Enanos</h4>
			<p>
				Señores de la roca y maestros en el arte de la guerra. Sus fornidos cuerpos les dan una implacable resistencia física. Diestros en el uso de armas a dos manos. Su fortaleza es la fuerza misma y su debilidad es su carencia de magia en su sangre. Excelentes herreros, solo ambicionan una cosa... ¡la conquista!.
			</p>
		</div>
	</li>

	<li style="vertical-align: top;">
		<div class="clan-member-link" style="width: 400px; height: 150px;">
			<div class="icon-race-30 icon-race-30-drow_female pull-left" style="margin-right: 10px;"></div>
			<h4 style="margin-top: 5px; color: white;">Drow</h4>
			<p>
				Orden fundada por antiguos elfos renegados. Despreciaron el amor de los dioses y se sumergieron en las artes más oscuras. Tan fuertes en la magia como los enanos en la fuerza, pero débiles en el combate cuerpo a cuerpo. Estos elfos de piel oscurecida son los maestros de las sombras, los hechizeros por excelencia.
			</p>
		</div>
	</li>

	<li style="vertical-align: top;">
		<div class="clan-member-link" style="width: 400px; height: 150px;">
			<div class="icon-race-30 icon-race-30-elf_male pull-left" style="margin-right: 10px;"></div>
			<h4 style="margin-top: 5px; color: white;">Elfos</h4>
			<p>
				Algunos creen que son los representantes de los dioses en el mundo. Son sabios y su destreza tanto en el combate físico como en la magia es casi perfecto. Implacables con el arco y las curaciones, estos seres jamás mostrarán temor alguno, pues son enviados de los dioses y la sabiduría es su fortaleza.
			</p>
		</div>
	</li>

	<li style="vertical-align: top;">
		<div class="clan-member-link" style="width: 400px; height: 150px;">
			<div class="icon-race-30 icon-race-30-human_female pull-left" style="margin-right: 10px;"></div>
			<h4 style="margin-top: 5px; color: white;">Humanos</h4>
			<p>
				Diestros tanto en el combate cuerpo a cuerpo como en la magia. Respetan a los dioses pero no a un alto grado. La guerra es su mayor fuente de ingresos. Con buenas herrerias y excelentes líderes, son una de las razas más gloriosas y duras en el combate.
			</p>
		</div>
	</li>
</ul>
</div>

<div class="clearfix" style="margin-bottom: 50px;"></div>

<ul class="inline text-center">
	<li class="text-left" style="width: 400px; margin-bottom: 50px; vertical-align: top;">
		<img src="{{ URL::base() }}/img/items.png" alt="" width="302px" height="362px">
		<h2>¡Hazte con los objetos mas preciados!</h2>
		<p>No dejes que nadie te gane, consigue los mejores objetos, la mejor calidad. Solo con ellos lograrás derrotar a tus enemigos en la batalla.</p>
		<p>Armadura, pocion, espada o báculo. Date prisa antes de que otro se te adelante. No hay peor negocio que uno perdido!</p>
	</li>

	<li class="text-left" style="width: 400px; margin-bottom: 50px; vertical-align: top;">
		<img src="{{ URL::base() }}/img/trade.png" alt="" width="302px" height="362px">
		<h2>Comercia entre jugadores</h2>
		<p>El oro, la herramienta que mueve mundos y gana guerras, pues hazte con él. Vende lo que no necesites a otros jugadores, compra lo que ofertan los demas. ¡Oferta!, ¡demanda!, ¡oferta!, ¡demanda!</p>
	</li>

	<li class="text-left" style="width: 400px; vertical-align: top;">
		<img src="{{ URL::base() }}/img/clan.png" alt="" width="302px" height="362px">
		<h2>La unión hace la fuerza</h2>
		<p>¿Qué es un capitán sin su tripulación?, ¿o su comandante sin sus tropas?.</p>
		<p>Crea un grupo y recluta combatientes, esta guerra no se gana sola. ¿Serás un soldado, un líder o un guerrero solitario?</p>
	</li>

	<li class="text-left" style="width: 400px; vertical-align: top;">
		<img src="{{ URL::base() }}/img/quest.png" alt="" width="302px" height="362px">
		<h2>Misiones</h2>
		<p>Todo tiene su recompensa, matar un Esqueleto, llevar un accesorio a su dueño, hablar con un maestro de armas. Obten un objetivo, cúmplelo y cobra la jugosa recompensa.</p>
	</li>
</ul>

<div class="clearfix" style="margin-bottom: 50px;"></div>

<div class="text-center dark-box" style="margin-left: -15px;">
<h2>¡Y mucho mas!, pero... ¡¿por qué contártelo cuando puedes vivirlo?!</h2>
<a href="{{ Config::get('game.registration_url') }}" class="btn btn-large btn-primary">Regístrate y juega completamente gratis</a>
<p><small>(si ya tienes una cuenta en IronFist, <a href="{{ Config::get('game.login_url') }}">ingresa</a>)</small></p>
</div>

<div class="clearfix" style="margin-bottom: 50px;"></div>

<img src="{{ URL::base() }}/img/ares.png" alt="" class="pull-left" width="302px" height="362px">
<div style="margin-top: 150px;">
<div style="font-family: georgia;">
<p>Mis criaturas están siendo desplegadas alrededor de sus ciudades. Pronto caerán y el caos reinara. Se que muchos de ustedes desearan pelear, incluso muchos pensaran que es lo correcto. Pero dudo que alguno tenga la fuerza para siquiera llegar a mi.</p>
<p>Su destino esta marcado mortales, ¡la tierra de leyendas es mía!.</p>
</div>

<p>Arrodillense y teman, ¡Ares lo ordena!</p>

<a href="{{ Config::get('game.registration_url') }}" class="btn btn-large btn-primary">¡Batalla contra Ares!</a>
</div>

