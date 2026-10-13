<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = AppSetting::current();
        return view('admin.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:100',
            'instagram'  => 'nullable|string|max:100',
            'telepon'    => 'nullable|string|max:50',
            'logo'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ], [
            'brand_name.required' => 'Nama toko wajib diisi.',
            'logo.image'          => 'File harus berupa gambar.',
            'logo.mimes'          => 'Format gambar harus JPG, PNG, GIF, atau WebP.',
            'logo.max'            => 'Ukuran gambar maksimal 2MB.',
        ]);

        $setting = AppSetting::current();

        $instagram = $request->instagram ?? '';
        $instagram = '@' . ltrim(trim($instagram), '@'); // Normalize: selalu prefix @

        $data = [
            'brand_name' => $request->brand_name,
            'instagram'  => $instagram ?: null,
            'telepon'    => $request->telepon,
        ];

        // Upload logo baru jika ada
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            // Gunakan ekstensi dari MIME type (aman), bukan dari nama file asli
            $mimeExtMap = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'image/webp' => 'webp',
            ];
            $ext      = $mimeExtMap[$file->getMimeType()] ?? 'jpg';
            $filename = 'logo_struk_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;

            // Hapus logo lama jika bukan logo default
            if ($setting->logo_path && $setting->logo_path !== 'img/shanghaejo.jpg') {
                $oldPath = public_path($setting->logo_path);
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Simpan ke public/img/
            $file->move(public_path('img'), $filename);
            $data['logo_path'] = 'img/' . $filename;
        }

        // Reset logo ke default jika diminta
        if ($request->boolean('reset_logo')) {
            $data['logo_path'] = 'img/shanghaejo.jpg';
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan struk berhasil disimpan!');
    }
}
