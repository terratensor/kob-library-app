<?php

declare(strict_types=1);

namespace app\controllers;

use src\forms\SearchForm;
use src\services\ManticoreService;
use yii\web\Controller;

class BookController extends Controller
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

    public function actionView($id): string
    {
        $results = null;
        $form = new SearchForm();
        $errorQueryMessage = '';

        $results = $this->service->book((int)$id);

        return $this->render('view', [
            'results' => $results,
            'model' => $form
        ]);
    }
}
