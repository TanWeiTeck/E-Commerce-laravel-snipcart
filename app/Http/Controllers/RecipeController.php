<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function save(Request $request)
    {
        $recipe = new Recipe;
        $recipe->name = $request->input('name');
        $recipe->size = $request->input('size');
        $recipe->save();
        $items = array_map(function ($item) use ($recipe) {
            return [
                'recipe_id' => $recipe->id,
                'ingredient_id' => $item['id'],
                'quantity' => $item['quantity'],
            ];
        }, $request->input('items'));
        IngredientRecipe::insert($items);
        $ingredients = Recipe::find($recipe->id)
            ->ingredients->map(function ($ingredient) {
                $ingredient->quantity = $ingredient->pivot->quantity;
                return $ingredient;
            });
        $price = $this->calculatePrice($ingredients, $recipe->size);
        return response()
            ->json([
                'id' => $recipe->id,
                'name' => 'Recipe ' . $recipe->name . ' (' . $recipe->size . ')',
                'url' => '/api/recipe/' . $recipe->id,
                'price' => $price,
            ]);
    }

    public function fetch($id)
    {
        $recipe = Recipe::find($id);
        $ingredients = $recipe->ingredients
            ->map(function ($ingredient) {
                $ingredient->quantity = $ingredient->pivot->quantity;
                return $ingredient;
            });

        $price = $this->calculatePrice($ingredients, $recipe->size);
        return response()
            ->json([
                'id' => $recipe->id,
                'name' => 'Recipe ' . $recipe->name . ' (' . $recipe->size . ')',
                'url' => '/api/recipe/' . $recipe->id,
                'price' => $price,
            ]);
    }

    public function preview(Request $request)
    {
        $items = $request->input('items');
        $ingredientIds = array_map(function ($item) {
            return $item['id'];
        }, $items);
        $quantityForId = function ($id) use ($items) {
            for ($i = 0; $i < count($items); $i++) {
                if ($items[$i]['id'] == $id) {
                    return $items[$i]['quantity'];
                }
            }
        };
        $ingredients = Ingredient::whereIn('id', $ingredientIds)
            ->get()
            ->map(function ($ingredient) use ($quantityForId) {
                $ingredient->quantity = $quantityForId($ingredient->id);
                return $ingredient;
            });
        $size = $request->input('size');
        return response()
            ->json([
                'price' => $this->calculatePrice($ingredients, $size),
            ]);
    }
}
