<div class="row">
	<div class="span12">
		<ul class="breadcrumb">
			<li><a href="{{ URL::to('admin') }}">Panel de administracion</a> <span class="divider">/</span></li>
			<li class="active">NPCs</li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="span12">
		<h1>NPCs</h1>
		<a href="{{ URL::to_route('get_admin_npc_create') }}">Crear npc</a>
	</div>
</div>

<div class="row">
	<div class="span6">
		<h1>Mercaderes</h1>
		<ul class="media-list">
		@foreach ( $merchants as $merchant )
			<li class="media">
				<div class="clan-member-link" data-toggle="tooltip" data-original-title="{{ $merchant->get_text_for_tooltip() }}">
					<div class="pull-left span3" style="margin-top: -15px;">
						<img class="media-object" width="64px" height="64px" src="{{ URL::base() }}/img/icons/npcs/{{ $merchant->id }}.png" alt="">
					</div>
					<div class="media-body">
						<div class="pull-right">
							<a onclick="return confirm('¿Seguro que queres borrar el mercader {{ $merchant->name }}?');" href="{{ URL::to_route('get_admin_npc_delete', array($merchant->id)) }}" class="close">&times;</a>
						</div>
						<a href="{{ URL::to_route('get_admin_npc_edit', array($merchant->id)) }}">{{ $merchant->name }}</a><br>
						<b>Zona:</b> {{ $merchant->zone->name }}
					</div>
				</div>
			</li>
		@endforeach
		</ul>
	</div>

	<div class="span6">
		<h1>Monstruos</h1>
		<ul class="media-list">
			@foreach ( $monsters as $monster )
			<li class="media">
				<div class="clan-member-link" data-toggle="tooltip" data-original-title="{{ $monster->get_text_for_tooltip() }}">
					<div class="span2">
						<img class="media-object" src="{{ URL::base() }}/img/icons/npcs/{{ $monster->id }}.png" alt="">
					</div>
					<div class="media-body">
						<div class="pull-right">
							<a onclick="return confirm('¿Seguro que queres borrar el monstruo {{ $monster->name }}?');" href="{{ URL::to_route('get_admin_npc_delete', array($merchant->id)) }}" class="close">&times;</a>
						</div>
						<a href="{{ URL::to_route('get_admin_npc_edit', array($monster->id)) }}">{{ $monster->name }}</a> - Nivel {{ $monster->level }}<br>
						<b>Zona:</b> {{ $monster->zone->name }}
					</div>
				</div>
			</li>
			@endforeach
		</ul>
	</div>
</div>