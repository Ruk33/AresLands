<?php

/**
 * 	Esta clase contiene la lógica de las batallas, incluyendo:
 * 
 * 		- Eventos (cuando se ataca, etc.)
 * 		- Recompensas
 * 		- Notificaciones por mensaje privado
 * 		- Robo de orbes
 * 		- Proteccion contra abusos
 * 		- Mensaje de batalla
 * 
 *	@author Franco Montenegro
 */
 
class Battle
{
	/**
	 *	Informacion del luchador uno
	 * 
	 * 	@var Array
	 */
	private $fighter_one = array();
	
	/**
	 *	Informacion del luchador dos
	 * 
	 * 	@var Array
	 */
	private $fighter_two = array();
	
	/**
	 * 	Entidad que a la que le toca atacar
	 * 
	 * 	@var Character/Npc
	 */
	private $attacker = null;
	
	/**
	 *	Entidad a la que le toca defender
	 * 
	 * 	@var Character/Npc
	 */
	private $defender = null;
	
	/**
	 *	Ganador de la batalla
	 * 
	 * 	@var Character/Npc
	 */
	private $winner = null;
	
	/**
	 *	Perdedor de la batalla
	 * 
	 * 	@var Character/Npc
	 */
	private $loser = null;
	
	/**
	 *	Mensajes de batalla
	 * 
	 * 	@var String
	 */
	private static $messages = array(
		'%1$s logra asestar un gran golpe a %2$s. %2$s no puede evitar soltar un pequeño alarido',
		'%1$s golpea con majestuocidad a %2$s. %2$s se queja',
		'%1$s lanza un feroz ataque a %2$s que sufre algunas heridas',
		'%1$s ataca salvajemente a %2$s que sufre dolorosas heridas',
		'%1$s se mueve ágil y velozmente hacia %2$s para propinarle un gran golpe',
		'%2$s se ve algo cansado, intenta esquivar pero el ataque %1$s lo alcanza',
		'%1$s se dirige a toda marcha hacia %2$s logrando un gran golpe',
		'%2$s da un paso en falso y %1$s aprovecha la oportunidad para golpear',
		'%1$s se avalanza sobre %2$s para propinarle un zarpazo',
		'%2$s lanza un ataque a %1$s, ¡pero éste lo esquiva y lo devuelve!',
		'%2$s intenta defenderse, pero %1$s se las arregla para herirlo',
		'%1$s lanza un sanguinareo ataque contra %2$s el cual da en el blanco',
		'%2$s se distrae y no se percata del ataque de %1$s',
		'%1$s titubea un poco, pero finalmente logra acertar un golpe a %2$s',
		'%1$s con mucha suerte, logra golpear eficazmente a %2$s',
		'%2$s se avalanza contra %1$s, pero %1$s lo sorprende con un inesperado golpe',
		'%1$s hace uso de su agilidad para llegar rápidamente a %2$s y propinar un gran golpe',
	);
	
	/**
	 *	Registro de la batalla
	 * 
	 * 	@var BattleLog
	 */
	public $log = null;
	
	/**
	 *	Obtenemos el ganador
	 * 
	 * 	@return Character/Npc
	 */
	public function get_winner()
	{
		return $this->winner;
	}
	
	/**
	 *	Obtenemos al perdedor
	 *
	 * 	@return Character/Npc
	 */
	public function get_loser()
	{
		return $this->loser;
	}
	
	private static function on_excellent_attack_warrior($attacker, $defender, &$damage){}
	private static function on_excellent_attack_mage($attacker, $defender, &$damage){}
	private static function on_poor_attack($attacker, $defender, &$damage){}
	private static function on_normal_attack($attacker, $defender, &$damage){}
	
	private static function on_excellent_defense($attacker, $defender, &$defense){}
	private static function on_poor_defense($attacker, $defender, &$defense){}
	private static function on_normal_defense($attacker, $defender, &$defense){}
	
	private static function before_hit($attacker, $defender, &$damage){}
	private static function after_hit($attacker, $defender, $damage){}
	
	/**
	 *	Obtenemos un mensaje de batalla aleatorio
	 * 
	 * 	@return String
	 */
	private static function get_random_message()
	{
		return self::$messages[mt_rand(0, count(self::$messages) - 1)];
	}
	
	/**
	 *	Agregamos un mensaje al log
	 * 
	 * 	@param String 	$message 	Mensaje a agregar
	 * 	@param Bool 	$onTop 		true si queremos el mensaje arriba del todo
	 */
	private function add_message_to_log($message, $onTop = false)
	{
		if ( $onTop )
		{
			$this->log->message = '<li>' . $message . '</li>' . $this->log->message;
		}
		else
		{
			$this->log->message .= '<li>' . $message . '</li>';
		}
	}
	
	/**
	 *	Agrega salto de linea al log
	 * 
	 * 	@param Bool $onTop
	 */
	private function add_blank_space($onTop = true)
	{
		$this->add_message_to_log('&nbsp;', $onTop);
	}
	
	private function give_experience()
	{
		$winnerExperience = 0;
		$loserExperience = 0;
		
		if ( $this->winner instanceof Character )
		{
			$this->add_blank_space();
			
			if ( $this->loser instanceof Npc )
			{
				$winnerExperience = $this->loser->xp * Config::get('game.xp_rate');
				
				// Actualizamos db
				$this->winner->xp += $winnerExperience;
				$this->winner->points_to_change += $winnerExperience;
			}
			else
			{
				// Experiencia del ganador
				if ( $this->winner->level <= $this->loser->level )
				{
					$winnerExperience = (3 + ($this->loser->level - $this->winner->level)) * Config::get('game.xp_rate');
				}
				
				// Experiencia del perdedor
				$loserExperience = 1 * Config::get('game.xp_rate');
				
				// Actualizamos en db
				$this->winner->xp += $winnerExperience;
				$this->winner->points_to_change += $winnerExperience;
				
				$this->loser->xp += $loserExperience;
				$this->loser->points_to_change += $loserExperience;
				
				$this->add_message_to_log($this->loser->name . ' recibe ' . $loserExperience . ' punto(s) de experiencia.', true);
			}
			
			$this->add_message_to_log($this->winner->name . ' recibe ' . $winnerExperience . ' punto(s) de experiencia.', true);
		}
	}
	
	private function give_rewards()
	{
		// Le damos 2 puntos a la barra de actividad
		// del atacante
		ActivityBar::add($this->fighter_one['character'], 2);
		
		if ( $this->winner instanceof Character )
		{
			$coins = (50 * $this->loser->level) * Config::get('game.coins_rate');
			$percentage = 0;
			
			if ( $this->loser instanceof Character )
			{
				// Puntos de PVP
				$this->winner->pvp_points += 1;
				
				// Monedas
				$loserCoins = $this->loser->get_coins();
				
				if ( $loserCoins && $loserCoins->count > 0 )
				{
					if ( $this->loser->level > $this->winner->level )
					{
						// Si el perdedor tiene mas nivel que el ganador
						// entonces cada nivel de diferencia lo sumamos al porcentaje
						$percentage = 0.25 + ($this->loser->level - $this->winner->level) * 0.01;
					}
					else
					{
						// Si el ganador tiene mas nivel que el perdedor
						// entonces cada nivel de diferencia lo restamos al porcentaje
						$percentage = 0.25 - ($this->winner->level - $this->loser->level) * 0.01;
					}
					
					$percentage = round(abs($percentage));
					
					if ( $percentage > 0 )
					{
						$coins += $loserCoins->count * $percentage;
			
						$loserCoins->count -= $loserCoins->count * $percentage;
			
						if ( $loserCoins->count > 0 )
						{
							$loserCoins->save();
						}
						else
						{
							$loserCoins->delete();
						}
					}
				}
			}
			
			$this->add_blank_space();
			$this->add_message_to_log($this->winner->name . ' recibe ' . $coins . ' moneda(s) (' . $percentage . '% robado del enemigo).', true);
			
			$this->winner->add_coins($coins);
		}
	}

	private function check_for_orbs()
	{
		if ( $this->winner instanceof Character && $this->loser instanceof Character )
		{
            $winnerOrbs = $this->winner->orbs;

            // Primero verificamos si el perdedor
            // intentó robar alguno de los orbes
            // del ganador
            if ( $this->winner->has_orb() )
            {
                foreach ( $winnerOrbs as $winnerOrb )
                {
                    if ( $winnerOrb->can_be_stolen_by($this->loser) )
                    {
                        $winnerOrb->failed_robbery($this->loser);

                        // Evitamos que el tiempo se duplique
                        // en caso de que tenga dos orbes
                        break;
                    }
                }
            }
	
			// Verificamos si el perdedor tiene orbes
			// y si éstos pueden ser robados por el ganador
			if ( $this->loser->has_orb() && count($winnerOrbs) < 2 )
			{
				$loserOrbs = $this->loser->orbs;
				$stolenOrb = null;

				foreach ( $loserOrbs as $loserOrb )
				{
					if ( $loserOrb->can_be_stolen_by($this->winner) )
					{
						$loserOrb->give_to($this->winner);
						$stolenOrb = $loserOrb;
						
						break;
					}
				}
				
				// Si se robó un orbe, entonces
				// lo notificamos
				if ( $stolenOrb )
				{
					$this->add_blank_space();
					$this->add_message_to_log('<p>¡' . $this->winner->name . ' ha robado ' . $stolenOrb->name . ' de ' . $this->loser->name . '!</p>', true);
				}
			}
		}
	}
	
	private function check_for_protection()
	{
		if ( $this->winner instanceof Character && $this->loser instanceof Character )
		{
			if ( $this->winner->level > $this->loser->level )
			{
				AttackProtection::add($this->winner, $this->loser, Config::get('game.protection_time_on_lower_level_pvp'));
			}
		}
	}
	
	private function send_notification_message()
	{		
		if ( $this->fighter_two['character'] instanceof Character )
		{
			Message::attack_report($this->fighter_one['character'], $this->fighter_two['character'], $this->log->message, $this->winner);
			Message::defense_report($this->fighter_two['character'], $this->fighter_one['character'], $this->log->message, $this->winner);
		}
	}
	
	private function init_battle()
	{
		$damage = 0;
		$defense = 0;
		$realDamage = 0;
		
		// Mientras tengan vida
		while ( $this->fighter_one['current_life'] > 0 && $this->fighter_two['current_life'] > 0 )
		{
			// Golpea el que menos CD tenga
			if ( $this->fighter_one['current_cd'] <= $this->fighter_two['current_cd'] )
			{
				$this->attacker = &$this->fighter_one;
				$this->defender = &$this->fighter_two;
			}
			else
			{
				$this->attacker = &$this->fighter_two;
				$this->defender = &$this->fighter_one;
			}
	
			// Actualizamos CD
			$this->attacker['current_cd'] += $this->attacker['cd'];
			
			// Calculamos el daño
			$this->attacker['average_damage'] = mt_rand($this->attacker['min_damage'], $this->attacker['max_damage']);

			// 35% de crítico físico
			if ( $this->attacker['is_warrior'] && mt_rand(0, 100) <= 35 )
			{
				$damage = $this->attacker['average_damage'] * 1.50;
				self::on_excellent_attack_warrior($this->attacker, $this->defender, $damage);
			}
			// 25% de crítico mágico
			elseif ( ! $this->attacker['is_warrior'] && mt_rand(0, 100) <= 25 )
			{
				$damage = $this->attacker['average_damage'] * 2.50;
				self::on_excellent_attack_mage($this->attacker, $this->defender, $damage);
			}
			// 10% de golpe fallido
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$damage = $this->attacker['average_damage'] * 0.75;
				self::on_poor_attack($this->attacker, $this->defender, $damage);
			}
			else
			{
				$damage = $this->attacker['average_damage'];
				self::on_normal_attack($this->attacker, $this->defender, $damage);
			}
			
			// Calculamos la defensa
			$this->defender['average_defense'] = mt_rand($this->defender['min_defense'], $this->defender['max_defense']);
			
			// 30% de defensa exitosa
			if ( mt_rand(0, 100) <= 30 )
			{
				$defense = $this->defender['average_defense'] * 1.75;
				self::on_excellent_defense($this->attacker, $this->defender, $defense);
			}
			// 10% de defensa fallida
			elseif ( mt_rand(0, 100) <= 10 )
			{
				$defense = $this->defender['average_defense'] * 0.75;
				self::on_poor_defense($this->attacker, $this->defender, $defense);
			}
			else
			{
				$defense = $this->defender['average_defense'];
				self::on_normal_defense($this->attacker, $this->defender, $defense);
			}
			
			// Calculamos el daño verdadero
			$realDamage = min($damage - $defense * 0.4, $this->defender['current_life']);

			// Evitamos que un daño negativo
			// cure al oponente
			if ( $realDamage < 0 )
			{
				$realDamage = 0;
			}
			
			// HIT!
			self::before_hit($this->attacker, $this->defender, $realDamage);
			
			$this->defender['current_life'] -= $realDamage;
			$this->attacker['damage_done'] += $realDamage;
			
			self::after_hit($this->attacker, $this->defender, $realDamage);
			
			$this->add_message_to_log(
				sprintf(
					self::get_random_message(), 
					$this->attacker['character']->name, 
					$this->defender['character']->name
				) . ' (daño: ' . number_format($realDamage, 2) . ', defendido: ' . number_format($damage - $realDamage, 2) . ')'
			);
		}

		// Verificamos si no fue un empate
		if ( $this->fighter_one['current_life'] <= 0 && $this->fighter_two['current_life'] <= 0 )
		{
			$this->winner = null;
			$this->loser = null;
		}
		else
		{
			if ( $this->fighter_one['current_life'] > 0 )
			{
				$this->winner = &$this->fighter_one['character'];
				$this->loser = &$this->fighter_two['character'];
			}
			else
			{
				$this->winner = &$this->fighter_two['character'];
				$this->loser = &$this->fighter_one['character'];
			}
		}
		
		$this->give_experience();
		$this->give_rewards();
		$this->check_for_orbs();
		$this->check_for_protection();
		$this->send_notification_message();
		
		// Agregamos al registro la vida inicial de ambos
		$this->add_blank_space();
		$this->add_message_to_log('Vida inicial de ' . $this->fighter_one['character']->name . ': ' . $this->fighter_one['initial_life'], true);
		$this->add_message_to_log('Vida inicial de ' . $this->fighter_two['character']->name . ': ' . $this->fighter_two['initial_life'], true);
		
		// Agregamos al registro el daño realizado por ambos
		$this->add_blank_space();
		$this->add_message_to_log('Daño realizado por ' . $this->fighter_one['character']->name . ': ' . number_format($this->fighter_one['damage_done'], 2), true);
		$this->add_message_to_log('Daño realizado por ' . $this->fighter_two['character']->name . ': ' . number_format($this->fighter_two['damage_done'], 2), true);
		
		// Actualizamos las vidas
		$this->fighter_one['character']->current_life = $this->fighter_one['current_life'];
		$this->fighter_one['character']->save();
		
		if ( $this->fighter_two['is_player'] )
		{
			$this->fighter_two['character']->current_life = $this->fighter_two['current_life'];
			$this->fighter_two['character']->save();
		}
		
		// Disparamos el evento de batalla
		if ( $this->fighter_two['is_player'] )
		{
			Event::fire('battle', array($this->fighter_one['character'], $this->fighter_two['character']));
		}
		else
		{
			Event::fire('pveBattle', array($this->fighter_one['character'], $this->fighter_two['character'], $this->winner));
		}
	}
	
	/**
	 *	Obtenemos algunos datos (como atributos)
	 * 	y los guardamos
	 * 
	 *	@param Character/Npc $fighter
	 */
	private function set_variables(&$fighter)
	{
		if ( ! isset($fighter['character']) )
		{
			return;
		}
		
		if ( ! ($fighter['character'] instanceof Character || $fighter['character'] instanceof Npc) )
		{
			return;
		}
		
		$fighter['is_player'] = $fighter['character'] instanceof Character;
		$fighter['stats'] = $fighter['character']->get_stats();
		$fighter['is_warrior'] = $fighter['stats']['stat_strength'] > $fighter['stats']['stat_magic'];
		
		if ( $fighter['is_player'] )
		{
			$fighter['current_life'] = $fighter['character']->current_life;
		}
		else
		{
			$fighter['current_life'] = $fighter['character']->life;
		}
		
		if ( $fighter['is_warrior'] )
		{
			if ( $fighter['stats']['stat_dexterity'] > 0 )
			{
				$fighter['cd'] = 1000 / $fighter['stats']['stat_dexterity'];
			}
			else
			{
				$fighter['cd'] = 1000;
			}
		}
		else
		{
			if ( $fighter['stats']['stat_magic_skill'] > 0 )
			{
				$fighter['cd'] = 1000 / $fighter['stats']['stat_magic_skill'];
			}
			else
			{
				$fighter['cd'] = 1000;
			}
		}
		
		$fighter['current_cd'] = $fighter['cd'];
		
		
		// Daños
		// --------------------------------
		//    VER ESTA PARTE, ¡RE-HACER!
		// --------------------------------
		$fighter['min_damage'] = ( $fighter['is_warrior'] ) ? $fighter['stats']['stat_strength'] : $fighter['stats']['stat_magic'];
		$fighter['max_damage'] = $fighter['min_damage'];
		
		$fighter['min_damage'] *= 0.25;
		$fighter['max_damage'] *= 0.75;
		
		// Defensas
		// --------------------------------
		//    VER ESTA PARTE, ¡RE-HACER!
		// --------------------------------
		$fighter['min_defense'] = ( $fighter['is_warrior'] ) ? $fighter['stats']['stat_resistance'] : $fighter['stats']['stat_magic_resistance'];
		$fighter['max_defense'] = $fighter['min_defense'];
		
		$fighter['min_defense'] *= 0.75;
		$fighter['max_defense'] *= 1.25;
		
		if ( $fighter['min_defense'] > $fighter['max_defense'] )
		{
			$fighter['max_defense'] = $fighter['min_defense'];
		}
		
		// Log
		$fighter['initial_life'] = $fighter['current_life'];
		$fighter['damage_done'] = 0;
	}
	
	/**
	 *	@param Character $fighter_one 		Atacante
	 *	@param Character/Npc $fighter_two 	Objetivo
	 */
	public function __construct(Character $fighter_one, $fighter_two)
	{		
		if ( ! ($fighter_two instanceof Character || $fighter_two instanceof Npc) )
		{
			return;
		}
		
		$this->log = new BattleLog();
		
		$this->fighter_one['character'] = $fighter_one;
		$this->fighter_two['character'] = $fighter_two;
		
		self::set_variables($this->fighter_one);
		self::set_variables($this->fighter_two);
		
		self::init_battle();
		
		$this->log->message = '<ul class="unstyled">' . $this->log->message . '</ul>';
		$this->log->save();
		
		$fighter_one->after_battle();
	}
}