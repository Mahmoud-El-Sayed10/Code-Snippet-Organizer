<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Snippet;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'type_id' => 1 // Admin
        ]);

        // Create regular user
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'type_id' => 0 // Regular user
        ]);

        // Create languages
        $languages = [
            ['name' => 'PHP', 'alias' => 'php'],
            ['name' => 'JavaScript', 'alias' => 'js'],
            ['name' => 'Python', 'alias' => 'py'],
            ['name' => 'Java', 'alias' => 'java'],
            ['name' => 'C#', 'alias' => 'cs'],
        ];

        foreach ($languages as $lang) {
            Language::create($lang);
        }

        // Create tags
        $tags = [
            'algorithm', 'frontend', 'backend', 'database', 'api',
            'validation', 'authentication', 'security', 'optimization',
            'debugging'
        ];

        foreach ($tags as $tagName) {
            Tag::create(['name' => $tagName]);
        }

        // Create sample snippets
        $phpId = Language::where('alias', 'php')->first()->id;
        $jsId = Language::where('alias', 'js')->first()->id;
        
        $backendTag = Tag::where('name', 'backend')->first()->id;
        $apiTag = Tag::where('name', 'api')->first()->id;
        $authTag = Tag::where('name', 'authentication')->first()->id;
        $frontendTag = Tag::where('name', 'frontend')->first()->id;

        // PHP API Snippet
        $snippet1 = Snippet::create([
            'user_id' => $admin->id,
            'title' => 'Laravel API Authentication',
            'code_content' => 'public function login(Request $request){
                $credentials = [
                    "email" => $request["email"], 
                    "password"=> $request["password"]
                ];
                if (! $token = Auth::attempt($credentials)) {
                    return response()->json([
                        "success" => false,
                        "error" => "Unauthorized"
                    ], 401);
                }
                $user = Auth::user();
                $user->token = $token;
                return response()->json([
                    "success" => true,
                    "user" => $user
                ]);
            }',
            'language_id' => $phpId,
            'description' => 'Laravel JWT authentication login method',
        ]);
        
        $snippet1->tags()->attach([$backendTag, $apiTag, $authTag]);

        $snippet2 = Snippet::create([
            'user_id' => $user->id,
            'title' => 'Fetch API Call',
            'code_content' => 'async function fetchData() {
    try {
        const response = await fetch(\'https://api.example.com/data\');
        const data = await response.json();
        return data;
    } catch (error) {
        console.error(\'Error fetching data:\', error);
    }
}',
            'language_id' => $jsId,
            'description' => 'Simple JavaScript fetch API call with async/await',
        ]);
        
        $snippet2->tags()->attach([$frontendTag, $apiTag]);
    }
}