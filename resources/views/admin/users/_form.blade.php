<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required maxlength="255" class="soft-field">
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required maxlength="255" class="soft-field">
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Peran</label>
        <select name="role" class="soft-select">
            <option value="receptionist" @selected(old('role', $user->role ?? '') === 'receptionist')>Resepsionis / Security</option>
            <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin (SDM & Umum)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Password</label>
        <input type="password" name="password" {{ isset($user) ? '' : 'required' }} minlength="8" class="soft-field">
        @if(isset($user))
            <p class="text-xs text-bank-gray mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-bank-navy mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" minlength="8" class="soft-field">
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-bank-blue px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-bank-blue/25 hover:bg-indigo-700 active:scale-95 transition-all duration-200">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Simpan
        </button>
        <a href="{{ route('admin.users.index') }}" class="rounded-xl bg-bank-light px-5 py-2.5 text-sm font-medium text-bank-navy hover:bg-bank-light/80 active:scale-95 transition-all duration-200">Batal</a>
    </div>
</div>
