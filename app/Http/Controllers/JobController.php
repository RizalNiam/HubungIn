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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2048',
            'pt' => 'required|string|max:2048',
            'education' => 'required|string|max:2048',
            'salary' => 'string|max:255',
            'photo' => 'required|image|file',
            'province' => 'required|string|max:2048',
            'category_id' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'job failed to add');
        }

        $link = null;
        $salary = "IDR ";
        $salary .= number_format($request['salary'], 2, ",", ".");

        if ($request->file('photo') != null) {
            $path = $request->file('photo')->store('public', 'public');
            $link = "https://magang.crocodic.net/ki/RizalAfifun/HubungIn/storage/app/public/";
            $link .= $path;
        }

        DB::table('jobs')->insert([
            'title' => $request['title'],
            'pt' => $request['pt'],
            'salary' => $salary,
            'description' => $request['description'],
            'category_id' => $request['category_id'],
            'education' => $request['education'],
            'photo' => $link,
            'province' => $request['province'],
        ]);

        return $this->requestSuccess('job successfully added');
    }

    function get_jobs() {

        $user = auth("api")->user();

        // $rawData = DB::table('jobs')
        // ->join('saves', 'jobs.id', '=', 'saves.job_id')
        // ->select('saves.job_id as favorited', 'jobs.title as title', 'jobs.pt as pt','jobs.description as description', 'jobs.photo as photo', 'jobs.education as education', 'jobs.salary as salary', 'jobs.province as province', 'jobs.category_id as category_id', 'jobs.created_at as created_at', 'jobs.updated_at as updated_at')
        // ->where('saves.user_id', '=', $user->id)
        // ->get(); 

        // $saved = DB::table('saves')
        // ->select('*')
        // ->inRandomOrder()
        // ->get(); 

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

    function filter(Request $request) {

        $user = auth("api")->user();

        $province = $_GET['province'];

        $rawData = DB::table('jobs')
        ->select('*')
        ->where('province', $province)
        ->get(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    function delete_job(Request $request) {

        $job = auth("api")->user();

        DB::table('jobs')->where('id', $job->id)->delete();
        
        return $this->requestSuccess('the job has been deleted!');
    }

    public function find_jobs()
	{
	$keyword = $_GET['keyword'];

	$user = auth("api")->user();
    
        $notes = DB::table('jobs')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->orWhere('province', 'like', '%' . $keyword . '%')
            ->get();

	return $this->requestSuccessData('Success!', $notes);	
	}

}
