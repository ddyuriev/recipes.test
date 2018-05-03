<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Misc;
use App\Http\Controllers\Utils\PasswordHasher;
use App\User;
use Illuminate\Http\Request;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $expected = [
            'login', 'password', 'name', 'address', 'image'
        ];

        foreach ($expected as $item) {
            if (empty($data[$item])) {
                return response()->json('The required field was not found: ' . $item, 400);
            }
        }

        $path = base_path('/public') . '/uploaded/img';

        if ($data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $filename = Misc::generateApiToken($data['name']);
            $ext = $request->image->getClientOriginalExtension();
            $filenameWithExt = $filename . '.' . $ext;
            $request->image->move($path, $filenameWithExt);
        }

        try {
            $user = new User();

            foreach ($data as $key => $item) {
                if ($key != 'image' && $key != 'password') {
                    $user->{$key} = $item;
                }
            }
            $hasher = new PasswordHasher();
            $user->password = $hasher->hash($data['password']);
            $user->api_token = Misc::generateApiToken($data['login']);
            $user->token_created_at = date("Y-m-d H:i:s");
            $user->image = $filenameWithExt;
            $user->save();

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json($e->getSql(), 400);
        }

        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $user = User::getUserByLogin($data['login']);

        if (!empty($user) && $user['password']) {
            $hasher = new PasswordHasher();
            $checkPassword = $hasher->check($data['password'], $user->password);
            if ($checkPassword) {
                //обновляем токен
                $newToken = Misc::generateApiToken($data['login']);
                $user->api_token = $newToken;
                $user->token_created_at = date("Y-m-d H:i:s");
                $user->save();

                return response()->json(['api_token' => $newToken], 200);
            }
        }

        return response()->json('authentication error', 401);

    }
}
