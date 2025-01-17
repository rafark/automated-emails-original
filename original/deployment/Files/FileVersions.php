<?php

namespace AutomatedEmails\Original\Deployment\Files;

use AutomatedEmails\Original\Characters\StringManager;
use AutomatedEmails\Original\Deployment\Directories\Directories;
use AutomatedEmails\Original\Deployment\Directories\File;
use SplFileInfo;

class FileVersions
{
    public function __construct(
        protected SplFileInfo $sourceFile,
        protected Directories $directories
    ) {}
    
    public function source() : File
    {
        return new File($this->sourceFile, basePath: $this->directories->source());
    }

    public function mirror() : File
    {
        return $this->for(basePath: $this->directories->mirror());
    }

    public function for(StringManager $basePath) : File
    {
        return new File(
            new SplFileInfo("{$basePath}/{$this->source()->relativePath()}"), 
            basePath: $basePath
        );
    }
}