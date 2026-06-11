<?php

namespace App\Jobs;

use App\Models\Buku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateStokJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $buku_id;
    public $tipe; // 'pinjam' untuk kurang, 'kembali' untuk tambah

    public function __construct($buku_id, $tipe = 'pinjam')
    {
        $this->buku_id = $buku_id;
        $this->tipe = $tipe;
    }

    public function handle(): void
    {
        $buku = Buku::find($this->buku_id);

        if (!$buku) {
            return;
        }

        if ($this->tipe === 'pinjam') {
            if ($buku->stok > 0) {
                $buku->decrement('stok');
            }
        } elseif ($this->tipe === 'kembali') {
            $buku->increment('stok');
        }
    }
}
