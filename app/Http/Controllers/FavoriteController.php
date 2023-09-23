<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class FavoriteController extends Controller
{
    use ApiResponses;

    public function add_favortie(Request $request) {
        $validator = Validator::make(request()->all(), [
            'book_id' => 'required|numeric|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'book failed to add');
        }

        $user = auth('api')->user();

        DB::table('favorites')->insert([
            'book_id' => $request['book_id'],
            'user_id' => $user->id,
        ]);

        return $this->requestSuccess('book successfully added');
    }
}
