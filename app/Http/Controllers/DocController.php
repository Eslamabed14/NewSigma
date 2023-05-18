<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Doctor;
use App\Models\Email;
use App\Models\Partner;
use App\Models\Request as ModelsRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DocController extends Controller
{
    public function index()
    {
        $partners = Partner::latest()->take(3)->get();
        return view('home.home', compact('partners'))->render();
    }

    public function prtners()
    {
        $partners = Partner::all();
        return view('home.partners', compact('partners'));
    }

    public function us()
    {
        return view('home.about_us');
    }

    public function form()
    {
        return view('home.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required|numeric'
        ]);
        $data = $request->all();
        ModelsRequest::create($data);
        return redirect(route('home'))->with('success', 'تم تسجيل بياناتك بنجاح');
    }

    public function doc()
    {
        return view('home.doc');
    }

    public function storeDoc(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'address' => 'required',
            'field' => 'required',
            'logo' => 'required',
            'number' => 'required|numeric',
            'cl_number' => 'required|numeric',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['userType'] = 2;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filepath = 'admin/images/logos/' . date('Y') . '/' . date('m') . '/';
            $filename = $filepath . time() . '-' . $file->getClientOriginalName();
            $file->move($filepath, $filename);
            $data['logo'] = $filename;
        }
        User::create($data);
        return redirect(route('home'))->with('alert', 'تم تسجيل بياناتك وجاري مراجعتها من الإدارة .. سوف يتم ارسال ايميل لك عند الموافقة');
    }

    public function docServ()
    {
        return view('home.docServ');
    }

    public function storeDocServ(Request $request)
    {
        $request->validate([
            'service' => 'required',
            'price' => 'required|numeric',
            'discount' => 'required|numeric',
        ]);
        $data = $request->all();
        $data['doctor_id'] = Auth::user()->id;
        Service::create($data);
        return redirect(route('doc.serv'))->with('success', 'تم إضافة الخدمة بنجاح');
    }

    public function storeEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('please Validate error', $validator->errors());
        }

        $data = $request->all();
        Email::create($data);
        return redirect(route('home'))->with('success', 'تم التسجيل في الخدمة بنجاح');
    }

    public function articles()
    {
        $articles = Article::paginate(8);
        return view('home.articles', compact('articles'));
    }

    public function article($id)
    {
        $articles = Article::latest()->take(4)->get();
        $partners = Partner::latest()->take(3)->get();
        $article = Article::find($id);
        return view('home.article', compact('article', 'articles', 'partners'));
    }
}
