<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class JobController extends Controller
{
    use ApiResponses;

    public function add_job(Request $request) {
        $validator = Validator::make(request()->all(), [
            'title' => 'require|string|max:255',
            'description' => 'require|string|max:2048',
            'education' => 'require|string|max:2048',
            'salary' => 'string|max:255',
            'photo' => 'require|image|file',
            'province' => 'require|string|max:2048',
            'category_id' => 'require|string|max:255',
            'creator_id' => 'require|integer'
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'job failed to add');
        }

        $link = null;

        if ($request->file('photo') != null) {
            $path = $request->file('photo')->store('public', 'public');
            $link = "https://magang.crocodic.net/ki/RizalAfifun/HubungIn/storage/app/public/";
            $link .= $path;
        }

        DB::table('jobs')->insert([
            'title' => $request['title'],
            'salary' => $request['salary'],
            'description' => $request['description'],
            'education' => $request['education'],
            'photo' => $link,
            'province' => $request['province'],
            'creator_id' => $request['creator_id'],
        ]);

        return $this->requestSuccess('job successfully added');
    }

    function get_jobs() {

        $user = auth("api")->user();

        $rawData = DB::table('jobs')
        ->select('*')
        ->inRandomOrder()
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function get_education_jobs_desc() {

        $user = auth("api")->user();

        $rawData = DB::table('jobs')
        ->select('*')
        ->orderBy('education','DESC')
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function get_education_jobs_asc() {

        $user = auth("api")->user();

        $rawData = DB::table('jobs')
        ->select('*')
        ->orderBy('education','ASC')
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function filter_location_jobs(Request $request) {

        $user = auth("api")->user();

        $rawData = DB::table('jobs')
        ->select('*')
        ->where('province', $request['province'])
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    public function find_jobs()
	{
	$keyword = $_POST['keyword'];

	$user = auth("api")->user();
    
        $notes = DB::table('jobs')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->orWhere('province', 'like', '%' . $keyword . '%')
            ->get();

	return $this->requestSuccessData('Success!', $notes);	
	}

}
