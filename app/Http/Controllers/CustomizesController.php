<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Customize;
use App\CounterPage;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image as Image;

class CustomizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if(!$this->islogged())
        {
            return redirect('main');
        }
        if (empty(session('id'))) {
            return redirect('session');
        }
        /*$keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $customizes = Customize::where('theme', 'LIKE', "%$keyword%")
				->orWhere('imagepath', 'LIKE', "%$keyword%")
				->paginate($perPage);
        } else {
            $customizes = Customize::paginate($perPage);
        }

        return view('customizes.index', compact('customizes'));*/
        if (!empty(session('customize_id'))) {
            $cant = $this->contarfuncion('/customizes/create');
            $customizes = Customize::findOrFail(session('customize_id'));
            return view('customizes.edit', compact('customizes','cant'));
        }
        $cant = $this->contarfuncion('/customizes/edit'); 
        $customizes = new Customize;
        return view('customizes.create', compact('customizes','cant'));
    }

    public function contarfuncion($funcion)
    {
        $accions = CounterPage::where('pageroute',$funcion)->get();
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $customizes = new Customize;
        return view('customizes.create', compact('customizes'));
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $filename = $this->postNewImage($request);
        
        $requestData = $request->all();
        $requestData['imagepath'] = $filename;
        $customize = new Customize;
        $customize->theme = $requestData['theme'];
        $customize->imagepath = $requestData['imagepath'];
        $customize->user_id = $requestData['user_id'];
        $customize->save();
        $request->session()->put('customize_id', $customize->id);
        $request->session()->put('theme', $customize->theme);
        $request->session()->put('imagepath', $customize->imagepath);

        Session::flash('flash_message', 'Customize added!');

        return redirect('customizes');
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $customize = Customize::findOrFail($id);

        return view('customizes.show', compact('customize'));
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $customizes = Customize::findOrFail($id);

        return view('customizes.edit', compact('customizes'));
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $requestData = $request->all();
        if (empty($requestData['imagepath'])) {
            $requestData['imagepath'] = $requestData['filename'];
        } else {
            $fileName = $this->postNewImage($request);
            $requestData['imagepath'] = $fileName;
            unlink('img/users/'. $requestData['filename']);
        }

        $customizes = Customize::findOrFail($id);
        $customizes->update($requestData);
        $request->session()->put('theme', $customizes->theme);
        $request->session()->put('imagepath', $customizes->imagepath);

        Session::flash('flash_message', 'Customize updated!');

        return redirect('customizes');
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
        if(!$this->islogged())
        {
            return redirect('main');
        }
        Customize::destroy($id);

        Session::flash('flash_message', 'Customize deleted!');

        return redirect('customizes');
    }

    public function postNewImage(Request $request)
    {
        if(!$this->islogged())
        {
            return redirect('main');
        }
        $this->validate($request, ['imagepath' => 'required|image']);
        $filename = session('id'). $request->file('imagepath')->getClientOriginalName();
        Image::make($request->file('imagepath'))->resize(200, 200)->save('img/users/'. $filename);

        return $filename;
    }
}
