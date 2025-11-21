<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvatarController extends Controller
{
    public function upload(Request $request)
    {
        \Log::info('🔄 Начало загрузки аватара');
        
        try {
            $user = $request->user();
            
            if (!$user) {
                \Log::warning('❌ Пользователь не авторизован');
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            \Log::info('👤 Пользователь:', ['id' => $user->id, 'username' => $user->username]);

            // Валидация файла
            $validator = Validator::make($request->all(), [
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                \Log::warning('❌ Ошибка валидации', $validator->errors()->toArray());
                return response()->json([
                    'error' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Проверяем что файл есть
            if (!$request->hasFile('avatar')) {
                \Log::warning('❌ Файл не получен');
                return response()->json(['error' => 'Файл не получен'], 400);
            }

            $file = $request->file('avatar');
            \Log::info('📁 Получен файл:', [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);

            // Создаем папку avatars если нет
            $avatarsPath = public_path('avatars');
            if (!file_exists($avatarsPath)) {
                \Log::info('📁 Создаем папку avatars');
                mkdir($avatarsPath, 0755, true);
            }

            // Удаляем старый аватар если есть
            if ($user->avatar_url && file_exists(public_path($user->avatar_url))) {
                \Log::info('🗑️ Удаляем старый аватар:', ['path' => $user->avatar_url]);
                unlink(public_path($user->avatar_url));
            }

            // Генерируем уникальное имя файла
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'avatars/' . $fileName;
            
            \Log::info('💾 Сохраняем файл:', ['path' => $filePath]);

            // Сохраняем файл в public/avatars
            $file->move(public_path('avatars'), $fileName);
            
            // Обновляем пользователя
            $user->avatar_url = $filePath;
            $user->save();

            // Формируем полный URL для ответа
            $fullUrl = url($filePath);

            \Log::info('✅ Аватар успешно сохранен', ['url' => $fullUrl]);

            return response()->json([
                'message' => 'Аватар успешно обновлен',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'avatar_url' => $fullUrl,
                    'created_at' => $user->created_at
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Ошибка загрузки аватара: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Ошибка загрузки аватара: ' . $e->getMessage()
            ], 500);
        }
    }

    public function remove(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json(['error' => 'Не авторизован'], 401);
            }

            // Удаляем файл если есть
            if ($user->avatar_url && file_exists(public_path($user->avatar_url))) {
                unlink(public_path($user->avatar_url));
            }

            // Обновляем пользователя
            $user->avatar_url = null;
            $user->save();

            return response()->json([
                'message' => 'Аватар успешно удален',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'avatar_url' => null,
                    'created_at' => $user->created_at
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Avatar remove error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ошибка удаления аватара: ' . $e->getMessage()
            ], 500);
        }
    }
}