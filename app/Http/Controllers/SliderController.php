<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class SliderController extends Controller
{
    use ApiResponses;

    public function get_img_slider()
    {
        $user = auth("api")->user();

        $rawData = DB::table('sliders')
        ->select('id', 'photo')
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    public function add_sliders(Request $request) {
        $validator = Validator::make(request()->all(), [
            'photo' => 'image|file',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'image failed to add');
        }

        $link = null;

        if ($request->file('photo') != null) {
            $path = $request->file('photo')->store('public', 'public');
            $link = "https://magang.crocodic.net/ki/RizalAfifun/HubungIn/storage/app/public/";
            $link .= $path;
        }

        DB::table('sliders')->insert([
            'photo' => $link
        ]);

        return $this->requestSuccess('image successfully added');
    }
}
