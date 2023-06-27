<?php

declare(strict_types=1);

namespace src\repositories;

use Manticoresearch\Client;
use Manticoresearch\Index;
use Manticoresearch\Query\BoolQuery;
use Manticoresearch\Query\In;
use Manticoresearch\Query\MatchPhrase;
use Manticoresearch\Query\MatchQuery;
use Manticoresearch\Query\QueryString;
use Manticoresearch\Search;
use src\forms\SearchForm;
use src\helpers\SearchHelper;

class ParagraphRepository
{
    private Client $client;
    public Index $index;
    private Search $search;

    private string $indexName = 'vpsssr_library';
    public int $pageSize = 20;

    public function __construct(Client $client, $pageSize)
    {
        $this->client = $client;
        $this->setIndex($this->client->index('vpsssr_library'));
        $this->search = new Search($this->client);
        $this->pageSize = $pageSize;
    }

    /**
     * @param string $queryString
     * @param string|null $indexName
     * @param SearchForm|null $form
     * @return Search
     * "query_string" accepts an input string as a full-text query in MATCH() syntax
     */
    public function findByQueryStringNew(
        string $queryString,
        ?string $indexName = null,
        ?SearchForm $form = null
    ): Search {
        $this->search->reset();
        if ($indexName) {
            $this->setIndex($this->client->index($indexName));
        }

        $queryString = SearchHelper::escapingCharacters($queryString);

        // Запрос переделан под фильтр
        $query = new BoolQuery();

        if ($form->query) {
            $query->must(new QueryString($queryString));
        }

        // Выполняем поиск если установлен фильтр или установлен строка поиска
        if ($form->query) {
            $search = $this->index->search($query);
//            $search->facet('book_id');
//            var_dump($search->facet('book_id'));
        } else {
            throw new \DomainException('Задан пустой поисковый запрос');
        }

        // Если нет совпадений no_match_size возвращает пустое поле для подсветки
        $search->highlight(
            ['text'],
            [
                'limit' => 0,
                'no_match_size' => 0,
                'pre_tags' => '<mark>',
                'post_tags' => '</mark>'
            ],
        );
        return $search;
    }

    /**
     * @param string $queryString
     * @param string|null $indexName
     * @param SearchForm|null $form
     * @return Search
     * "match" is a simple query that matches the specified keywords in the specified fields.
     */
    public function findByQueryStringMatch(
        string $queryString,
        ?string $indexName = null,
        ?SearchForm $form = null
    ): Search {
        $this->search->reset();
        if ($indexName) {
            $this->setIndex($this->client->index($indexName));
        }

        // Запрос переделан под фильтр
        $query = new BoolQuery();

        if ($form->query) {
            $query->must(new MatchQuery($queryString, '*'));
        }

        // Выполняем поиск если установлен фильтр или установлен строка поиска
        if ($form->query) {
            $search = $this->index->search($query);
        } else {
            throw new \DomainException('Задан пустой поисковый запрос');
        }

        $search->highlight(
            ['text'],
            [
                'limit' => 0,
                'no_match_size' => 0,
                'pre_tags' => '<mark>',
                'post_tags' => '</mark>'
            ]
        );

        return $search;
    }

    /**
     * @param string $queryString
     * @param string|null $indexName
     * @return Search
     * "match_phrase" is a query that matches the entire phrase. It is similar to a phrase operator in SQL.
     */
    public function findByMatchPhrase(
        string $queryString,
        ?string $indexName = null,
        ?SearchForm $form = null
    ): Search
    {
        $this->search->reset();
        if ($indexName) {
            $this->setIndex($this->client->index($indexName));
        }

        // Запрос переделан под фильтр
        $query = new BoolQuery();

        if ($form->query) {
            $query->must(new MatchPhrase($queryString, '*'));
        }


        // Выполняем поиск если установлен фильтр или установлен строка поиска
        if ($form->query) {
            $search = $this->index->search($query);
        } else {
            throw new \DomainException('Задан пустой поисковый запрос');
        }

        $search->highlight(
            ['text'],
            [
                'limit' => 0,
                'no_match_size' => 0,
                'pre_tags' => '<mark>',
                'post_tags' => '</mark>'
            ]
        );

        return $search;
    }

    /**
     * @param $queryString String Число или строка чисел через запятую
     * @param string|null $indexName
     * @return Search
     * Поиск по data_id, вопрос или комментарий, число или массив data_id
     */
    public function findByParagraphId(
        string $queryString,
        ?string $indexName = null,
        ?SearchForm $form = null
    ): Search
    {
        $this->search->reset();
        if ($indexName) {
            $this->setIndex($this->client->index($indexName));
        }

        $result = explode(',', $queryString);

        foreach ($result as $key => $item) {
            $item = (int)$item;
            if ($item == 0) {
                unset($result[$key]);
                continue;
            }
            $result[$key] = $item;
        }
        // Запрос переделан под фильтр
        $query = new BoolQuery();

        if (!empty($result)) {
            $query->must(new In('id', array_values($result)));
        } else {
            throw new \DomainException('Неправильный запрос, при поиске по номеру(ам) надо указать номер вопроса или комментария, или перечислить номера через запятую');
        }

        // Выполняем поиск если установлен фильтр или установлен строка поиска
        if ($form->query) {
            $search = $this->index->search($query);
        } else {
            throw new \DomainException('Задан пустой поисковый запрос');
        }

        $search->highlight(
            ['text'],
            [
                'limit' => 0,
                'no_match_size' => 0,
                'pre_tags' => '<mark>',
                'post_tags' => '</mark>'
            ]
        );
        return $search;
    }

    /**
     * @param Index $index
     */
    public function setIndex(Index $index): void
    {
        $this->index = $index;
    }
}
