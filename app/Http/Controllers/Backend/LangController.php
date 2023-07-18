<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App;

class LangController extends Controller
{
    /**
 
     * Display a listing of the resource.
 
     *
 
     * @return \Illuminate\Http\Response
 
    */
 
    public function change()
    {
        if (App::getLocale() == 'en') {
            $lang = 'ar';
        }else {
            $lang = 'en';
        }
        App::setLocale($lang);
 
        session()->put('locale', $lang);
 
        return redirect()->back();
 
    }
}
