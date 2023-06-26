<?php

/** @var yii\web\View $this
 * @var QuestionDataProvider $results
 * @var Pagination $pages
 * @var SearchForm $model
 * @var string $errorQueryMessage
 */

use yii\bootstrap5\ActiveForm;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;

$inputTemplate = '<div class="input-group mb-2">
          {input}
          <button class="btn btn-primary" type="submit" id="button-search">Поиск</button>
          <button class="btn btn-outline-secondary ' .
    (Yii::$app->session->get('show_search_settings') ? 'active' : "") . '" id="button-search-settings">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sliders" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05zM4.5 7a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zM2.05 8a2.5 2.5 0 0 1 4.9 0H16v1H6.95a2.5 2.5 0 0 1-4.9 0H0V8h2.05zm9.45 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-2.45 1a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0v-1h9.05z"/>
            </svg>
          </button>
          </div>';

?>
<div class="search-block">
  <div class="container-fluid">

      <?php $form = ActiveForm::begin(
          [
              'method' => 'GET',
              'action' => ['site/index'],
              'options' => ['class' => 'pb-1 mb-2 pt-3', 'autocomplete' => 'off'],
          ]
      ); ?>
    <div class="d-flex align-items-center">
        <?= $form->field($model, 'query', [
            'inputTemplate' => $inputTemplate,
            'options' => [
                'class' => 'w-100', 'role' => 'search'
            ]
        ])->textInput(
            [
                'type' => 'search',
                'class' => 'form-control form-control-lg',
                'placeholder' => "Поиск",
                'autocomplete' => 'off',
            ]
        )->label(false); ?>
    </div>
    <div id="search-setting-panel"
         class="search-setting-panel <?= Yii::$app->session->get('show_search_settings') ? 'show-search-settings' : '' ?>">

        <?= $form->field($model, 'matching', ['inline' => true, 'options' => ['class' => 'pb-2']])
            ->radioList($model->getMatching(), ['class' => 'form-check-inline'])
            ->label(false); ?>

      <div class="row">
        <div class="col-md-6 d-flex align-items-center">
            <?= $form->field($model, 'dictionary', ['options' => ['class' => 'pb-2']])
                ->checkbox()
                ->label('Словарь концептуальных терминов (тестирование)'); ?>
        </div>
      </div>
    </div>
      <?php ActiveForm::end(); ?>
  </div>
</div>
