<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SnippetController extends Controller
{

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $snippets = Snippet::with(['language', 'tags'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $snippets,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'code_content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $snippet = Snippet::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'code_content' => $request->code_content,
            'language_id' => $request->language_id,
            'description' => $request->description,
        ]);

        if ($request->has('tags') && is_array($request->tags)) {
            $snippet->tags()->attach($request->tags);
        }

        $snippet->load(['language', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Snippet created successfully',
            'data' => $snippet,
        ], 201);
    }

    public function show($id)
    {
        $snippet = Snippet::with(['language', 'tags'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found',
            ], 404);
        }

        // Get current user ID and manually load the User model
        $userId = Auth::id();
        $user = User::find($userId);
        
        // Check if favorited using the proper User model
        $isFavorited = $user->favorites()->where('snippet_id', $id)->exists();

        return response()->json([
            'success' => true,
            'data' => $snippet,
            'is_favorited' => $isFavorited,
        ]);
    }

    public function update(Request $request, $id)
    {
        $snippet = Snippet::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'code_content' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $snippet->update([
            'title' => $request->title,
            'code_content' => $request->code_content,
            'language_id' => $request->language_id,
            'description' => $request->description,
        ]);

        if ($request->has('tags') && is_array($request->tags)) {
            $snippet->tags()->sync($request->tags);
        }

        $snippet->load(['language', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Snippet updated successfully',
            'data' => $snippet,
        ]);
    }

    public function destroy($id)
    {
        $snippet = Snippet::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found',
            ], 404);
        }

        $snippet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Snippet deleted successfully',
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
            'language_id' => 'nullable|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = Snippet::with(['language', 'tags'])
            ->where('user_id', Auth::id())
            ->where(function ($query) use ($request) {
                $term = $request->query;
                $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('code_content', 'like', "%{$term}%");
            });

        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($query) use ($request) {
                $query->where('tags.id', $request->tag_id);
            });
        }

        $snippets = $query->latest()->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $snippets,
        ]);
    }

    public function favorites(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        
        $userId = Auth::id();
        $user = User::find($userId);

        $favorites = $user->favorites()
            ->with(['language', 'tags'])
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $favorites,
        ]);
    }

    public function addToFavorites($id)
    {
        $snippet = Snippet::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found',
            ], 404);
        }

        // Get current user ID and manually load the User model
        $userId = Auth::id();
        $user = User::find($userId);
        
        // Check if already favorited using the proper User model
        if ($user->favorites()->where('snippet_id', $id)->exists()) {
            return response()->json([
                'success' => true,
                'message' => 'Snippet is already in favorites',
            ]);
        }

        $user->favorites()->attach($id);

        return response()->json([
            'success' => true,
            'message' => 'Snippet added to favorites',
        ]);
    }

    public function removeFromFavorites($id)
    {
        $snippet = Snippet::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found',
            ], 404);
        }

        // Get current user ID and manually load the User model
        $userId = Auth::id();
        $user = User::find($userId);
        
        $user->favorites()->detach($id);

        return response()->json([
            'success' => true,
            'message' => 'Snippet removed from favorites',
        ]);
    }
}