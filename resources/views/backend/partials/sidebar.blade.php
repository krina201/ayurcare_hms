<div id="sidebar"
    class="fixed top-0 left-0 h-full w-64 bg-white shadow-lg transform transition-transform duration-300 z-30 md:translate-x-0 -translate-x-full">
    <div class="p-5 border-b border-gray-200">
        <div class="flex items-center justify-center">
            <i class="fa-solid fa-leaf text-green-700 text-2xl mr-2"></i>
            <h1 class="text-xl font-bold text-stone-800">AyurCare HMS</h1>
        </div>
    </div>

    <div class="py-4 px-3 overflow-y-auto">
        <ul class="space-y-2">
            {{-- patient management --}}
            @can('view-patient')
                <li>
                    <a href="{{ route('patients') }}"
                        class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('patients*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}"
                        aria-current="{{ request()->routeIs('patients*') ? 'page' : 'false' }}">
                        <i class="fa-solid fa-user-plus w-6 text-green-700"></i>
                        <span class="ml-3">{{ __('Patient Management') }}</span>
                    </a>
                </li>
            @endcan

            {{-- doctor management --}}
            <li>
                <a href="{{ route('doctors') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('doctors*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-user-doctor w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Doctor & Therapist') }}</span>
                </a>
            </li>

            {{-- appointment management --}}
            <li>
                <a href="{{ route('appointments.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('appointments*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-calendar-check w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Appointments') }}</span>
                </a>
            </li>

            {{-- panchkarma management --}}
            <li>
                <a href="{{ route('panchkarma.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('panchkarma*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-spa w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Panchkarma') }}</span>
                </a>
            </li>

            {{-- pharmacy management --}}
            <li>
                <a href="{{ route('pharmacy.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('pharmacy*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-mortar-pestle w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Pharmacy') }}</span>
                </a>
            </li>

            {{-- billing management --}}
            <li>
                <a href="{{ route('billing.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('billing*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-file-invoice w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Billing') }}</span>
                </a>
            </li>

            {{-- inventory management --}}
            <li>
                <a href="{{ route('inventory.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('inventory*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-boxes-stacked w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Inventory') }}</span>
                </a>
            </li>

            {{-- commission management --}}
            <li>
                <a href="{{ route('commission.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('commission*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-coins w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Commission & Ledger') }}</span>
                </a>
            </li>

            {{-- reports management --}}
            <li>
                <a href="{{ route('reports.index') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('reports*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-chart-line w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Reports & Analytics') }}</span>
                </a>
            </li>

            {{-- admin settings --}}
            <li>
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('admin*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                    <i class="fa-solid fa-gears w-6 text-green-700"></i>
                    <span class="ml-3">{{ __('Admin Settings') }}</span>
                </a>
            </li>

            {{-- role management --}}
            @can('view-roles')
                <li>
                    <a href="{{ route('role') }}"
                        class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('role*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                        <i class="fa-solid fa-user-shield w-6 text-green-700"></i>
                        <span class="ml-3">{{ __('Role Management') }}</span>
                    </a>
                </li>
            @endcan

            {{-- permission management --}}
            @can('view-permission')
                <li>
                    <a href="{{ route('permission') }}"
                        class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('permission*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                        <i class="fa-solid fa-key w-6 text-green-700"></i>
                        <span class="ml-3">{{ __('Permission Management') }}</span>
                    </a>
                </li>
            @endcan

            {{-- user management --}}
            @can('view-user')
                <li>
                    <a href="{{ route('user') }}"
                        class="flex items-center p-2 text-base font-medium rounded-lg cursor-pointer
                           {{ request()->routeIs('user*') ? 'text-stone-800 bg-green-100' : 'text-stone-700 hover:bg-green-100' }}">
                        <i class="fa-solid fa-users w-6 text-green-700"></i>
                        <span class="ml-3">{{ __('User Management') }}</span>
                    </a>
                </li>
            @endcan

        </ul>
    </div>
</div>
