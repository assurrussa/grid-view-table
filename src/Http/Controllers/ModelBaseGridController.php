<?php

namespace Assurrussa\GridView\Http\Controllers;

use Assurrussa\GridView\GridView;
use Assurrussa\GridView\Models\Model;
use Illuminate\Routing\Controller as BaseController;
use Request;

class ModelBaseGridController extends BaseController
{

    /** @var GridView */
    protected $gridView;

    /**
     * ModelBaseGridController constructor.
     *
     * @param GridView $gridView
     */
    public function __construct(GridView $gridView)
    {
        $this->gridView = $gridView;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $header = GridView::trans('grid.general');
        return GridView::view(config('amigrid.pathView'), compact('header'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param string $model
     * @return array
     */
    public function sync($model, $scope = null)
    {
        /** @var Model $modelName */
        $modelName = config('amigrid.namespace') . str_singular(ucfirst($model));
        $query = $modelName::query();
        if($scope) {
            $query->where(function ($query) use ($scope) {
                $query->$scope();
            });
        }
        return $this->gridView->setQuery($query)->get();
    }
}
