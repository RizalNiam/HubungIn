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
 
 
class AuthController extends Controller
{   
    use ApiResponses;

     /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $input = request(['phone', 'password']);
 
        if (! $token = auth('api')->attempt($input)) {
            return response()->json(['error' => 'User fot found, Failed to login'], 401);
        }

        $user = auth('api')->user();
    
        $data = DB::table('users')
            ->select('id', 'username', 'phone', 'email', 'created_at', 'updated_at')
            ->where('users.id', '=', $user->id)
            ->get();
 
        return $this->loginSuccess($data[0], $token);
    }

    public function editpassword(Request $request)
    {	
        if (! $user = auth("api")->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validator = Validator::make(request()->all(), [
            'old_password' => 'required|string|min:8|max:255',
            'password' => 'required|string|same:password|min:8|max:255',
        ]);

	    $user = auth('api')->user();

        $input = [
            'id' => $user->id, 
            'password' => request('old_password')
        ];

        if (!auth("api")->attempt($input)) {
            return response()->json(['message' => 'Password not changed, old password is not valid'], 401);
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|max:255',
        ]);

        if ($validator->fails()) {
            return $this->responseValidation($validator->errors(), 'Password not changed, new password is not valid. (min. 8 character)');
        }

        $request['new_password'] = bcrypt($request['new_password']);        

        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => $request['new_password'],
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