<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Recipe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $recipes = Recipe::select('id', 'name', 'description', 'ingredients', 'steps', 'time')
                ->paginate(15);
            $recipes->appends(request()->query());

            return response()->json($recipes);
        } catch (QueryException $e) {
            // Handle database query exception
            return response()->json(['error' => 'Failed to retrieve recipes. Database error.'], 500);
        } catch (\Exception $e) {
            // Handle general exception
            return response()->json(['error' => 'Failed to retrieve recipes.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'ingredients' => 'required|string',
            'steps' => 'required|string',
            'time' => 'required|string',
        ]);

        $recipe = Recipe::create($data);

        return response()->json($recipe, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        try {
            return response()->json(['data' => $recipe]);
        } catch (ModelNotFoundException $e) {
            // Handle model not found exception
            return response()->json(['error' => 'Recipe not found'], 404);
        } catch (\Exception $e) {
            // Handle general exception
            return response()->json(['error' => 'Failed to retrieve recipe.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $recipe = Recipe::findOrFail($id);

            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'ingredients' => 'sometimes|string',
                'steps' => 'sometimes|string',
                'time' => 'sometimes|string',
            ]);

            $recipe->update($data);

            return response()->json(['message' => 'Recipe updated successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Recipe not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating recipe'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $recipe = Recipe::findOrFail($id);
            $recipe->delete();
            return response()->json(['message' => 'Recipe deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Recipe not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting recipe'], 500);
        }
    }
}
