<h2>Nuevo comercio</h2>

<div class="span11">
	@if ( Session::has('successMessage') )
		<div class="alert alert-success">
			<p>{{ Session::get('successMessage') }}</p>
		</div>
	@else
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
		{{ Form::token() }}
	
		<div>
		{{ Form::label('name', 'Nombre del personaje') }}
		{{ Form::text('name', Input::old('name'), array('class' => 'input-block-level')) }}
		</div>
		
		Objeto y cantidad a comerciar
		<ul class="inline">
		@foreach ( $characterItems as $characterItem )
			<li style="display: table-cell; vertical-align: top; padding: 10px;" class="text-center">
				{{ Form::radio('item', $characterItem->id, false, array('id' => $characterItem->id)) }}
				<label for="{{ $characterItem->id }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $characterItem->item->get_text_for_tooltip() }}">
					<div class="inventory-item">
						<img src="{{ URL::base() }}/img/icons/items/{{ $characterItem->item_id }}.png" alt="" width="80px" height="80px">
					</div>
				</label>

				<?php
					$amount = array(1 => 1);

					if ( $characterItem->item->stackable )
					{
						for ( $i = 1, $max = $characterItem->count; $i <= $max; $i++ )
						{
							$amount[$i] = $i;
						}
					}
				?>

				<span data-toggle="tooltip" data-original-title="Cantidad">
					{{ Form::select('amount['.$characterItem->id.']', $amount, 1, array('style' => 'width: 64px;')) }}
				</span>
			</li>
		@endforeach
		</ul>

		<div>
			{{ Form::label('price', 'Precio (en cobre)') }}
			{{ Form::number('price', null, array('min' => 1, 'class' => 'input-block-level')) }}
		</div>

		<div class="text-center">
			<span class="ui-button button">
				<i class="button-icon arrow"></i>
				<span class="button-content">
					{{ Form::submit('Enviar oferta', array('class' => 'ui-button ui-input-button')) }}
				</span>
			</span>
		</div>
	{{ Form::close() }}
	@endif
</div>