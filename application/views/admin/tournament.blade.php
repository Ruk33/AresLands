{{ Form::open() }}
	
	{{ Form::hidden('id', $tournament->id) }}

	<div>
		<label for="name">Nombre</label>
		{{ Form::text('name', Input::old('name', $tournament->name)) }}
	</div>
	
	<div>
		<label for="clan_winner_id">Clan ganador</label>
		{{ Form::select('clan_winner_id', array(0 => 'nadie', 'grupos' => Clan::lists('name', 'id')), Input::old('clan_winner_id', $tournament->clan_winner_id)) }}
	</div>

	<div>
		<label for="starts_at">Fecha de inicio</label>
		{{ Form::number('starts_at', Input::old('starts_at', $tournament->starts_at)) }}
	</div>

	<div>
		<label for="ends_at">Fecha de finalizacion</label>
		{{ Form::number('ends_at', Input::old('ends_at', $tournament->ends_at)) }}
	</div>

	<div class="alert alert-info">
		Tiempo ahora: {{ time() }}
	</div>

	<div>
		<label for="all_clans">¿Todos los grupos?</label>
		{{ Form::select('all_clans', array(0 => 'No', 1 => 'Si'), Input::old('all_clans', $tournament->all_clans)) }}
	</div>

	<div>
		<label for="min_members">Miembros minimos</label>
		{{ Form::number('min_members', Input::old('min_members', $tournament->min_members)) }}
	</div>

	<div>
		<label for="mvp_id">Personaje MVP</label>
		{{ Form::select('mvp_id', array(0 => 'nadie', 'personajes' => Character::lists('name', 'id')), Input::old('mvp_id', $tournament->mvp_id)) }}
	</div>

	<div>
		<label for="mvp_received_reward">¿Recibio el MVP su recompensa?</label>
		{{ Form::select('mvp_received_reward', array(0 => 'No', 1 => 'Si'), Input::old('mvp_received_reward', $tournament->mvp_received_reward)) }}
	</div>

	<div>
		<label for="clan_leader_received_reward">¿Recibio el lider de clan la recompensa?</label>
		{{ Form::select('clan_leader_received_reward', array(0 => 'No', 1 => 'Si'), Input::old('clan_leader_received_reward', $tournament->clan_leader_received_reward)) }}
	</div>

	<div>
		<label for="battle_counter">Conteo de batallas</label>
		{{ Form::number('battle_counter', Input::old('battle_counter', $tournament->battle_counter)) }}
	</div>

	<div>
		<label for="life_potion_counter">Conteo de pociones de vida</label>
		{{ Form::number('life_potion_counter', Input::old('life_potion_counter', $tournament->life_potion_counter)) }}
	</div>

	<div>
		<label for="potion_counter">Contador de pociones</label>
		{{ Form::number('potion_counter', Input::old('potion_counter', $tournament->potion_counter)) }}
	</div>

	<div>
		<label for="allow_potions">¿Se permiten pociones?</label>
		{{ Form::select('allow_potions', array(0 => 'No', 1 => 'Si'), Input::old('allow_potions', $tournament->allow_potions)) }}
	</div>

	<div>
		<label for="coin_reward">Recompensa de monedas para todos</label>
		{{ Form::number('coin_reward', Input::old('coin_reward', $tournament->coin_reward)) }}
	</div>

	<div>
		<label for="mvp_coin_reward">Recompensa de monedas para jugador MVP</label>
		{{ Form::number('mvp_coin_reward', Input::old('mvp_coin_reward', $tournament->mvp_coin_reward)) }}
	</div>

	<div>
		<label for="item_reward">Objeto recompensa</label>
		{{ Form::select('item_reward', array('objetos' => Item::lists('name', 'id')), Input::old('item_reward', $tournament->item_reward)) }}
	</div>

	<div>
		<label for="item_reward_amount">Cantidad del objeto recompensa</label>
		{{ Form::number('item_reward_amount', Input::old('item_reward_amount', $tournament->item_reward_amount)) }}
	</div>
	
	<div>
		<label for="active">¿Esta activo el torneo?</label>
		{{ Form::select('active', array(0 => 'No', 1 => 'Si'), Input::old('active', $tournament->get_attribute('active'))) }}
	</div>
	
	<div>
		{{ Form::submit('Enviar') }}
	</div>

{{ Form::close() }}