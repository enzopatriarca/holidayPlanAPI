<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller{
    

    public function List_User(){

        $users = User::select('id','name')->get();
        return response()->json(['users' => $users], 200);
    }
}
