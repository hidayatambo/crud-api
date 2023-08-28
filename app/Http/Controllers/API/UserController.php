<?php
       
namespace App\Http\Controllers\API;
       
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Validator;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
       
class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $take = $request->input('take', 10);
        $skip = $request->input('skip', 0);
        $search = $request->input('search', '');

        $user = User::where('name', 'like', '%' . $search . '%')
                     ->take($take)
                     ->skip($skip)
                     ->get();
        return $this->sendResponse(UserResource::collection($user), 'User retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
       
        return $this->sendResponse(new UserResource($user), 'Users created successfully.');
    } 
     
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);
      
        if (is_null($user)) {
            return $this->sendError('User not found.');
        }
       
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }
      
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'     => 'required|email|unique:users,email,' . $request->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'status'    => 'error',
                'message'   => 'Validation Error',
                'error'     => $validator->errors()
            ], 422);
        }
        $user = User::where('id', $id)->update([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
        ]);
        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'message'   => 'User update data successfully!',
            'data'   => $user
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user) {
            $user->delete();
            return $this->sendResponse([], 'User soft deleted successfully.');
        } else {
            return $this->sendError('User not found.');
        }
    }
}