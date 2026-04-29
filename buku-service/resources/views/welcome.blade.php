<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    @php
        $books = [
            [
                'judul' => 'Pemrograman Microservices Laravel',
                'pengarang' => 'Taylor Otwell',
                'stok' => 5,
            ],
            [
                'judul' => 'Percakapan Diatas MDPL',
                'pengarang' => 'Barra Narendra Hawajirin',
                'stok' => 10,
            ],
            [
                'judul' => 'Nyanyian Arus dan Napas Samudra',
                'pengarang' => 'Hartita Wirastri',
                'stok' => 15,
            ],
            [
                'judul' => 'Pilar Cahaya di Era Modern',
                'pengarang' => "Muthi'ah Fadiyah",
                'stok' => 5,
            ],
            [
                'judul' => 'Simfoni Rimba yang Terlupakan',
                'pengarang' => 'Shandy Okta Ramadhani',
                'stok' => 2,
            ],
        ];

        $totalJudul = count($books);
        $totalStok = collect($books)->sum('stok');
        $stokTersedia = $totalStok;
        $stokDipinjam = 0;
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8 flex flex-col gap-4 rounded-3xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">Buku-Service</p>
                <h1 class="mt-1 text-3xl font-bold text-slate-900">Perpustakaan Digital</h1>
                <p class="mt-2 text-sm text-slate-500">
                    Koleksi Novel Tersedia Diperpustakaan.
                </p>
            </div>

            <button class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                + Tambah Buku
            </button>
        </header>

        <section class="mb-8 grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Total Judul Buku</p>
                <h2 class="mt-2 text-3xl font-bold">{{ $totalJudul }}</h2>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Total Stok Tersedia</p>
                <h2 class="mt-2 text-3xl font-bold text-green-600">{{ $stokTersedia }}</h2>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Buku Dipinjam</p>
                <h2 class="mt-2 text-3xl font-bold text-amber-600">{{ $stokDipinjam }}</h2>
            </div>
        </section>

        <main class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-3xl bg-white p-6 shadow-sm lg:col-span-1">
                <h2 class="text-xl font-bold">Input Buku</h2>
                <p class="mt-1 text-sm text-slate-500">Tambahkan data buku baru.</p>

                <form class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Judul Buku</label>
                        <input type="text" placeholder="Contoh: Pemrograman Web" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Pengarang</label>
                        <input type="text" placeholder="Contoh: John Doe" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Stok</label>
                        <input type="number" placeholder="Jumlah stok" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                    </div>

                    <button type="button" class="w-full rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">
                        Simpan Data
                    </button>
                </form>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm lg:col-span-2">
                <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-bold">Daftar Buku</h2>
                        <p class="mt-1 text-sm text-slate-500">Data buku terbaru di perpustakaan.</p>
                    </div>

                    <input type="text" placeholder="Cari buku..." class="rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-semibold">No</th>
                                <th class="px-4 py-3 font-semibold">Judul Buku</th>
                                <th class="px-4 py-3 font-semibold">Pengarang</th>
                                <th class="px-4 py-3 font-semibold">Stok</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($books as $index => $book)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4 font-medium">{{ $book['judul'] }}</td>
                                    <td class="px-4 py-4">{{ $book['pengarang'] }}</td>
                                    <td class="px-4 py-4">{{ $book['stok'] }}</td>
                                    <td class="px-4 py-4">
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Tersedia
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>