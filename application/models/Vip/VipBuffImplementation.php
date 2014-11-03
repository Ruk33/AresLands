<?php namespace Models\Vip;

use Character;
use Skill;
use Laravel\IoC;

abstract class VipBuffImplementation implements VipImplementation
{
    /**
     *
     * @var Character
     */
    protected $entity;
    
    /**
     *
     * @var array
     */
    protected $attributes;
    
    /**
     *
     * @var Skill 
     */
    protected $skillRepository;
    
    public function __construct(Character $entity, array $attributes)
    {
        $this->entity = $entity;
        $this->attributes = $attributes;
    }
    
    /**
     * @return Skill
     */
    abstract protected function getSkill();
    
    public function execute()
    {
        if (! $buff = $this->getSkill()) {
            return false;
        }
        
        return $buff->cast($this->entity, $this->entity);
    }
    
    /**
     * 
     * @param Skill $skillRepository
     */
    public function setSkillRepository(Skill $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }
    
    /**
     * 
     * @return Skill
     */
    protected function getSkillRepository()
    {
        if (! $this->skillRepository) {
            $this->setSkillRepository(IoC::resolve("Skill"));
        }
        
        return $this->skillRepository;
    }
}