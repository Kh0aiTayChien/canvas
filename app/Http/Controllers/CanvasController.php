<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CanvasImage;

class CanvasController extends Controller
{
    public function index()
    {
        $images = CanvasImage::all();
        return view('canvas', ['images' => $images]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        CanvasImage::create([
            'image' => $request->input('image'),
        ]);

        return response()->json(['message' => 'Image saved successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $image = CanvasImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        $image->image = $request->input('image');
        $image->save();

        return response()->json(['message' => 'Image updated successfully.']);
    }
    public function destroy($id)
    {
        $image = CanvasImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found.'], 404);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }
}
