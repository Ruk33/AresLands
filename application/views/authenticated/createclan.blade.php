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

	{{ Form::open(URL::to_route("post_authenticated_clan_create")) }}
		{{ Form::token() }}
	
		<div>
		{{ Form::label('name', 'Nombre del grupo') }}
		{{ Form::text('name', '', array('class' => 'input-block-level')) }}

		{{ Form::label('message', 'Mensaje del grupo') }}
		{{ Form::textarea('message', null, array('class' => 'ckeditor input-block-level')) }}
		</div>
		
		<div class="pull-right">
			<span class="ui-button button">
				<i class="button-icon castle"></i>
				<span class="button-content">
					{{ Form::submit('Crear grupo', array('class' => 'ui-button ui-input-button')) }}
				</span>
			</span>
		</div>
		
	{{ Form::close() }}
</div>

<script type="text/javascript" src="{{ URL::base() }}/js/libs/ckeditor/ckeditor.js"></script>