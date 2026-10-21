<?php
declare(strict_types=1);

interface DapatDipinjam {
    public function pinjam(): string;
    public function kembalikan(): string;
}

abstract class Perangkat implements DapatDipinjam {
    private string $kode;
    private string $nama;
    private string $kondisi;
    private bool $tersedia = true;

    public function __construct(string $kode, string $nama, string $kondisi = 'Baik') {
        if (trim($kode) === '') throw new InvalidArgumentException('Kode perangkat tidak boleh kosong.');
        if (trim($nama) === '') throw new InvalidArgumentException('Nama perangkat tidak boleh kosong.');
        if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak'], true)) {
            throw new InvalidArgumentException('Kondisi harus Baik, Rusak Ringan, atau Rusak.');
        }
        $this->kode = $kode;
        $this->nama = $nama;
        $this->kondisi = $kondisi;
    }

    public function getKode(): string { return $this->kode; }
    public function getNama(): string { return $this->nama; }
    public function getKondisi(): string { return $this->kondisi; }
    public function isTersedia(): bool { return $this->tersedia; }

    public function ubahKondisi(string $kondisi): void {
        if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak'], true)) {
            throw new InvalidArgumentException('Kondisi perangkat tidak valid.');
        }
        $this->kondisi = $kondisi;
    }

    public function pinjam(): string {
        if (!$this->tersedia) throw new RuntimeException($this->nama . ' sedang dipinjam.');
        if ($this->kondisi === 'Rusak') throw new RuntimeException($this->nama . ' tidak dapat dipinjam karena rusak.');
        $this->tersedia = false;
        return $this->nama . ' berhasil dipinjam.';
    }

    public function kembalikan(): string {
        if ($this->tersedia) throw new RuntimeException($this->nama . ' tidak sedang dipinjam.');
        $this->tersedia = true;
        return $this->nama . ' berhasil dikembalikan.';
    }

    abstract public function getKategori(): string;
    abstract public function getCatatanPenggunaan(): string;

    public function ringkasan(): string {
        return sprintf('%s | %s | Kategori: %s | Kondisi: %s | Status: %s',
            $this->kode, $this->nama, $this->getKategori(), $this->kondisi,
            $this->tersedia ? 'Tersedia' : 'Dipinjam');
    }
}

class PerangkatInput extends Perangkat {
    public function getKategori(): string { return 'Perangkat Input'; }
    public function getCatatanPenggunaan(): string { return 'Periksa kabel atau baterai sebelum digunakan.'; }
}

class PerangkatJaringan extends Perangkat {
    public function getKategori(): string { return 'Perangkat Jaringan'; }
    public function getCatatanPenggunaan(): string { return 'Pastikan port dan koneksi jaringan berfungsi.'; }
}

/** Composition: setiap transaksi peminjaman menyimpan satu objek Perangkat. */
class Peminjaman {
    private string $peminjam;
    private Perangkat $perangkat;
    private bool $aktif = false;

    public function __construct(string $peminjam, Perangkat $perangkat) {
        if (trim($peminjam) === '') throw new InvalidArgumentException('Nama peminjam tidak boleh kosong.');
        $this->peminjam = $peminjam;
        $this->perangkat = $perangkat;
    }

    public function prosesPinjam(): string {
        if ($this->aktif) throw new RuntimeException('Transaksi peminjaman ini sudah aktif.');
        $pesan = $this->perangkat->pinjam();
        $this->aktif = true;
        return $pesan . ' Peminjam: ' . $this->peminjam . '.';
    }

    public function prosesKembali(): string {
        if (!$this->aktif) throw new RuntimeException('Belum ada peminjaman aktif untuk transaksi ini.');
        $pesan = $this->perangkat->kembalikan();
        $this->aktif = false;
        return $pesan . ' Peminjam: ' . $this->peminjam . '.';
    }
}

function uji(string $judul, callable $aksi): void {
    echo "\n-- {$judul} --\n";
    try { echo $aksi() . "\n"; }
    catch (Throwable $e) { echo 'Ditolak dengan aman: ' . $e->getMessage() . "\n"; }
}

echo "MINI APPLICATION OOP: PEMINJAMAN PERANGKAT LAB\n";
echo "Nama: Rafael Maulana Prasetya | NIM: 20251020056\n";

$mouse = new PerangkatInput('LAB-IN-01', 'Mouse Wireless');
$router = new PerangkatJaringan('LAB-NW-02', 'Router Praktikum');
$router->ubahKondisi('Rusak');

echo "\n=== DAFTAR PERANGKAT (POLYMORPHISM) ===\n";
foreach ([$mouse, $router] as $perangkat) {
    echo $perangkat->ringkasan() . "\n";
    echo 'Catatan: ' . $perangkat->getCatatanPenggunaan() . "\n";
}

$transaksiMouse = new Peminjaman('Rafael Maulana Prasetya', $mouse);
$transaksiRouter = new Peminjaman('Rafael Maulana Prasetya', $router);
uji('UJI 1 — PEMINJAMAN NORMAL', fn() => $transaksiMouse->prosesPinjam());
uji('UJI 2 — PINJAM ULANG PERANGKAT', fn() => $mouse->pinjam());
uji('UJI 3 — PENGEMBALIAN NORMAL', fn() => $transaksiMouse->prosesKembali());
uji('UJI 4 — PERANGKAT RUSAK', fn() => $transaksiRouter->prosesPinjam());
uji('UJI 5 — KONDISI TIDAK VALID', function () use ($mouse): string {
    $mouse->ubahKondisi('Retak Total'); return 'Seharusnya tidak tercapai.';
});
uji('UJI 6 — NAMA PERANGKAT KOSONG', function (): string {
    new PerangkatInput('LAB-IN-99', '   '); return 'Seharusnya tidak tercapai.';
});

echo "\n=== STATUS AKHIR ===\n";
echo $mouse->ringkasan() . "\n";
echo $router->ringkasan() . "\n";
