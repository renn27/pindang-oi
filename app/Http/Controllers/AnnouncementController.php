<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    // Halaman CRUD
    public function index() {
        $this->authorize('kelola-master-data');
        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        return view('pages.main.admin.announcements.index', [
            'title' => 'Pengumuman',
            'announcements' => $announcements
        ]);
    }

    // Simpan pengumuman baru
    public function store(Request $request) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            'end_date' => 'required|date|after_or_equal:today'
        ]);

        // Upload gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        // Buat pengumuman dengan start_date otomatis hari ini
        Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $imagePath,
            'start_date' => Carbon::today(),
            'end_date' => $validated['end_date'],
            'is_active' => true
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil ditambahkan');
    }

    // Update pengumuman
    public function update(Request $request, Announcement $announcement) {
        $this->authorize('kelola-master-data');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'end_date' => 'required|date|after_or_equal:today'
        ]);

        $data = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'end_date' => $validated['end_date']
        ];

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil diupdate');
    }

    // Hapus pengumuman
    public function delete(Announcement $announcement) {
        $this->authorize('kelola-master-data');
        // Hapus gambar
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Pengumuman berhasil dihapus');
    }

    // Toggle status aktif
    public function toggleActive(Announcement $announcement) {
        $this->authorize('kelola-master-data');
        $announcement->update([
            'is_active' => !$announcement->is_active
        ]);

        $status = $announcement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('announcements.index')
            ->with('success', "Pengumuman berhasil $status");
    }

    // API untuk modal (mengembalikan data pengumuman aktif)
    public function getActiveAnnouncements() {
        $this->authorize('kelola-master-data');
        $announcements = Announcement::active()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return $item->toModalFormat();
            });

        return response()->json($announcements);
    }

    public function pegawaiIndex() {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        return view('pages.main.admin.announcements.pengumuman_pegawai', [
            'title' => 'Pengumuman',
            'announcements' => $announcements
        ]);
    }
}
