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

		<div>
			<i class="icon-user"></i>
			{{ Form::label('to', 'Para:', array('style' => 'display: inline;')) }}
		</div>

		@if ( $to )
			{{ Form::text('to', $to, array('class' => 'input', 'style' => 'width: 100%;')) }}
		@else
			{{ Form::text('to', Input::old('to'), array('class' => 'input', 'style' => 'width: 100%;')) }}
		@endif

		<div style="margin-top: 15px;">
			<i class="icon-comment"></i>
			{{ Form::label('subject', 'Asunto:', array('style' => 'display: inline;')) }}
		</div>

		{{ Form::text('subject', Input::old('subject'), array('class' => 'input', 'style' => 'width: 100%;')) }}


		<div style="margin-top: 15px;">
			<i class="icon-pencil"></i>
			{{ Form::label('content', 'Mensaje', array('style' => 'display: inline;')) }}
		</div>

		{{ Form::textarea('content', Input::old('content'), array('class' => 'input', 'style' => 'width: 100%;')) }}

		<div class="text-center">
		{{ Form::submit('Enviar', array('class' => 'normal-button', 'style' => 'width: 222px;')) }}
		</div>

	{{ Form::close() }}
</div>