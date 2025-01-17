<?php

namespace AutomatedEmails\Original\Data\Schema;

use AutomatedEmails\Original\Characters\StringManager;
use AutomatedEmails\Original\Data\Schema\DatabaseColumn\DatabaseColumnDefault;
use AutomatedEmails\Original\Utilities\className;

Class DatabaseColumn
{
    use className;

    protected $name;
    protected $type;
    protected $extra;

    public function __construct($name, $type = 'VARCHAR(250)', $extra = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->extra = $extra;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return (array) $this->type;
    }

    public function getDefinition()
    {
        return "{$this->name} {$this->getClean('type')} {$this->getClean('extra')}";
    }

    public function getClean($property)
    {
        if ($this->{$property} instanceof DatabaseColumnDefault) {
            return $this->{$property}->getDefinition();
        }

        return (new StringManager($this->{$property}))->getOnly('A-Za-z0-9_() ');
    }
}