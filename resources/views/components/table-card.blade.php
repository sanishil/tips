@props(['id', 'title', 'count', 'placeholder' => 'Search'])

{{-- The `actions` slot (Add…, Export) sits beside the search box and shows even when the table is empty. --}}
<div class="card table-card">
    <div class="card-header d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <h2 class="mb-0">{{ $title }}</h2>
        <div class="d-flex flex-column flex-sm-row flex-wrap gap-2">
            @if ($count > 0)
                {{ $filters ?? '' }}
                <div class="input-group table-search tw:sm:w-72">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                    <input type="search" class="form-control" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}" data-table-search="{{ $id }}">
                </div>
            @endif
            {{ $actions ?? '' }}
        </div>
    </div>

    <div class="table-responsive">
        <table id="{{ $id }}" class="table table-hover align-middle mb-0">
            {{ $slot }}
        </table>
    </div>

    @if ($count > 0)
        <div class="card-footer small text-body-secondary">
            Showing <span data-table-count="{{ $id }}">{{ $count }}</span> of {{ $count }}
        </div>
    @endif
</div>
