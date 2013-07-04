<h2>Enviar mensaje</h2>

<div class="span11">
	@if ( Session::has('errorMessages') )
		<div class="alert alert-error">
			<strong>Oops!</strong>

			<ul>
			@foreach (Session::get('errorMessages') as $errorMessage)
				<li>{{ $errorMessage }}</li>
			@endforeach
			</ul>
		</div>
	@endif

	{{ Form::open() }}

		{{ Form::label('to_label', 'Para:') }}

		@if ( $to )
			{{ Form::text('to', $to), array('class' => 'input-block-level') }}
		@else
			{{ Form::text('to', Input::old('to'), array('class' => 'input-block-level')) }}
		@endif

		{{ Form::label('to_label', 'Asunto:') }}
		{{ Form::text('subject', Input::old('subject'), array('class' => 'input-block-level')) }}

		{{ Form::label('content_label', 'Mensaje') }}
		{{ Form::textarea('content', Input::old('content'), array('class' => 'input-block-level')) }}

		{{ Form::submit('Enviar') }}

	{{ Form::close() }}
</div>