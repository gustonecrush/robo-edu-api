<?php

namespace App\Http\Controllers;

use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Module::with(['user', 'category'])->get();
        return $this->sendResponse(
            ModuleResource::collection($categories),
            'Modules retrieve succussfully!'
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
            'category' => 'required',
            'contributor' => 'required',
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
            $module = new Module();
            $module->name = $request->name;
            $module->category_id = $request->category;
            $module->user_id = $request->contributor;
            $module->save();
        } catch (Exception $e) {
            return $this->sendError(
                'There\'s something error when uploading data!',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse([], 'Module created successfully!');
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
        if (Module::where('id', '=', $id)->exists()) {
            $module = Module::where('id', '=', $id)->first();
            $module->name = is_null($request->name)
                ? $module->name
                : $request->name;
            $module->category_id = is_null($request->category)
                ? $module->category_id
                : $request->category;
            $module->save();

            return $this->sendResponse([], 'Module updated successfully!');
        } else {
            return $this->sendError(
                'Module not found!',
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
        $module = Module::where('id', '=', $id)->first();

        try {
            $module->delete();
        } catch (Exception $e) {
            return $this->sendError(
                "There's something wrong when deleting data!",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse([], 'Module deleted successfully!');
    }
}
