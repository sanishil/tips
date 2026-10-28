@props(['id', 'title', 'count', 'placeholder' => 'Search'])

<div class="card shadow-sm">
    <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 py-3">
        <h2 class="h6 mb-0">{{ $title }}</h2>
        @if ($count > 0)
            <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
                {{ $filters ?? '' }}
                <div class="input-group input-group-sm tw:sm:w-60">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                    <input type="search" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" data-table-search="{{ $id }}">
                </div>
            </div>
        @endif
    </div>

    <div class="table-responsive">
        <table id="{{ $id }}" class="table table-hover table-stack align-middle mb-0">
            {{ $slot }}
        </table>
    </div>

    @if ($count > 0)
        <div class="card-footer small text-body-secondary">
            Showing <span data-table-count="{{ $id }}">{{ $count }}</span> of {{ $count }}
        </div>
    @endif
</div>
