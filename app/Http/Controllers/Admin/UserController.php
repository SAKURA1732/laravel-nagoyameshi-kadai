<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
      $keyword = $request->input('keyword');
      if($keyword != null){
        $users = User::where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('kana', 'LIKE', "%{$keyword}%")
            ->paginate(15);
        $total = User::where('name', 'like', "%{$keyword}%")->count();
      }else{
        $users = User::paginate(15);
        $total = User::all()->count();
      }

      return view('admin.users.index', compact('keyword', 'users', 'total'));
    }


    public function show(User $user)
    {
      return view('admin.users.show', compact('user'));
    }

    public function login(Request $request,$user)
    {
        session()->flash('successMessage','ログインしました');
        return redirect('/');
    }
}