<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
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
    public function index()
    {
        $videos = Video::with(['module'])->get();
        return $this->sendResponse(
            VideoResource::collection($videos),
            'Videos retrieve succussfully!'
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
            'desc' => 'required',
            'file' => 'required|file|mimes:mp4,mov,avi',
            'module' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error on your input',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->file('file')->isValid()) {
            $file = StorageHelper::store($request->file('file'), to: 'videos');
            $video = new Video();
            $video->name = $request->name;
            $video->desc = $request->desc;
            $video->duration = $request->duration;
            $video->file = $file;
            $video->module_id = $request->module;
            $video->save();

            return $this->sendResponse([], 'Video uploaded successfully!');
        } else {
            return $this->sendError(
                'There\'s something error when uploading data!',
                'Video uploaded failed!',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'desc' => 'required',
            'file' => 'required|file|mimes:mp4,mov,avi',
            'module' => 'required',
        ]);
    
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('file'); 
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }
    
        $video = Video::where('id', '=', $id)->first();
    
        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                // Delete the previous file if it exists
                Storage::delete($video->file);
    
                // Store the new file
                $file = StorageHelper::store($request->file('file'), to: 'videos');
                $video->file = $file;
            } else {
                return $this->sendError(
                    'Error',
                    'Video uploaded failed, please check your file!',
                    Response::HTTP_BAD_REQUEST
                );
            }
        }
    
        // Update other fields
        $video->name = $request->name;
        $video->desc = $request->desc;
        $video->duration = $request->duration;
        $video->file = $file;
        $video->module_id = $request->module;
        $video->save();
    
        return $this->sendResponse(
            $video,
            'Content updated successfully!'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $video = Video::where('id', '=', $id)->first();
    
        if (!$video) {
            return $this->sendError(
                "Video not found!",
                [],
                Response::HTTP_NOT_FOUND
            );
        }
    
        try {
            // Check if the file exists before trying to delete
            if (Storage::exists($video->file)) {
                Storage::delete($video->file);
            } else {
                return $this->sendError(
                    "File not found!",
                    [],
                    Response::HTTP_NOT_FOUND
                );
            }
            
            $video->delete();
        } catch (Exception $e) {
            return $this->sendError(
                "There's something wrong when deleting data!",
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    
        return $this->sendResponse([], 'Video deleted successfully!');
    }
    
}