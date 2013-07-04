<h2>Comerciar</h2>

<div class="span11">

	@if ( Session::has('successMessages') )
		<div class="alert alert-success">
			<ul>
			@foreach ( Session::get('successMessages') as $message )
				<li>{{ $message }}</li>
			@endforeach	
			</ul>
		</div>
	@endif

	@if ( Session::has('errorMessages') )
		<div class="alert alert-error">
			<strong>Oops!</strong>
			<ul>
			@foreach ( Session::get('errorMessages') as $message )
				<li>{{ $message }}</li>
			@endforeach	
			</ul>
		</div>
	@endif

	<a href="{{ URL::to('authenticated/newTrade') }}" class="btn btn-primary">Nuevo comercio</a>

	<h2>Comercios pendientes</h2>

	@if ( count($trades) > 0 )
		<table class="table table-hover">
			<thead>
				<tr>
					<th style="width: 40%;">Personaje</th>
					<th>Objeto</th>
					<th>Cantidad</th>
					<th>Precio</th>
					<th>Acciones</th>
				</tr>
			</thead>

			<tbody>
				@foreach ( $trades as $trade )
				@if ( $trade->status == 'pending' || $trade->seller_id == $character->id )
				<tr>
					<td>
						@if ( $trade->buyer->id == $character->id )
							Vendedor {{ $trade->seller->name }}
						@else
							Comprador {{ $trade->buyer->name }}
						@endif
					</td>
					<td>
						<div class="inventory-item">
							<img src="{{ URL::base() }}/img/icons/inventory/items/{{ $trade->item_id }}.png" alt="" data-toggle="tooltip" data-placement="top" data-original-title="{{ $trade->item->get_text_for_tooltip() }}">
						</div>
					</td>
					<td>{{ $trade->amount }}</td>
					<td>
						<img src="{{ URL::base() }}/img/copper.gif" alt="">
						{{ $trade->price_copper }}
					</td>
					<td>
						@if ( $character->id == $trade->buyer_id )
							<a href="{{ URL::to('authenticated/acceptTrade/' . $trade->id) }}" class="btn btn-success">Aceptar</a>
						@endif

						<a href="{{ URL::to('authenticated/cancelTrade/' . $trade->id) }}" class="btn btn-danger">Cancelar</a>
					</td>
				</tr>
				@endif
				@endforeach
			</tbody>
		</table>
	@else
		<p>No tienes comercios pendientes</p>
	@endif
</div>