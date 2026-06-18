<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Bidang;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
 
class BidangController extends Controller
{
    public function index() {
        $this->authorize('kelola-master-data');
        $bidangs = Bidang::orderBy('urutan', 'asc')
            ->orderBy('nama_bidang', 'asc')
            ->get();
 
        return view('pages.main.admin.bidang-kerja.index', [
            'title'   => 'Seluruh Bidang Kerja',
            'bidangs' => $bidangs,
        ]);
    }
 
    public function store(Request $request) {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'nama_bidang' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bidangs,slug',
            'detail_bidang' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1',
        ]);
 
        // Kalau slug kosong (misal user hapus)
        if (empty($validatedData['slug'])) {
            $validatedData['slug'] = Str::slug($validatedData['nama_bidang']);
        }
 
        $bidang = DB::transaction(function () use ($validatedData) {
            // Geser urutan bidang lain yang >= urutan baru (+1)
            Bidang::where('urutan', '>=', $validatedData['urutan'])
                ->increment('urutan');
 
            return Bidang::create($validatedData);
        });
 
        return redirect()
            ->route('bidang.index', $bidang->slug)
            ->with('success', 'Bidang berhasil ditambahkan');
    }
 
    public function update(Request $request, Bidang $bidang) {
        $this->authorize('kelola-master-data');
        $validatedData = $request->validate([
            'nama_bidang' => 'required|string|max:255',
            'slug' => ['nullable','string','max:255',
                Rule::unique('bidangs', 'slug')
                    ->ignore($bidang->id_bidang, 'id_bidang'),
            ],
            'detail_bidang' => 'required|string|max:255',
            'urutan' => 'required|integer|min:1',
        ]);
 
        $oldUrutan = $bidang->urutan;
        $newUrutan = $validatedData['urutan'];
 
        try {
            DB::transaction(function () use ($bidang, $oldUrutan, $newUrutan, $validatedData) {
                // Pergeseran urutan jika ada perubahan
                if ($newUrutan < $oldUrutan) {
                    // Geser ke bawah (+1) untuk urutan di antara newUrutan s/d oldUrutan - 1
                    Bidang::whereBetween('urutan', [$newUrutan, $oldUrutan - 1])
                        ->increment('urutan');
                } elseif ($newUrutan > $oldUrutan) {
                    // Geser ke atas (-1) untuk urutan di antara oldUrutan + 1 s/d newUrutan
                    Bidang::whereBetween('urutan', [$oldUrutan + 1, $newUrutan])
                        ->decrement('urutan');
                }
 
                $bidang->update($validatedData);
            });
 
            // Redirect dengan flash message
            return redirect()->route('bidang.index')
                ->with('success', 'Bidang berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui Bidang. Silakan coba lagi.')
                ->withInput();
        }
    }
 
    public function delete(Bidang $bidang) {
        $this->authorize('kelola-master-data');
        try {
            $deletedUrutan = $bidang->urutan;
 
            DB::transaction(function () use ($bidang, $deletedUrutan) {
                $bidang->delete();
 
                // Rapatkan barisan bidang kerja tersisa (-1)
                Bidang::where('urutan', '>', $deletedUrutan)
                    ->decrement('urutan');
            });
 
            return redirect()
                ->route('bidang.index')
                ->with('success', 'Bidang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus Bidang');
        }
    }
}
