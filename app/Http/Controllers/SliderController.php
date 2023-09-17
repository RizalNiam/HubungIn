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
}
