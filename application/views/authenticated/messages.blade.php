<div class="span11">
	<h2 class="pull-left" style="margin-left: 10px;">Mensajes privados</h2>
	<a href="{{ URL::to('authenticated/sendMessage') }}" class="ui-button button pull-right" style="margin-top: 20px;">
		<i class="button-icon document"></i>
		<span class="button-content">Enviar nuevo mensaje</span>
	</a>
</div>

<div class="clearfix"></div>

<ul class="inline text-center span11" id="messages-tabs" style="margin-top: 20px;">
	<li>
		<a href="#received-messages" class="ui-button button">
			<i class="button-icon document"></i>
			<span class="button-content">
				Mensajes recibidos
			</span>
		</a>
	</li>
	<li>
		<a href="#attack-reports" class="ui-button button">
			<i class="button-icon axe"></i>
			<span class="button-content">
				Informes de Ataques
			</span>
		</a>
	</li>
	<li>
		<a href="#defense-reports" class="ui-button button">
			<i class="button-icon hearth"></i>
			<span class="button-content">
				Informes de Defensas
			</span>
		</a>
	</li>
</ul>

<div class="tab-content dark-box span11" style="margin-top: 20px;">
	<div class="tab-pane active" id="received-messages">
		<strong style="color: white;">Mensajes recibidos</strong>

		@if ( count($messages['received']) > 0 )
		{{ Form::open(URL::to('authenticated/deleteMessage')) }}
			<div style="margin-top: 15px;">
				<span class="ui-button button pull-right">
					<i class="button-icon cross"></i>
					<span class="button-content">
						{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
					</span>
				</span>

				<a href="{{ URL::to('authenticated/clearAllMessages/received') }}" class="ui-button button pull-left" onclick="return confirm('¿Seguro que quieres eliminar TODOS los mensajes?');">
					<i class="button-icon fire"></i>
					<span class="button-content">Borrar todos los mensajes</span>
				</a>
			</div>

			<div class="clearfix"></div>
			<hr style="border-top: 1px solid #252525;">

			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width: 40%;">Asunto</th>
						<th>Enviado por</th>
						<th>Fecha de envío</th>
						<th>Borrar</th>
					</tr>
				</thead>

				<tbody>
					@foreach ( $messages['received'] as $message )
					<tr>
						<td>
							@if ( $message->unread )
								(*)
							@endif
							<a href="{{ URL::to('authenticated/readMessage/' . $message->id) }}">{{{ $message->subject }}}</a>
						</td>
						<td>{{ $message->sender->get_link() }}</td>
						<td>{{ date("H:i:s d/m/Y", $message->date) }}</td>
						<td>{{ Form::checkbox('messages[]', $message->id) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<hr style="border-top: 1px solid #252525;">

			<span class="ui-button button pull-right">
				<i class="button-icon cross"></i>
				<span class="button-content">
					{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
				</span>
			</span>
		{{ Form::close() }}
		@else
			<p>No tienes mensajes recibidos.</p>
		@endif
	</div>

	<div class="tab-pane" id="attack-reports">
		<strong style="color: white;">Informes de ataque</strong>
		@if ( count($messages['attack']) > 0 )
		{{ Form::open(URL::to('authenticated/deleteMessage')) }}
			<div style="margin-top: 15px;">
				<span class="ui-button button pull-right">
					<i class="button-icon cross"></i>
					<span class="button-content">
						{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
					</span>
				</span>

				<a href="{{ URL::to('authenticated/clearAllMessages/attack') }}" class="ui-button button pull-left" onclick="return confirm('¿Seguro que quieres eliminar TODOS los mensajes?');">
					<i class="button-icon fire"></i>
					<span class="button-content">Borrar todos los mensajes</span>
				</a>
			</div>

			<div class="clearfix"></div>
			<hr style="border-top: 1px solid #252525;">

			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width: 40%;">Asunto</th>
						<th>Enviado por</th>
						<th>Fecha de envío</th>
						<th>Borrar</th>
					</tr>
				</thead>

				<tbody>
					@foreach ( $messages['attack'] as $message )
					<tr>
						<td>
							@if ( $message->unread )
								(*)
							@endif
							<a href="{{ URL::to('authenticated/readMessage/' . $message->id) }}">{{{ $message->subject }}}</a>
						</td>
						<td>{{ $message->sender->get_link() }}</td>
						<td>{{ date("H:i:s d/m/Y", $message->date) }}</td>
						<td>{{ Form::checkbox('messages[]', $message->id) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<hr style="border-top: 1px solid #252525;">

			<span class="ui-button button pull-right">
				<i class="button-icon cross"></i>
				<span class="button-content">
					{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
				</span>
			</span>
		{{ Form::close() }}
		@else
			<p>No tienes informes de batallas.</p>
		@endif
	</div>

	<div class="tab-pane" id="defense-reports">
	<strong style="color: white;">Informes de defensa</strong>
		@if ( count($messages['defense']) > 0 )
		{{ Form::open(URL::to('authenticated/deleteMessage')) }}
			<div style="margin-top: 15px;">
				<span class="ui-button button pull-right">
					<i class="button-icon cross"></i>
					<span class="button-content">
						{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
					</span>
				</span>

				<a href="{{ URL::to('authenticated/clearAllMessages/defense') }}" class="ui-button button pull-left" onclick="return confirm('¿Seguro que quieres eliminar TODOS los mensajes?');">
					<i class="button-icon fire"></i>
					<span class="button-content">Borrar todos los mensajes</span>
				</a>
			</div>

			<div class="clearfix"></div>
			<hr style="border-top: 1px solid #252525;">

			<table class="table table-hover">
				<thead>
					<tr>
						<th style="width: 40%;">Asunto</th>
						<th>Enviado por</th>
						<th>Fecha de envío</th>
						<th>Borrar</th>
					</tr>
				</thead>

				<tbody>
					@foreach ( $messages['defense'] as $message )
					<tr>
						<td>
							@if ( $message->unread )
								(*)
							@endif
							<a href="{{ URL::to('authenticated/readMessage/' . $message->id) }}">{{{ $message->subject }}}</a>
						</td>
						<td>{{ $message->sender->get_link() }}</td>
						<td>{{ date("H:i:s d/m/Y", $message->date) }}</td>
						<td>{{ Form::checkbox('messages[]', $message->id) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>

			<hr style="border-top: 1px solid #252525;">

			<span class="ui-button button pull-right">
				<i class="button-icon cross"></i>
				<span class="button-content">
					{{ Form::submit('Borrar mensajes seleccionados', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro quieres eliminar los mensajes seleccionados?");')) }}
				</span>
			</span>
		{{ Form::close() }}
		@else
			<p>No tienes informes de defensas.</p>
		@endif
	</div>
</div>

<script type="text/javascript">
	$('#messages-tabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
</script>