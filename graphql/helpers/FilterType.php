<?php

namespace app\graphql\helpers;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\IntType;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\StringType;
use GraphQL\Type\Definition\Type;
use yii\db\ActiveQuery;

class FilterType extends InputObjectType
{
    use SingletonTrait;

    protected static $operators = [
        'eq' => '=',
        'not' => 'not',
        'in' => 'in',
        'not_in' => 'not in',
        'gt' => '>',
        'lt' => '<',
        'gte' => '>=',
        'lte' => '<=',
        // Disabled for performance reasons (temporally?)
        //'contains' => 'ilike',
        //'not_contains' => 'not ilike',
    ];

    public static $operatorsForInt = ['eq', 'not', 'in', 'not_in', 'gt', 'lt', 'gte', 'lte'];
    public static $operatorsForString = ['eq', 'not', 'in', 'not_in', 'contains', 'not_contains'];

    /**
     * @var FilterField[]
     */
    private $fields;

    public function __construct(array $config)
    {
        $newFields = [];
        foreach ($config['fields'] as $name => $field) {
            if ($field instanceof Type) {
                $field = ['type' => $field];
            }
            if (!array_key_exists('operators', $field) && !isset($field['process'])) {
                if ($field['type'] instanceof IntType) {
                    $field['operators'] = self::$operatorsForInt;
                } elseif ($field['type'] instanceof StringType) {
                    $field['operators'] = self::$operatorsForString;
                }
            }

            if (isset($field['operators'])) {
                if (!isset($field['name'])) {
                    $field['name'] = $name;
                }
                if (!isset($field['column'])) {
                    $field['column'] = $name;
                }
                $originalField = $field;

                foreach ($originalField['operators'] as $operator) {
                    $field = $originalField;
                    $field['operator'] = $operator;
                    if ($operator == 'eq') {
                        $newFields[$name] = $field;
                    } else {
                        $name = $originalField['name'] . '_' . $operator;
                        $field['name'] = $name;
                        if ($operator == 'in' || $operator == 'not_in') {
                            $field['type'] = Type::listOf($field['type']);
                        }
                        $newFields[$name] = $field;
                    }
                }

            } else {
                $newFields[$name] = $field;
            }
        }
        $config['fields'] = $newFields;

        parent::__construct($config);
    }

    /**
     * @return FilterField[]
     */
    public function getFields()
    {
        if (null === $this->fields) {
            $this->fields = [];
            $fields = isset($this->config['fields']) ? $this->config['fields'] : [];
            $fields = is_callable($fields) ? call_user_func($fields) : $fields;
            foreach ($fields as $name => $field) {
                if ($field instanceof Type) {
                    $field = ['type' => $field];
                }
                $field = new FilterField($field + ['name' => $name]);
                $this->fields[$field->name] = $field;
            }
        }

        return $this->fields;
    }

    /**
     * @param ActiveQuery $query
     * @param array $args
     * @throws Error
     */
    public function processQuery(ActiveQuery $query, array $args)
    {
        foreach ($this->getFields() as $field) {
            if (isset($args[$field->name])) {
                $column = $field->column ?? $field->name;
                $value = $args[$field->name];
                if ($field->process !== null) {
                    $process = $field->process;
                    $process($query, $value);
                } elseif ($field->operator != null) {
                    $realOperator = self::$operators[$field->operator] ?? null;
                    if ($realOperator != null) {
                        $query->andWhere([$realOperator, $column, $value]);
                    } else {
                        throw Error::createLocatedError('Internal Server Error: Operator "' . $field['operator'] . '" not exists.');
                    }
                } elseif ($field->type instanceof ListOfType) {
                    $query->andWhere([$column => $value]);
                } else {
                    $query->andWhere([$column => $value]);
                }
            }
        }
    }
}