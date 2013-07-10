<h2>Crear grupo</h2>

<div class="span11">
	@if ( Session::has('errorMessages') )
		<div class="alert alert-error">
			<strong>Oops!</strong>
			<ul>
			@foreach ( Session::get('errorMessages') as $error )
				<li>{{ $error }}</li>
			@endforeach
			</ul>
		</div>
	@endif

	{{ Form::open() }}
		<div>
		{{ Form::label('name_label', 'Nombre del grupo') }}
		{{ Form::text('name', '', array('class' => 'input-block-level')) }}
		{{ Form::textarea('message', null, array('class' => 'ckeditor input-block-level')) }}
		</div>
		
		<div class="pull-right">
		{{ Form::submit('Crear', array('class' => 'btn btn-primary')) }}
		</div>
		
	{{ Form::close() }}
</div>

<script type="text/javascript" src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>