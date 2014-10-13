<h2>Mercado secreto</h2>
<p>
	Ven aqui extraño, ven a ver mis tesoros, no te decepsionaras pues mis objetos son regalo de los dioses.<br>
	Si tienes buen ojo y unas monedas divinas sabras de lo que hablo...
</p>

@if ( Session::has('errors') )
<div class="row">
	<div class="alert alert-error span10 offset1">
		<strong>Oops!</strong>
		<ul>
		@foreach ( Session::get('errors') as $error )
			<li>{{ $error }}</li>
		@endforeach
		</ul>
	</div>
</div>
@endif

<div class="row" style="margin-top: 50px;">
	<ul class="inline table-content">
		<li class="span12" style="color: white; font-size: 18px; text-shadow: black 0 0 5px, black 0 0 5px; padding: 20px;">
			<div class="alert-center text-center" style="margin: 0 auto;">
				<div class="alert-top"></div>

				<div class="alert-content" style="height: 75px;">
					<div style="margin-top: 20px">
						Tus <span data-toggle="tooltip" data-original-title="Las TitanCoins son monedas especiales que adquieres mediante dinero real para comprar diferentes objetos en los juegos de TitanGames"><u>TitanCoins</u></span>: {{ Auth::user()->coins }} (<a href="//titangames.com.ar/profile/coins/peso">Conseguir</a>)
					</div>
				</div>

				<div class="alert-bot"></div>
			</div>
		</li>
		@foreach ( $vipObjects as $id => $vipObject )
		<li class="text-left" style="margin-top: 7px; margin-bottom: 7px; vertical-align: top;">
			<div class="alert-center">
				<div class="alert-top"></div>

				<div class="alert-content" style="height: 175px;">
					{{ Form::open(URL::to_route("post_authenticated_secret_shop_buy")) }}

					{{ Form::token() }}
					{{ Form::hidden('id', $id) }}

					<strong style="color: white;">{{ $vipObject->getName() }}</strong>
					<div class="pull-left" style="margin-right: 10px;">
						<img src="{{ $vipObject->getIcon() }}" />
					</div>
					<p>{{ $vipObject->getDescription() }}</p>

					<div class="clearfix"></div>

					<div style="margin-top: 10px;">
                        {{ $vipObject->getInput() }}
					</div>

					<div class="clearfix"></div>

					<div style="position: absolute; bottom: 10px; left: 10px; font-size: 11px; text-transform: uppercase;">
						TitanCoins: {{ $vipObject->getPrice() }}
					</div>

					<div style="position: absolute; bottom: 10px; right: 10px;">
						{{ Form::submit('Comprar', array('class' => 'ui-button ui-input-button', 'onclick' => 'return confirm("¿Seguro que quieres comprar ' . $vipObject->getName() . ' por ' . $vipObject->getPrice() . ' IronCoins?");')) }}
					</div>

					{{ Form::close() }}
				</div>

				<div class="alert-bot"></div>
			</div>
		</li>
		@endforeach
	</ul>
</div>