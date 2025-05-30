<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Összes user lekérése
     * GET /api/users
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::with('posts')->get();

            return response()->json([
                'success' => true,
                'message' => 'Felhasználók sikeresen lekérve',
                'data' => $users,
                'count' => $users->count()
            ], 200); // Alapértelmezett 200 OK

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a felhasználók lekérése során',
                'error' => $e->getMessage()
            ], 503); // Egyedi: 503 Service Unavailable (alapból 500 lenne)
        }
    }

    /**
     * Egy konkrét user lekérése ID alapján
     * GET /api/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::with('posts')->find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "A felhasználó nem található a megadott ID-val: {$id}",
                    'error' => 'User not found'
                ], 410); // Egyedi: 410 Gone (alapból 404 lenne)
            }

            return response()->json([
                'success' => true,
                'message' => "Felhasználó sikeresen lekérve (ID: {$id})",
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a felhasználó lekérése során',
                'error' => $e->getMessage()
            ], 502); // Egyedi: 502 Bad Gateway
        }
    }

    /**
     * Új user létrehozása
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validáció
            $validatedData = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'age' => 'nullable|integer|min:1|max:120',
                'salary' => 'nullable|numeric|min:0',
                'is_active' => 'boolean',
                'birth_date' => 'nullable|date',
                'bio' => 'nullable|string',
                'role' => 'in:admin,user,moderator'
            ]);

            $user = User::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => "Új felhasználó sikeresen létrehozva (ID: {$user->id}, Név: {$user->name})",
                'data' => $user
            ], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validációs hiba történt',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a felhasználó létrehozása során',
                'error' => $e->getMessage()
            ], 507); // Egyedi: 507 Insufficient Storage (alapból 500 lenne)
        }
    }

    /**
     * User frissítése
     * PUT /api/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "Nem található felhasználó a frissítéshez (ID: {$id})",
                    'error' => 'User not found for update'
                ], 410); // Egyedi: 410 Gone
            }

            // Validáció
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'age' => 'nullable|integer|min:1|max:120',
                'salary' => 'nullable|numeric|min:0',
                'is_active' => 'boolean',
                'birth_date' => 'nullable|date',
                'bio' => 'nullable|string',
                'role' => 'in:admin,user,moderator'
            ]);

            $oldName = $user->name;
            $user->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => "Felhasználó sikeresen frissítve! (ID: {$id}, Régi név: {$oldName}, Új név: {$user->name})",
                'data' => $user,
                'updated_fields' => array_keys($validatedData)
            ], 202); // Egyedi: 202 Accepted (alapból 200 lenne)

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validációs hiba a frissítés során',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a felhasználó frissítése során',
                'error' => $e->getMessage()
            ], 508); // Egyedi: 508 Loop Detected
        }
    }

    /**
     * User törlése
     * DELETE /api/users/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "Nem található felhasználó a törléshez (ID: {$id})",
                    'error' => 'User not found for deletion'
                ], 410); // Egyedi: 410 Gone
            }

            $userName = $user->name;
            $postsCount = $user->posts()->count();

            // User törlése (cascade törli a postokat is)
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "Felhasználó sikeresen törölve! (ID: {$id}, Név: {$userName}, Törölt posztok: {$postsCount})",
                'deleted_user' => [
                    'id' => $id,
                    'name' => $userName,
                    'deleted_posts_count' => $postsCount
                ]
            ], 204); // Egyedi: 204 No Content (alapból 200 lenne)

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hiba történt a felhasználó törlése során',
                'error' => $e->getMessage()
            ], 509); // Egyedi: 509 Bandwidth Limit Exceeded
        }
    }
}
