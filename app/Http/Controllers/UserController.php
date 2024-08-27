<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    //
        function showAllUsers(){
        $do = isset($_GET['do']) ? $do = $_GET['do'] : 'Manage';
        $users = User::with('profile')->get();
        return view('manageUsers', [
            'users'      => $users,
            'do'        => $do,
        ]);
    }

}
