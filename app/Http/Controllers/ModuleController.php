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
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => ['index'],
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if 'user_id' query param is present
        $query = Module::with(['user', 'category']);

        if ($request->has('user_id')) {
            // Filter modules by 'user_id'
            $query->where('user_id', $request->user_id);
        }

        // Get the result
        $modules = $query->get();

        return $this->sendResponse(
            ModuleResource::collection($modules),
            'Modules retrieved successfully!'
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
        // Validate input data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category' => 'required',
            'contributor' => 'required',
            'file' => 'nullable|file|mimes:jpg,png,pdf', // Validation for file input
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

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('modules', $filename); // Store file in 'storage/app/public/modules'
                $module->file = $filename; // Save the file path in the database
            }

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
        // Check if the module exists
        if (Module::where('id', '=', $id)->exists()) {
            $module = Module::find($id);

            // Update module fields
            $module->name = $request->name ?? $module->name;
            $module->category_id = $request->category ?? $module->category_id;

            // Handle file upload if a new file is provided
            if ($request->hasFile('file')) {
                // Delete the old file if it exists
                if ($module->file_path) {
                    $oldFilePath = storage_path('app/public/modules/' . $module->file_path);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Remove the old file
                    }
                }

                // Store the new file
                $file = $request->file('file');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('modules', $filename); // Store file in 'storage/app/public/modules'
                $module->file = $filename; // Update the file path in the database
            }

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
        // Retrieve the module record
        $module = Module::find($id);

        if (!$module) {
            return $this->sendError(
                'Module not found',
                'The specified module does not exist.',
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            // Delete the associated file if it exists
            if ($module->file_path) {
                $filePath = storage_path('app/public/modules/' . $module->file_path);

                if (file_exists($filePath)) {
                    unlink($filePath); // Remove the file from storage
                }
            }

            // Delete the module record
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