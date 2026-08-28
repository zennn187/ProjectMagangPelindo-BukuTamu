<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama / Instansi</label>
        <input type="text" name="name_or_institution" value="{{ old('name_or_institution', $blacklist->name_or_institution ?? '') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
        <textarea name="reason" rows="4" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('reason', $blacklist->reason ?? '') }}</textarea>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700">Simpan</button>
        <a href="{{ route('admin.blacklists.index') }}" class="rounded-lg bg-slate-100 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Batal</a>
    </div>
</div>