@extends('backend.layouts.master')

@section('content')
    <main class="p-4">
        <div class="mb-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">{{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-2 rounded border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-stone-800">User Management</h3>

            </div>

            @php $isEdit = isset($editUser); @endphp
            <form id="user-form" method="POST"
                action="{{ $isEdit ? route('user.update', $editUser->id) : route('user.store') }}" novalidate
                class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-stone-700">User Name</label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name', $isEdit ? $editUser->name : '') }}"
                            class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="User Name" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 js-error" id="name-error" style="display:none"></p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-stone-700">Email Address</label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', $isEdit ? $editUser->email : '') }}"
                            class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="example@domain.com" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 js-error" id="email-error" style="display:none"></p>
                    </div>

                    <div>
                        <label for="role_id" class="block text-sm font-medium text-stone-700">User Role</label>
                        <select name="role_id" id="role_id"
                            class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500"
                            {{ $isEdit && $editUser->role_id == 1 ? 'disabled' : '' }} required>
                            <option value="">Select User Role</option>
                            @foreach ($role as $roles)
                                <option value="{{ $roles->id }}"
                                    {{ old('role_id', $isEdit ? $editUser->role_id : '') == $roles->id ? 'selected' : '' }}>
                                    {{ $roles->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($isEdit && $editUser->role_id == 1)
                            <input type="hidden" name="role_id" value="{{ $editUser->role_id }}">
                        @endif
                        @error('role_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 js-error" id="role_id-error" style="display:none"></p>
                    </div>

                    @if (!$isEdit || ($isEdit && auth()->id() != $editUser->id))
                        <div>
                            <label for="web_loging" class="block text-sm font-medium text-stone-700">Login Devices</label>
                            <select name="web_loging" id="web_loging"
                                class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500"
                                {{ $isEdit ? '' : 'required' }}>
                                <option value="">Select Login Devices</option>
                                @for ($i = 1; $i <= 15; $i++)
                                    <option value="{{ $i }}"
                                        {{ old('web_loging', $isEdit ? $editUser->web_loging ?? '' : '') == $i ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                            @error('web_loging')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-red-600 js-error" id="web_loging-error" style="display:none"></p>
                        </div>
                    @endif

                    @if (!$isEdit)
                        <div>
                            <label for="password" class="block text-sm font-medium text-stone-700">Password</label>
                            <input type="password" name="password" id="password" value="{{ old('password') }}"
                                class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Password" required>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-red-600 js-error" id="password-error" style="display:none"></p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-stone-700">Confirm
                                Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Confirm Password" required>
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-red-600 js-error" id="password_confirmation-error"
                                style="display:none"></p>
                        </div>
                    @endif

                    @if (!$isEdit || ($isEdit && auth()->id() != $editUser->id))
                        <div>
                            <label for="is_active" class="block text-sm font-medium text-stone-700">Status</label>
                            <select name="is_active" id="is_active"
                                class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-green-500"
                                {{ $isEdit ? '' : 'required' }}>
                                <option value="1"
                                    {{ old('is_active', $isEdit ? $editUser->is_active : '1') == '1' ? 'selected' : '' }}>
                                    Enable</option>
                                <option value="0"
                                    {{ old('is_active', $isEdit ? $editUser->is_active : '1') == '0' ? 'selected' : '' }}>
                                    Disable</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('user') }}"
                        class="px-4 py-2 rounded border border-stone-300 text-stone-700 hover:bg-stone-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                        {{ $isEdit ? 'Save Changes' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-stone-800">Users List</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-left" id="UserTable">
                    <thead class="bg-stone-50 text-stone-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Role ID</th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Is Active</th>
                            <th class="px-4 py-3 font-medium">Login Devices</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($user as $users)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-stone-700">{{ $users->role_id }}</td>
                                <td class="px-4 py-2 text-stone-700">{{ $users->name }}</td>
                                <td class="px-4 py-2 text-stone-700">{{ $users->email }}</td>
                                <td class="px-4 py-2">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ (int) $users->is_active === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ (int) $users->is_active === 1 ? 'Enable' : 'Disable' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-stone-700">{{ $users->web_loging }}</td>
                                <td class="px-4 py-2">
                                    {{-- edit butten --}}
                                    <a href="{{ route('user.edit', $users->id) }}"
                                        class="text-stone-700 hover:text-stone-900 mr-3" title="Edit"><i
                                            class="fa-solid fa-pen-to-square"></i></a>

                                    {{-- delete butten --}}
                                    <button type="button" class="delete-user-btn text-red-600 hover:text-red-900"
                                        data-id="{{ $users->id }}" title="Delete"><i
                                            class="fa-solid fa-trash"></i></button>
                                    <form id="delete-user-form-{{ $users->id }}"
                                        action="{{ route('user.delete', $users->id) }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-4 text-center text-stone-600">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Client-side validation for create/edit user form
        document.addEventListener('DOMContentLoaded', function() {

            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-user-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-user-form-' + id).action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this User?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'No, cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    _method: 'DELETE'
                                })
                            }).then(res => {
                                if (res.ok) {
                                    Swal.fire({
                                        text: 'User deleted successfully!',
                                        icon: 'success'
                                    }).then(() => location.reload());
                                } else {
                                    throw new Error('Failed');
                                }
                            }).catch(() => {
                                Swal.fire({
                                    text: 'Something went wrong!',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });

            const form = document.getElementById('user-form');
            if (!form) return;

            const fields = {
                name: document.getElementById('name'),
                email: document.getElementById('email'),
                role_id: document.getElementById('role_id'),
                web_loging: document.getElementById('web_loging'),
                password: document.getElementById('password'),
                password_confirmation: document.getElementById('password_confirmation'),
                is_active: document.getElementById('is_active')
            };

            function showError(fieldName, message) {
                const el = document.getElementById(fieldName + '-error');
                const input = fields[fieldName];
                if (el) {
                    el.textContent = message;
                    el.style.display = 'block';
                }
                if (input) input.classList.add('border-red-500');
            }

            function clearError(fieldName) {
                const el = document.getElementById(fieldName + '-error');
                const input = fields[fieldName];
                if (el) {
                    el.textContent = '';
                    el.style.display = 'none';
                }
                if (input) input.classList.remove('border-red-500');
            }

            function validateEmail(email) {
                // simple RFC-like email regex
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            function validate() {
                let valid = true;
                // name
                const name = fields.name && fields.name.value.trim();
                clearError('name');
                if (!name) {
                    showError('name', 'User Name field is required.');
                    valid = false;
                } else if (name.length > 255) {
                    showError('name', 'User Name may not be greater than 255 characters.');
                    valid = false;
                }

                // email
                const email = fields.email && fields.email.value.trim();
                clearError('email');
                if (!email) {
                    showError('email', 'Email field is required.');
                    valid = false;
                } else if (!validateEmail(email)) {
                    showError('email', 'Please provide a valid Email address.');
                    valid = false;
                } else if (email.length > 255) {
                    showError('email', 'Email may not be greater than 255 characters.');
                    valid = false;
                }

                // role
                clearError('role_id');
                const role = fields.role_id && fields.role_id.value;
                if (!role) {
                    showError('role_id', 'Role selection is required.');
                    valid = false;
                }

                // web_loging (only if present in form)
                if (fields.web_loging) {
                    clearError('web_loging');
                    const web = fields.web_loging.value;
                    if (!web) {
                        showError('web_loging', 'Login Devices selection is required.');
                        valid = false;
                    }
                }

                // password rules only on create (password input exists for create)
                if (fields.password) {
                    clearError('password');
                    clearError('password_confirmation');
                    const pwd = fields.password.value || '';
                    const pwdc = fields.password_confirmation && fields.password_confirmation.value || '';
                    if (!pwd) {
                        showError('password', 'Password field is required.');
                        valid = false;
                    } else if (pwd.length < 8) {
                        showError('password', 'Password must be at least 8 characters long.');
                        valid = false;
                    }
                    if (!pwdc) {
                        showError('password_confirmation', 'Password confirmation is required.');
                        valid = false;
                    } else if (pwd !== pwdc) {
                        showError('password_confirmation', 'Password confirmation does not match.');
                        valid = false;
                    }
                }

                return valid;
            }

            // Clear field error on input/change
            Object.keys(fields).forEach(function(key) {
                const el = fields[key];
                if (!el) return;
                el.addEventListener('input', function() {
                    clearError(key);
                });
                el.addEventListener('change', function() {
                    clearError(key);
                });
            });

            form.addEventListener('submit', function(e) {
                if (!validate()) {
                    e.preventDefault();
                    // Scroll to first visible error
                    const firstErr = document.querySelector('.js-error[style*="display: block"]');
                    if (firstErr) firstErr.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });
        });
    </script>
@endpush
