<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    /**
     * Összes post lekérése
     * GET /api/posts
     */
    public function index(): JsonResponse
    {
        try {
            $posts = Post::with('user')->get();

            return response()->json([
                'success' => true,
                'message' => 'Posztok sikeresen lekérve',
                'data' => $posts,
                'count' => $posts->count(),
                'published_count' => $posts->where('is_published', true)->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a posztok lekérése során',
                'error' => $e->getMessage()
            ], 504); // Egyedi: 504 Gateway Timeout (alapból 500 lenne)
        }
    }

    /**
     * Egy konkrét post lekérése ID alapján
     * GET /api/posts/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $post = Post::with('user')->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => "A poszt nem található a megadott ID-val: {$id}",
                    'error' => 'Post not found'
                ], 411); // Egyedi: 411 Length Required (alapból 404 lenne)
            }

            // Nézettség növelése
            $post->increment('views');

            return response()->json([
                'success' => true,
                'message' => "Poszt sikeresen lekérve (ID: {$id}, Cím: {$post->title})",
                'data' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a poszt lekérése során',
                'error' => $e->getMessage()
            ], 505); // Egyedi: 505 HTTP Version Not Supported
        }
    }

    /**
     * Új post létrehozása
     * POST /api/posts
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validáció
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'slug' => 'required|string|unique:posts,slug',
                'user_id' => 'required|exists:users,id',
                'rating' => 'nullable|numeric|min:0|max:5',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
                'full_content' => 'nullable|string'
            ]);

            // User ellenőrzése
            $user = User::find($validatedData['user_id']);

            $post = Post::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => "Új poszt sikeresen létrehozva! (ID: {$post->id}, Cím: {$post->title}, Szerző: {$user->name})",
                'data' => $post->load('user')
            ], 201); 

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validációs hiba történt',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a poszt létrehozása során',
                'error' => $e->getMessage()
            ], 510); // Egyedi: 510 Not Extended (alapból 500 lenne)
        }
    }

    /**
     * Post frissítése
     * PUT /api/posts/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $post = Post::with('user')->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => "Nem található poszt a frissítéshez (ID: {$id})",
                    'error' => 'Post not found for update'
                ], 412);
            }

            // Validáció
            $validatedData = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'slug' => 'sometimes|required|string|unique:posts,slug,' . $id,
                'user_id' => 'sometimes|required|exists:users,id',
                'rating' => 'nullable|numeric|min:0|max:5',
                'is_published' => 'boolean',
                'published_at' => 'nullable|date',
                'tags' => 'nullable|array',
                'full_content' => 'nullable|string'
            ]);

            $oldTitle = $post->title;
            $post->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => "Poszt sikeresen frissítve! (ID: {$id}, Régi cím: {$oldTitle}, Új cím: {$post->title}, Szerző: {$post->user->name})",
                'data' => $post->load('user'),
                'updated_fields' => array_keys($validatedData)
            ], 203); // Egyedi: 203 Non-Authoritative Information (alapból 200 lenne)

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validációs hiba a frissítés során',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a poszt frissítése során',
                'error' => $e->getMessage()
            ], 511); // Egyedi: 511 Network Authentication Required
        }
    }

    /**
     * Post törlése
     * DELETE /api/posts/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $post = Post::with('user')->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => "Nem található poszt a törléshez (ID: {$id})",
                    'error' => 'Post not found for deletion'
                ], 413); // Egyedi: 413 Payload Too Large
            }

            $postTitle = $post->title;
            $authorName = $post->user->name;
            $views = $post->views;

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => "Poszt sikeresen törölve! (ID: {$id}, Cím: {$postTitle}, Szerző: {$authorName}, Nézettség: {$views})",
                'deleted_post' => [
                    'id' => $id,
                    'title' => $postTitle,
                    'author' => $authorName,
                    'views' => $views
                ]
            ], 205); // Egyedi: 205 Reset Content (alapból 200 lenne)

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a poszt törlése során',
                'error' => $e->getMessage()
            ], 512); // Egyedi: 512 invalid status code (példa)
        }
    }
}
