<?php

class Message extends Base_Model
{
	public static $softDelete = false;
	public static $timestamps = false;
	public static $table = 'messages';
	public static $key = 'id';

	protected $rules = array(
		//'receiver_id' => 'exists:characters,id',
		'subject' => 'required|between:3,100',
		'content' => 'required'
	);

	protected $messages = array(
		//'receiver_id_exists' => 'El destinatario no existe',

		'subject_required' => 'El asunto del mensaje es requerido',
		'subject_between' => 'El asunto del mensaje debe tener entre 3 y 100 carácteres',

		'content_required' => 'El contenido del mensaje es requerido',
	);
	
	/**
	 * Obtenemos el link para leer el mensaje
	 * @return String
	 */
	public function get_link()
	{
		return URL::to('authenticated/readMessage/' . $this->id);
	}

	public function sender()
	{
		return $this->belongs_to('Character', 'sender_id');
	}
	
	public static function group_tournament(Character $character, $rankPosition, $reward)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Felicidades por la posición de tu grupo en el ranking';
		$message->content = 'Felicitaciones, tu grupo se ha hecho con el puesto número ' . $rankPosition . ' en el ranking. Se te ha recompensado con ' . $reward . '.';
		$message->unread = true;
		$message->date = time();
		$message->type = 'received';
		$message->is_special = true;

		$message->save();
	}

	public static function tournament_disquialify(Character $character)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Tu grupo ha sido descalificado del torneo';
		$message->content = 'Tu grupo ha sido descalificado del torneo por tener demasiadas derrotas.';
		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function tournament_mvp_reward(Character $character, $rewards)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Recompensa por MVP en torneo';
		
		$message->content = '¡Felicitaciones por ser el MVP del torneo!. Se te ha recompensado con:';
		$message->content .= '<ul>';

		foreach ( $rewards as $reward )
		{
			if ( $reward['name'] == 'Monedas' )
			{
				$coins = Item::get_divided_coins((int) $reward['amount']);
				$reward['amount'] = $coins['text'];
			}
			$message->content .= '<li>' . $reward['amount'] . ' ' . $reward['name'] . '</li>';
		}

		$message->content .= '</ul>';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->is_special = true;

		$message->save();
	}

	public static function tournament_clan_lider_reward(Character $character, Item $reward, $amount)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Recompensa por ganar el torneo';
		$message->content = '¡Felicitaciones lider!. Su grupo ha demostrado ser el mejor en este torneo, y por ello, se le ha recompensado con: ' . $amount . ' ' . $reward->name;

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->is_special = true;

		$message->save();
	}

	public static function tournament_coin_reward(Character $character, $amount)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Recompensa por tu desempeño en el torneo';

		$coins = Item::get_divided_coins($amount);
		$message->content = 'Has batallado valiente y ferozmente en el campo de batalla. Estas ' . $coins['text'] . ' monedas son para ti.';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->is_special = true;

		$message->save();
	}

	public static function activity_bar_reward(Character $character, $rewards)
	{
		$message = new Message();

		$message->sender_id = $character->id;
		$message->receiver_id = $character->id;

		$message->subject = 'Completaste tu barra de actividad';
		$message->content = 'Por completar tu barra de actividad, haz sido recompensado con: <ul>';

		foreach ( $rewards as $reward )
		{
			if ( $reward['name'] == 'Monedas' )
			{
				$coins = Item::get_divided_coins((int) $reward['amount']);
				$reward['amount'] = $coins['text'];
			}
			$message->content .= '<li>' . $reward['amount'] . ' ' . $reward['name'] . '</li>';
		}

		$message->content .= '</ul>';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->is_special = true;

		$message->save();
	}

	public static function completed_exploration($receiver, $experienceGained, $reward)
	{
		$message = new Message();

		$message->sender_id = $receiver->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Completaste tu exploración';

		$coins = Item::get_divided_coins((int) $reward);
		$coinsText = $coins['text'];

		$message->content = 'Haz terminado de explorar. Obtuviste ' . number_format($experienceGained, 0) . ' de experiencia y ' . $coinsText . ' de cobre.';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->is_special = true;

		$message->save();
	}

	public static function exploration_finished($receiver, $monster, $battleMessage, $winner)
	{
		$message = new Message();

		$message->sender_id = $receiver->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Terminaste la exploración';

		if ( $winner )
		{
			$message->content = '<p>Terminaste de explorar, luchaste contra ' . $monster->name . ' y el ganador fue ' . $winner->name . '</p><p>Dessarrollo de la pelea:</p><p>' . $battleMessage . '</p>';
		}
		else
		{
			$message->content = '<p>Terminaste de explorar, luchaste contra ' . $monster->name . '. El resultado ha sido un empate!.</p><p>Dessarrollo de la pelea:</p><p>' . $battleMessage . '</p>';	
		}

		$message->unread = true;
		$message->date = time();

		$message->is_special = true;
		$message->type = 'defense';

		$message->save();
	}

	/**
	 *	@param <Character> $sender
	 *	@param <Character> $receiver
	 */
	public static function clan_expulsion_message($sender, $receiver)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Haz sido expulsado del grupo';
		$message->content = 'Se te ha expulsado del grupo';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_accept_message($sender, $receiver, $clan)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Tu solicitud ha sido aceptada';
		$message->content = 'Tu solicitud para ingresar al grupo ' . $clan->name . ' ha sido aceptada';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_reject_message($sender, $receiver, $clan)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Tu solicitud ha sido rechazada';
		$message->content = 'Tu solicitud para ingresar al grupo ' . $clan->name . ' ha sido rechazada';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	public static function clan_new_petition($sender, $receiver)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		$message->subject = 'Nueva petición';
		$message->content = 'El personaje ' . $sender->name . ' ha solicitado inclusión en tu grupo';

		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}
	
	/**
	 * Notificamos al vendedor que alguien compro
	 * @param Trade $trade
	 * @param Character $buyer
	 */
	public static function trade_buy(Trade $trade, Character $buyer)
	{
		$message = new Message();

		$message->sender_id = $buyer->id;
		$message->receiver_id = $trade->seller_id;
		
		$message->subject = 'He aceptado tu oferta en los comercios';
		$message->content = View::make('messages.tradebuy')->with('trade', $trade)->with('buyer', $buyer)->render();

		$message->is_special = true;
		$message->unread = true;
		$message->date = time();
		$message->type = 'received';

		$message->save();
	}

	/**
	 * @param Character $sender
	 * @param Character $receiver
	 * @param Battle    $battle
	 * @return Message
	 */
	public static function battle_report(Character $sender, Character $receiver, Battle $battle)
	{
		$message = new Message();

		$message->sender_id = $sender->id;
		$message->receiver_id = $receiver->id;

		if ( $battle->get_attacker()->id == $sender->id )
		{
			$message->subject = 'Has atacado a ' . $battle->get_target()->name;
		}
		else
		{
			$message->subject = $battle->get_attacker()->name . ' te ha atacado';
		}

		if ( $battle->get_target() instanceof Character )
		{
			$message->content = View::make('messages.battlepvp')->with('battle', $battle)->render();
			$message->type = 'attack';
		}
		else
		{
			$message->content = View::make('messages.battlepve')->with('battle', $battle)->render();
			$message->type = 'defense';
		}

		$message->is_special = true;
		$message->unread = true;
		$message->date = time();

		$message->save();

		return $message;
	}
}