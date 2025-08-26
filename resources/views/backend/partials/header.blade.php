<header class="bg-white shadow-sm sticky top-0 z-20">
    <div class="px-4 py-3 flex items-center justify-between">
        <div class="flex items-center">
            <button id="sidebarToggle" class="md:hidden text-stone-700 mr-2">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            {{-- @php
                $segments = Request::segments();
                $filteredSegments = $segments;

                // Remove the last segment if it appears to be an encoded string
                if (str_starts_with(end($segments), 'eyJpdiI6Im')) {
                    array_pop($filteredSegments);
                }
            @endphp

            @foreach ($filteredSegments as $index => $segment)
                @php
                    // Skip segments containing "admin" or numeric-only segments
                    if (str_contains(strtolower($segment), 'admin') || ctype_digit($segment)) {
                        continue;
                    }
                @endphp

                @if ($index + 1 < count($filteredSegments))
                @else
                    <h2 class="text-lg font-semibold text-stone-800">{{ ucfirst($segment) }}</h2>
                @endif
            @endforeach --}}

            <h2 class="text-lg font-semibold text-stone-800">{{ $pagename }}</h2>
        </div>

        <div class="flex items-center space-x-4">
            {{-- @permission('users.view') --}}
            <a href="{{ route('user') }}" class="text-stone-700 hover:bg-green-100 rounded-full p-2"
                title="User Management">
                <i class="fa-solid fa-users"></i>
            </a>
            {{-- @endpermission --}}
            <button class="text-stone-700 hover:bg-green-100 rounded-full p-2"><i class="fa-solid fa-bell"></i></button>
            <div class="relative">
                <button class="flex items-center text-sm focus:outline-none">
                    <img class="h-8 w-8 rounded-full"
                        src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-3.jpg"
                        alt="User avatar">
                    <span class="hidden md:block ml-2 text-stone-800">Dr. Sharma</span>
                    <i class="fa-solid fa-chevron-down ml-1 text-xs text-stone-600"></i>
                </button>
            </div>
        </div>
    </div>
</header>
