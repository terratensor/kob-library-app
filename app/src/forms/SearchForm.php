<?php

declare(strict_types=1);

namespace src\forms;

use yii\base\Model;

class SearchForm extends Model
{
    public string $query = '';
    public string $matching = 'query_string';
    public bool $dictionary = false;

    public function rules(): array
    {
        return [
            ['query', 'string'],
            ['dictionary', 'boolean']
        ];
    }

    public function getMatching(): array
    {
        return [
            'query_string' => 'По умолчанию',
            'match_phrase' => 'По соответствию фразе',
            'match' => 'По совпадению слов',
            'in' => 'По номеру(ам) комментария или вопроса, номера через запятую',
        ];
    }

    public function formName(): string
    {
        return 'search';
    }
}
