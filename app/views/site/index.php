<?php

/** @var yii\web\View $this
 * @var ParagraphDataProvider $results
 * @var Pagination $pages
 * @var SearchForm $model
 * @var string $errorQueryMessage
 */

use app\widgets\FollowParagraph;
use app\widgets\NeighboringParagraphs;
use app\widgets\ScrollWidget;
use app\widgets\SearchResultsSummary;
use src\forms\SearchForm;
use src\models\Paragraph;
use src\repositories\ParagraphDataProvider;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\data\Pagination;

$this->title = Yii::$app->name;
$this->params['breadcrumbs'][] = $this->title;


echo Html::beginForm(['/site/search-settings'], 'post', ['name' => 'searchSettingsForm', 'class' => 'd-flex']);
echo Html::hiddenInput('value', 'toggle');
echo Html::endForm();
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
  <div class="site-index">
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
    <div class="container-fluid search-results">
        <?php if (!$results): ?>
            <?php if ($errorQueryMessage): ?>
            <div class="card border-danger mb-3">
              <div class="card-body"><?= $errorQueryMessage; ?></div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($results): ?>
        <?php
        // Property totalCount пусто пока не вызваны данные модели getModels(),
        // сначала получаем массив моделей, потом получаем общее их количество
        /** @var Paragraph[] $paragraphs */
        $paragraphs = $results->getModels();
        $pagination = new Pagination(
            [
                'totalCount' => $results->getTotalCount(),
                'defaultPageSize' => Yii::$app->params['searchResults']['pageSize'],
            ]
        );
        ?>
      <div class="row">
        <div class="col-md-12">
            <?php if ($pagination->totalCount === 0): ?>
              <h5>По вашему запросу ничего не найдено</h5>
            <?php else: ?>
              <div class="row">
                <div class="col-md-8 d-flex align-items-center">
                    <?= SearchResultsSummary::widget(['pagination' => $pagination]); ?>
                </div>
              </div>

              <div class="card pt-3">
                <div class="card-body">
                    <?php foreach ($paragraphs as $paragraph): ?>
                      <div class="px-xl-5 px-lg-5 px-md-5 px-sm-3 paragraph" data-entity-id="<?= $paragraph->getId(); ?>">
                        <div class="paragraph-header">
                          <div class="d-flex justify-content-between">
                            <div>

                            </div>
                            <div>
                                <?php Html::a("#" . $paragraph->getId(), ['site/neighboring', 'id' => $paragraph->getId(), 'num' => 3]); ?>
                                <?= FollowParagraph::widget(['paragraph' => $paragraph, 'pagination' => $pagination]); ?>
                            </div>
                          </div>
                        </div>
                        <div>
                          <div class="paragraph-text">
                              <?php if (!$paragraph->highlight['text'] || !$paragraph->highlight['text'][0]): ?>
                                  <?php echo Yii::$app->formatter->asRaw(htmlspecialchars_decode($paragraph->text)); ?>
                              <?php else: ?>
                                  <?php echo Yii::$app->formatter->asRaw(htmlspecialchars_decode($paragraph->highlight['text'][0])); ?>
                              <?php endif; ?>
                          </div>
                        </div>
                        <div class="d-flex justify-content-start book-name">
                          <div><strong><i>ВП СССР — <?=$paragraph->book_name; ?></i></strong></div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                </div>
              </div>

            <?php endif; ?>

          <div class="container container-pagination">
            <div class="detachable fixed-bottom">
                <?php echo LinkPager::widget(
                    [
                        'pagination' => $pagination,
                        'firstPageLabel' => true,
                        'lastPageLabel' => true,
                        'maxButtonCount' => 3,
                        'options' => [
                            'class' => 'd-flex justify-content-center'
                        ],
                        'listOptions' => ['class' => 'pagination mb-0']
                    ]
                ); ?>
            </div>
          </div>

        </div>
      </div>
    </div>
      <?= ScrollWidget::widget(['data_entity_id' => isset($paragraph) ? $paragraph->getId() : 0]); ?>
      <?php else: ?>
        <div class="card">
          <div class="card-body">
            <div class="container px-md-5 px-sm-3 pb-3">
              <div class="py-3">
                <h5>Товарищи! </h5>
                <p>Мы запустили поисковик по текстам толстых книг ВП СССР.</p>
                <strong>★ <a href="https://kob.svodd.ru">https://kob.svodd.ru</a></strong>
              </div>
            </div>

            <div class="px-md-5 px-sm-3 pb-3">
              <p>Поиск реализуется внутри содержания отдельного параграфа, включая сноски, если они есть. Таким образом
                текстовый запрос в строке поиска служит цели найти наиболее подходящие под этот запрос параграфы. Рядом
                с кнопкой «поиск» есть кнопка опций, где можно выбрать режим и подключить «концептуальный словарь
                синонимов». Режимы поиска позволяют выбрать точное совпадение фразы в результатах (по соответствию
                фразе), появление хотя бы одного из слов запроса в параграфе (по совпадению слов), или найдя подходящий
                параграф, можно выбрать его номер и взяв соседние (отняв или добавив единицу), показать несколько
                параграфов подряд. Это удобно, если нужна более развёрнутая информация, но нет необходимости обращаться
                к целой книге.</p>

              <p>Если цель поиска — найти книгу по искомому запросу, то для этой цели в выдаче к каждой цитате
                присоединяется название толстой книги, откуда она была получена. Поисковый запрос, к которому вы часто
                обращаетесь, можно сохранить при помощи кнопки «короткая ссылка» — в ней также сохраняется режим
                конкретного поиска. Режим «словаря концептуальных терминов» позволяет искать все синонимы (иногда
                антонимы) понятий, перечисленных в поисковой строке, так, при поиске «пфу» со включенным «концептуальным
                словарём» будут получены все связанные термины, например «полная функция управления» или «цели», однако
                если вы поищите «пфу» без словаря, результатом будет лишь единственный параграф, где понятие полной
                функции управления было обозначено аббревиатурой.</p>

              <h5 class="py-3">Пример одного из сценариев работы с поисковиком.</h5>

              <p>Представьте, что вам нужно найти в толстых книгах пару терминов, между которыми вы предполагаете
                существование какой-либо связи. Если вы воспользуетесь обычным поиском операционной системы или
                редактора файлов по тексту книги, вы можете найти книгу, содержащую искомые понятия, но это не означает,
                что в книге эти понятия будут связаны в рамках одной страницы текста или даже одной главы. Понятия
                просто могут присутствовать в совершенно раздельных частях текста, поскольку тексты толстых книг
                достаточно обширны и включают много понятий. Такие запросы практически бесполезны при использовании
                стандартных средств поиска по тексту книги в файле, поскольку в большинстве случаев стандартный поиск
                слеп к расстоянию между поисковыми словами. В нашем поисковике запрос ограничивается параграфами книг,
                поэтому поиск связи между понятиями намного эффективнее.</p>


              <h5 class="py-3">Вот примеры связок, которые будет сложно найти быстро без нашего поисковика:</h5>

              <div class="row">
                <div class="col-md-4 mb-3 mb-sm-3">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">«предиктор + пфу»</h5>
                      <p class="card-text">★ <a href="https://svodd.ru/exxyAS9p">https://svodd.ru/exxyAS9p</a></p>
                    </div>
                  </div>
                </div>
              <div class="col-md-4 mb-3 mb-sm-3">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">«достоевский + магия»</h5>
                    <p class="card-text">★ <a href="https://svodd.ru/NYxu2lGg">https://svodd.ru/NYxu2lGg</a></p>
                  </div>
                </div>
              </div>
              <div class="col-md-4 mb-3 mb-sm-3">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">«пушкин + масоны»</h5>
                    <p class="card-text">★ <a href="https://svodd.ru/hOJ0iS0u">https://svodd.ru/hOJ0iS0u</a></p>
                  </div>
                </div>
              </div>
              </div>

              <p class="pt-4">Вопросы, предложения, замечания и найденные ошибки приветствуются.<br>
              Можно написать в комментариях на <a href="https://xn----8sba0bbi0cdm.xn--p1ai/qa/question/view-2226"
                                                     target="_blank">ФКТ</a>, на <a
                        href="https://github.com/terratensor/kob-library-app/discussions/16" target="_blank">гитхабе</a>
                или воспользоваться страницей <a href="https://svodd.ru/contact" target="_blank">обратной связи</a>.</p>
              <strong>Удачного поиска, друзья!</strong>

            </div>
          </div>
        </div>
      <?php endif; ?>
  </div>
<?php $js = <<<JS
  let menu = $(".search-block");
var menuOffsetTop = menu.offset().top;
var menuHeight = menu.outerHeight();
var menuParent = menu.parent();
var menuParentPaddingTop = parseFloat(menuParent.css("padding-top"));
 
checkWidth();
 
function checkWidth() {
    if (menu.length !== 0) {
      $(window).scroll(onScroll);
    }
}
 
function onScroll() {
  if ($(window).scrollTop() > menuOffsetTop) {
    menu.addClass("shadow");
    menuParent.css({ "padding-top": menuParentPaddingTop });
  } else {
    menu.removeClass("shadow");
    menuParent.css({ "padding-top": menuParentPaddingTop });
  }
}

const btn = document.getElementById('button-search-settings');
btn.addEventListener('click', toggleSearchSettings, false)

function toggleSearchSettings(event) {
  event.preventDefault();
  btn.classList.toggle('active')
  document.getElementById('search-setting-panel').classList.toggle('show-search-settings')
  
  const formData = new FormData(document.forms.searchSettingsForm);
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "/site/search-settings");
  xhr.send(formData);
}

$('input[type=radio]').on('change', function() {
    $(this).closest("form").submit();
});

JS;

$this->registerJs($js);
