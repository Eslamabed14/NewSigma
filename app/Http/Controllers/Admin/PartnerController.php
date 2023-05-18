<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Partner;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Carbon::setLocale('ar');
        $partners = Partner::all();
        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::all();
        return view('admin.partners.add', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required',
            'discount' => 'required|numeric',
            'city_id' => 'required'
        ]);
        $data = $request->all();
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filepath = 'admin/images/pertners/' . date('Y') . '/' . date('m') . '/';
            $filename = $filepath . time() . '-' . $file->getClientOriginalName();
            $file->move($filepath, $filename);
            $data['image'] = $filename;
        }
        Partner::create($data);
        return redirect(route('admin.partners'))->with('success', 'تم اضافة الشريك بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = Partner::find($id);
        $cities = City::all();
        return view('admin.partners.edit', compact('partner', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $partner = Partner::find($id);
        $request->validate([
            'name' => 'required',
            'discount' => 'required|numeric',
            'city_id' => 'required'
        ]);
        $data = $request->except(['old-image']);

        if($request->hasfile('image')){
            $file = $request->file('image');
            $filepath = 'admin/images/pertners/'.date('Y').'/'.date('m').'/';
            $filename =$filepath.time().'-'.$file->getClientOriginalName();
            $file->move($filepath, $filename);
            if(request('old-image')){
                $oldpath=request('old-image');
                if(File::exists($oldpath)){
                    unlink($oldpath);
                }

            }
          $data['image'] = $filename;
        }
        $partner->update($data);
        return redirect()->back()->with('success', 'تم تعديل الشريك بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $partner = Partner::find($id);
        $partner->delete();
        return redirect(route('admin.partners'))->with('success', 'تم حذف الشريك بنجاح');
    }
}
