<?php

namespace AutomatedEmails\App\Components\Data\Builtin\Post\Values;

use AutomatedEmails\App\Components\Abilities\Descriptable;
use AutomatedEmails\App\Components\Abilities\Formable;
use AutomatedEmails\App\Components\Abilities\Identifiable;
use AutomatedEmails\App\Components\Abilities\Nameable;
use AutomatedEmails\App\Components\Abilities\Typeable;
use AutomatedEmails\App\Components\Data\Forms\TextFormComponent;
use AutomatedEmails\App\Domain\Data\Post\Values\PostTitleRaw;
use function AutomatedEmails\Original\Utilities\Text\__;

class PostTitleRawComponent implements Typeable, Identifiable, Nameable, Descriptable, Formable
{
    public function identifier(): string
    {
        return PostTitleRaw::ID;
    } 

    public function type(): string
    {
        return PostTitleRaw::class;
    } 

    public function form(): Identifiable
    {
        return new TextFormComponent;
    } 

    public function name()/*: \Stringable*/  
    {
        return __('Title (unfiltered)');
    }

    public function description()/*: \Stringable*/ 
    {
        return __('The title of the post, as saved in the database, without filters applied');
    } 
}