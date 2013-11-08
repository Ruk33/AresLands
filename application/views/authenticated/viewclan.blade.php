<h1 class="text-center" style="padding-top: 25px; padding-bottom: 25px;">{{{ $clan->name }}}</h1>

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

	@if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_ACCEPT_PETITION) || $clan->has_permission($character, Clan::PERMISSION_DECLINE_PETITION) )
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
								@if ( $clan->has_permission($character, Clan::PERMISSION_ACCEPT_PETITION) )
								<td>
									<div data-toggle="tooltip" data-original-title="Acepta la petición para que este jugador ingrese al grupo">
										<a href="{{ URL::to('authenticated/clanAcceptPetition/' . $petition->id) }}" class="btn btn-primary">Aceptar</a>
									</div>
								</td>
								@endif
								
								@if ( $clan->has_permission($character, Clan::PERMISSION_DECLINE_PETITION) )
								<td>
									<div data-toggle="tooltip" data-original-title="Rechaza la petición de ingreso de este jugador">
										<a href="{{ URL::to('authenticated/clanRejectPetition/' . $petition->id) }}" class="btn btn-danger">Rechazar</a>
									</div>
								</td>
								@endif
							</tr>
						@endforeach
					</tbody>
				</table>
			@else
				<p>No hay peticiones.</p>
			@endif
		</div>
		@endif
	@endif
	
	@if ( $character->id == $clan->leader_id )
		@if ( count($members) == 1 )
		<div class="alert alert-error" style="margin-top: 20px;">
			<strong>Borrar grupo</strong>
			<p>Una vez borrado el grupo no será posible restaurarlo</p>
			<a href="{{ URL::to('authenticated/deleteClan') }}" onclick="return confirm('¿Seguro que quieres borrar el grupo?');" class="btn btn-danger">Borrar grupo</a>
		</div>
		@endif
	@endif

	<h2 style="margin-top: 50px;">Información básica</h2>

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
            
            @if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
            <tr>
				<td>Puntos para cambiar</td>
				<td>{{ $clan->points_to_change }}</td>
			</tr>
            @endif
		</thead>
	</table>

	<h2 style="margin-top: 50px;">Habilidades</h2>

	<ul class="inline text-center" ng-controller="Skill">
	@foreach ( $clanSkills as $skill )
		<li style="vertical-align: top;">
			<img src="{{ URL::base() }}/img/icons/skills/{{ $skill->skill_id }}.png" alt="" skill-tooltip skill-id="{{ $skill->skill_id }}" skill-level="{{ $skill->level }}" skill-show-next-level="true" width="64px" height="64px">
			@if ( $clan->points_to_change > 0 && ($character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL)) )
				<?php $nextLevel = Skill::where('level', '=', $skill->level + 1)->where('id', '=', $skill->skill_id)->first(); ?>
				@if ( $nextLevel && $nextLevel->can_be_learned_by_clan($clan) )
					<p><a href="{{ URL::to('authenticated/learnClanSkill/' . $skill->skill_id . '/' . ($skill->level + 1)) }}">subir de nivel</a></p>
				@endif
			@endif
		</li>
	@endforeach
	@foreach ( $skills as $skill )
		<li style="vertical-align: top;">
			<img class="grayEffect" src="{{ URL::base() }}/img/icons/skills/{{ $skill->id }}.png" alt="" skill-tooltip skill-id="{{ $skill->id }}" skill-level="{{ $skill->level }}" width="64px" height="64px">
			@if ( $clan->points_to_change > 0 && ($character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL)) )
				@if ( $skill->can_be_learned_by_clan($clan) )
					<p><a href="{{ URL::to('authenticated/learnClanSkill/' . $skill->id) }}">aprender</a></p>
				@endif
			@endif
		</li>
	@endforeach
	</ul>

	<h2 style="margin-top: 50px;">Mensaje</h2>
	@if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_EDIT_MESSAGE) )
		<p id="message" name="message" contenteditable="true" alt="" data-toggle="tooltip" data-placement="top" data-original-title="Haz clic para editar">{{{ $clan->message }}}</p>
	@else
		<p id="message" name="message">{{{ $clan->message }}}</p>
	@endif
	
	<h2 style="margin-top: 50px;">Miembros</h2>

	<ul class="inline text-center">
	@foreach ( $members as $member )
		<li style="width: 45%;">
			<div class="clan-member-link">
				<ul class="inline">
					<li>
						<div class="icon-race-30 icon-race-30-{{ $member->race }}_{{ $member->gender }} pull-left"></div>
					</li>
					
					<li style="vertical-align: 10px;" character-tooltip="{{ $member->name }}">
						<a href="{{ URL::to('authenticated/character/' . $member->name) }}" style="line-height: 30px; margin-left: 10px;">{{ $member->name }} ({{ $member->level }})</a>
					</li>
	
					@if ( $character->id == $clan->leader_id && $member->id != $character->id )
					<li style="vertical-align: 10px;">
						<div id="{{ $member->id }}_permissions" style="display: none;">
							{{ Form::open('authenticated/clanModifyMemberPermissions') }}
							
								{{ Form::token() }}
								{{ Form::hidden('id', $member->id) }}
								
								<div class="text-center">
									<b>Permisos de {{ $member->name }}</b>
								</div>
								
								<ul class="unstyled" style="margin-top: 10px;">
									<li class="clan-member-link">
										{{ Form::checkbox('can_accept_petition', 'can_accept_petition', $member->has_permission(Clan::PERMISSION_ACCEPT_PETITION), array('style' => 'margin-top: -3px;')) }}
										Aceptar peticiones
									</li>
									
									<li class="clan-member-link">
										{{ Form::checkbox('can_decline_petition', 'can_decline_petition', $member->has_permission(Clan::PERMISSION_DECLINE_PETITION), array('style' => 'margin-top: -3px;')) }}
										Declinar peticiones
									</li>
									
									<li class="clan-member-link">
										{{ Form::checkbox('can_kick_member', 'can_kick_member', $member->has_permission(Clan::PERMISSION_KICK_MEMBER), array('style' => 'margin-top: -3px;')) }}
										Expulsar miembros
									</li>
									
									<li class="clan-member-link">
										{{ Form::checkbox('can_learn_spell', 'can_learn_spell', $member->has_permission(Clan::PERMISSION_LEARN_SPELL), array('style' => 'margin-top: -3px;')) }}
										Aprender habilidades
									</li>
									
									<li class="clan-member-link">
										{{ Form::checkbox('can_edit_message', 'can_edit_message', $member->has_permission(Clan::PERMISSION_EDIT_MESSAGE), array('style' => 'margin-top: -3px;')) }}
										Editar mensaje del grupo
									</li>
								</ul>
								
								<div class="text-center" style="margin-top: 20px;">
									{{ Form::submit('Actualizar', array('class' => 'btn btn-primary')) }}
								</div>
							
							{{ Form::close() }}
						</div>
						<i class="icon icon-chevron-up" name="link_permissions" data-placement="top" data-target="{{ $member->id }}_permissions" style="cursor: pointer;"></i>
					</li>
					@endif
					
					@if ( $character->id != $member->id && $member->id != $clan->leader_id && ($character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_KICK_MEMBER)) )
					<li class="pull-right" style="margin-top: 4px;" data-toggle="tooltip" data-original-title="Expulsar miembro del grupo">
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
		
		$('[name="link_permissions"]').each(function() {
			$(this).popover({
				html: true,
				content: function() {
					return $("#" + $(this).attr('data-target')).html();
				}
			})
		});
	});
</script>