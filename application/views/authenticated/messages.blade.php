<div class="span12">
	<h2 class="pull-left">
		@if ( $type == 'received' )
			Mensajes privados
		@elseif ( $type == 'attack' )
			Informes de ataque
		@elseif ( $type == 'defense' )
			Informes de defensa
		@endif
	</h2>
	
	<ul class="inline text-center pull-right">
		<li>
			<a href="{{ URL::to_route("get_authenticated_message_category", array("received")) }}" class="ui-button button">
				<i class="button-icon document"></i>
				<span class="button-content">
					Mensajes recibidos
				</span>
			</a>
		</li>
		<li>
			<a href="{{ URL::to_route("get_authenticated_message_category", array("attack")) }}" class="ui-button button">
				<i class="button-icon axe"></i>
				<span class="button-content">
					Ataques
				</span>
			</a>
		</li>
		<li>
			<a href="{{ URL::to_route("get_authenticated_message_category", array("defense")) }}" class="ui-button button">
				<i class="button-icon hearth"></i>
				<span class="button-content">
					Defensas
				</span>
			</a>
		</li>
	</ul>
</div>

<div class="clearfix"></div>

<div style="height: 75px;">
	<a href="{{ URL::to_route("get_authenticated_message_send") }}" class="ui-button button pull-left" style="margin-top: 20px;">
		<i class="button-icon document"></i>
		<span class="button-content">Enviar nuevo mensaje</span>
	</a>

	@if ( count($messages) > 0 )
	<div class="pull-right">
		{{ Form::open(URL::to_route("post_authenticated_message_clear")) }}
			{{ Form::token() }}
			{{ Form::hidden('type', $type) }}
			<span class="ui-button button" style="margin-top: 20px;">
				<i class="button-icon cross"></i>
				<span class="button-content">
					{{ Form::submit('Borrar todos los mensajes', array('class' => 'ui-input-button ui-button', 'onclick' => 'return confirm("¿Seguro que deseas borrar TODOS los mensajes?")')) }}
				</span>
			</span>
		{{ Form::close() }}
	</div>
	@endif
</div>

<div class="row">
	@if ( count($messages) > 0 )
	{{ Form::open(URL::to_route("post_authenticated_message_delete")) }}
		{{ Form::token() }}
		<table class="table table-striped brown-table">
			<thead>
				<tr class="text-left">
					<th class="span5" style="background-image: url('/img/messages-background.png'); background-repeat: no-repeat;">Asunto</th>
                    <th class="span2"><div class="text-center">Enviado por</div></th>
					<th class="span3">Fecha de envío</th>
					<th class="span1" style="background-color: rgb(74, 18, 17);" data-toggle="tooltip" data-original-title="Borrar mensajes seleccionados">
                        {{ Form::submit('Borrar', array('class' => 'ui-button ui-input-button', 'style' => 'font-weight: bold;')) }}
                    </th>
				</tr>
			</thead>

			<tbody>
				@foreach ( $messages as $message )
				<tr>
					<td class="span5">
						<span data-toggle="tooltip" data-original-title="{{ Str::limit(strip_tags($message->content), 50) }}">
							@if ( $message->unread )
                                *
							@endif
							<a href="{{ $message->get_link() }}">{{ $message->subject }}</a>
						</span>
					</td>
                    <td class="span2"><div class="text-center">{{ $message->sender->get_link() }}</div></td>
					<td class="span3">{{ date("H:i:s d/m/Y", $message->date) }}</td>
					<td class="span1"><div class="text-center" style="margin-top: -5px;">{{ Form::checkbox('messages[]', $message->id) }}</div></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	{{ Form::close() }}
	@else
	<div class="text-center">
		<h4 style="margin-top: 100px;">No tienes mensajes</h4>
	</div>
	@endif
</div>