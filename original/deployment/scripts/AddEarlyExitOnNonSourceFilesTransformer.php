<?php

namespace AutomatedEmails\Original\Deployment\Scripts;

use AutomatedEmails\Original\Characters\StringManager;
use AutomatedEmails\Original\Deployment\Files\FileVersions;
use AutomatedEmails\Original\Deployment\Transformers\Abilities\Transformable;
use AutomatedEmails\Original\Deployment\Transformers\Abilities\ValidatableTransformable;
use AutomatedEmails\Original\Validation\Validator;
use AutomatedEmails\Original\Validation\Validators;
use AutomatedEmails\Original\Validation\Validators\ValidWhen;

class AddEarlyExitOnNonSourceFilesTransformer implements Transformable, ValidatableTransformable
{
    public function canBeTransformed(StringManager $fileContents, FileVersions $fileVersions): Validator
    {
        return new Validators([
            new ValidWhen(!$fileContents->matchesRegEx('/namespace\s+\w+/i'))
        ]);
    } 

    public function transform(StringManager $fileContents, FileVersions $fileVersions): StringManager
    {
        /*
        $fileContents = i(<<<FILEDATA
            <?php
            function kug() {
                return new Class {
                    public function do() : void
                    {
                        return \$this->globalFunctionWrapper->do_action(hook: 'init');
                    }
                };
            }

            public function register(): void
            {
                foreach (\$this->subscribers as \$subscriber) {
                    (object) \$eventHandler = \$this->eventHandlerFactory->create(\$subscriber);
                    (object) \$handle = \$eventHandler->handle(...);

                    \$this->addHandler(\$handle, \$subscriber->priority());

                    \$this->globalFunctionWraper->add_action(
                        hook_name: \$this->name, 
                        callback: \$handle,
                        priority: \$subscriber->priority(),
                        accepted_args: \$this->numberOfAcceptedArgumentsForSubscriber(\$subscriber)
                    );
                }
            }

            public function unregister(): void
            {
                \$this->handlers->forEvery(
                    fn(array \$handlerAndPriority) => \$this->globalFunctionWraper->remove_action(
                        hook_name: \$this->name,
                        callback: \$handlerAndPriority['handler'],
                        priority: \$handlerAndPriority['priority']
                    )
                );

                \$this->handlers = _();
            } 

        FILEDATA);
        */

        return $fileContents->trim()->trimLeft('<?php')->prepend("<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly");
    } 
}