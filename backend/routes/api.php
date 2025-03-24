<?php
// In routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SnippetController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Middleware\EnsureIsAdmin;
use App\Http\Middleware\Cors;

// OPTIONS route for handling preflight requests
Route::options('/{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', '*')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Apply Cors middleware directly with full class name
Route::middleware([Cors::class])->group(function () {
    Route::group(["prefix" => "v0.1"], function(){
        // Public Routes
        Route::group(["prefix" => "guest"], function(){
            Route::post('login', [AuthController::class, 'login']);
            Route::post('register', [AuthController::class, 'register']);
            Route::get('languages', [LanguageController::class, 'index']);
            Route::get('tags', [TagController::class, 'index']);
            Route::post('tags', [TagController::class, 'store']);
        });
        
        // Protected Routes (require authentication)
        Route::group(["middleware" => "auth:api"], function(){
            // User Profile
            Route::post('profile', [AuthController::class, 'editProfile']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('user', [AuthController::class, 'userProfile']);
            
            // Regular user snippet routes
            Route::group(["prefix" => "snippets"], function(){
                Route::get('/', [SnippetController::class, 'index']);
                Route::post('/', [SnippetController::class, 'store']);
                Route::get('/search', [SnippetController::class, 'search']);
                Route::get('/{id}', [SnippetController::class, 'show']);
                Route::put('/{id}', [SnippetController::class, 'update']);
                Route::delete('/{id}', [SnippetController::class, 'destroy']);
            });
            
            // Favorites
            Route::group(["prefix" => "favorites"], function(){
                Route::get('/', [SnippetController::class, 'favorites']);
                Route::post('/{id}', [SnippetController::class, 'addToFavorites']);
                Route::delete('/{id}', [SnippetController::class, 'removeFromFavorites']);
            });
            
            // View-only routes for tags and languages
            Route::get('languages/{id}', [LanguageController::class, 'show']);
            Route::get('languages/{id}/snippets', [LanguageController::class, 'snippets']);
            Route::get('tags/{id}', [TagController::class, 'show']);
            Route::get('tags/{id}/snippets', [TagController::class, 'snippets']);
            
            // Admin Routes - using full class name for middleware
            Route::group(["prefix" => "admin", "middleware" => EnsureIsAdmin::class], function(){
                // Language Management
                Route::post('/languages', [LanguageController::class, 'store']);
                Route::put('/languages/{id}', [LanguageController::class, 'update']);
                Route::delete('/languages/{id}', [LanguageController::class, 'destroy']);
                
                // Tag Management
                Route::post('/tags', [TagController::class, 'store']);
                Route::put('/tags/{id}', [TagController::class, 'update']);
                Route::delete('/tags/{id}', [TagController::class, 'destroy']);
                
                // Stats/Dashboard
                Route::get('/stats', function() {
                    $stats = [
                        'total_users' => \App\Models\User::count(),
                        'total_snippets' => \App\Models\Snippet::count(),
                        'total_languages' => \App\Models\Language::count(),
                        'total_tags' => \App\Models\Tag::count(),
                    ];
                    
                    return response()->json([
                        "success" => true,
                        "data" => $stats
                    ]);
                });
            });
        });
    });
});