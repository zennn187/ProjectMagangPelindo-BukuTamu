<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required maxlength="255" class="soft-field">
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-bank-navy mb-1">Divisi</label>
            <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}" required maxlength="255" placeholder="cth: Divisi Umum, IT, SDM" class="soft-field">
        </div>
        <div>
            <label class="block text-sm font-medium text-bank-navy mb-1">Jabatan</label>
            <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" required maxlength="255" class="soft-field">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">No. Telepon</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $employee->phone_number ?? '') }}" maxlength="255" class="soft-field">
    </div>
    <label class="flex items-center gap-2 text-sm text-bank-navy">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active ?? true)) class="rounded border-bank-light text-bank-blue focus:ring-bank-blue/20">
        Aktif (tampil di kiosk)
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 active:scale-95 transition-all duration-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan
        </button>
        <a href="{{ route('admin.employees.index') }}" class="rounded-xl bg-bank-light px-5 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80 active:scale-95 transition-all duration-200">Batal</a>
    </div>
</div>
