<h2>Mensajes privados</h2>

<a href="{{ URL::to('authenticated/sendMessage') }}" class="btn btn-primary">Enviar mensaje</a>

@if ( count($messages) > 0 )
	<table class="table table-hover" style="margin-left: -7.5px;">
		<thead>
			<tr>
				<th style="width: 40%;">Asunto</th>
				<th>Enviado por</th>
				<th>Fecha de envío</th>
				<th>Borrar</th>
			</tr>
		</thead>

		<tbody>
			@foreach ( $messages as $message )
			<tr @if ( $message->unread ) class="info" @endif>
				<td><a href="{{ URL::to('authenticated/readMessage/' . $message->id) }}">{{{ $message->subject }}}</a></td>
				<td>{{ $message->sender->get_link() }}</td>
				<td>{{ date("H:i:s d/m/Y", $message->date) }}</td>
				<td><a href="{{ URL::to('authenticated/deleteMessage/' . $message->id) }}" class="btn btn-danger">Borrar mensaje</a></td>
			</tr>
			@endforeach
		</tbody>
	</table>
@else
	<p>No tienes mensajes privados</p>
@endif