<?php

namespace AutomatedEmails\Original\Data\Drivers;

use AutomatedEmails\Original\Characters\StringManager;
use AutomatedEmails\Original\Collections\Collection;
use AutomatedEmails\Original\Data\Instructions\Instruction;
use AutomatedEmails\Original\Data\Schema\DatabaseCredentials;
use AutomatedEmails\Original\Data\Schema\DatabaseTable;

Class WordPressDatabaseDriver extends DatabaseDriver
{
    const NEW_LINE_PLACEHOLDER = '___NEWLINE__';

    public $wordPressDatabase;
    protected $likePlaceholders = [];
    protected static $connections = [];

    protected function setConnection()
    {
        if ($this->credentials instanceof DatabaseCredentials) {
            if (!isset(static::$connections[$this->credentials->get('name')])) {
                (object) $connection = new \wpdb(
                    $this->credentials->get('username'),
                    $this->credentials->get('password'),
                    $this->credentials->get('name'),
                    $this->credentials->get('host')
                );

                static::$connections[$this->credentials->get('name')] = $connection; 
            }

            $this->wordPressDatabase = static::$connections[$this->credentials->get('name')];
        } else {
            $this->wordPressDatabase = $GLOBALS['wpdb'];
        }

    }

    public static function errors()
    {
        (array) $errors = [];

        if (isset($GLOBALS['EZSQL_ERROR']) && is_array($GLOBALS['EZSQL_ERROR'])) {
            $errors = $GLOBALS['EZSQL_ERROR'];
        }

        return (new Collection($errors))->map(function(Array $error){
            return (new Collection($error))->mapWithKeys(function($element, $key){
                return [
                    'key' => $key,
                    'value' => is_string($element)? new StringManager($element) : $element
                ];
            });
        });
    }
    
    public function execute(Instruction $instruction)
    {
        (string) $statement = $this->getStatement($instruction->getStatement(), $instruction->getParameters());

        return $instruction->shouldGet()? 
                    $this->wordPressDatabase->get_results($statement, ARRAY_A) : 
                    $this->wordPressDatabase->query($statement);
    }

    protected function getStatement(string $statement, $parameters)
    {
        if (empty($parameters)) {
            return $statement;
        }
        $parameters = array_map(function($value) {
            return is_numeric($value)? (integer) $value : str_replace(static::NEW_LINE_PLACEHOLDER, "\n", (string) $value);
        }, array_map(function($value){
            return is_numeric($value)? (integer) $value : str_replace("\n", static::NEW_LINE_PLACEHOLDER, (string) $value);
        }, $parameters));//sanitize_text_field(: sql values are escaped and all input is safely rendered, we'll still add support for sanitize_text_field per field

        return $this->wordPressDatabase->prepare(
            $this->getPreparedStatement($statement, $parameters), 
            $parameters
        );
    }

    protected function getPreparedStatement($statement, $parameters)
    {
        $parameters = array_values($parameters);
        $index = -1;
        return preg_replace_callback('/\?+/', function($matches) use($parameters, &$index) {
            $index++;
            $value = $parameters[$index];
            return is_numeric($value)? '%d' : '%s';
        }, $statement);
    }

    public function escapeLike($value)
    {
        if (array_search($value, $this->likePlaceholders) === false) {
            throw new \Exception("please prepare like statement first");
        }

        return $this->wordPressDatabase->esc_like($value);   
    }

    public function getLIKEPlaceHolder($value)
    {
        $this->likePlaceholders[] = $value;

        if (is_null($value) || (is_string($value) && $value === '')) {
            return "= ? AND 1=0";
        }

        return "LIKE ?";   
    }
}