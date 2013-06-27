<h2>¡Elige tu raza!</h2>
<p>Elige la raza con la que iniciarás tu aventura, pero elige con cuidado, ya que cada raza tiene su particularidad.</p>

<div class="text-center">
	<a href="{{ URL::to('charactercreation/create/dwarf') }}">
		<img src="/img/enano-test.png" alt="Jugar como Enano" data-toggle="popover" data-trigger="hover" data-placement="right" data-original-title="<b>Enanos<b>" data-content=
		"<small>
			<em><strong>Raza pura</strong></em>
			<p>Los enanos son maestros en el combate cuerpo a cuerpo y evitarán a toda costa el mágico, puesto que para éste último son pésimos.</p>
			<p>
				<b>Estadísticas iniciales:</b>
				<ul>
					<li><b>Vida:</b> 18</li>
					<li><b>Destreza:</b> 5</li>
					<li><b>Magia:</b> 3</li>
					<li><b>Fuerza:</b> 28</li>
					<li><b>Suerte:</b> 5</li>
					<li><b>Salud máxima:</b> 400</li>
				</ul>
			</p>
		</small>"
		>
	</a>

	<a href="{{ URL::to('charactercreation/create/elf') }}">
		<img src="/img/elfo-test.png" alt="Jugar como Elfo" data-toggle="popover" data-trigger="hover" data-placement="left" data-original-title="<b>Elfos<b>" data-content=
		"<small>
			<em><strong>Raza mixta</strong></em>
			<p>Los elfos se desempeñan muy bien tanto en combates físicos como mágicos, pero tienen una ligera hacia la magia.</p>
			<p>
				<b>Estadísticas iniciales:</b>
				<ul>
					<li><b>Vida:</b> 10</li>
					<li><b>Destreza:</b> 17</li>
					<li><b>Magia:</b> 15</li>
					<li><b>Fuerza:</b> 12</li>
					<li><b>Suerte:</b> 5</li>
					<li><b>Salud máxima:</b> 300</li>
				</ul>
			</p>
		</small>"
		>
	</a>

	<a href="{{ URL::to('charactercreation/create/drow') }}">
		<img src="/img/drow-test.png" alt="Jugar como Drow" data-toggle="popover" data-trigger="hover" data-placement="right" data-original-title="<b>Drows<b>" data-content=
		"<small>
			<em><strong>Raza pura</strong></em>
			<p>Los Drows tienen una excelente destreza en los combates mágicos, pero dejan mucho que desear en los físicos.</p>
			<p>
				<b>Estadísticas iniciales:</b>
				<ul>
					<li><b>Vida:</b> 4</li>
					<li><b>Destreza:</b> 6</li>
					<li><b>Magia:</b> 37</li>
					<li><b>Fuerza:</b> 7</li>
					<li><b>Suerte:</b> 5</li>
					<li><b>Salud máxima:</b> 150</li>
				</ul>
			</p>
		</small>"
		>
	</a>

	<a href="{{ URL::to('charactercreation/create/human') }}">
		<img src="/img/humano-test.png" alt="Jugar como Humano" data-toggle="popover" data-trigger="hover" data-placement="left" data-original-title="<b>Humanos<b>" data-content=
		"<small>
			<em><strong>Raza mixta</strong></em>
			<p>Los humanos tienen grandes destrezas tanto en el combate físico como mágico.</p>
			<p>
				<b>Estadísticas iniciales:</b>
				<ul>
					<li><b>Vida:</b> 15</li>
					<li><b>Destreza:</b> 10</li>
					<li><b>Magia:</b> 13</li>
					<li><b>Fuerza:</b> 17</li>
					<li><b>Suerte:</b> 5</li>
					<li><b>Salud máxima:</b> 400</li>
				</ul>
			</p>
		</small>"
		>
	</a>
</div>