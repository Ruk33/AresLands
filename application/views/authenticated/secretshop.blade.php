<h2>Mercado secreto</h2>
<p>
	Ven aqui extraño, ven a ver mis tesoros, no te decepsionaras pues mis objetos son regalo de los dioses.<br>
	Si tienes buen ojo y unas monedas divinas sabras de lo que hablo...
</p>

@if ( Session::has('error') )
<div class="row">
	<div class="alert alert-error span10 offset1">
		<strong>Oops!</strong>
		<p>{{ Session::get('error') }}</p>
	</div>
</div>
@endif

<div class="row" style="margin-top: 50px;">
	<ul class="inline table-content">
		@foreach ( $vipObjects as $id => $vipObject )
		<li class="text-left" style="margin-top: 7px; margin-bottom: 7px; vertical-align: top;">
			<div class="alert-center">
				<div class="alert-top"></div>

				<div class="alert-content" style="height: 100px;">
					{{ Form::open(URL::to('authenticated/buyFromSecretShop')) }}

					{{ Form::token() }}
					{{ Form::hidden('id', $id) }}

					<strong style="color: white;">{{ $vipObject->get_name() }}</strong>
					<div class="pull-left" style="margin-right: 10px;">
						<img src="{{ $vipObject->get_icon() }}" />
					</div>
					<p>{{ $vipObject->get_description() }}</p>

					<div style="position: absolute; bottom: 5px; right: 10px;">
					{{ Form::submit('Comprar', array('class' => 'ui-button ui-input-button')) }}
					</div>

					{{ Form::close() }}
				</div>

				<div class="alert-bot"></div>
			</div>
		</li>
		@endforeach
	</ul>
</div>