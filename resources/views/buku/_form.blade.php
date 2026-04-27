@php
    $isEdit = isset($buku);
@endphp

<form method="POST" action="{{ $isEdit ? route('buku.update', $buku) : route('buku.store') }}" class="form-grid">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="form-group full">
        <label for="judul">Judul Buku</label>
        <input
            id="judul"
            type="text"
            name="judul"
            value="{{ old('judul', $isEdit ? $buku->judul : '') }}"
            maxlength="255"
            required
        >
    </div>

    <div class="form-group">
        <label for="pengarang">Pengarang</label>
        <input
            id="pengarang"
            type="text"
            name="pengarang"
            value="{{ old('pengarang', $isEdit ? $buku->pengarang : '') }}"
            maxlength="255"
            required
        >
    </div>

    <div class="form-group">
        <label for="tahun_terbit">Tahun Terbit</label>
        <input
            id="tahun_terbit"
            type="number"
            name="tahun_terbit"
            value="{{ old('tahun_terbit', $isEdit ? $buku->tahun_terbit : '') }}"
            min="1000"
            max="9999"
            required
        >
    </div>

    <div class="form-group">
        <label for="stok">Stok</label>
        <input
            id="stok"
            type="number"
            name="stok"
            value="{{ old('stok', $isEdit ? $buku->stok : '') }}"
            min="0"
            required
        >
    </div>

    <div class="form-actions">
        <span class="form-note">Semua field wajib diisi.</span>

        <div class="actions">
            <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Buku' }}
            </button>
        </div>
    </div>
</form>
