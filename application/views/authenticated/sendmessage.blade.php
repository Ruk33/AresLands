@if ( Session::has("errors") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>Oops!</h4>
        <ul>
        @foreach (Session::get("errors") as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
</div>
@endif

<h2>Enviar mensaje</h2>

<div class="span12">
	{{ Form::open(URL::to_route("post_authenticated_message_send")) }}
		{{ Form::token() }}
		<div>
			<i class="icon-user pull-left"></i>
			{{ Form::label('to', 'Para:') }}
		</div>

		{{ Form::text('to', Input::old('to', $to), array("class" => "span11")) }}

		<div style="margin-top: 15px;">
			<i class="icon-comment pull-left"></i>
			{{ Form::label('subject', 'Asunto:') }}
		</div>

		{{ Form::text('subject', Input::old('subject', $subject), array("class" => "span11")) }}


		<div style="margin-top: 15px;">
			<i class="icon-pencil pull-left"></i>
			{{ Form::label('content', 'Mensaje') }}
		</div>

		{{ Form::textarea('content', Input::old('content'), array("class" => "span11")) }}

		<div class="text-center">
		<span class="ui-button button">
			<i class="button-icon arrow"></i>
			<span class="button-content">
				{{ Form::submit('Enviar mensaje', array('class' => 'ui-button ui-input-button')) }}
			</span>
		</span>
		</div>
	{{ Form::close() }}
</div>