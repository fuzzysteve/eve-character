<?php
namespace EveCharacter;

class EveCharacter implements \ArrayAccess
{

    public static $version="1.0.0";
   
    private $skills=array();

    /*
     * There two aren't needed for anything, but potentially handy for storage
     * down the road, or when I set up phealng integration.
     */

    /**
     * @var int
     */

    private $characterid;

    /**
     * @var string
     */

    public $charactername;



    /**
     * Used if the skill level isn't defined.
     * @var int
     */

    protected $defaultSkill;

    public function __construct($default = 0, $characterid = null)
    {
        if ($default<0 || $default>5)
        {
            throw new \Exception('Skills are limited to integers between 0 and 5');
        }
        $this->defaultSkill = (int)$default;
        $this->characterid = (int)$characterid;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \Exception(sprintf("Trying to use %s as a list.", __CLASS__));
        }
        if ($value>5 || $value <0) {
            throw new \Exception('Skills are limited to integers between 0 and 5');
        }
        if (is_numeric($offset) && is_numeric($value)) {
            $this->skills[$offset] = floor($value);
        } else {
            throw new \Exception(
                sprintf("Can only set numeric skill levels and IDs with setSkill in ", __CLASS__)
            );
        }
    }
          
    public function offsetExists($offset)
    {
        /*
        * As we're returning a value for everything.
        */
        return true;
    }
           
    public function offsetUnset($offset)
    {
        unset( $this->skills[$offset] );
    }
            
    public function &offsetGet($offset)
    {
        if (!isset($this->skills[$offset])) {
            if (is_callable($this->defaultSkill)) {
                $value = call_user_func($this->defaultSkill, $offset);
            } else {
                $value = $this->defaultSkill;
            }
            $this->skills[$offset] = $value;
        }
        return $this->skills[$offset];
    }

    public function getSkill($skillId)
    {
        if (isset($this->skills[$skillId])) {
            return $this->skills[$skillId];
        } else {
            return $this->defaultSkill;
        }
    }

    public function setSkill($skillId, $skillLevel = 0)
    {
        if ($skillLevel>5 || $skillLevel <0) {
            throw new \Exception('Skills are limited to integers between 0 and 5');
        }
        if (is_numeric($skillLevel) && is_numeric($skillId)) {
            $this->skills[$skillId]= $skillLevel;
        } else {
            throw new \Exception(
                sprintf("Can only set numeric skill levels and IDs with setSkill in ", __CLASS__)
            );
        }
    }

    public function fromJSON($json)
    {
        $parsed=json_decode($json);
        foreach ($parsed as $key => $value) {
            if (is_numeric($value) && is_numeric($key) && $value >=0 && $value <=5) {
                $this->skills[$key]=$value;
            } else {
                throw new \Exception(
                    sprintf("Can only set numeric skill levels and IDs with setSkill in ", __CLASS__)
                );
            }
        }
    }

    public function toJSON()
    {
        return json_encode($this->$skills);
    }
}
