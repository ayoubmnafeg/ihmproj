<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(): View
    {
        $members = User::with('profile')
            ->where('status', 'active')
            ->latest()
            ->paginate(20);

        return view('members.index', compact('members'));
    }
}
