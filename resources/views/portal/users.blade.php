@php
    $roleDescriptions = [
        'tech' => 'Can manage departments, subjects and teachers.',
        'admin' => 'Everything above, plus user accounts.',
    ];
    $statusLabel = fn (\App\Models\User $user): string => match (true) {
        (bool) $user->deactivated_at => 'Deactivated',
        $user->must_change_password => 'Waiting for first sign-in',
        default => 'Active',
    };
    $statusPill = fn (\App\Models\User $user): string => match (true) {
        (bool) $user->deactivated_at => 'pill-danger',
        $user->must_change_password => 'pill-warning',
        default => 'pill-success',
    };

    $createErrors = $errors->getBag('default');
    $updateErrors = $errors->getBag('updateUser');

    /* After a failed edit, reopen the dialog pointed at the account being edited. */
    $editAction = $updateErrors->any() && old('user_id')
        ? route('admin.users.update', old('user_id'))
        : route('admin.users');
@endphp

<x-layouts.portal :portal="$portal" title="User accounts">
    <x-page-header title="User accounts" />

    <x-table-card id="users-table" title="All accounts" :count="$users->count()" placeholder="Search name or email">
        <x-slot:actions>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add-user"><i class="fa-solid fa-user-plus me-1" aria-hidden="true"></i> Create account</button>
        </x-slot:actions>
        <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php($record = ['user_id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role->value])
                @php($isMe = $user->is(auth()->user()))
                <tr>
                    <td class="fw-semibold">{{ $user->name }} @if ($isMe)<span class="small text-body-secondary fw-normal">(you)</span>@endif</td>
                    <td class="text-break">{{ $user->email }}</td>
                    <td><span class="pill pill-info">{{ $user->role->label() }}</span></td>
                    <td data-export="{{ $statusLabel($user) }}"><span class="pill {{ $statusPill($user) }}">{{ $statusLabel($user) }}</span></td>
                    <td class="text-body-secondary" data-export="{{ $user->created_at->toDateString() }}">{{ $user->created_at->diffForHumans() }}</td>
                    <x-row-actions :record="$record" :label="$user->name" edit="edit-user" :edit-action="route('admin.users.update', $user)" :delete="$isMe ? null : 'delete-user'" :delete-action="route('admin.users.destroy', $user)">
                        @unless ($isMe)
                            <button type="button" class="btn btn-sm btn-icon btn-icon-warning" data-bs-toggle="modal" data-bs-target="#reset-user" data-record="{{ json_encode($record) }}" data-action="{{ route('admin.users.new-password', $user) }}" aria-label="Send {{ $user->name }} a new password" title="Send new password">
                                <i class="fa-solid fa-key" aria-hidden="true"></i>
                            </button>
                            @if ($user->deactivated_at)
                                <button type="button" class="btn btn-sm btn-icon btn-icon-warning" data-bs-toggle="modal" data-bs-target="#reactivate-user" data-record="{{ json_encode($record) }}" data-action="{{ route('admin.users.reactivate', $user) }}" aria-label="Reactivate {{ $user->name }}" title="Reactivate">
                                    <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-sm btn-icon btn-icon-danger" data-bs-toggle="modal" data-bs-target="#deactivate-user" data-record="{{ json_encode($record) }}" data-action="{{ route('admin.users.deactivate', $user) }}" aria-label="Deactivate {{ $user->name }}" title="Deactivate">
                                    <i class="fa-solid fa-user-lock" aria-hidden="true"></i>
                                </button>
                            @endif
                        @endunless
                    </x-row-actions>
                </tr>
            @empty
                <x-empty-state colspan="6" title="No accounts yet" icon="fa-user">
                    Create an account for each admin or tech who needs to sign in. Students sign themselves up.
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    <x-modal id="add-user" icon="fa-paper-plane" title="Create account" submit="Create and send email" busy="Creating…" :action="route('admin.users.store')" :show-on-load="$createErrors->any()">
        <div>
            <label class="form-label" for="add_user_name">Full name</label>
            <input id="add_user_name" name="name" @class(['form-control', 'is-invalid' => $createErrors->has('name')]) required maxlength="100" value="{{ $createErrors->any() ? old('name') : '' }}">
            <x-field-error for="name">Enter their name.</x-field-error>
        </div>
        <div>
            <label class="form-label" for="add_user_email">Email</label>
            <input id="add_user_email" name="email" type="email" @class(['form-control', 'is-invalid' => $createErrors->has('email')]) required maxlength="255" value="{{ $createErrors->any() ? old('email') : '' }}">
            <x-field-error for="email">Enter a valid email address. It's also their username.</x-field-error>
        </div>
        <fieldset>
            <legend class="form-label fs-6">Role</legend>
            @foreach (\App\Enums\UserRole::staff() as $role)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="role" value="{{ $role->value }}" id="add_role_{{ $role->value }}" @checked(($createErrors->any() ? old('role', 'tech') : 'tech') === $role->value) required>
                    <label class="form-check-label" for="add_role_{{ $role->value }}">
                        {{ $role->label() }} <span class="d-block small text-body-secondary">{{ $roleDescriptions[$role->value] }}</span>
                    </label>
                </div>
            @endforeach
            <x-field-error for="role">Choose a role.</x-field-error>
        </fieldset>
    </x-modal>

    <x-modal id="edit-user" icon="fa-floppy-disk" title="Edit account" submit="Save changes" busy="Saving…" :action="$editAction" method="PUT" :show-on-load="$updateErrors->any()">
        <input type="hidden" name="user_id" value="{{ $updateErrors->any() ? old('user_id') : '' }}">
        <div>
            <label class="form-label" for="edit_user_name">Full name</label>
            <input id="edit_user_name" name="name" @class(['form-control', 'is-invalid' => $updateErrors->has('name')]) required maxlength="100" value="{{ $updateErrors->any() ? old('name') : '' }}">
            <x-field-error for="name" bag="updateUser">Enter their name.</x-field-error>
        </div>
        <div>
            <label class="form-label" for="edit_user_email">Email</label>
            <input id="edit_user_email" name="email" type="email" @class(['form-control', 'is-invalid' => $updateErrors->has('email')]) required maxlength="255" value="{{ $updateErrors->any() ? old('email') : '' }}">
            <x-field-error for="email" bag="updateUser">Enter a valid email address. It's also their username.</x-field-error>
        </div>
        <fieldset>
            <legend class="form-label fs-6">Role</legend>
            @foreach (\App\Enums\UserRole::cases() as $role)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="role" value="{{ $role->value }}" id="edit_role_{{ $role->value }}" @checked($updateErrors->any() && old('role') === $role->value) required>
                    <label class="form-check-label" for="edit_role_{{ $role->value }}">{{ $role->label() }}</label>
                </div>
            @endforeach
            <x-field-error for="role" bag="updateUser">Choose a role.</x-field-error>
        </fieldset>
    </x-modal>

    <x-modal id="reset-user" variant="warning" title="Send a new password?" submit="Send new password" busy="Sending…" :action="route('admin.users')">
        <p class="mb-0">We'll email a new password to <strong data-record-text="email"></strong>. Their old password stops working and they'll be signed out.</p>
    </x-modal>

    <x-modal id="deactivate-user" icon="fa-user-lock" title="Deactivate this account?" submit="Deactivate" busy="Deactivating…" danger :action="route('admin.users')">
        <p class="mb-0"><strong data-record-text="name"></strong> will be signed out and won't be able to sign in until you reactivate the account. Nothing is deleted.</p>
    </x-modal>

    <x-modal id="reactivate-user" variant="warning" icon="fa-user-check" title="Reactivate this account?" submit="Reactivate" busy="Reactivating…" :action="route('admin.users')">
        <p class="mb-0"><strong data-record-text="name"></strong> will be able to sign in again with their current password.</p>
    </x-modal>

    <x-modal id="delete-user" title="Delete this account?" submit="Delete for good" busy="Deleting…" danger :action="route('admin.users')" method="DELETE">
        <p class="mb-0"><strong data-record-text="email"></strong> will be deleted. This can't be undone. Deactivate it instead if you might need it again.</p>
    </x-modal>
</x-layouts.portal>
