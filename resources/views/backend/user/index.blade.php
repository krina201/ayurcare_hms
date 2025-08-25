    @extends('backend.layouts.master')

    @section('content')
        <main class="p-4">
            <div class="mb-4">
                @if (session('success'))
                    <div class="bg-green-50 text-green-700 px-4 py-2 rounded border border-green-200">
                        {{ session('success') }}
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
                                <label for="web_loging" class="block text-sm font-medium text-stone-700">Login
                                    Devices</label>
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
                                <p class="mt-1 text-sm text-red-600 js-error" id="web_loging-error" style="display:none">
                                </p>
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
                @if (
                    $user instanceof \Illuminate\Contracts\Pagination\Paginator ||
                        $user instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="flex justify-between items-center mb-4" id="searchResults">
                        <h3 class="text-lg font-semibold text-stone-800">Search Results
                            <span class="text-green-600 text-sm font-normal">({{ $user->total() }} users found)</span>
                        </h3>

                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-stone-600">Show:</span>
                            <form method="GET" action="{{ route('user') }}">
                                @foreach (request()->except(['per_page']) as $k => $v)
                                    @if (is_array($v))
                                        @foreach ($v as $vv)
                                            <input type="hidden" name="{{ $k }}[]"
                                                value="{{ $vv }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                    @endif
                                @endforeach
                                <select name="per_page" onchange="this.form.submit()"
                                    class="text-sm rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                    </option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </form>

                            <form method="GET" action="{{ route('user') }}" id="userSearchForm">
                                @foreach (request()->except(['q']) as $k => $v)
                                    @if (is_array($v))
                                        @foreach ($v as $vv)
                                            <input type="hidden" name="{{ $k }}[]"
                                                value="{{ $vv }}">
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                    @endif
                                @endforeach
                                <input type="search" name="q" id="userSearchInput" value="{{ request('q') }}"
                                    placeholder="Search users..." class="border rounded px-2 py-1 text-sm">
                            </form>

                        </div>
                    </div>
                @else
                    <div class="mb-4">&nbsp;</div>
                @endif
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="UserTable">
                        <thead class="bg-stone-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Role ID <i class="fa-solid fa-sort ml-1"></i>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Name <i class="fa-solid fa-sort ml-1"></i>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Email <i class="fa-solid fa-sort ml-1"></i>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Status <i class="fa-solid fa-sort ml-1"></i>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    <div class="flex items-center cursor-pointer">
                                        Login Devices <i class="fa-solid fa-sort ml-1"></i>
                                    </div>
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-stone-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($user->items() ?? $user as $users)
                                <tr class="hover:bg-stone-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-800">{{ $users->role_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-stone-800">{{ $users->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">{{ $users->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ (int) $users->is_active === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ (int) $users->is_active === 1 ? 'Enable' : 'Disable' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-700">
                                        {{ $users->web_loging }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        {{-- edit button --}}
                                        <a href="{{ route('user.edit', $users->id) }}"
                                            class="text-stone-600 hover:text-stone-900 mr-2" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        {{-- delete button --}}
                                        <button type="button" class="delete-user-btn text-red-600 hover:text-red-900"
                                            data-id="{{ $users->id }}" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
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
                                    <td colspan="6" class="px-6 py-4 text-center text-stone-600">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (
                    $user instanceof \Illuminate\Contracts\Pagination\Paginator ||
                        $user instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <!-- Modern Pagination -->
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-sm text-stone-600">
                            Showing <span class="font-medium">{{ $user->firstItem() ?? 0 }}</span> to
                            <span class="font-medium">{{ $user->lastItem() ?? 0 }}</span> of
                            <span class="font-medium">{{ $user->total() }}</span> results
                        </div>
                        <div class="flex space-x-1">
                            {{-- Previous Button --}}
                            @if ($user->onFirstPage())
                                <button disabled
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <i class="fa-solid fa-chevron-left text-xs mr-1"></i> Previous
                                </button>
                            @else
                                <a href="{{ $user->previousPageUrl() }}"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-green-50">
                                    <i class="fa-solid fa-chevron-left text-xs mr-1"></i> Previous
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @php
                                $start = max($user->currentPage() - 2, 1);
                                $end = min($start + 4, $user->lastPage());
                                $start = max($end - 4, 1);
                            @endphp

                            @if ($start > 1)
                                <a href="{{ $user->url(1) }}"
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-green-50">1</a>
                                @if ($start > 2)
                                    <span class="inline-flex items-center px-2 py-1 text-sm text-stone-500">...</span>
                                @endif
                            @endif

                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $user->currentPage())
                                    <button
                                        class="inline-flex items-center px-2 py-1 border border-green-500 text-sm font-medium rounded-md text-white bg-green-500">
                                        {{ $i }}
                                    </button>
                                @else
                                    <a href="{{ $user->url($i) }}"
                                        class="inline-flex items-center px-2 py-1 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-green-50">
                                        {{ $i }}
                                    </a>
                                @endif
                            @endfor

                            @if ($end < $user->lastPage())
                                @if ($end < $user->lastPage() - 1)
                                    <span class="inline-flex items-center px-2 py-1 text-sm text-stone-500">...</span>
                                @endif
                                <a href="{{ $user->url($user->lastPage()) }}"
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-green-50">{{ $user->lastPage() }}</a>
                            @endif

                            {{-- Next Button --}}
                            @if ($user->hasMorePages())
                                <a href="{{ $user->nextPageUrl() }}"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-stone-700 bg-white hover:bg-green-50">
                                    Next <i class="fa-solid fa-chevron-right text-xs ml-1"></i>
                                </a>
                            @else
                                <button disabled
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Next <i class="fa-solid fa-chevron-right text-xs ml-1"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </main>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('userSearchInput');
                const searchForm = document.getElementById('userSearchForm');
                let timer = null;

                if (searchInput && searchForm) {
                    searchInput.addEventListener('input', function() {
                        clearTimeout(timer);
                        timer = setTimeout(() => {
                            searchForm.submit(); // Auto submit form
                        }, 500); // Delay 500ms after typing stops
                    });
                }
            });
        </script>
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
