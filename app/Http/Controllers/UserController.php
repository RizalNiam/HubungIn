<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use SebastianBergmann\Type\NullType;    
use Validator;
 
 
class UserController extends Controller
{   
    use ApiResponses;
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'role_id' => 'required|integer|max:255',
            'password' => 'required|string|min:8|max:255',
            'confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);
        
        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'register gagal, silahkan coba kembali');
        }
        
        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Sorry the phone number is already used. Please use a different one');
        }

        $user = User::where('username', $request->username)->first();

        if ($user) {
            // Jika nomor telepon sudah terdaftar, kirim response dengan pesan error
            return $this->badRequest('Sorry the username number is already used. Please use a different one');
        }

        if ($request["email"] != NULL){
            $user = User::where('email', $request->email)->first();

            if ($user) {
                // Jika nomor email sudah terdaftar, kirim response dengan pesan error
                return $this->badRequest('Sorry the email is already used. Please use a different one');
            }
    
        }
        
        $request['password'] = bcrypt($request['password']);
        $user = User::create($request->all());


       return $this->requestSuccess('Registration Success', '200');

    }

    public function getprofile()
    {
        if (! $user = auth("api")->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth("api")->user();

        $rawData = DB::table('users')
        ->select('id', 'username', 'phone', 'profile_photo_path', 'email', 'created_at', 'updated_at')
        ->where('users.id', '=', $user->id)
        ->first(); 
        
        return $this->requestSuccessData('Success!', $rawData);
    }

    public function editprofile(Request $request)
    {
        if (! $user = auth("api")->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'photo' => 'image|file|max:10240'
        ]);

        // get user's id
        $user = auth('api')->user();

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'Sorry data failed to edit, Please try again');
        }

        // hapus foto sebelumnya terlebih dulu, jika ada
        $this->delete_image();

        if($request['photo'] != null){
            $path = $request->file('photo')->store('public', 'public');
            $link = "https://magang.crocodic.net/ki/RizalAfifun/HubungIn/storage/app/public/";
            $link .= $path;

            DB::table('users')
            ->where('id', $user->id)
            ->update([
                'username' => $request['username'],
                'profile_photo_path' => $link
            ]);
        } else{
            DB::table('users')
            ->where('id', $user->id)
            ->update([
                'username' => $request['username'],
                'profile_photo_path' => null
            ]);
        } 

        $rawData = DB::table('users')
        ->select('id', 'username', 'profile_photo_path', 'phone', 'email', 'created_at', 'updated_at')
        ->where('id', '=', $user->id)
        ->first();

        return $this->requestSuccessData('Edit Profile Success', $rawData);
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
 
    public function editpassword(Request $request)
    {	
        if (! $user = auth("api")->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make(request()->all(), [
            'Old_password' => 'required|string|min:8|max:255',
            'New_password' => 'required|string|same:password|min:8|max:255',
            'Confirm_password' => 'required|string|same:password|min:8|max:255',
        ]);

	    $user = auth('api')->user();

        $input = [
            'id' => $user->id, 
            'New_password' => request('Old_password')
        ];

        if (!auth("api")->attempt($input)) {
            return response()->json(['message' => 'Password not changed, old password is not valid'], 401);
        }

        $validator = Validator::make($request->all(), [
            'New_password' => 'required|string|min:8|max:255',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'Password not changed, new password is not valid. (min. 8 character)');
        }

        $request['New_password'] = bcrypt($request['New_password']);        

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => $request['New_password'],
            ]);

        return $this->requestSuccess('Edit Password Success');
    }
 
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
 
        return response()->json(['message' => 'Successfully logged out']);
    }
 
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }
 
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}