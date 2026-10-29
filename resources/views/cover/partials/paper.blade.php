@php
    $field = fn (string $key, string $placeholder) => '<span data-preview-target="'.$key.'" data-placeholder="'.e($placeholder).'">'.e($placeholder).'</span>';
@endphp

{{-- Always light, so it prints the same in either theme. Sizes use cqw so the preview and the A4 print match. --}}
<div id="contentToPrint" data-bs-theme="light" class="cover-paper bg-white text-dark shadow tw:@container tw:aspect-[210/297] tw:font-serif">
    <div class="d-flex flex-column text-center h-100 tw:p-[7cqw]">
        <div class="d-flex flex-column h-100 tw:border-[0.4cqw] tw:border-solid tw:border-[#0f5c5e] tw:p-[5cqw]">
            <img class="mx-auto tw:size-[13cqw]" src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">
            <p class="fw-bold text-uppercase mb-0 tw:mt-[3cqw] tw:text-[4.2cqw] tw:leading-tight tw:tracking-wide">Tripura Institute of Paramedical Sciences</p>

            <div class="tw:my-[6cqw] tw:h-[0.3cqw] tw:w-[20cqw] mx-auto tw:bg-[#0f5c5e]"></div>

            <p class="fw-bold text-uppercase mb-0 tw:text-[6.5cqw] tw:tracking-[0.12em]">{!! $field('page_type', 'Assignment') !!}</p>
            <p class="fst-italic mb-0 tw:mt-[2cqw] tw:text-[3cqw] tw:text-[#555]">on</p>
            <p class="fw-semibold mb-0 tw:mt-[1cqw] tw:text-[4.6cqw] tw:leading-snug">{!! $field('subject', 'Subject') !!}</p>
            <p class="mb-0 tw:mt-[1cqw] tw:text-[3cqw]">Subject code: {!! $field('code', '—') !!}</p>

            <div class="d-grid text-start mt-auto tw:grid-cols-2 tw:gap-[5cqw] tw:text-[2.8cqw] tw:leading-relaxed">
                <div>
                    <p class="fw-bold mb-0 tw:mb-[1.5cqw] tw:border-b-[0.25cqw] tw:border-solid tw:border-[#999] tw:pb-[0.8cqw] tw:text-[3cqw]">Submitted by</p>
                    <p class="mb-0"><strong>{!! $field('title', 'Mr.') !!} {!! $field('name', 'Your name') !!}</strong></p>
                    <p class="mb-0">College ID: {!! $field('std_id', '—') !!}</p>
                    <p class="mb-0">University roll: {!! $field('tu_roll', '—') !!}</p>
                    <p class="mb-0">{!! $field('course', 'Course') !!}, {!! $field('sem', '—') !!} semester, section {!! $field('section', 'A') !!}</p>
                    <p class="mb-0">{!! $field('std_dept', 'Department') !!}</p>
                </div>
                <div>
                    <p class="fw-bold mb-0 tw:mb-[1.5cqw] tw:border-b-[0.25cqw] tw:border-solid tw:border-[#999] tw:pb-[0.8cqw] tw:text-[3cqw]">Submitted to</p>
                    <p class="mb-0"><strong>{!! $field('f_name', 'Teacher') !!}</strong></p>
                    <p class="mb-0">{!! $field('f_designation', 'Designation') !!}</p>
                    <p class="mb-0">{!! $field('f_dept', 'Department') !!}</p>
                </div>
            </div>

            <p class="mb-0 tw:mt-[6cqw] tw:text-[2.8cqw]">Date of submission: {!! $field('dos', '—') !!}</p>
        </div>
    </div>
</div>
