<?php

namespace AutomatedEmails\Original\Dependency;

use AutomatedEmails\Original\Cache\Cache;
use AutomatedEmails\Original\Cache\MemoryCache;
use AutomatedEmails\Original\Dependency\Abilities\StaticType;

class CachedInstanceDependencyContainer extends DependencyContainer
{
    public function __construct(
        protected Dependency&StaticType $dependency,
        protected Cache $cache = new MemoryCache()
    ) {}
    
    public function get(string $type): object
    {
        return $this->cache->getIfExists('dependency')
                            ->otherwise($this->dependency->create(...));
    } 
}