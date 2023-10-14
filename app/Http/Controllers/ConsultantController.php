<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function get_consultant()
    {
        $user = auth("api")->user();

        $data = DB::table('users')
            ->where('users.role_id', '=', 3)
            ->select('id', 'username', 'profile_photo_path', 'phone', 'email', 'created_at', 'updated_at')
            ->get();


        return $this->requestSuccessData('Get Consultant Success', $data);
    }
}
