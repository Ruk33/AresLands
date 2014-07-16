<div class="row">
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
</div>

<div class="span11">
	@if ( ! $message->is_special )
        {{ Form::open(URL::to_route("get_authenticated_message_send"), "get") }}
            {{ Form::hidden("to", $message->sender->name) }}
            {{ Form::hidden("subject", "RE: " . $message->subject) }}
            
            <span class="ui-button button pull-left">
                <i class="button-icon document"></i>
                <span class="button-content">
                    {{ Form::submit("Responder", array("class" => "ui-button ui-input-button")) }}
                </span>
            </span>
        {{ Form::close() }}
	@endif
	
	{{ Form::open(URL::to_route("post_authenticated_message_delete")) }}
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
