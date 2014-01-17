<?php namespace Libraries;

// todo diagrama uml, tipos de daño, bloqueo, etc., etc.

class Damage
{
	protected $source;
	protected $target;
	protected $amount;
	protected $originalAmount;
	protected $type;
	
	public function __construct($source, $target, $amount, $originalAmount, $type = null)
	{
		$this->source = $source;
		$this->target = $target;
		$this->amount = $amount;
		$this->originalAmount = $originalAmount;
		$this->type = $type;
	}
	
	public static function getNormalHit($source, $target)
	{		
		$sourceStats = $source->get_attribute('stats');
		$sourceIsWarrior = $sourceStats['stat_strength'] > $sourceStats['stat_magic'];
		$sourceMainAttribute = ( $sourceStats['stat_strength'] ) ? $sourceStats['stat_strength'] : $sourceStats['stat_magic'];
		
		$sourceMinDamage = $sourceMainAttribute * 0.25;
		$sourceMaxDamage = max($sourceMinDamage, $sourceMainAttribute * 0.75);
		
		$targetStats = $target->get_attribute('stats');
		$targetMainAttribute = ( $targetStats['stat_resistance'] ) ? $targetStats['stat_resistance'] : $targetStats['stat_magic_resistance'];
		
		$targetMinDefense = $targetMainAttribute * 0.75;
		$targetMaxDefense = max($targetMinDefense, $targetMainAttribute * 1.25);
		
		$averageDamage = mt_rand($sourceMinDamage, $sourceMaxDamage);
		
		// 35% de crítico físico
		if ( $sourceIsWarrior && mt_rand(0, 100) <= 35 )
		{
			$damage = $averageDamage * 1.50;
		}
		// 25% de crítico mágico
		elseif ( ! $sourceIsWarrior && mt_rand(0, 100) <= 25 )
		{
			$damage = $averageDamage * 2.50;
		}
		// 10% de golpe fallido
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$damage = $averageDamage * 0.75;
		}
		else
		{
			$damage = $averageDamage;
		}
		
		// Calculamos la defensa
		$averageDefense = mt_rand($targetMinDefense, $targetMaxDefense);

		// 30% de defensa exitosa
		if ( mt_rand(0, 100) <= 30 )
		{
			$defense = $averageDefense * 1.75;
		}
		// 10% de defensa fallida
		elseif ( mt_rand(0, 100) <= 10 )
		{
			$defense = $averageDefense * 0.75;
		}
		else
		{
			$defense = $averageDefense;
		}
		
		$amount = max(0, min($damage - $defense * 0.4, $target->current_life));
		
		return new self($source, $target, $amount, $damage, null);
	}
	
	public function getSource()
	{
		return $this->source;
	}
	
	public function getTarget()
	{
		return $this->target;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}
	
	public function getOriginalAmount()
	{
		return $this->originalAmount;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function execute()
	{
		if ( $this->target instanceof \Character )
		{
			$triggers = $this->target->triggers()->get();
		}
		
		$this->target->current_life -= $this->amount;
	}
}