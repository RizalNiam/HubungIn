<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class RatingController extends Controller
{
    use ApiResponses;

    public function add_rating(Request $request) {
        $validator = Validator::make(request()->all(), [
            'job_id' => 'required|numeric|max:255',
            'rating' => 'required|numeric|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'rating failed to add');
        }


        $user = auth('api')->user();

        DB::table('ratings')->insert([
            'job_id' => $request['job_id'],
            'rating' => $request['rating'],
            'user_id' => $user->id,
            'user_name' => $user->username,
        ]);

        return $this->requestSuccess('rating successfully added');
    }

    function get_rating(Request $request) {
        auth('api')->user();    

        $total = DB::table('ratings')
        ->where('job_id', $_GET['job_id'])
        ->sum('rating');

        $many_review = DB::table('ratings')
        ->where('job_id', $_GET['job_id'])
        ->count('rating');

        $rata2 = $total / $many_review;    
        
        return number_format($rata2, 1, ',', '.'); 
            
    }
}
