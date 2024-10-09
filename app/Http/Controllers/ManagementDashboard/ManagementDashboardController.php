<?php

namespace App\Http\Controllers\ManagementDashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\ManagementDashboard;


class ManagementDashboardController extends Controller
{
    public function getDashboard()
    {
        $dashboards = ManagementDashboard::all();

        if ($dashboards->isEmpty()) {
            return response()->json([
                'message' => 'Data dashboard tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'ok.',
            'data' => $dashboards
        ], 200);
    }

    public function getDashboardId($id)
    {
        $dashboards = ManagementDashboard::find($id);

        if (!$dashboards) {
            return response()->json([
                'message' => 'Data dashboard tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'message' => 'Data dashboad ditemukan.',
            'data' => $dashboards
        ], 200);
    }

    public function storeDashboard(Request $request)
    {
        $validatedData = $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif',
            'title' => 'required|string|max:255',
            'alternative_text' => 'nullable|string|max:255',
            'description' => 'required',
            'link' => 'required',
        ]);

        $path = $request->file('thumbnail')->store('thumbnails', 'public');

        $post = ManagementDashboard::create([
            'thumbnail' => $path,
            'title' => $validatedData['title'],
            'alternative_text' => $validatedData['alternative_text'],
            'description' => $validatedData['title'],
            'link' => $validatedData['link']
        ]);

        return response()->json([
            'message' => 'Data dashboard berhasil ditambah.',
            'data' => $post
        ], 201);
    }

    public function updateDashboard(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif',
            'title' => 'required|string|max:255',
            'alternative_text' => 'nullable|string|max:255',
            'description' => 'required',
            'link' => 'required',
        ]);

        // Temukan item yang akan diupdate
        $post = ManagementDashboard::findOrFail($id);

        // Jika ada file gambar yang diupload
        if ($request->hasFile('thumbnail')) {
            // Hapus gambar lama jika ada
            Storage::disk('public')->delete($post->thumbnail);

            // Simpan gambar baru
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $path; // Update path thumbnail
        }

        // Update data lainnya
        $post->title = $validatedData['title'];
        $post->alternative_text = $validatedData['alternative_text'];
        $post->description = $validatedData['description'];
        $post->link = $validatedData['link'];
        $post->save(); // Simpan perubahan

        return response()->json([
            'message' => 'Data dashboard berhasil diperbarui.',
            'data' => $post
        ], 200);
    }

    public function destroyDashboard($id)
    {
        $dashboard = ManagementDashboard::find($id);

        if (!$dashboard) {
            return response()->json([
                'message' => 'Data dashboard tidak ditemukan.'
            ], 404);
        }

        $dashboard->delete();

        return response()->json([
            'message' => 'Data dashboard berhasil dihapus',
            'data' => $dashboard
        ], 200);
    }
}
