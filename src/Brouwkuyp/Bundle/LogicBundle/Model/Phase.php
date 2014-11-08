<?php

namespace Brouwkuyp\Bundle\LogicBundle\Model;

/**
 * Phase
 * 
 * A phase is the smallest element of procedural control that can
 * accomplish process-oriented tasks. Phases perform unique and
 * generally independent, basic process-oriented functions,
 * such as charging an ingredient or agitating a tank. 
 * Simply put, phases are the workhorses of recipes. 
 * All other elements (procedures, unit procedures, and operations)
 * simply group, organize, and direct phases.
 */
class Phase implements ExecutableInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Set name
     *
     * @param  string       $name
     * @return Phase
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Starts stage
     */
    public function start()
    {
        
    }
    
    /**
     * Executes stage
    */
    public function execute()
    {
        
    }
    
    /**
     * Returns started state
     *
     * @return bool
    */
    public function isStarted()
    {
        
    }
    
    /**
     * Returns finished state
     *
     * @return bool
    */
    public function isFinished()
    {
        
    }
}
