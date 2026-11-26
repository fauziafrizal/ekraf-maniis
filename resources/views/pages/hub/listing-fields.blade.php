@php($fieldId = $record?->id ?? 'new')
<label for="kind-{{ $fieldId }}">Jenis data</label><select id="kind-{{ $fieldId }}" name="kind">
    <option value="product" @selected($record?->kind === 'product')>Produk</option>
    <option value="business" @selected($record?->kind === 'business')>Usaha</option>
</select>
@foreach (['name' => 'Nama', 'category' => 'Kategori / subsektor', 'village' => 'Desa', 'price' => 'Harga (rupiah, khusus produk)', 'whatsapp' => 'Nomor WhatsApp publik (kode negara)'] as $field => $label)
    <label for="{{ $field }}-{{ $fieldId }}">{{ $label }}</label><input
        id="{{ $field }}-{{ $fieldId }}" name="{{ $field }}" value="{{ $record?->$field }}"
        @if (in_array($field, ['name', 'category'])) required @endif
        @if ($field === 'price') type="number" min="0" max="100000000000" @else type="text" @endif>
@endforeach
<label for="desc-{{ $fieldId }}">Deskripsi</label>
<textarea id="desc-{{ $fieldId }}" name="description" rows="4" maxlength="3000" required>{{ $record?->description }}</textarea>
<label>Foto (JPG/PNG/WebP, maks. 4 MB)<input type="file" name="photo"
        accept="image/jpeg,image/png,image/webp"></label>
<label>Latitude lokasi usaha publik (opsional)<input type="number" step="any" min="-90" max="90"
        name="latitude" value="{{ $record?->latitude }}"></label>
<label>Longitude lokasi usaha publik (opsional)<input type="number" step="any" min="-180" max="180"
        name="longitude" value="{{ $record?->longitude }}"></label>
<label>ID toko untuk produk (opsional)<input type="number" min="1" name="business_id"
        value="{{ $record?->business_id }}"></label>
@if ($record)
    <p>ID katalog: {{ $record->id }}</p>
@endif
<label><input type="checkbox" name="published" value="1" @checked($record?->published)> Tampilkan kepada
    publik</label>
<label><input type="checkbox" name="consent" value="1"> Saya telah memastikan izin publikasi data dan kontak dari
    pemilik.</label>
