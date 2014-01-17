<?php

use \Libraries\Damage;

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
	 * Unidad que ataca
	 * @var Eloquent
	 */
	private $_attackerUnit;
	
	/**
	 * Unidad que es atacada
	 * @var Eloquent
	 */
	private $_attackedUnit;
	
	/**
	 * Unidad que es pareja de $_attacker
	 * @var Eloquent
	 */
	private $_pair;
	
	/**
	 *	Ganador de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $_winner;
	
	/**
	 *	Perdedor de la batalla
	 * 
	 * 	@var Eloquent
	 */
	private $_loser;
	
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
		return $this->_winner;
	}
	
	/**
	 *	Obtenemos al perdedor
	 *
	 * 	@return Character/Npc
	 */
	public function get_loser()
	{
		return $this->_loser;
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
		
		if ( $this->_winner instanceof Character )
		{
			$this->add_blank_space();
			
			if ( $this->_loser instanceof Npc )
			{
				$winnerExperience = (int) ($this->_loser->xp + max($this->_winner->level, 5) / 5 * Config::get('game.xp_rate'));
				
				// Actualizamos db
				$this->_winner->xp += $winnerExperience;
				$this->_winner->points_to_change += $winnerExperience;
			}
			else
			{
				// Experiencia del ganador
				if ( $this->_winner->level <= $this->_loser->level )
				{
					$winnerExperience = (3 + ($this->_loser->level - $this->_winner->level)) * Config::get('game.xp_rate');
				}
				
				// Experiencia del perdedor
				$loserExperience = 1 * Config::get('game.xp_rate');
				
				// Actualizamos en db
				$this->_winner->xp += $winnerExperience;
				$this->_winner->points_to_change += $winnerExperience;
				
				$this->_loser->xp += $loserExperience;
				$this->_loser->points_to_change += $loserExperience;
				
				$this->add_message_to_log($this->_loser->name . ' recibe ' . $loserExperience . ' punto(s) de experiencia.', true);
			}
			
			$this->add_message_to_log($this->_winner->name . ' recibe ' . $winnerExperience . ' punto(s) de experiencia.', true);
		}
	}
	
	private function give_rewards()
	{
		// Le damos 2 puntos a la barra de actividad
		// del atacante
		ActivityBar::add($this->_attacker, 2);
		
		if ( $this->_winner instanceof Character )
		{
			$coins = (50 * $this->_loser->level) * Config::get('game.coins_rate');
			$percentage = 0;
			
			if ( $this->_loser instanceof Character )
			{
				// Puntos de PVP
				$this->_winner->pvp_points += 1;
				
				// Monedas
				$loserCoins = $this->_loser->get_coins();
				
				if ( $loserCoins && $loserCoins->count > 0 )
				{
					if ( $this->_loser->level > $this->_winner->level )
					{
						// Si el perdedor tiene mas nivel que el ganador
						// entonces cada nivel de diferencia lo sumamos al porcentaje
						$percentage = 0.25 + ($this->_loser->level - $this->_winner->level) * 0.01;
					}
					else
					{
						// Si la diferencia es de mas de 10 niveles
						if ( $this->_winner->level - $this->_loser->level > 10 )
						{
							$percentage = 0;
							$coins /= 2;
						}
						else
						{
							// Si el ganador tiene mas nivel que el perdedor
							// entonces cada nivel de diferencia lo restamos al porcentaje
							$percentage = 0.25 - ($this->_winner->level - $this->_loser->level) * 0.01;
						}
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
			$this->add_message_to_log($this->_winner->name . ' recibe (' . $percentage . '% robado del enemigo): ' . Item::get_divided_coins($coins)['text'], true);
			
			$this->_winner->add_coins($coins);
		}
	}

	private function check_for_orbs()
	{
		if ( $this->_attacker instanceof Character && $this->_attacked instanceof Character )
		{
			$attackedOrbs = $this->_attacked->orbs;
			
            if ( $this->_attacker == $this->_winner )
            {
                // Verificamos si el perdedor tiene orbes
                // y si éstos pueden ser robados por el ganador
                if ( count($attackedOrbs) > 0 && $this->_winner->orbs()->count() < 2 )
                {
                    $stolenOrb = null;

                    foreach ( $attackedOrbs as $attackedOrb )
                    {
                        if ( $attackedOrb->can_be_stolen_by($this->_winner) )
                        {							
                            $attackedOrb->give_to($this->_winner);
                            $stolenOrb = $attackedOrb;

                            break;
                        }
                    }
                    
                    // Si se robó un orbe, entonces
                    // lo notificamos
                    if ( $stolenOrb )
                    {
                        $this->add_blank_space();
                        $this->add_message_to_log('<p>¡' . $this->_winner->name . ' ha robado ' . $stolenOrb->name . ' de ' . $this->_loser->name . '!</p>', true);
                    }
                }
            }
			else
			{
				foreach ( $attackedOrbs as $attackedOrb )
				{
					if ( $attackedOrb->can_be_stolen_by($this->_attacker) )
					{
						$attackedOrb->failed_robbery($this->_attacker);
						
						// Solo 1 proteccion
						break;
					}
				}
			}
		}
	}
	
	private function check_for_protection()
	{
        if ( $this->_attacker instanceof Character && $this->_attacked instanceof Character )
        {
            if ( $this->_attacker->level > $this->_attacked->level )
            {
                AttackProtection::add($this->_attacker, $this->_attacked, Config::get('game.protection_time_on_lower_level_pvp'));
            }
        }
	}
	
	private function send_notification_message()
	{		
		if ( $this->_attacked instanceof Character )
		{
			if ( $this->_pair )
			{
				Message::attack_report($this->_pair, $this->_pair, $this->log->message, $this->_winner);
			}
			Message::attack_report($this->_attacker, $this->_attacked, $this->log->message, $this->_winner);
			Message::defense_report($this->_attacked, $this->_attacker, $this->log->message, $this->_winner);
		}
	}
		
	/**
	 * 
	 * @param Eloquent $unit
	 * @return array
	 */
	private static function get_unit_info(Eloquent $unit)
	{
		$info = array();
		
		$info['name'] = $unit->name;
		$info['is_player'] = $unit instanceof Character;
		$info['stats'] = $unit->get_stats();
		$info['is_warrior'] = $info['stats']['stat_strength'] > $info['stats']['stat_magic'];
		$info['max_life'] = ( $info['is_player'] ) ? $unit->current_life : $unit->life;
		$info['current_life'] = $info['max_life'];
		
		if ( $info['is_warrior'] )
		{
			$info['cd'] = 1000 / ($info['stats']['stat_dexterity'] + 1);
		}
		else
		{
			$info['cd'] = 1000 / ($info['stats']['stat_magic_skill'] + 1);
		}
		
		$info['current_cd'] = $info['cd'];
		
		// Log
		$info['initial_life'] = $info['current_life'];
		$info['damage_done'] = 0;
		
		return $info;
	}
	
	/**
	 * Obtenemos los stats de una pareja
	 * @param Eloquent $unit
	 * @param Eloquent $pair
	 * @return array
	 */
	private static function get_pair_info(Eloquent $unit, Eloquent $pair)
	{
		$unitStats = self::get_unit_info($unit);
		$pairStats = self::get_unit_info($pair);
		
		$info = array();
		
		$info['name'] = "El equipo de {$unit->name} y {$pair->name}";
		$info['is_player'] = $unitStats['is_player'];
		
		foreach ( $unitStats['stats'] as $stat => $amount )
		{
			$info['stats'][$stat] = $amount + $pairStats['stats'][$stat] * 0.7;
		}
		
		$info['is_warrior'] = $unitStats['is_warrior'];
		$info['current_life'] = $unitStats['current_life'] + $pairStats['current_life'];
		
		$info['cd'] = $unitStats['cd'] + $pairStats['cd'];
		$info['current_cd'] = $info['cd'];
		
		// Log
		$info['initial_life'] = $info['current_life'];
		$info['damage_done'] = 0;
		
		return $info;
	}
	
	private function to_battle()
	{
		$start = microtime(true);
		
		if ( is_null($this->_pair) )
		{
			$attackerStats = self::get_unit_info($this->_attacker);
		}
		else
		{
			$attackerStats = self::get_pair_info($this->_attacker, $this->_pair);
		}
		
		if ( $attackerStats['is_player'] )
		{
			$attackerStats = new Character($attackerStats);
		}
		else
		{
			$attackerStats = new Npc($attackerStats);
		}
		
		$attackedStats = self::get_unit_info($this->_attacked);
		
		if ( $attackedStats['is_player'] )
		{
			$attackedStats = new Character($attackedStats);
		}
		else
		{
			$attackedStats = new Npc($attackedStats);
		}
		
		$attacker;
		$defender;
		
		$damage = null;
		
		while ( $attackerStats->current_life > 0 && $attackedStats->current_life > 0 )
		{
			// Golpea el que menos CD tenga
			if ( $attackerStats->current_cd <= $attackedStats->current_cd )
			{
				$attacker = &$attackerStats;
				$defender = &$attackedStats;
			}
			else
			{
				$attacker = &$attackedStats;
				$defender = &$attackerStats;
			}
			
			// Actualizamos CD
			$attacker->current_cd += $attacker->cd;
			
			$damage = Damage::getNormalHit($attacker, $defender);
			
			$damage->execute();
			$attacker->damage_done += $damage->getAmount();
			
			//self::after_hit($attacker, $defender, $realDamage);
			
			$this->add_message_to_log(
				sprintf(
					self::get_random_message(), 
					$attacker->name, 
					$defender->name
				) . ' (daño: ' . number_format($damage->getAmount(), 2) . ', defendido: ' . number_format($damage->getOriginalAmount() - $damage->getAmount(), 2) . ')'
			);
			
			unset($damage);
		}
		
		// Verificamos si no fue un empate
		if ( $attackerStats->current_life <= 0 && $attackedStats->current_life <= 0 )
		{
			$this->_winner = null;
			$this->_loser = null;
		}
		else
		{
			if ( $attackerStats->current_life > 0 )
			{
				$this->_winner = $this->_attacker;
				$this->_loser = $this->_attacked;
			}
			else
			{
				$this->_winner = $this->_attacked;
				$this->_loser = $this->_attacker;
			}
		}
		
		// Solo se da experiencia si no hay torneo activo
		if ( ! Tournament::is_active() )
		{
			$this->give_experience();
		}

		$this->give_rewards();
		$this->check_for_orbs();
		$this->check_for_protection();
		$this->send_notification_message();
		
		// Agregamos al registro la vida inicial de ambos
		$this->add_blank_space();
		$this->add_message_to_log('Vida inicial de ' . $attackerStats->name . ': ' . $attackerStats->initial_life, true);
		$this->add_message_to_log('Vida inicial de ' . $attackedStats->name . ': ' . $attackedStats->initial_life, true);
		
		// Agregamos al registro el daño realizado por ambos
		$this->add_blank_space();
		$this->add_message_to_log('Daño realizado por ' . $attackerStats->name . ': ' . number_format($attackerStats->damage_done, 2), true);
		$this->add_message_to_log('Daño realizado por ' . $attackedStats->name . ': ' . number_format($attackedStats->damage_done, 2), true);
		
		$damageDone = $attackedStats->damage_done;
		
		unset($attackerStats->cd, $attackerStats->current_cd, $attackerStats->is_player, $attackerStats->is_warrior, $attackerStats->stats, $attackerStats->initial_life, $attackerStats->damage_done);
		unset($attackedStats->cd, $attackedStats->current_cd, $attackedStats->is_player, $attackedStats->is_warrior, $attackedStats->stats, $attackedStats->initial_life, $attackedStats->damage_done);
		
		// Actualizamos las vidas
		if ( $this->_pair )
		{
			$this->_pair->current_life -= $damageDone / 2;
			$this->_pair->save();
			
			$this->_attacker->current_life -= $damageDone / 2;
			$this->_attacker->save();
		}
		else
		{
			$this->_attacker->current_life = $attackerStats->current_life;
			$this->_attacker->save();
		}
		
		if ( $attackedStats->is_player )
		{
			$this->_attacked->current_life = $attackedStats->current_life;
			$this->_attacked->save();
		}
		
		// Disparamos el evento de batalla
		if ( $attackedStats instanceof Character )
		{
			Event::fire('battle', array($this->_attacker, $this->_attacked, $this->_winner));
		}
		else
		{
			Event::fire('pveBattle', array($this->_attacker, $this->_attacked, $this->_winner));
		}
		
		\Log::write('Battle execution time', microtime(true) - $start);
	}
	
	/**
	 * 
	 * @param Eloquent $attacker
	 * @param Eloquent $attacked
	 * @param Eloquent $pair
	 */
	public function __construct(Eloquent $attacker, Eloquent $attacked, Eloquent $pair = null)
	{		
		$this->log = new BattleLog();
		
		$this->_attacker = $attacker;
		$this->_attacked = $attacked;
		$this->_pair = $pair;
		
		$this->to_battle();
		
		$this->log->message = '<ul class="unstyled">' . $this->log->message . '</ul>';
		$this->log->save();
		
		if ( $attacked instanceof Character )
		{
			$attacker->after_pvp_battle();
		}
		else
		{
			$attacker->after_battle();
		}
	}
}