<?php

namespace App\Traits;

use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

const EXPLODE_SYMBOL = ',';
const SEPARATOR_SYMBOL = ':';

const SORT_COLUMN = 0;
const SORT_DIRECTION = 1;

Trait Searchable {

    protected function getFilters(Builder $model, array $filters): Builder
    {
        foreach($filters as $filter) {
            if(isset($filter['pivot'])) {
                if($filter['multiply']) {
                    $values = explode(EXPLODE_SYMBOL, $filter['value']);
                    if(isset($filter['operator']) && $filter['operator'] == 'in')
                        $model->whereHas($filter['pivot'], fn(Builder $query) => $query->whereIn($filter['pivot'] . '.' . $filter['field'], $values));
                    else {
                        foreach ($values as $value)
                            $model->whereHas($filter['pivot'], fn(Builder $query) => $query->where($filter['pivot'] . '.' . $filter['field'], $filter['operator'] ?? '=', $value));
                    }
                } else
                    $model->whereHas($filter['pivot'], fn(Builder $query) => $query->where($filter['pivot'].'.'.$filter['field'], $filter['operator'] ?? '=', $filter['value']));
            } else {
                if($filter['multiply']) {
                    $values = explode(EXPLODE_SYMBOL, $filter['value']);
                    if(isset($filter['operator']) && $filter['operator'] == 'in')
                        $model->whereIn($filter['field'], $values);
                    else {
                        foreach ($values as $value) {
                            $model->where($filter['field'], $filter['operator'] ?? '=', $value);
                        }
                    }
                } else
                    $model->where($filter['field'], $filter['operator'] ?? '=', $filter['value']);
            }
        }
        return $model;
    }

    protected function getSort(Builder $query, array $queryParams): Builder
    {
        if(!isset($queryParams['sort']))
            return $query;

        $sorters = explode(EXPLODE_SYMBOL, $queryParams['sort']);
        foreach($sorters as $sorter) {
            $data = explode(SEPARATOR_SYMBOL, $sorter);

            if(!isset($data[SORT_DIRECTION]))
                $data[SORT_DIRECTION] = 'desc';

            $query->orderBy($data[SORT_COLUMN], ($data[SORT_DIRECTION] == 'desc' ? 'desc' : 'asc'));
        }
        return $query;
    }

    protected function getLimit(array $queryParams) {
        return $queryParams['limit'] ?? -1;
    }

    protected function find(array $queryParams, string $modelName, array $filters): Collection {

        $model = $modelName::query();

        $this->getFilters($model, $filters);
        $this->getSort($model, $queryParams);
        $model->limit($this->getLimit($queryParams));

        return $model->get();
    }
}
