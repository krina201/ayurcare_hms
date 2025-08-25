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
                <h3 class="text-xl font-semibold text-stone-800">Permission Management</h3>
            </div>

            @php $isEdit = isset($editPermission); @endphp
            <form id="permission-form" method="POST"
                action="{{ $isEdit ? route('permission.update', $editPermission->id) : route('permission.store') }}"
                novalidate class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-stone-700">Permission Name</label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name', $isEdit ? $editPermission->name : '') }}"
                            class="mt-1 block w-full rounded border border-stone-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Permission Name" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-red-600 js-error" id="name-error" style="display:none"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('permission') }}"
                        class="px-4 py-2 rounded border border-stone-300 text-stone-700 hover:bg-stone-50">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                        {{ $isEdit ? 'Save Changes' : 'Create Permission' }}
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-medium text-stone-800">Permission List</h4>
            </div>
            @if (
                $permission instanceof \Illuminate\Contracts\Pagination\Paginator ||
                    $permission instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex justify-between items-center mb-4" id="searchResults">
                    <h3 class="text-lg font-semibold text-stone-800">Search Results <span
                            class="text-green-600 text-sm font-normal">({{ $permission->total() }} permissions
                            found)</span></h3>

                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-stone-600">Show:</span>
                        <form method="GET" action="{{ route('permission') }}">
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

                        <form method="GET" action="{{ route('permission') }}">
                            @foreach (request()->except(['q']) as $k => $v)
                                @if (is_array($v))
                                    @foreach ($v as $vv)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach
                            <input type="search" name="q" value="{{ request('q') }}"
                                placeholder="Search permissions..." class="border rounded px-2 py-1 text-sm">
                        </form>
                    </div>
                </div>
            @else
                <div class="mb-4">&nbsp;</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="PermissionTable">
                    <thead class="bg-stone-50 text-stone-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Created</th>
                            <th class="px-4 py-3 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permission->items() ?? $permission as $permissiones)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-stone-700">{{ $permissiones->name }}</td>
                                <td class="px-4 py-2 text-stone-700">{{ $permissiones->created_at->format('d-m-Y') }}</td>
                                <td class="px-4 py-2">
                                    {{-- edit butten --}}
                                    <a href="{{ route('permission.edit', $permissiones->id) }}"
                                        class="text-stone-700 hover:text-stone-900 mr-3" title="Edit"><i
                                            class="fa-solid fa-pen-to-square"></i></a>

                                    {{-- delet butten --}}
                                    <button type="button" class="delete-permission-btn text-red-600 hover:text-red-900"
                                        data-id="{{ $permissiones->id }}" title="Delete"><i
                                            class="fa-solid fa-trash"></i></button>
                                    <form id="delete-permission-form-{{ $permissiones->id }}"
                                        action="{{ route('permission.delete', $permissiones->id) }}" method="post"
                                        style="display:none;">
                                        @csrf
                                        @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-4 text-center text-stone-600">No permissions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (
                $permission instanceof \Illuminate\Contracts\Pagination\Paginator ||
                    $permission instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="flex items-center justify-between mt-4">
                    <div class="text-sm text-stone-600">
                        Showing <span class="font-medium">{{ $permission->firstItem() }}</span> to <span
                            class="font-medium">{{ $permission->lastItem() }}</span> of <span
                            class="font-medium">{{ $permission->total() }}</span> results
                    </div>
                    <div class="flex items-center space-x-2">
                        <div>{{ $permission->appends(request()->query())->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Client-side validation for create/edit permission form
        document.addEventListener('DOMContentLoaded', function() {

            // Confirm delete helper using fetch to send DELETE
            document.querySelectorAll('.delete-permission-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const url = document.getElementById('delete-permission-form-' + id).action;
                    Swal.fire({
                        text: 'Are you sure you want to delete this Permission?',
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
                                        text: 'Permission deleted successfully!',
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

            const form = document.getElementById('permission-form');
            if (!form) return;

            const fields = {
                name: document.getElementById('name')
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

            function validate() {
                let valid = true;
                // name
                const name = fields.name && fields.name.value.trim();
                clearError('name');
                if (!name) {
                    showError('name', 'Permission field is required.');
                    valid = false;
                } else if (name.length > 255) {
                    showError('name', 'Permission may not be greater than 255 characters.');
                    valid = false;
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
