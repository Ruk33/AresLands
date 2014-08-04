@if ( Session::has("errors") )
<div class="clearfix row">
    <div class="alert alert-error no-border-radius span12">
        <h4>Oops!</h4>
        <ul>
            @foreach (Session::get("errors") as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

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

	{{ Form::open(URL::to_route("post_authenticated_trade_new")) }}
		{{ Form::token() }}
		
        <label>Objeto y cantidad a comerciar</label>
		<ul class="inline">
		@foreach ( $characterItems as $characterItem )
			<li style="padding: 10px;" class="text-center clan-member-link">
				{{ Form::radio('trade_item_id', $characterItem->id, false) }}
				<label for="{{ $characterItem->id }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ $characterItem->item->get_text_for_tooltip() }}">
					<div class="box box-box-64-blue">
						<img src="{{ $characterItem->item->get_image_path() }}" alt="" width="80px" height="80px">
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
		
		<div style="margin-top: 25px;">
			{{ Form::label('duration', 'Tiempo hasta que el comercio se cancele') }}
			{{ Form::select('duration', array(8 => '8 horas (5% de comisión)', 16 => '16 horas  (9% de comisión)', 24 => '24 horas  (14% de comisión)'), null, array('class' => 'input-block-level')) }}
		</div>

		<div style="margin-top: 25px;">
			{{ Form::label('copper', 'Precio') }}
			
			<span data-toggle="tooltip" data-original-title="Oro" style="position: relative;">
				<i class="coin coin-gold" style="position: absolute; top: -2px; left: 5px;"></i>
				{{ Form::number('gold', 0, array('class' => 'span8 text-right')) }}
			</span>
			
			<span data-toggle="tooltip" data-original-title="Plata" style="position: relative; margin-left: 10px;">
				<i class="coin coin-silver" style="position: absolute; top: -2px; left: 5px;"></i>
				{{ Form::number('silver', 0, array('max' => 99, 'class' => 'span2 text-right')) }}
			</span>
			
			<span data-toggle="tooltip" data-original-title="Cobre" style="position: relative; margin-left: 10px;">
				<i class="coin coin-copper" style="position: absolute; top: -2px; left: 5px;"></i>
				{{ Form::number('copper', 0, array('max' => 99, 'class' => 'span2 text-right')) }}
			</span>
		</div>

		@if ( $character->clan_id > 0 )
		<div style="margin-top: 25px;">
			{{ Form::checkbox('only_clan') }}
			{{ Form::label('only_clan', 'Comercio visible solo para los miembros de tu grupo', array('style' => 'display: inline; vertical-align: -15%;')) }}
		</div>
		@endif

		<div class="text-center" style="margin-top: 25px;">
			<span class="ui-button button">
				<i class="button-icon arrow"></i>
				<span class="button-content">
					{{ Form::submit('Crear comercio', array('class' => 'ui-button ui-input-button')) }}
				</span>
			</span>
		</div>
	{{ Form::close() }}
	@endif
</div>