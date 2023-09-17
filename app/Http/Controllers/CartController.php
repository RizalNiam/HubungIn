<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class CartController extends Controller
{
    use ApiResponses;

    public function addcart(Request $request) {
        $validator = Validator::make(request()->all(), [
            'book_id' => 'required|numeric|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'book failed to add');
        }

        $user = auth('api')->user();

        DB::table('carts')->insert([
            'book_id' => $request['book_id'],
            'user_id' => $user->id,
        ]);

        return $this->requestSuccess('book successfully added');
    }
}
