<h2>{{{ $clan->name }}}</h2>

<div class="span11" ng-controller="ViewClanController">
	@if ( Session::has('errorMessage') )
		<div class="alert alert-error">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			{{ Session::get('errorMessage') }}
		</div>
	@endif

	@if ( Session::has('successMessage') )
		<div class="alert alert-success">
			<a class="close" data-dismiss="alert" href="#">&times;</a>
			{{ Session::get('successMessage') }}
		</div>
	@endif

	@if ( $character->id == $clan->leader_id )
		@if ( isset($petitions) )
		<div class="dark-box">
			<h5>Peticiones de inclusión</h5>
			@if ( count($petitions) > 0 )
				<table class="table">
					<thead>
						<tr>
							<td style="width: 400px;">Nombre de personaje</td>
							<td colspan="2">Acciones</td>
						</tr>
					</thead>

					<tbody>
						@foreach ( $petitions as $petition )
							<tr>
								<td>{{ $petition->character->name }}</td>
								<td><a href="{{ URL::to('authenticated/clanAcceptPetition/' . $petition->id) }}" class="btn btn-primary">Aceptar</a></td>
								<td><a href="{{ URL::to('authenticated/clanRejectPetition/' . $petition->id) }}" class="btn btn-danger">Rechazar</a></td>
							</tr>
						@endforeach
					</tbody>
				</table>
			@else
				<p>No hay peticiones.</p>
			@endif
		</div>
		@endif

		@if ( count($members) == 1 )
		<div class="alert alert-error" style="margin-top: 20px;">
			<strong>Borrar grupo</strong>
			<p>Una vez borrado el grupo no será posible restaurarlo</p>
			<a href="{{ URL::to('authenticated/deleteClan') }}" onclick="return confirm('¿Seguro que quieres borrar el grupo?');" class="btn btn-danger">Borrar grupo</a>
		</div>
		@endif
	@endif

	<h2>Información básica</h2>

	<table class="table">
		<thead>
			<tr>
				<td style="width: 200px;">Creador</td>
				<td>
					<img src="{{ URL::base() }}/img/icons/crown-gold-icon.png" alt="">
					<b style="color: white;">{{ $clan->lider->name }}</b>
				</td>
			</tr>

			<tr>
				<td style="width: 200px;">Cantidad de miembros</td>
				<td>{{ count($members) }}</td>
			</tr>

			<tr>
				<td>Nivel</td>
				<td>{{ $clan->level }}</td>
			</tr>

			<tr>
				<td>Experiencia</td>
				<td>{{ $clan->xp }}/{{ $clan->xp_next_level }}</td>
			</tr>
		</thead>
	</table>

	<h2>Habilidades</h2>
	<ul class="inline text-center" ng-controller="Skill">
	@foreach ( $skills as $skill )
		<?php unset($skillsToLearn[$skill->skill_id]); ?>
		<li style="vertical-align: top;">
			<img src="{{ URL::base() }}/img/icons/skills/{{ $skill->skill_id }}.png" alt="" ng-mouseover="onMouseOver({{ $skill->skill_id }}, {{ $skill->level }}, false, true)" dynamic-tooltip="skill[{{ $skill->skill_id }}]" width="64px" height="64px">
			@if ( $clan->points_to_change > 0 && $character->id == $clan->leader_id )
				@if ( ClanSkillList::get_instance()->get_skill($skill->skill_id, $skill->level + 1) && ClanSkillList::get_instance()->can_learn($clan, $skill->skill_id, $skill->level + 1) )
					<p><a href="{{ URL::to('authenticated/learnClanSkill/' . $skill->skill_id . '/' . ($skill->level + 1)) }}">subir de nivel</a></p>
				@endif
			@endif
		</li>
	@endforeach
	@foreach ( $skillsToLearn as $key => $value )
		<li style="vertical-align: top;">
			<img class="grayEffect" src="{{ URL::base() }}/img/icons/skills/{{ $key }}.png" alt="" ng-mouseover="onMouseOver({{ $key }}, 1, true, false)" dynamic-tooltip="skill[{{ $key }}]" width="64px" height="64px">
			@if ( $clan->points_to_change > 0 && $character->id == $clan->leader_id )
				@if ( ClanSkillList::get_instance()->can_learn($clan, $key, 1) )
					<p><a href="{{ URL::to('authenticated/learnClanSkill/' . $key) }}">aprender</a></p>
				@endif
			@endif
		</li>
	@endforeach
	</ul>

	<h2>Mensaje</h2>
	@if ( $character->id == $clan->leader_id )
		<p id="message" name="message" contenteditable="true" alt="" data-toggle="tooltip" data-placement="top" data-original-title="Haz clic para editar">{{{ $clan->message }}}</p>
	@else
		<p id="message" name="message">{{{ $clan->message }}}</p>
	@endif
	
	<h2>Miembros</h2>

	<ul class="inline">
	@foreach ( $members as $member )
		<li>
			<div class="clan-member-link">
				<ul class="inline">
					<li>
						<!--<img src="{{ URL::base() }}/img/icons/race/{{ $member->race }}_{{ $member->gender }}.jpg" alt="">-->
						<div class="icon-race-30 icon-race-30-{{ $member->race }}_{{ $member->gender }} pull-left"></div>
						<a href="{{ URL::to('authenticated/character/' . $member->name) }}" style="line-height: 30px; margin-left: 10px;">{{ $member->name }} ({{ $member->level }})</a>
					</li>
	
					@if ( $character->id == $clan->leader_id && $member->id != $character->id )
					<li style="vertical-align: top;">
						<a class="close" onclick="return confirm('¿Seguro que quieres eliminar a {{ $member->name }} del grupo?');" href="{{ URL::to('authenticated/clanRemoveMember/' . $member->name) }}">&times;</a>
					</li>
					@endif
				</ul>
			</div>
		</li>
	@endforeach
	</ul>

	@if ( $character->clan_id == 0 && ! $character->petitions()->where('clan_id', '=', $clan->id)->first() )
		<h2>¿Quieres ingresar?</h2>
		<a href="{{ URL::to('authenticated/clanJoinRequest/' . $clan->id) }}">Solicitar inclusión</a>
	@elseif ( $character->clan_id == $clan->id && $character->id != $clan->leader_id )
		<h2>¿Salir del grupo?</h2>
		<a href="{{ URL::to('authenticated/leaveFromClan') }}">Salir del grupo</a>
	@endif
</div>

<script type="text/javascript" src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{ URL::base() }}/js/bbcode-parser.js"></script>
<script type="text/javascript" src="{{ URL::base() }}/js/controllers/ViewClanController.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var result = XBBCODE.process({
			text: $.trim($('#message').html()),
			removeMisalignedTags: false,
			addInLineBreaks: true
		});

		$('#message').html(result.html);
	});
</script>