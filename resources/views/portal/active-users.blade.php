<x-layouts.portal :portal="$portal" title="Signed in now">
    <x-page-header title="Signed in now" />

    @if ($users->isNotEmpty())
        <div class="card">
            <ul class="list-group list-group-flush">
                @foreach ($users as $user)
                    <li class="list-group-item d-flex align-items-center gap-3 py-3">
                        <x-avatar :user="$user" />
                        <span class="flex-grow-1 tw:min-w-0">
                            <span class="d-block fw-semibold text-truncate">{{ $user->name }}</span>
                            <span class="d-block small text-body-secondary text-truncate">{{ $user->role->label() }} · active {{ $user->last_active_at->diffForHumans() }}</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" data-bs-toggle="modal" data-bs-target="#force-logout" data-record="{{ json_encode(['name' => $user->name]) }}" data-action="{{ route('admin.active-users.sign-out', $user) }}">
                            <i class="fa-solid fa-arrow-right-from-bracket me-1" aria-hidden="true"></i> Sign out
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card">
            <x-empty-state icon="fa-moon" title="Nobody else is signed in">
                This list updates when you reload the page.
            </x-empty-state>
        </div>
    @endif

    <x-modal id="force-logout" icon="fa-arrow-right-from-bracket" title="Sign this person out?" submit="Sign out" busy="Signing out…" danger :action="route('admin.active-users')">
        <p class="mb-0"><strong data-record-text="name"></strong> will be signed out on every device straight away. Anything they haven't saved will be lost.</p>
    </x-modal>
</x-layouts.portal>
