<?php

namespace app\graphql\helpers;

use Closure;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ActiveRecordHelpers
{
    /**
     * Lazy-load load relation $relationName.
     * Instead of loading one-by-one, it'll load relation for whole parent list.
     *
     * For example you query list of "Users" with related "Comments". You use this function for "comments" field.
     * This function will load "comments" relation for whole list of "Users" at once.
     *
     * This function requires usage of listResolver
     *
     * @param string $relationName
     * @return Closure
     */
    public static function relationResolver($relationName)
    {
        return function (ActiveRecord $model, $args, $context, ResolveInfo $info) use ($relationName) {
            /** @var AppContext $context not in function params in order to not force extending our AppContext (you just need to have property "cachedLists") */
            if (!$model->isRelationPopulated($relationName)) {
                $sourceParentKey = implode('.', array_slice($info->path, 0, -2));
                $sourceParent = $context->cachedLists[$sourceParentKey];

                if (is_array($sourceParent) && !empty($sourceParent)) {
                    $relationMethodName = 'get' . ucfirst($relationName);
                    if (method_exists($model, $relationMethodName)) {
                        // Load db columns for relation
                        /** @var ActiveQuery $relationQuery */
                        $relationQuery = $model->$relationMethodName();
                        $relationClassName = $relationQuery->modelClass;
                        $selectColumns = ActiveRecordHelpers::getSelectColumns($info, $relationClassName);
                        // Relation link column should be selected too
                        $selectColumns[] = key($relationQuery->link);
                        $selectColumns = array_unique($selectColumns);

                        $relationsToLoad = [
                            $relationName => function (ActiveQuery $query) use ($selectColumns) {
                                $query->select($selectColumns);
                            }
                        ];

                        $model::find()->findWith($relationsToLoad, $sourceParent);
                    }
                }
            }

            $list = $model->$relationName;
            $context->cachedLists[implode('.', $info->path)] = $list;

            return $list;
        };
    }


    /**
     * Determine which columns to select from database.
     *
     * By default it looks for columns with the same name as field.
     * But if your field is dynamic you can hint which columns to select with "columns" parameter.
     *
     * For example:
     * 'fields' => [
     *  [
     *    'name' => 'name',
     *    'type' => Type::string(),
     *    'columns' => ['firstName', 'lastName'] // also can be string
     *    'resolve' => function ($source) {
     *      return $source->firstName . ' ' . $source->lastName;
     *    }
     *   ],
     * ]
     *
     * @param ResolveInfo $info
     * @param $modelName
     * @return array
     * @throws Error
     */
    protected static function getSelectColumns(ResolveInfo $info, $modelName): array
    {
        $selectColumns = [];
        $queryFields = $info->getFieldSelection();
        if ($info->returnType instanceof ListOfType && $info->returnType->ofType instanceof ObjectType) {
            /** @var ObjectType $type */
            $type = $info->returnType->ofType;
            $typeFields = $type->getFields();

            foreach ($queryFields as $field => $t) {
                if (isset($typeFields[$field])) {
                    if (isset($typeFields[$field]->config['columns'])) {
                        $columnsInConfig = $typeFields[$field]->config['columns'];
                        if (is_array($columnsInConfig)) {
                            foreach ($columnsInConfig as $column) {
                                $selectColumns[] = $column;
                            }
                        } else {
                            $selectColumns[] = $columnsInConfig;
                        }
                    } else {
                        $selectColumns[] = $field;
                    }
                }
            }
        }

        $dbColumns = $modelName::getTableSchema()->columns;

        /** @var ActiveRecord $model */
        $model = new $modelName;

        $newSelectColumns = [];

        foreach ($selectColumns as $column) {
            if (isset($dbColumns[$column])) {
                $newSelectColumns[] = $column;
            } else {
                $methodName = 'get' . ucfirst($column);
                if (!method_exists($model, $methodName) || !($model->$methodName() instanceof ActiveQuery)) {
                    throw Error::createLocatedError('Internal Server Error: Column "' . $column . '" not found in database.', $info->fieldNodes, $info->path);
                }
            }
        }

        return $newSelectColumns;
    }

    public static function listResolver(callable $resolver)
    {
        return function ($source, $args, AppContext $context, ResolveInfo $info) use ($resolver) {
            $list = $resolver($source, $args, $context, $info);
            if ($list instanceof ActiveQuery) {
                $selectColumns = self::getSelectColumns($info, $list->modelClass);
                $list->select($selectColumns);
                $list = $list->all();
            }
            $context->cachedLists[implode('.', $info->path)] = $list;
            return $list;
        };
    }
}