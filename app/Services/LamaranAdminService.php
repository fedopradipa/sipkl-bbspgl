<?php

namespace App\Services;

use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Pembimbing;
use Illuminate\Support\Facades\DB;

class LamaranAdminService
{
  public function getMentors()
  {
    return Pembimbing::where('is_active', true)
      ->orderBy('nama')
      ->get(['id', 'nama']);
  }

  public function getLowongans()
  {
    return Lowongan::orderBy('posisi')->get(['id', 'posisi']);
  }

  public function getLamaransForIndex(string $status, ?string $search, ?int $lowonganId)
  {
    return Lamaran::with(['pengguna.profile', 'lowongan'])
      ->when($status !== 'all', fn($q) => $q->where('status', $status))
      ->when($lowonganId, fn($q) => $q->where('lowongan_id', $lowonganId))
      ->when($search, function ($query, $search) {
        return $query->where(function ($q) use ($search) {
          $q->whereHas('pengguna', fn($subQ) => $subQ->where('name', 'like', "%{$search}%"))
            ->orWhereHas('lowongan', function ($subQ) use ($search) {
              $subQ->where('posisi', 'like', "%{$search}%");
            });
        });
      })
      ->latest()
      ->paginate(15)
      ->withQueryString();
  }

  public function getLamaranDetail(int $id): Lamaran
  {
    return Lamaran::with(['pengguna.profile', 'lowongan', 'pembimbing'])->findOrFail($id);
  }

  public function approve(Lamaran $lamaran, array $validated): void
  {
    DB::transaction(function () use ($lamaran, $validated) {
      $lamaran->update([
        'status'            => 'diterima',
        'pembimbing_id'     => $validated['pembimbing_id'],
        'tgl_mulai_resmi'   => $validated['tgl_mulai_resmi'],
        'tgl_selesai_resmi' => $validated['tgl_selesai_resmi'],
        'hari_kerja_resmi'  => $validated['hari_kerja_resmi'],
      ]);
    });
  }

  public function reject(Lamaran $lamaran, array $validated): void
  {
    DB::transaction(function () use ($lamaran, $validated) {
      $lamaran->update([
        'status'        => 'ditolak',
        'catatan_admin' => $validated['catatan_admin'],
      ]);
    });
  }

  public function updateStatus(int $id, array $validated): void
  {
    $lamaran = Lamaran::findOrFail($id);

    DB::transaction(function () use ($lamaran, $validated) {
      $status = $validated['status'];

      if ($status === 'berhenti') {
        $lamaran->status = 'berhenti';
        $lamaran->tgl_selesai_resmi = now()->toDateString();

        $detail = $validated['catatan_detail'] ?? '-';
        $lamaran->catatan_admin = 'Berhenti - ' . $detail;
      } elseif ($status === 'selesai') {
        $lamaran->status = 'selesai';
      } else {
        $lamaran->status = $status;

        if (!empty($validated['catatan'])) {
          $lamaran->catatan_admin = $validated['catatan'];
        }
      }

      $lamaran->save();
    });
  }
}
