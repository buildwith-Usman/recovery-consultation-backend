<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
  public function uplaod(Request $request)
  {
    try {
      $request->validate([
        'file' => 'required',
        'directory' => 'required'
      ]);

      $file = $request->file('file');
      $directory = $request->input('directory');
      $data = [];
      if ($file) {
        $path = $this->uploadImage($file, $directory);
        $data['file_name'] = $file->getClientOriginalName();
        $data['extension'] = $file->getClientOriginalExtension();
        $data['mime_type'] = $file->getMimeType();
        $data['size'] = (string)$file->getSize();
        $data['path'] = $path;
        $data['url'] = Storage::url($path);

        $file = File::create($data);

        return response()->json([
          'message' => 'File updated successfully',
          'data' => ['file' => $file]
        ], 200);
      }

    } catch (\Illuminate\Validation\ValidationException $e) {
      $errorsList = [];
      foreach ($e->errors() as $err) {
        $errorsList = array_merge($errorsList, $err);
      }
      return response()->json([
        'message' => 'Validation failed',
        'errors' => $errorsList
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Registration failed',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }

  public function file(Request $request) {
    try{
      $id = $request->input('id');
      $file = File::where('id', $id)->first();

      return response()->json([
        'message' => 'File received.',
        'data' => ['file' => $file]
      ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
      $errorsList = [];
      foreach ($e->errors() as $err) {
        $errorsList = array_merge($errorsList, $err);
      }
      return response()->json([
        'message' => 'Validation failed',
        'errors' => $errorsList
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'Registration failed',
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }
}
