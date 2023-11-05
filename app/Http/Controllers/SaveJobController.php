<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Validator;

class SaveJobController extends Controller
{
    use ApiResponses;

    public function save_job(Request $request) {
        $user = auth("api")->user();

        DB::table('saves')->insert([
            'user_id' => $user->id,
            'job_id' => $request->job_id,
        ]);

        return $this->requestSuccess('job successfully saved');
    }

    public function unset_job(Request $request) {
        $user = auth("api")->user();

        DB::table('saves')
        ->where('job_id', $request->job_id)
        ->where('user_id', $user->id)
        ->delete();

        return $this->requestSuccess('job successfully unsaved');
    }

    function get_saved_jobs() {
        $user = auth("api")->user();

        $rawData = DB::table('saves')
        ->select('*')
        ->where('saves.user_id', '=', $user->id)
        ->get();      
        
        return $this->requestSuccessData('Success!', $rawData);
    }
}
