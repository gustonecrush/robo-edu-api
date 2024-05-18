<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

use function Laravel\Prompts\error;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return $this->sendResponse(
            CategoryResource::collection($categories),
            'Categories retrieve succussfully!'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error on your input',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $category = new Category();
            $category->name = $request->name;
            $category->save();
        } catch (Exception $e) {
            return $this->sendError(
                'There\'s something error when uploading data!',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse([], 'Category created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (Category::where('id', '=', $id)->exists()) {
            $category = Category::where('id', '=', $id)->first();
            $category->name = is_null($request->name)
                ? $category->name
                : $request->name;
            $category->save();

            return $this->sendResponse([], 'Category updated successfully!');
        } else {
            return $this->sendError(
                'Category not found!',
                [],
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::where('id', '=', $id)->first();

        try {
            $category->delete();
        } catch (Exception $e) {
            return $this->sendError(
                "There's something wrong when deleting data!",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse([], 'Category deleted successfully!');
    }
}
