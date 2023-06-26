<?php

namespace app\controllers;

use src\forms\SearchForm;
use src\Search\Http\Action\V1\SearchSettings\ToggleAction;
use src\services\EmptySearchRequestExceptions;
use src\services\ManticoreService;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    private ManticoreService $service;

    public function __construct(
        $id,
        $module,
        ManticoreService $service,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'search-settings' => [
                'class' => ToggleAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $results = null;
        $form = new SearchForm();
        $errorQueryMessage = '';

        try {
            if ($form->load(Yii::$app->request->queryParams) && $form->validate()) {
                $results = $this->service->search($form);
            }
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        } catch (EmptySearchRequestExceptions $e) {
            $errorQueryMessage = $e->getMessage();
        }

        return $this->render('index', [
            'results' => $results ?? null,
            'model' => $form,
            'errorQueryMessage' => $errorQueryMessage,
        ]);
    }
}
