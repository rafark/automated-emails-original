<?php

namespace AutomatedEmails\Original\Deployment\Files;

use AutomatedEmails\Original\Deployment\Directories\Directories;
use SplFileInfo;

use function AutomatedEmails\Original\Utilities\Text\i;

class FileVersionsFactory
{
    public function __construct(
        protected Directories $directories
    ) {}
    
    public function createFromSource(SplFileInfo $sourceFile) : FileVersions
    {
        (object) $sourceFileInfo = new SplFileInfo(
            i($sourceFile->getPathname())->replace($this->directories->mirror(), $this->directories->source())
        );

        return new FileVersions($sourceFileInfo, $this->directories);
    }

    public function createFromMirror(SplFileInfo $mirrorFile) : FileVersions
    {
        (object) $mirrorFileInfo = new SplFileInfo(
            i($mirrorFile->getPathname())->replace($this->directories->mirror(), $this->directories->source())
        );

        return new FileVersions($mirrorFileInfo, $this->directories);
    }
}