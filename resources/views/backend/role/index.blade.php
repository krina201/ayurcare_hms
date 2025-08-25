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
                <h3 class="text-xl font-semibold text-stone-800">Role Management</h3>
            </div>

            @php $isEdit = isset($editRole); @endphp
            <form id="role-form" method="POST"
                action="{{ $isEdit ? route('role.update', $editRole->id) : route('role.store') }}" novalidate
                class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-stone-700">Role Name</label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name', $isEdit ? $editRole->name : '') }}"
                            class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Role Name" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 js-error" id="name-error" style="display:none"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700">Permissions</label>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto p-2 border rounded">
                            @if (!empty($permission) && $permission->isNotEmpty())
                                @foreach ($permission as $groupName => $groupPermissions)
                                    @php $groupSlug = \Illuminate\Support\Str::slug($groupName); @endphp
                                    <div class="border p-2 rounded">
                                        <div class="flex items-center justify-between mb-2">
                                            <strong class="text-sm">{{ ucfirst(last(explode('-', $groupName))) }}</strong>
                                            <div>
                                                <input type="checkbox" class="form-check-input select-all mr-2"
                                                    data-group="{{ $groupSlug }}" id="select-all-{{ $groupSlug }}">
                                                <label for="select-all-{{ $groupSlug }}"
                                                    class="text-xs text-stone-600">Select All</label>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 gap-2">
                                            @foreach ($groupPermissions as $perm)
                                                @php $checked = isset($hasepermission) && $hasepermission->contains($perm->name); @endphp
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="permissiones[]"
                                                        class="permission-checkbox {{ $groupSlug }} mr-2"
                                                        value="{{ $perm->name }}" id="permission-{{ $perm->id }}"
                                                        {{ old('permissiones') && in_array($perm->name, (array) old('permissiones')) ? 'checked' : ($checked ? 'checked' : '') }}>
                                                    <label for="permission-{{ $perm->id }}"
                                                        class="text-sm text-stone-700">{{ $perm->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-sm text-stone-600">No permissions found.</p>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-red-600 js-error" id="permissiones-error" style="display:none"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('role') }}"
                        class="px-4 py-2 rounded border border-stone-300 text-stone-700 hover:bg-stone-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                        {{ $isEdit ? 'Save Changes' : 'Create Role' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-stone-800">Role List</h4>
            </div>
            @if (
                $role instanceof \Illuminate\Contracts\Pagination\Paginator ||
                    $role instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex justify-between items-center mb-4" id="searchResults">
                    <h3 class="text-lg font-semibold text-stone-800">Search Results <span
                            class="text-green-600 text-sm font-normal">({{ $role->total() }} roles
                            found)</span></h3>

                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-stone-600">Show:</span>
                        <form method="GET" action="{{ route('role') }}">
                            @foreach (request()->except(['per_page']) as $k => $v)
                                @if (is_array($v))
                                    @foreach ($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <select name="per_page" onchange="this.form.submit()"
                                class="text-sm rounded-md border-stone-300 px-2 py-1">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </form>

                        <form method="GET" action="{{ route('role') }}">
                            @foreach (request()->except(['q']) as $k => $v)
                                @if (is_array($v))
                                    @foreach ($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search roles..."
                                class="border rounded px-2 py-1 text-sm">
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-4">&nbsp;</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="roleTable">
                    <thead class="bg-stone-50 text-stone-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Permissions</th>
                            <th class="px-4 py-3 font-medium">Created</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($role) && ($role->count() || (method_exists($role, 'items') && count($role->items()))))
                            @foreach ($role->items() ?? $role as $roles)
                                <tr class="border-t">
                                    <td class="px-4 py-2 text-stone-700">{{ $roles->name }}</td>
                                    <td class="px-4 py-2 text-stone-700">
                                        @if ($roles->permissions->isNotEmpty())
                                            @php $counter = 0; @endphp
                                            @foreach ($roles->permissions as $permissiones)
                                                {{ $permissiones->name }}
                                                @php $counter++; @endphp
                                                @if ($counter % 10 == 0 && !$loop->last)
                                                    <br>
                                                @else
                                                    {{ !$loop->last ? ', ' : '' }}
                                                @endif
                                            @endforeach
                                        @else
                                            No Permissions Assigned
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $roles->created_at->format('d-m-Y') }}</td>
                                    <td class="px-4 py-2">
                                        {{-- edit button --}}
                                        {{-- @can('edit-roles') --}}
                                        <a class='text-stone-700 hover:text-stone-900 mr-3'
                                            href='{{ route('role.edit', $roles->id) }}' title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        {{-- @endcan --}}

                                        {{-- delete button --}}
                                        {{-- @can('delete-roles') --}}
                                        <button type="button" class="delete-role-btn text-red-600 hover:text-red-900"
                                            data-id="{{ $roles->id }}" title="Delete"><i
                                                class="fa-solid fa-trash"></i></button>
                                        <form id="delete-role-form-{{ $roles->id }}"
                                            action="{{ route('role.delete', $roles->id) }}" method="post"
                                            style="display:none;">
                                            @csrf
                                            @method('delete')
                                        </form>
                                        {{-- @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            @if (
                $role instanceof \Illuminate\Contracts\Pagination\Paginator ||
                    $role instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-stone-600">
                        Showing <span class="font-medium">{{ $role->firstItem() }}</span> to <span
                            class="font-medium">{{ $role->lastItem() }}</span> of <span
                            class="font-medium">{{ $role->total() }}</span> results
                    </div>
                    <div class="flex items-center space-x-2">
                        <div>{{ $role->appends(request()->query())->links() }}</div>
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
            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-role-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-role-form-' + id).action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this role?',
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
                                        text: 'Role deleted successfully!',
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

            // Select all behavior per permission group
            document.querySelectorAll('.select-all').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    const group = this.getAttribute('data-group');
                    const checked = this.checked;
                    document.querySelectorAll('.' + group + '.permission-checkbox').forEach(
                        function(c) {
                            c.checked = checked;
                        });
                });
            });

            // Client-side validation for create/edit role form
            const form = document.getElementById('role-form');
            if (!form) return;

            const fields = {
                name: document.getElementById('name'),
                permissiones: () => document.querySelectorAll('input[name="permissiones[]"]')
            };

            function showError(id, msg) {
                const el = document.getElementById(id + '-error');
                if (el) {
                    el.textContent = msg;
                    el.style.display = 'block';
                }
                const input = document.getElementById(id);
                if (input) input.classList.add('border-red-500');
            }

            function clearError(id) {
                const el = document.getElementById(id + '-error');
                if (el) {
                    el.textContent = '';
                    el.style.display = 'none';
                }
                const input = document.getElementById(id);
                if (input) input.classList.remove('border-red-500');
            }

            form.addEventListener('submit', function(e) {
                let valid = true;
                // name
                const name = fields.name && fields.name.value.trim();
                clearError('name');
                if (!name) {
                    showError('name', 'Role Name is required.');
                    valid = false;
                } else if (name.length < 3) {
                    showError('name', 'Role Name must be at least 3 characters.');
                    valid = false;
                } else if (name.length > 50) {
                    showError('name', 'Role Name may not be greater than 50 characters.');
                    valid = false;
                }

                // permissions
                clearError('permissiones');
                const anyChecked = Array.from(fields.permissiones()).some(i => i.checked);
                if (!anyChecked) {
                    showError('permissiones', 'Please select at least one permission.');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    const firstErr = document.querySelector('.js-error[style*="display: block"]');
                    if (firstErr) firstErr.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });

            if (fields.name) fields.name.addEventListener('input', () => clearError('name'));
            document.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'permissiones[]') clearError('permissiones');
            });
        });
    </script>
@endpush
