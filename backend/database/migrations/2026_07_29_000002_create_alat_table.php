{{--
    ============================================================
    Modal "Pinjam Alat" — versi input manual dengan autocomplete
    ============================================================

    Cara pakai:
    1. Simpan file ini di: resources/views/components/pinjam-alat-modal.blade.php
       (atau resources/views/partials/... sesuaikan struktur project kamu)

    2. Di controller yang menampilkan halaman "Peminjaman Alat", kirim data alat
       (menyesuaikan tabel 'alat': kolom nama_alat & jumlah sebagai stok):

        public function index()
        {
            $alats = Alat::select('id', 'nama_alat', 'jumlah')->orderBy('nama_alat')->get();
            return view('peminjaman.index', compact('alats'));
        }

    3. Panggil komponen ini di view utama, misalnya:

        <x-pinjam-alat-modal :alats="$alats" />

       Atau kalau tidak pakai Blade Component, cukup:

        @include('partials.pinjam-alat-modal', ['alats' => $alats])

    4. Route submit form disesuaikan dengan action="{{ route('peminjaman.store') }}"
       di bawah — ganti sesuai nama route kamu.

    5. Struktur data yang dikirim ke server saat submit:
       - items[] = array of { id_alat (bisa kosong kalau alat tidak ada di database), nama_alat, jumlah }
       - tanggal_pinjam
       - keperluan

       Karena user boleh ketik manual (termasuk nama alat yang belum terdaftar),
       field id_alat bisa kosong. Sesuaikan validasi di controller/Request sesuai kebutuhan,
       misalnya izinkan nama_alat baru untuk dicatat manual oleh admin.
--}}

<div x-data="pinjamAlatModal()" x-cloak>

    {{-- Tombol pembuka modal, sesuaikan dengan tombol "Pinjam Alat" yang sudah ada di halaman kamu --}}
    {{-- <button @click="open = true" class="btn-primary">+ Pinjam Alat</button> --}}

    <div
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
        style="display: none;"
    >
        <div
            x-show="open"
            x-transition
            @click.outside="open = false"
            class="bg-white w-full max-w-lg rounded-2xl shadow-xl p-8"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Pinjam Alat</h2>
                <button
                    @click="open = false"
                    type="button"
                    class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200"
                >
                    &times;
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('peminjaman.store') }}"
                @submit="return items.length > 0"
            >
                @csrf

                {{-- ============ Input Nama Alat (manual + autocomplete) ============ --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-2 h-2 rounded-sm bg-indigo-600 inline-block"></span>
                        Nama Alat
                    </label>

                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input
                                type="text"
                                x-model="query"
                                @input="filterSuggestions"
                                @focus="filterSuggestions"
                                @keydown.escape="showSuggestions = false"
                                autocomplete="off"
                                placeholder="Ketik nama alat..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                            >

                            {{-- Dropdown saran, muncul saat mengetik --}}
                            <div
                                x-show="showSuggestions"
                                x-transition
                                @click.outside="showSuggestions = false"
                                class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-lg shadow-lg max-h-56 overflow-y-auto z-10"
                                style="display: none;"
                            >
                                <template x-if="suggestions.length === 0">
                                    <div class="px-3 py-2.5 text-sm text-gray-400">
                                        Alat tidak ditemukan, tetap bisa diajukan manual.
                                    </div>
                                </template>

                                <template x-for="alat in suggestions" :key="alat.id ?? alat.nama_alat">
                                    <div
                                        @click="pilihAlat(alat)"
                                        class="px-3 py-2.5 text-sm cursor-pointer hover:bg-indigo-50 flex justify-between items-center"
                                    >
                                        <span x-text="alat.nama_alat"></span>
                                        <span
                                            x-show="alat.jumlah !== undefined"
                                            class="text-xs text-gray-400 bg-gray-100 rounded-full px-2 py-0.5"
                                            x-text="'Stok: ' + alat.jumlah"
                                        ></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <input
                            type="number"
                            x-model.number="qty"
                            min="1"
                            class="w-20 border border-gray-300 rounded-lg px-3 py-2.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                        >

                        <button
                            type="button"
                            @click="tambahItem"
                            class="w-11 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xl leading-none"
                        >
                            +
                        </button>
                    </div>

                    <p class="text-xs text-gray-400 mt-1.5">
                        Ketik nama alat secara manual, sistem akan menyarankan alat yang cocok dari data yang ada. Bisa tambah lebih dari satu alat.
                    </p>

                    {{-- Daftar alat yang sudah ditambahkan --}}
                    <div class="mt-3 space-y-2">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                                <span>
                                    <span class="font-semibold" x-text="item.nama_alat"></span>
                                    <span class="text-gray-400 ml-1" x-text="'x' + item.jumlah"></span>
                                </span>
                                <button type="button" @click="items.splice(index, 1)" class="text-red-500 text-xs hover:underline">
                                    Hapus
                                </button>

                                {{-- Data tersembunyi yang benar-benar dikirim ke server --}}
                                <input type="hidden" :name="'items[' + index + '][id_alat]'" :value="item.id_alat">
                                <input type="hidden" :name="'items[' + index + '][nama_alat]'" :value="item.nama_alat">
                                <input type="hidden" :name="'items[' + index + '][jumlah]'" :value="item.jumlah">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ============ Tanggal Pinjam ============ --}}
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-2 h-2 rounded-sm bg-indigo-600 inline-block"></span>
                        Tanggal Pinjam
                    </label>
                    <input
                        type="date"
                        name="tanggal_pinjam"
                        value="{{ now()->format('Y-m-d') }}"
                        required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                    >
                </div>

                {{-- ============ Keperluan ============ --}}
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <span class="w-2 h-2 rounded-sm bg-indigo-600 inline-block"></span>
                        Keperluan (opsional)
                    </label>
                    <textarea
                        name="keperluan"
                        rows="3"
                        placeholder="Untuk keperluan apa?"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-y focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-500"
                    ></textarea>
                </div>

                {{-- ============ Aksi ============ --}}
                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold border border-indigo-600 text-indigo-600 hover:bg-indigo-50"
                    >
                        &times; Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white hover:bg-indigo-700"
                    >
                        &check; Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============ Alpine.js logic ============ --}}
{{-- Butuh Alpine.js. Kalau project belum pakai, tambahkan di layout utama sebelum </body>:
     <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
--}}
<script>
    function pinjamAlatModal() {
        return {
            open: false,
            query: '',
            qty: 1,
            showSuggestions: false,
            suggestions: [],
            items: [],

            // Data alat dikirim dari server (lihat komentar di atas file ini).
            // Format: [{ id: 1, nama: 'Bor Listrik', stok: 5 }, ...]
            daftarAlat: @json($alats ?? []),

            filterSuggestions() {
                const q = this.query.trim().toLowerCase();
                if (!q) {
                    this.showSuggestions = false;
                    this.suggestions = [];
                    return;
                }
                this.suggestions = this.daftarAlat.filter(a =>
                    a.nama.toLowerCase().includes(q)
                );
                this.showSuggestions = true;
            },

            pilihAlat(alat) {
                this.query = alat.nama;
                this.showSuggestions = false;
            },

            tambahItem() {
                const nama = this.query.trim();
                if (!nama) return;

                // Kalau nama cocok persis dengan alat yang ada di database, ambil id-nya.
                // Kalau tidak ketemu (user ketik manual, alat belum terdaftar), id_alat dikosongkan.
                const match = this.daftarAlat.find(
                    a => a.nama.toLowerCase() === nama.toLowerCase()
                );

                this.items.push({
                    id_alat: match ? match.id : null,
                    nama_alat: nama,
                    jumlah: this.qty && this.qty > 0 ? this.qty : 1,
                });

                this.query = '';
                this.qty = 1;
                this.showSuggestions = false;
            },
        };
    }
</script>