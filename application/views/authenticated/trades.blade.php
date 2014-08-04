<a href="{{ URL::to_route("get_authenticated_trade_new") }}" class="ui-button button pull-right">
	<i class="button-icon check"></i>
	<span class="button-content">
		Crear comercio
	</span>
</a>

<h2>Comercios</h2>
<p>Vamos aventurero, te sobra el oro y a otros les sobran objetos, ¡un buen negocio jamas se deja pasar!.</p>

<ul class="inline text-center" style="margin-top: 40px; margin-bottom: 20px;">
	<li>
		<a href="{{ URL::to_route("get_authenticated_trade_index") }}" class="ui-button button">
			<i class="button-icon arrow"></i>
			<span class="button-content">
				Todo
			</span>
		</a>
	</li>

	<li>
		<a href="{{ URL::to_route("get_authenticated_trade_category", array("self")) }}" class="ui-button button">
			<i class="button-icon arrow"></i>
			<span class="button-content">
				Mis comercios
			</span>
		</a>
	</li>
	
	<li>
		<a href="{{ URL::to_route("get_authenticated_trade_category", array("weapon")) }}" class="ui-button button">
			<i class="button-icon axe"></i>
			<span class="button-content">
				Armas
			</span>
		</a>
	</li>
	
	<li>
		<a href="{{ URL::to_route("get_authenticated_trade_category", array("armor")) }}" class="ui-button button">
			<i class="button-icon boot"></i>
			<span class="button-content">
				Armaduras
			</span>
		</a>
	</li>
	
	<li>
		<a href="{{ URL::to_route("get_authenticated_trade_category", array("consumible")) }}" class="ui-button button">
			<i class="button-icon hearth"></i>
			<span class="button-content">
				Consumibles
			</span>
		</a>
	</li>
</ul>

<div class="row">
@if ( count($trades) > 0 )
<table class="table table-striped brown-table">
	<thead>
		<tr>
			<th style="width: 64px; text-align: center;">Objeto</th>
			<th style="width: 64px; text-align: center;">Cantidad</th>
			<th style="width: 200px;">Precio</th>
			<th>Vendedor</th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		@foreach ( $trades as $key => $trade )
		@if ( $trade->has_expired() && ! $trade->can_be_cancelled_by($character) )

		@else
		<tr>
			<td>
				<div class="box box-box-32-gold" style="margin: 0 auto;">
                    <img src="{{ $trade->item->get_image_path() }}" data-toggle="tooltip" data-original-title="{{ $trade->item->get_text_for_tooltip() }}" />
				</div>
			</td>
			<td style="text-align: center;">{{ $trade->amount }}</td>
			<td>{{ Item::get_divided_coins($trade->price_copper)['text'] }}</td>
			<td>{{ $trade->seller->get_link() }}</td>
			<td class="text-right">
				<ul class="inline" style="margin: 0; padding: 0;">
					@if ( $trade->has_expired() && $trade->can_be_cancelled_by($character) )
						<li>
							{{ Form::open(URL::to_route("post_authenticated_trade_cancel")) }}
							{{ Form::token() }}
							{{ Form::hidden('id', $trade->id) }}

							{{ Form::submit('Retirar', array('class' => 'ui-button ui-input-button', 'style' => 'font-size: 14px;')) }}
							{{ Form::close() }}
						</li>
					@else
						@if ( $trade->can_be_buyed_by($character) )
						<li>
							{{ Form::open(URL::to_route("post_authenticated_trade_buy")) }}
								{{ Form::token() }}
								{{ Form::hidden('id', $trade->id) }}

								{{ Form::submit('Comprar', array('class' => 'ui-button ui-input-button', 'style' => 'font-size: 14px;')) }}
							{{ Form::close() }}
						</li>
						@endif

						@if ( $trade->can_be_cancelled_by($character) )
						<li>
							{{ Form::open(URL::to_route("post_authenticated_trade_cancel")) }}
								{{ Form::token() }}
								{{ Form::hidden('id', $trade->id) }}

								{{ Form::submit('Cancelar', array('class' => 'ui-button ui-input-button', 'style' => 'font-size: 14px;')) }}
							{{ Form::close() }}
						</li>
						@endif
					@endif
				</ul>
			</td>
		</tr>
		@endif
		@endforeach
	</tbody>
</table>
@endif

@if ( count($trades) == 0 )
<h4 class="text-center" style="margin-top: 50px;">Sin comercios</h4>
@endif
</div>