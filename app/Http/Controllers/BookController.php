<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class BookController extends Controller
{
    use ApiResponses;

    public function addbook(Request $request) {
        $validator = Validator::make(request()->all(), [
            'title' => 'string|max:255',
            'price' => 'string|max:255',
            'description' => 'string|max:2048',
            'photo' => 'image|file|max:10240',
            'category' => 'string|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'book failed to add');
        }

        $path = $request->file('photo')->store('public', 'public');
        $link = "https://magang.crocodic.net/ki/RizalAfifun/EcommerceApp/storage/app/public/";
        $link .= $path;

        DB::table('books')->insert([
            'title' => $request['title'],
            'price' => $request['price'],
            'description' => $request['description'],
            'photo' => $link,
            'category' => $request['category'],
        ]);

        return $this->requestSuccess('book successfully added');
    }

    function get_children_destinations() {
        $user = auth("api")->user();

        $rawData = DB::table('destinations')
        ->select('id', 'name', 'address', 'description', 'photo', 'category', 'budget', 'created_at', 'updated_at')
        ->where('category', '=', 'children')
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function get_nature_destinations() {
        $user = auth("api")->user();

        $rawData = DB::table('destinations')
        ->select('id', 'name', 'address', 'description', 'photo', 'category', 'budget', 'created_at', 'updated_at')
        ->where('category', '=', 'nature')
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function get_all_destinations() {
        $destination = auth("api")->user();

        $rawData = DB::table('destinations')
        ->select('id', 'name', 'address', 'description', 'photo', 'category', 'budget', 'created_at', 'updated_at')
        ->inRandomOrder()
        ->get();
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    public function delete_image()
    {
        $user = auth('api')->user();

        $file = storage_path('/app/public/public') . $user->photo;

        if (file_exists($file)) {
            @unlink($file);
        }

        $user->delete;
    }
}
