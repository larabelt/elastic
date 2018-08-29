<?php

namespace Belt\Elastic\Modifiers;

use Belt;
use Belt\Core\Helpers;
use Belt\Core\Http\Requests\PaginateRequest;
use Belt\Content\Term;

class TermableSortModifier extends Belt\Elastic\Modifiers\PaginationQueryModifier
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
     * @param $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function find($ids)
    {
        $terms = $this->terms()
            ->newQuery()
            ->whereIn('id', (array) $ids)
            ->orWhereIn('slug', (array) $ids)
            ->get(['terms.id']);

        return $terms;
    }

    /**
     * Modify the query
     *
     * @param  PaginateRequest $request
     * @return $query
     */
    public function modify(PaginateRequest $request)
    {

        if ($orderBy = $request->get('orderBy')) {
            $sortHelper = new Helpers\SortHelper($orderBy);
            if ($sort = $sortHelper->getByColumn('term')) {
                $terms = $this->find($sort->params);
                if ($terms) {
                    $this->engine->sort['_script'] = [
                        'type' => 'number',
                        'order' => 'desc',
                        'script' => [
                            'lang' => 'painless',
                            'inline' => "
                                int category;
                                int count = 0; 
                                for(int i=0; i < doc['categories'].length; i++) { 
                                    category = (int) (long) doc['categories'][i];
                                    if (params.categories.indexOf(category) > -1) {
                                        count = count + 1;
                                    } 
                                } 
                                return count;
                            ",
                            'params' => [
                                'categories' => $terms->pluck('id')->all(),
                            ],
                        ]
                    ];
                }
            }
        }
    }
}