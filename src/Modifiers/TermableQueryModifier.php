<?php

namespace Belt\Elastic\Modifiers;

use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Elastic\Modifiers\PaginationQueryModifier;
use Belt\Content\Term;

class TermableQueryModifier extends PaginationQueryModifier
{


    /**
     * @var Term
     */
    public $terms;

    /**
     * @return Term
     */
    public function terms()
    {
        return $this->terms ?: $this->terms = new Term();
    }

    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {
        $params = $this->params($request);

        $this->filter($params);
        $this->query($params);
    }

    /**
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function find($ids)
    {
        $terms = $this->terms()
            ->newQuery()
            ->whereIn('id', $ids)
            ->orWhereIn('slug', $ids)
            ->get(['terms.id', 'terms._lft', 'terms._rgt']);

        return $terms;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function params($request)
    {
        $params['query'] = [];
        $params['filter'] = [];

        if ($value = $request->get('term')) {

            $sets = explode(',', $value);
            foreach ($sets as $n => $set) {

                $filtered = substr($set, 0, 1) == '~' ? false : true;
                $set = str_replace(['~', ' '], ['', '+'], $set);
                $ids = explode('+', $set);

                foreach ($ids as $id) {
                    $terms = $this->find([$id]);
                    $list = [];
                    foreach ($terms as $term) {
                        $list[] = $term->id;
                        $list = array_merge($list, $term->descendants->pluck('id')->all());
                    }
                    if ($list) {
                        if ($filtered) {
                            $params['filter'][$n][] = $list;
                        } else {
                            $params['query'][$n][] = $list;
                        }
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @param $params
     */
    public function filter($params)
    {
        $groups = array_get($params, 'filter', []);

        if ($groups) {

            $filter = [];
            $filters = [];

            foreach ($groups as $group) {
                $filter['bool']['must'] = [];
                foreach ($group as $_group) {
                    $filter['bool']['must'][] = ['terms' => ['terms' => $_group]];
                }
                $filters[] = $filter;
            }
            if ($filters) {
                $this->engine->filter[]['bool']['should'] = $filters;
            }
        }
    }

    /**
     * @param $params
     */
    public function query($params)
    {
        $groups = array_get($params, 'query', []);

        if ($groups) {

            $query = [];
            $queries = [];

            foreach ($groups as $params) {
                $query['bool']['must'] = [];
                foreach ($params as $group) {
                    $query['bool']['must'][] = ['terms' => ['terms' => $group, 'boost' => 1]];
                }
                $queries[] = $query;
            }
            if ($queries) {
                $this->engine->query['bool']['should'][] = $queries;
            }
        }
    }
}