<?php

class Chat_Controller extends Base_Controller
{
	public $restful = true;

	public function __construct()
	{
		parent::__construct();

		/*
		 *	Solo queremos logueados
		 */
		$this->filter('before', 'auth');
	}

	public function get_connected($channel)
	{
		if ( $channel != 0 && Character::get_character_of_logged_user(array('clan_id'))->clan_id != $channel )
		{
			return;
		}

		if ( $channel == 0 ) {
			$connected = DB::table('characters')
			->left_join('chat', 'characters.id', '=', 'chat.character_id')
			->where('characters.last_activity_time', '>', time() - 300)
			->or_where('chat.time', '>', time() - 300)
			->distinct()
			->get(array('characters.name'));
		} else {
			$connected = DB::table('characters')
			->left_join('chat', 'characters.id', '=', 'chat.character_id')
			->where('characters.clan_id', '=', $channel)
			->where('characters.last_activity_time', '>', time() - 300)
			->or_where('chat.time', '>', time() - 300)
			->distinct()
			->get(array('characters.name'));
		}
		
		return json_encode($connected);
	}

	public function get_messages($time, $channel)
	{
		if ( $channel != 0 && Character::get_character_of_logged_user(array('clan_id'))->clan_id != $channel )
		{
			return;
		}

		$messages = DB::table('chat')
		->left_join('characters', 'chat.character_id', '=', 'characters.id')
		->where('time', '>', $time)
		->where('channel', '=', $channel)
		->take(5)
		->order_by('time', 'asc')
		->get(array('chat.message', 'chat.time', 'characters.name'));

		return json_encode($messages);
	}

	public function post_message()
	{
		$json = Input::json();

		if ( ! (isset($json->message) && isset($json->channel)) )
		{
			return;
		}

		$data = array(
			'character_id' => Character::get_character_of_logged_user(array('id'))->id,
			'message' => $json->message,
			'channel' => $json->channel,
			'time' => time()
		);

		DB::table('chat')->insert($data);

		DB::table('chat')->where('time', '<', time() - 5 * 60)->delete();
	}
}