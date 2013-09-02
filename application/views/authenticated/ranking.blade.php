<h2>Ranking</h2>

<ul class="inline text-center span11" id="ranking-tabs" style="margin-top: 20px;">
	<li>
		<a href="#pvp" class="ui-button button">
			<i class="button-icon dagger"></i>
			<span class="button-content">
				PVP
			</span>
		</a>
	</li>
	<li>
		<a href="#xp" class="ui-button button">
			<i class="button-icon axe"></i>
			<span class="button-content">
				Experiencia
			</span>
		</a>
	</li>
	<li>
		<a href="#clan" class="ui-button button">
			<i class="button-icon fire"></i>
			<span class="button-content">
				Grupos
			</span>
		</a>
	</li>
</ul>

<div class="tab-content span11" style="margin-top: 20px;">
	<div class="tab-pane active" id="pvp">
		<table class="table table-hover">
			<thead>
				<tr>
					<th width="20px">#</th>
					<th width="50px">Raza</th>
					<th>Nombre</th>
					<th width="150px">Puntos de PVP</th>
				</tr>
			</thead>

			<tbody>
				<?php $index = 0; ?>
				@foreach ( $characters_pvp as $character )
				<tr>
					<td>{{ ++$index }}</td>
					<td>
						<div class="icon-race-30 icon-race-30-{{ $character->race }}_{{ $character->gender }}"></div>
					</td>
					<td>
						@if ( $index == 1 )
							<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
						@elseif ( $index == 2 )
							<img src="{{ URL::base() }}/img/icons/crown-silver-icon.png" alt="">
						@elseif ( $index == 3 )
							<img src="{{ URL::base() }}/img/icons/crown-bronze-icon.png" alt="">
						@endif
						{{ $character->get_link() }}
					</td>
					<td>{{ $character->pvp_points }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="tab-pane" id="xp">
		<table class="table table-hover">
			<thead>
				<tr>
					<th width="20px">#</th>
					<th width="50px">Raza</th>
					<th>Nombre</th>
					<th width="50px">Nivel</th>
					<th width="100px">Experiencia</th>
				</tr>
			</thead>

			<tbody>
				<?php $index = 0; ?>
				@foreach ( $characters_xp as $character )
				<tr>
					<td>{{ ++$index }}</td>
					<td>
						<div class="icon-race-30 icon-race-30-{{ $character->race }}_{{ $character->gender }}"></div>
					</td>
					<td>
						@if ( $index == 1 )
							<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
						@elseif ( $index == 2 )
							<img src="{{ URL::base() }}/img/icons/crown-silver-icon.png" alt="">
						@elseif ( $index == 3 )
							<img src="{{ URL::base() }}/img/icons/crown-bronze-icon.png" alt="">
						@endif
						{{ $character->get_link() }}
					</td>
					<td>{{ $character->level }}</td>
					<td>{{ $character->xp }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="tab-pane" id="clan">
		<table class="table table-hover">
			<thead>
				<tr>
					<th width="20px">#</th>
					<th>Nombre</th>
					<th width="100px;">Puntos</th>
				</tr>
			</thead>

			<tbody>
				<?php $index = 0; ?>
				@foreach ( $clansPuntuation as $clanPuntuation )
				<tr>
					<td>{{ ++$index }}</td>
					<td>
						@if ( $index == 1 )
							<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
						@elseif ( $index == 2 )
							<img src="{{ URL::base() }}/img/icons/crown-silver-icon.png" alt="">
						@elseif ( $index == 3 )
							<img src="{{ URL::base() }}/img/icons/crown-bronze-icon.png" alt="">
						@endif

						@if ( $clanPuntuation->clan )
							{{ $clanPuntuation->clan->get_link() }}
						@endif
					</td>
					<td>{{ $clanPuntuation->points }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$('#ranking-tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
</script>