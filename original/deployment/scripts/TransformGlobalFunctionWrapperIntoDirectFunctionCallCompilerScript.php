<?php

namespace AutomatedEmails\Original\Deployment\Scripts;

use AutomatedEmails\Original\Characters\StringManager;
use AutomatedEmails\Original\Collections\Collection;
use AutomatedEmails\Original\Deployment\Script;
use Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Name;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

use function AutomatedEmails\Original\Utilities\Collection\{_, a};
use function AutomatedEmails\Original\Utilities\Text\i;

/**
 * IMPORTANT!
 * NAMED ARGUMENT SHOULD BE LEFT UNTOUCHED SO THAT RECTOR
 * CAN PROPERLY DOWNGRADE THEM, 
 *
 *For example: $this->globalFuncionWrapper->do_action(hook: 'init') 
 *should be compiled to: do_action(hook: 'init') 
 *then Rector will pick up that fuction and will transform the named arguments as needed
 *USING THE SOURCE OF THE FUNCTION!
 */
Class TransformGlobalFunctionWrapperIntoDirectFunctionCallCompilerScript extends Script
{
    static protected array $filesToExclude = [
        // strings with the file names ENDING IN, like: 'utilities/collection/collection.php'
    ];

    static public bool $replaced = false;


    static public function withFilesToExclude(array $filesToExclude) : string
    {
        static::$filesToExclude = array_merge(static::$filesToExclude, $filesToExclude);

        return self::class;
    }

    public function run()
    {
        if (
            !i($this->data->target)->endsWith('.php') ||
            i($this->data->target)->matchesRegEx('/automated-emails[A-Za-z0-9_-]*\/vendor/') ||
            _(static::$filesToExclude)->have(
                fn(string $fileEndingIn) => i($this->data->target)->endsWith($fileEndingIn)
            )
        ) {
            return;
        }


        $fileData = file_get_contents($this->data->target);<<<FILEDATA
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

        FILEDATA;


        (string) $pattern = '/\$this->(globalFunctionWrapper|globalFunctionWraper)->/i';

        if (i($fileData)->matchesRegEx($pattern)) {

            (object) $replaced = i($fileData)->replaceRegEx($pattern, replacement: '');

            file_put_contents(
                filename: $this->data->target, 
                data: $replaced,
            );
        }
    }
    
}