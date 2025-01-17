<?php

namespace AutomatedEmails\Original\Renderers\Comments;

use AutomatedEmails\Original\Renderers\Abilities\Renderable;

Class KeyValueAnnotationRenderer implements Renderable
{
    private $key;
    private $value;

    public function __construct(string $key, string $value = '')
    {
        $this->key = $key;
        $this->value = $value;
    }
    
    public function render() : string
    {
        return "{$this->key} {$this->value}";
    }
}
