<div class="span12">
	<h2 class="pull-left">
		@if ( $type == 'received' )
			Mensajes privados
		@elseif ( $type == 'attack' )
			Informes de ataque
		@elseif ( $type == 'defense' )
			Informes de defensa
		@endif
	</h2>
	
	<ul class="inline text-center pull-right">
		<li>
			<a href="{{ URL::to('authenticated/messages/received') }}" class="ui-button button">
				<i class="button-icon document"></i>
				<span class="button-content">
					Mensajes recibidos
				</span>
			</a>
		</li>
		<li>
			<a href="{{ URL::to('authenticated/messages/attack') }}" class="ui-button button">
				<i class="button-icon axe"></i>
				<span class="button-content">
					Ataques
				</span>
			</a>
		</li>
		<li>
			<a href="{{ URL::to('authenticated/messages/defense') }}" class="ui-button button">
				<i class="button-icon hearth"></i>
				<span class="button-content">
					Defensas
				</span>
			</a>
		</li>
	</ul>
</div>

<div class="clearfix"></div>

<div style="height: 75px;">
	<a href="{{ URL::to('authenticated/sendMessage') }}" class="ui-button button pull-left" style="margin-top: 20px;">
		<i class="button-icon document"></i>
		<span class="button-content">Enviar nuevo mensaje</span>
	</a>

	@if ( count($messages) > 0 )
	<div class="pull-right">
		{{ Form::open(URL::to('authenticated/clearAllMessages')) }}
			{{ Form::token() }}
			{{ Form::hidden('type', $type) }}
			<span class="ui-button button" style="margin-top: 20px;">
				<i class="button-icon cross"></i>
				<span class="button-content">
					{{ Form::submit('Borrar todos los mensajes', array('class' => 'ui-input-button ui-button')) }}
				</span>
			</span>
		{{ Form::close() }}
	</div>
	@endif
</div>

<div class="row">
	@if ( count($messages) > 0 )
	{{ Form::open(URL::to('authenticated/deleteMessage')) }}
		{{ Form::token() }}
		<table class="table table-striped brown-table">
			<thead>
				<tr class="text-left">
					<th class="span5" style="background-image: url('/img/messages-background.png'); background-repeat: no-repeat;">Asunto</th>
					<th class="span2">Enviado por</th>
					<th class="span3">Fecha de envío</th>
					<th class="span1" data-toggle="tooltip" data-original-title="Borra mensajes seleccionados">{{ Form::submit('Borrar', array('class' => 'btn btn-link', 'style' => 'color: orange; text-transform: uppercase; font-size: 11px; text-shadow: black 0 0 5px; font-weight: bold; padding: 0;')) }}</th>
				</tr>
			</thead>

			<tbody>
				@foreach ( $messages as $message )
				<tr>
					<td class="span5">
						<span data-toggle="tooltip" data-original-title="{{ Str::limit(strip_tags($message->content), 200) }}">
							@if ( $message->unread )
							*
							@endif
							<a href="{{ $message->get_link() }}">{{ $message->subject }}</a>
						</span>
					</td>
					<td class="span2">{{ $message->sender->get_link() }}</td>
					<td class="span3">{{ date("H:i:s d/m/Y", $message->date) }}</td>
					<td class="span1"><div class="text-center" style="margin-top: -5px;">{{ Form::checkbox('messages[]', $message->id) }}</div></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	{{ Form::close() }}
	@else
	<div class="text-center">
		<h4 style="margin-top: 100px;">No tienes mensajes</h4>
	</div>
	@endif
</div>