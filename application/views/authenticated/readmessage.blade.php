<table class="table">
	<thead>
		<tr>
			<td style="width: 100px;"><b>Enviado por:</b></td>
			<td>{{ $message->sender->get_link() }}</td>
		</tr>

		<tr>
			<td style="width: 100px;"><b>Asunto:</b></td>
			<td>{{{ $message->subject }}}</td>
		</tr>

		<td colspan="3">
			@if ( $message->is_special )
				{{ $message->content }}
			@else
				{{{ $message->content }}}
			@endif
		</td>
	</thead>
</table>

<div style="width: 730px;">
	@if ( ! $message->is_special )
		<a href="{{ URL::to('authenticated/sendMessage/' . $message->sender->name) }}" class="normal-button pull-left">Responder</a>
	@endif
	
	{{ Form::open(URL::to('authenticated/deleteMessage')) }}
		{{ Form::hidden('messages[]', $message->id) }}
		{{ Form::submit('Borrar mensaje', array('class' => 'normal-button danger-button pull-right', 'style' => 'width: 222px;')) }}
	{{ Form::close() }}
</div>