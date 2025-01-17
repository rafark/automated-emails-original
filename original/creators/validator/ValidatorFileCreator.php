<?php

namespace AutomatedEmails\Original\Creators\Validator;

use AutomatedEmails\Original\Creators\ClassFileCreator;
use AutomatedEmails\Original\Environment\Env;
use AutomatedEmails\Original\Validation\Validator;

Class ValidatorFileCreator extends ClassFileCreator
{
    private $validatorName;
    private $basePath;

    public function __construct(string $validatorName, string $basePath)
    {
        $this->validatorName = $validatorName;
        $this->basePath = $basePath;
    }

    protected function getClassName() : string
    {
        return ucfirst($this->validatorName);
    }

    protected function getBaseClassName() : string
    {
        return 'Validator';
    }

    protected function getBaseClassNamespace() : string
    {
        return Env::getWithBaseNamespace('Original\Validation');
    }

    protected function getRelativeDirectory() : string
    {
        return "{$this->basePath}/validators";
    }

    protected function getTemplatePath() : string
    {
        return dirname(__FILE__).'/ValidatorTemplate.php';
    }

    protected function getVariablestoPassToTemplate() : array
    {
        return [];
    }

    protected function getDataToPassToTasks() : array
    {
        (array) $defaultVariables = [
        ];

        return array_merge(parent::getDataToPassToTasks(), $defaultVariables, $this->getTemplateVariables());
    }
}