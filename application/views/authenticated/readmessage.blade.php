<table class="table">
	<thead>
		<tr>
			<td style="width: 100px;"><b>Enviado por:</b></td>
			<td>{{ $message->sender->name }}</td>
		</tr>

		<tr>
			<td style="width: 100px;"><b>Asunto:</b></td>
			<td>{{{ $message->subject }}}</td>
		</tr>
	</thead>

	<tbody>
		<td colspan="3">{{{ $message->content }}}</td>
	</tbody>
</table>

<div style="width: 730px;">
	<a href="{{ URL::to('authenticated/sendMessage/' . $message->sender->name) }}" class="btn btn-primary">Responder</a>
	<a href="{{ URL::to('authenticated/deleteMessage/' . $message->id) }}" class="btn btn-danger pull-right">Borrar mensaje</a>
</div>