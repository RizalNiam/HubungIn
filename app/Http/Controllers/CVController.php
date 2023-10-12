<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class CVController extends Controller
{
    use ApiResponses;

    public function add_cv(Request $request) {

        $validator = Validator::make(request()->all(), [
            'files' => 'required|file|max:5000|mimes:pdf,docx,doc'
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'job failed to add');
        }

        $link = null;

        if ($request->file('file') != null) {
            $path = $request->file('file')->store('public', 'public');
            $link = "https://magang.crocodic.net/ki/RizalAfifun/HubungIn/storage/app/public/";
            $link .= $path;
        }

        DB::table('jobs')->insert([
            'title' => $request['title'],
        ]);

        return $this->requestSuccess('job successfully added');
    }
}
