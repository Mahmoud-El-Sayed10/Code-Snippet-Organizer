<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{

    public function index()
    {
        $tags = Tag::all();

        return response()->json([
            "success" => true,
            "data" => $tags,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:tags,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $validator->errors(),
            ], 422);
        }

        $tag = Tag::create([
            'name' => $request->name,
        ]);

        return response()->json([
            "success" => true,
            "message" => 'Tag created successfully',
            "data" => $tag,
        ], 201);
    }

    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                "success" => false,
                "message" => 'Tag not found',
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $tag,
        ]);
    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                "success" => false,
                "message" => 'Tag not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $validator->errors(),
            ], 422);
        }

        $tag->update([
            'name' => $request->name,
        ]);

        return response()->json([
            "success" => true,
            "message" => 'Tag updated successfully',
            "data" => $tag,
        ]);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                "success" => false,
                "message" => 'Tag not found',
            ], 404);
        }

        if ($tag->snippets()->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => 'Cannot delete tag because it is being used by snippets',
            ], 409);
        }

        $tag->delete();

        return response()->json([
            "success" => true,
            "message" => 'Tag deleted successfully',
        ]);
    }

    public function snippets($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                "success" => false,
                "message" => 'Tag not found',
            ], 404);
        }

        $snippets = $tag->snippets()
            ->with(['language'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return response()->json([
            "success" => true,
            "data" => [
                'tag' => $tag->name,
                'snippets' => $snippets,
            ],
        ]);
    }
}