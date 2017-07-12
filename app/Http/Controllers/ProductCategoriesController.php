<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

use App\ProductCategory;
use App\CounterPage;
use Illuminate\Http\Request;
use Session;

class ProductCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index($accion,Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        $cant = $this->contarindex($accion); 
        if (!empty($keyword)) {
            $productcategories = ProductCategory::where('name', 'LIKE', "%$keyword%")
				->paginate($perPage);
        } else {
            $productcategories = ProductCategory::paginate($perPage);
        }
        return view('product-categories.'.$accion, compact('productcategories','cant'));
    }

    public function contarindex($accion)
    {
        $cant = 0;
        $accions = CounterPage::where('pageroute','/product-categories/index/'.$accion)->get();
        $accion = $accions->first();
        $cant = $accion->visitcount;
        $cant++;
        $accion->visitcount = $cant;
        $accion->save();
        return $cant;
    }

    public function contarfuncion($funcion)
    {
        $rutinga = '/product-categories/'.$funcion;
        $accions = CounterPage::where('pageroute',$rutinga)->get();
        $accion = $accions->first();
        $cant = $accion->visitcount;
        $cant++;
        $accion->visitcount = $cant;
        $accion->save();
        return $cant;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $productcategory = new ProductCategory;
        $cant = $this->contarfuncion('create');        
        return view('product-categories.create',compact('productcategory','cant'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        ProductCategory::create($requestData);

        Session::flash('flash_message', 'ProductCategory added!');
        return redirect('product-categories/index/index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $productcategory = ProductCategory::findOrFail($id);

        return view('product-categories.show', compact('productcategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $productcategory = ProductCategory::findOrFail($id);
        $cant = $this->contarfuncion('edit');
        return view('product-categories.edit', compact('productcategory','cant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        
        $requestData = $request->all();
        
        $productcategory = ProductCategory::findOrFail($id);
        $productcategory->update($requestData);

        Session::flash('flash_message', 'ProductCategory updated!');
        
        return redirect('product-categories/index/indexedit');
    }

    public function trash()
    {
        $productcategories = ProductCategory::intrash();
        $cant = $this->contarfuncion('trash');        
        return view('product-categories.trash', compact('productcategories','cant'));
    }

    public function restore($id)
    {
        $product = ProductCategory::withTrashed()->find($id);
        $product->restore();
        return redirect('product-categories/trash');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        ProductCategory::destroy($id);

        Session::flash('flash_message', 'ProductCategory deleted!');

        return Redirect::back();
    }
}
