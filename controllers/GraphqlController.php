<?php

namespace app\controllers;

use app\graphql\AppContext;
use app\graphql\QueryType;
use ErrorException;
use GraphQL\Error\FormattedError;
use GraphQL\GraphQL;
use GraphQL\Schema;
use GraphQL\Type\Definition\Config;
use Yii;
use yii\web\Controller;
use Youshido\GraphQL\Execution\Processor;

class GraphqlController extends Controller
{
    public function beforeAction($action)
    {
        Yii::$app->request->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        // TODO: Перевести в Yii2-стиль
        // TODO: Убрать предыдущую библиотеку для graphql

        // Disable default PHP error reporting - we have better one for debug mode (see bellow)
        ini_set('display_errors', 0);
        if (!empty($_GET['debug'])) {
            // Enable additional validation of type configs
            // (disabled by default because it is costly)
            Config::enableValidation();
            // Catch custom errors (to report them in query results if debugging is enabled)
            $phpErrors = [];
            set_error_handler(function ($severity, $message, $file, $line) use (&$phpErrors) {
                $phpErrors[] = new ErrorException($message, 0, $severity, $file, $line);
            });
        }
        try {
            // Parse incoming query and variables
            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                $raw = file_get_contents('php://input') ?: '';
                $data = json_decode($raw, true);
            } else {
                $data = $_REQUEST;
            }
            $data += ['query' => null, 'variables' => null];

            $schema = new Schema([
                'query' => new QueryType()
            ]);
            $context = new AppContext();
            $context->isHentaiSite = 0;
            $result = GraphQL::execute(
                $schema,
                $data['query'],
                null,
                $context,
                (array)$data['variables']
            );
            // Add reported PHP errors to result (if any)
            if (!empty($_GET['debug']) && !empty($phpErrors)) {
                $result['extensions']['phpErrors'] = array_map(
                    ['GraphQL\Error\FormattedError', 'createFromPHPError'],
                    $phpErrors
                );
            }
            $httpStatus = 200;
        } catch (\Exception $error) {
            $httpStatus = 500;
            if (!empty($_GET['debug'])) {
                $result['extensions']['exception'] = FormattedError::createFromException($error);
            } else {
                $result['errors'] = [FormattedError::create('Unexpected Error')];
            }
        }
        header('Content-Type: application/json', true, $httpStatus);
        echo json_encode($result);
    }

    public function actionIndexOld()
    {
        // TODO: Перевести в Yii2-стиль

        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Credentials: false', true);
        header('Access-Control-Allow-Origin: *');

        if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
            return;
        }

        $schema = new Schema();
        if ((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
            || isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json'
        ) {
            $rawBody = file_get_contents('php://input');
            $requestData = json_decode($rawBody ?: '', true);
        } else {
            $requestData = $_POST;
        }
        $payload = isset($requestData['query']) ? $requestData['query'] : null;
        $variables = isset($requestData['variables']) ? $requestData['variables'] : null;
        $processor = new Processor($schema);
        $response = $processor->processPayload($payload, $variables)->getResponseData();
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
