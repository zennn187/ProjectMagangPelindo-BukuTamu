<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required maxlength="255" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
        <select name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
            <option value="receptionist" @selected(old('role', $user->role ?? '') === 'receptionist')>Resepsionis / Security</option>
            <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin (SDM & Umum)</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" {{ isset($user) ? '' : 'required' }} minlength="8" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
        @if(isset($user))
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah password.</p>
        @endif
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" minlength="8" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2 text-sm font-medium text-white hover:bg-emerald-700">Simpan</button>
        <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-slate-100 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Batal</a>
    </div>
</div>