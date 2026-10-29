<x-layouts.portal :portal="$portal" title="User accounts">
    <x-page-header title="User accounts" subtitle="New users get their password by email. Nobody, including you, sees it.">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-user">Create account</button>
    </x-page-header>

    <x-table-card id="users-table" title="All accounts" :count="count($users)" placeholder="Search name or email">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last active</th><th data-export-skip><span class="visually-hidden">Actions</span></th></tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                @php($record = ['name' => $user['name'], 'email' => $user['email'], 'type' => $user['type']])
                <tr>
                    <td class="fw-semibold">{{ $user['name'] }}</td>
                    <td class="text-break">{{ $user['email'] }}</td>
                    <td>{{ $user['type'] }}</td>
                    <td>
                        @if ($user['active'])
                            Active
                        @else
                            <span class="text-body-secondary">Deactivated</span>
                        @endif
                    </td>
                    <td class="text-body-secondary">{{ $user['last_activity'] }}</td>
                    <x-row-actions :record="$record" :label="$user['name']" edit="edit-user" delete="delete-user">
                        <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#reset-user" data-record="{{ json_encode($record) }}" aria-label="Send {{ $user['name'] }} a new password" title="Send new password">
                            <i class="fa-solid fa-key" aria-hidden="true"></i>
                        </button>
                        @if ($user['active'])
                            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#deactivate-user" data-record="{{ json_encode($record) }}" aria-label="Deactivate {{ $user['name'] }}" title="Deactivate">
                                <i class="fa-solid fa-user-lock" aria-hidden="true"></i>
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#activate-user" data-record="{{ json_encode($record) }}" aria-label="Reactivate {{ $user['name'] }}" title="Reactivate">
                                <i class="fa-solid fa-user-check" aria-hidden="true"></i>
                            </button>
                        @endif
                    </x-row-actions>
                </tr>
            @empty
                <x-empty-state colspan="6" title="No accounts yet" icon="fa-user">
                    Create an account for each admin or student coordinator who needs to sign in.
                </x-empty-state>
            @endforelse
        </tbody>
    </x-table-card>

    @foreach (['add' => 'Create account', 'edit' => 'Edit account'] as $mode => $heading)
        <x-modal :id="$mode.'-user'" :title="$heading" :submit="$mode === 'add' ? 'Create and send email' : 'Save changes'" :busy="$mode === 'add' ? 'Creating…' : 'Saving…'" :message="$mode === 'add' ? 'Account created. The password is on its way by email.' : 'Changes saved.'">
            <div>
                <label class="form-label" for="{{ $mode }}_user_name">Full name</label>
                <input id="{{ $mode }}_user_name" name="name" class="form-control" required maxlength="100">
                <x-field-error for="name">Enter their name.</x-field-error>
            </div>
            <div>
                <label class="form-label" for="{{ $mode }}_user_email">Email</label>
                <input id="{{ $mode }}_user_email" name="email" type="email" class="form-control" required>
                <x-field-error for="email">Enter a valid email address — it's also their username.</x-field-error>
            </div>
            <fieldset>
                <legend class="form-label fs-6">Role</legend>
                @foreach (['Student' => 'Can manage departments, subjects and teachers.', 'Admin' => 'Everything above, plus user accounts.'] as $role => $description)
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="type" value="{{ $role }}" id="{{ $mode }}_role_{{ $role }}" @checked($role === 'Student') required>
                        <label class="form-check-label" for="{{ $mode }}_role_{{ $role }}">
                            {{ $role }} <span class="d-block small text-body-secondary">{{ $description }}</span>
                        </label>
                    </div>
                @endforeach
            </fieldset>
        </x-modal>
    @endforeach

    <x-modal id="reset-user" title="Send a new password?" submit="Send new password" busy="Sending…" message="New password sent.">
        <p class="mb-0">We'll email a new password to <strong data-record-text="email"></strong>. Their old password stops working and they'll be signed out.</p>
    </x-modal>

    <x-modal id="deactivate-user" title="Deactivate this account?" submit="Deactivate" danger message="Account deactivated.">
        <p class="mb-0"><strong data-record-text="name"></strong> won't be able to sign in until you reactivate the account. Nothing is deleted.</p>
    </x-modal>

    <x-modal id="activate-user" title="Reactivate this account?" submit="Reactivate" message="Account reactivated.">
        <p class="mb-0"><strong data-record-text="name"></strong> will be able to sign in again with their existing password.</p>
    </x-modal>

    <x-modal id="delete-user" title="Delete this account?" submit="Delete for good" busy="Deleting…" danger message="Account deleted.">
        <p class="mb-0"><strong data-record-text="email"></strong> will be deleted. This can't be undone — deactivate instead if you might need it again.</p>
    </x-modal>
</x-layouts.portal>
