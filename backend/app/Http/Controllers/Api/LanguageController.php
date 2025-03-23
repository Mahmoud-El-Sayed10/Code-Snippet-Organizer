<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{

    public function index()
    {
        $languages = Language::all();

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:languages,name',
            'alias' => 'required|string|max:50|unique:languages,alias',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $language = Language::create([
            'name' => $request->name,
            'alias' => $request->alias,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Language created successfully',
            'data' => $language,
        ], 201);
    }

    public function show($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $language,
        ]);
    }

    public function update(Request $request, $id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:languages,name,' . $language->id,
            'alias' => 'required|string|max:50|unique:languages,alias,' . $language->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $language->update([
            'name' => $request->name,
            'alias' => $request->alias,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully',
            'data' => $language,
        ]);
    }

    public function destroy($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found',
            ], 404);
        }

        if ($language->snippets()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete language because it is being used by snippets',
            ], 409);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully',
        ]);
    }

    public function snippets($id)
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found',
            ], 404);
        }

        $snippets = $language->snippets()
            ->with(['tags'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => [
                'language' => $language->name,
                'snippets' => $snippets,
            ],
        ]);
    }
}