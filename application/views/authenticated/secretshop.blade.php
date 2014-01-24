@if ( Session::has('error') )
	<div class="alert alert-error">
		<strong>Oops!</strong>
		<p>{{ Session::get('error') }}</p>
	</div>
@endif

@foreach ( $vipObjects as $id => $vipObject )
	{{ Form::open(URL::to('authenticated/buyFromSecretShop')) }}
		
		{{ Form::token() }}
		{{ Form::hidden('id', $id) }}
		
		<strong>{{ $vipObject->get_name() }}</strong>
		<div class="pull-left" style="margin-right: 10px;">
			<img src="{{ $vipObject->get_icon() }}" />
		</div>
		<p>{{ $vipObject->get_description() }}</p>
		
		{{ Form::submit('Comprar') }}
	
	{{ Form::close() }}
@endforeach
