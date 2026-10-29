@php
    $user = auth()->user();
@endphp

<x-layouts.portal :portal="$portal" title="My profile">
    <x-page-header title="My profile" />

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="card shadow-sm" aria-labelledby="photo-heading">
                <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                    <h2 id="photo-heading" class="visually-hidden">Photo</h2>
                    <span class="avatar tw:size-24! tw:text-2xl!" data-photo-preview>{{ $user->initials }}</span>
                    <p class="fw-semibold mt-3 mb-0">{{ $user->name }}</p>
                    <p class="small text-body-secondary mb-3">{{ $user->role->label() }}</p>
                    <label class="btn btn-sm btn-outline-success">
                        <i class="fa-solid fa-camera me-1" aria-hidden="true"></i> Change photo
                        <input type="file" accept="image/jpeg,image/png,image/webp" class="visually-hidden" data-photo-input="photo-error">
                    </label>
                    <p id="photo-error" class="small text-danger mt-2 mb-0" role="alert" hidden></p>
                    <p class="form-text mb-0">JPG, PNG or WebP, 2 MB at most.</p>
                </div>
            </section>
        </div>

        <div class="col-lg-8 vstack gap-4">
            <form method="POST" class="card shadow-sm" data-demo-form="Your details are saved." novalidate aria-labelledby="details-heading">
                @csrf
                <div class="card-header py-3"><h2 id="details-heading" class="h6 mb-0">Your details</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="profile_name">Full name</label>
                            <input id="profile_name" name="name" class="form-control" value="{{ $user->name }}" autocomplete="name" required maxlength="100">
                            <x-field-error for="name">Your name can't be empty.</x-field-error>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="profile_email">Email</label>
                            <input id="profile_email" class="form-control" value="{{ $user->email }}" readonly aria-describedby="profile_email_help">
                            <div id="profile_email_help" class="form-text">Ask an admin if this needs to change.</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end"><button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-1" aria-hidden="true"></i> Save</button></div>
            </form>

            <form method="POST" class="card shadow-sm" data-demo-form="Password changed. You've been signed out on other devices." novalidate aria-labelledby="password-heading">
                @csrf
                <div class="card-header py-3"><h2 id="password-heading" class="h6 mb-0">Change password</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="current_password">Current password</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                            <x-field-error for="current_password">Enter your current password.</x-field-error>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="password">New password</label>
                            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password" minlength="8" required>
                            <x-field-error for="password">Use at least 8 characters.</x-field-error>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="password_confirmation">Type it again</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" data-match="password" required>
                            <x-field-error for="password_confirmation">The two passwords don't match.</x-field-error>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end"><button type="submit" class="btn btn-success" data-busy="Updating…"><i class="fa-solid fa-key me-1" aria-hidden="true"></i> Change password</button></div>
            </form>
        </div>
    </div>
</x-layouts.portal>
