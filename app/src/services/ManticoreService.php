<?php

declare(strict_types=1);

namespace src\services;

use src\forms\SearchForm;
use src\repositories\ParagraphDataProvider;
use src\repositories\ParagraphRepository;
use Yii;

class ManticoreService
{
    private ParagraphRepository $paragraphRepository;

    public function __construct(ParagraphRepository $questionRepository)
    {
        $this->paragraphRepository = $questionRepository;
    }

    /**
     * @param SearchForm $form
     * @return ParagraphDataProvider
     * @throws EmptySearchRequestExceptions
     */
    public function search(SearchForm $form): ParagraphDataProvider
    {
        $queryString = $form->query;

        if ($form->dictionary) {
            $indexName = \Yii::$app->params['indexes']['concept'];
        }

        try {
            $comments = match ($form->matching) {
                'query_string' => $this->paragraphRepository
                    ->findByQueryStringNew($queryString, $indexName ?? null, $form),
                'match_phrase' => $this->paragraphRepository
                    ->findByMatchPhrase($queryString, $indexName ?? null, $form),
                'match' => $this->paragraphRepository
                    ->findByQueryStringMatch($queryString, $indexName ?? null, $form),
                'in' => $this->paragraphRepository
                    ->findByParagraphId($queryString, $indexName ?? null, $form),
            };
        } catch (\DomainException $e) {
            throw new EmptySearchRequestExceptions($e->getMessage());
        }


        return new ParagraphDataProvider(
            [
                'query' => $comments,
                'pagination' => [
                    'pageSize' => Yii::$app->params['searchResults']['pageSize'],
                ],
                'sort' => [
                    'attributes' => [
                        'position',
                    ]
                ],
            ]);
    }
}
