<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Consultant;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Validator;

class ConsultantController extends Controller
{
    use ApiResponses;

    public function add_consultant(Request $request) {
        $validator = Validator::make(request()->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'register gagal, silahkan coba kembali');
        }
        
        $consultant = Consultant::where('phone', $request->phone)->first();

        if ($consultant) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Sorry the phone number is already used. Please use a different one');
        }

        $consultant = Consultant::where('fullname', $request->fullname)->first();

        if ($consultant) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Sorry the fullname is already used. Please use a different one');
        }

        if ($request["email"] != NULL){
            $consultant = Consultant::where('email', $request->email)->first();

            if ($consultant) {
                // Jika nomor email sudah terdaftar, kirim response dengan pesan error
                return $this->badRequest('Sorry the email is already used. Please use a different one');
            }
    
        }
        
        $request['password'] = bcrypt($request['password']);
        $consultant = Consultant::create($request->all());


       return $this->requestSuccess('Registration Success', '200');

    }

    public function get_consultants()
    {
        $consultant = auth("api")->user();

        $data = DB::table('consultants')
            ->select('id', 'fullname', 'profile_photo_path', 'phone', 'link_chat', 'email', 'created_at', 'updated_at')
            ->get();


        return $this->requestSuccessData('Get Consultants Success', $data);
    }
}
