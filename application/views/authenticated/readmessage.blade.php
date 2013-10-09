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
		<a href="{{ URL::to('authenticated/sendMessage/' . $message->sender->name) }}" class="ui-button button pull-left">
			<i class="button-icon document"></i>
			<span class="button-content">
				Responder
			</span>
		</a>
	@endif
	
	{{ Form::open(URL::to('authenticated/deleteMessage')) }}
		{{ Form::token() }}
		{{ Form::hidden('messages[]', $message->id) }}
		<span class="ui-button button pull-right">
			<i class="button-icon fire"></i>
			<span class="button-content">
				{{ Form::submit('Borrar mensaje', array('class' => 'ui-button ui-input-button')) }}
			</span>
		</span>
	{{ Form::close() }}
</div>