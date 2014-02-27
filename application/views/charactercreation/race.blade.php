<h2>¡Elige tu raza!</h2>
<p>Elige la raza con la que iniciarás tu aventura, pero elige con cuidado, ya que cada raza tiene su particularidad.</p>

<div class="row">
	<ul class="inline">
		<li>
			<a href="{{ URL::to('charactercreation/create/dwarf') }}" data-toggle="popover" data-trigger="hover" data-placement="right" data-original-title="<b>Enanos<b>" data-content=
			"<small>
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
			</small>"><img src="{{ URL::base() }}/img/enano-test.png" alt="Jugar como Enano" /></a>
		</li>

		<li>
			<a href="{{ URL::to('charactercreation/create/elf') }}" data-toggle="popover" data-trigger="hover" data-placement="left" data-original-title="<b>Elfos<b>" data-content="
			<small>
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
			</small>"><img src="{{ URL::base() }}/img/elfo-test.png" alt="Jugar como Elfo" /></a>
		</li>

		<li>
			<a href="{{ URL::to('charactercreation/create/drow') }}" data-toggle="popover" data-trigger="hover" data-placement="right" data-original-title="<b>Drows<b>" data-content=
			"<small>
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
			</small>"><img src="{{ URL::base() }}/img/drow-test.png" alt="Jugar como Drow" /></a>
		</li>

		<li>
			<a href="{{ URL::to('charactercreation/create/human') }}" data-toggle="popover" data-trigger="hover" data-placement="left" data-original-title="<b>Humanos<b>" data-content=
			"<small>
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
			</small>"><img src="{{ URL::base() }}/img/humano-test.png" alt="Jugar como Humano" /></a>
		</li>
	</ul>
</div>