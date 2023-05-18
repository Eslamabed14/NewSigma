<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        Carbon::setLocale('ar');
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        $service->delete();
        return redirect(route('admin.services'))->with('success', 'تم حذف الخدمة بنجاح');
    }
}
