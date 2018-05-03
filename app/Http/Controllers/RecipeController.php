<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;


class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $recipes = Recipe::where('user_id', $user->id)->get();

        return response()->json($recipes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            $recipe = new Recipe();

            $recipe->user_id = $user->id;
            $recipe->doctor_name = $request->input('doctor-name');
            $recipe->preparation_name = $request->input('preparation-name');
            $recipe->description = $request->input('description');
            $recipe->image = $user->image;
            $recipe->date = date("Y-m-d H:i:s");
            $recipe->save();
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
        try {
            $recipe = Recipe::find($id);
            if (!empty($recipe)) {
                $recipe->delete();
            }

        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json($e->getSql(), 400);
        }
        return response()->json('', 200);
    }
}
