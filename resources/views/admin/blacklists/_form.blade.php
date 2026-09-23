<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Nama / Instansi</label>
        <input type="text" name="name_or_institution" value="{{ old('name_or_institution', $blacklist->name_or_institution ?? '') }}" required maxlength="255" class="soft-field">
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Alasan</label>
        <textarea name="reason" rows="4" required class="soft-field min-h-[120px] px-3 py-2">{{ old('reason', $blacklist->reason ?? '') }}</textarea>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 active:scale-95 transition-all duration-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan
        </button>
        <a href="{{ route('admin.blacklists.index') }}" class="rounded-xl bg-bank-light px-5 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80 active:scale-95 transition-all duration-200">Batal</a>
    </div>
</div>
