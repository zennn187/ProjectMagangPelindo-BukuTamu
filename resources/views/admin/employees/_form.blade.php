<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $employee->name ?? '') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
            <input type="text" name="department" value="{{ old('department', $employee->department ?? '') }}" required maxlength="255" placeholder="cth: Divisi Umum, IT, SDM" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
            <input type="text" name="position" value="{{ old('position', $employee->position ?? '') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $employee->phone_number ?? '') }}" maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employee->is_active ?? true)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
        Aktif (tampil di kiosk)
    </label>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700">Simpan</button>
        <a href="{{ route('admin.employees.index') }}" class="rounded-lg bg-slate-100 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Batal</a>
    </div>
</div>