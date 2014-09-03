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
            {{ Form::open(URL::to_route("post_authenticated_clan_delete")) }}
                {{ Form::submit("Borrar", array("class" => "btn btn-danger", "onclick" => "return confirm('¿Seguro que quieres borrar el grupo?');")) }}
            {{ Form::close() }}
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
	@foreach ( $skills as $skill )
		<li style="vertical-align: top;">
			@if ( $clan->has_skill($skill) )
				<img src="{{ $skill->get_image_path() }}" alt="" skill-tooltip skill-id="{{ $skill->id }}" skill-level="{{ $skill->level }}" skill-show-next-level="true" width="64px" height="64px">
				<?php $nextLevel = $skill->get_next_level()->first(); ?>
				@if ( $nextLevel && $clan->can_learn_skill($nextLevel) && $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
                    {{ Form::open(URL::to_route("post_authenticated_clan_learn_skill")) }}
                        {{ Form::hidden("skill_id", $nextLevel->id) }}
                        {{ Form::hidden("skill_level", $nextLevel->level) }}
                        
                        {{ Form::submit("Subir nivel", array("class" => "ui-button ui-input-button")) }}
                    {{ Form::close() }}
				@endif
			@else
				<img class="grayEffect" src="{{ $skill->get_image_path() }}" alt="" skill-tooltip skill-id="{{ $skill->id }}" skill-level="1" width="64px" height="64px">
				@if ( $clan->can_learn_skill($skill) && $clan->has_permission($character, Clan::PERMISSION_LEARN_SPELL) )
					{{ Form::open(URL::to_route("post_authenticated_clan_learn_skill")) }}
                        {{ Form::hidden("skill_id", $skill->id) }}
                        {{ Form::hidden("skill_level", 1) }}
                        
                        {{ Form::submit("Aprender", array("class" => "ui-button ui-input-button")) }}
                    {{ Form::close() }}
				@endif
			@endif
		</li>
	@endforeach
	</ul>

	<h2 style="margin-top: 50px;">Mensaje</h2>
	@if ( $character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_EDIT_MESSAGE) )
		<p id="message" name="message" contenteditable="true" alt="" data-toggle="tooltip" data-placement="top" data-original-title="Haz clic para editar" style="width: 100%; min-height: 50px;">{{{ $clan->message }}}</p>
	@else
		<p id="message" name="message">{{{ $clan->message }}}</p>
	@endif
	
	<h2 style="margin-top: 50px;">Miembros</h2>

	<div class="row" style="margin-bottom: 25px;">
	<ul class="inline text-center span12">
	@foreach ( $members as $member )
		<li style="width: 45%; vertical-align: top;">
			<div class="clan-member-link">
                <div class="pull-right">
                    @if ( $character->id != $member->id && $member->id != $clan->leader_id && ($character->id == $clan->leader_id || $clan->has_permission($character, Clan::PERMISSION_KICK_MEMBER)) )
					<span data-toggle="tooltip" data-original-title="Expulsar miembro del grupo">
						<a class="close" onclick="return confirm('¿Seguro que quieres eliminar a {{ $member->name }} del grupo?');" href="{{ URL::to('authenticated/clanRemoveMember/' . $member->name) }}">&times;</a>
                    </span>
					@endif
                </div>
				<ul class="inline">
					<li style="vertical-align: top;">
						<div class="icon-race-30 icon-race-30-{{ $member->race }}_{{ $member->gender }} pull-left"></div>
					</li>
					
					<li class="text-left" style="vertical-align: top;">
						@if ( $character->is_in_clan_of($member) )
							@if ( $member->is_online() )
								<span class="badge badge-success">ON</span>
							@else
								<span class="badge badge-important">OFF</span>
							@endif
						@endif
						<a href="{{ URL::to_route("get_authenticated_character_show", array($member->name)) }}" character-tooltip="{{ $member->name }}">{{ $member->name }} ({{ $member->level }})</a>
						@if ( $character->is_in_clan_of($member) )
							<ul style="font-size: 10px; text-transform: uppercase; margin-top: 10px;" class="unstyled">
								<li><b>Zona:</b> {{ $member->zone->name }}</li>
								<li><b>Ultima actividad:</b> {{ date("d/m/Y", $member->last_activity_time) }}</li>
								@if ( $character->id == $clan->leader_id && $character->id != $member->id )
								<li>
									{{ Form::open(URL::to_route("post_authenticated_clan_give_leader")) }}
										{{ Form::token() }}
										{{ Form::hidden('id', $member->id) }}

										{{ Form::submit('Ceder liderazgo', array('class' => 'ui-button ui-input-button', 'style' => 'color: orange; font-size: 10px; text-transform: uppercase;', 'onclick' => 'return confirm("¿Estas seguro que quieres cederle el liderazgo a ' . $member->name . '?");')) }}
									{{ Form::close() }}
								</li>
								@endif
							</ul>
						@endif
					</li>
	
					@if ( $character->id == $clan->leader_id && $member->id != $character->id )
					<li style="vertical-align: top; margin-top: 2px;">
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
									
									<li class="clan-member-link">
										{{ Form::checkbox('can_register_tournament', 'can_register_tournament', $member->has_permission(Clan::PERMISSION_REGISTER_TOURNAMENT), array('style' => 'margin-top: -3px;')) }}
										Registrar en torneo
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
				</ul>
			</div>
		</li>
	@endforeach
	</ul>
	</div>

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