<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class StaticController extends Controller
{
    public function forgot(): View
    {
        return view('auth.forgot');
    }

    public function settings(): View
    {
        return view('settings.index');
    }

    public function notifications(): View
    {
        return view('notifications.index');
    }

    public function messages(): View
    {
        return view('messages.index');
    }

    public function groups(): View
    {
        return view('groups.index');
    }

    public function groupShow(): View
    {
        return view('groups.show');
    }

}
