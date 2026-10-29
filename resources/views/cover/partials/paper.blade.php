@php
    $field = fn (string $key, string $placeholder) => '<span data-preview-target="'.$key.'" data-placeholder="'.e($placeholder).'">'.e($placeholder).'</span>';
    $college = 'Tripura Institute of Paramedical Sciences';
    $address = 'Agartala, Tripura';
@endphp

{{-- Always light, so it prints the same in either theme. Sizes use cqw so the preview and the A4 print match (1cqw ≈ 5.95pt on A4: 26pt = 4.37cqw, 18pt = 3.02cqw, 14pt = 2.35cqw, 12pt = 2.02cqw). --}}
<div id="contentToPrint" data-bs-theme="light" class="cover-paper bg-white tw:@container tw:aspect-[210/297] tw:font-['Times_New_Roman',Times,serif] tw:text-[#1f2d3d]">
    <div class="d-flex flex-column align-items-center text-center h-100 tw:px-[6cqw] tw:pt-[5cqw] tw:pb-[5cqw] tw:text-[2.02cqw] tw:leading-normal">
        <p class="fw-bold mb-0 tw:text-[3.02cqw]">{!! $field('page_type', 'Assignment') !!}</p>
        <p class="fw-bold mb-0 tw:mt-[0.5cqw] tw:text-[4.37cqw] tw:leading-tight tw:text-balance">{{ $college }}</p>

        <div class="d-flex flex-column tw:mt-[3cqw] tw:gap-[1.2cqw]">
            <p data-preview-row="case_presentation" hidden class="mb-0 tw:text-[2.35cqw]"><strong>Case Presentation:</strong> {!! $field('case_presentation', '—') !!}</p>
            <p data-preview-row="topic" hidden class="mb-0 tw:text-[2.35cqw]"><strong>Topic:</strong> {!! $field('topic', '—') !!}</p>
            <p data-preview-row="ward" hidden class="mb-0 tw:text-[2.35cqw]"><strong>Ward:</strong> {!! $field('ward', '—') !!}</p>
        </div>

        <img class="tw:mt-[3.5cqw] tw:size-[17cqw] tw:object-contain" src="{{ asset('img/logo.webp') }}" alt="" aria-hidden="true">

        <p class="fw-bold text-uppercase mb-0 tw:mt-[2.5cqw] tw:text-[2.35cqw]">{{ $address }}</p>

        <p class="mb-0 tw:mt-[2.5cqw] tw:text-[2.35cqw]"><strong>Subject Title:</strong> {!! $field('subject', 'Subject Title') !!}</p>
        <p class="mb-0 tw:mt-[1.2cqw] tw:text-[2.35cqw]"><strong>Subject Code:</strong> {!! $field('code', '—') !!}</p>

        <div class="tw:mt-[4.5cqw]">
            <p class="fw-bold text-decoration-underline mb-0 tw:mb-[1cqw] tw:text-[2.35cqw]">Submitted to</p>
            <p class="fw-bold mb-0">{!! $field('f_name', 'Teacher') !!}</p>
            <p class="mb-0">{!! $field('f_designation', 'Designation') !!}</p>
            <p class="fw-bold mb-0 tw:mt-[2cqw]">Department of {!! $field('f_dept', 'Department') !!}</p>
            <p class="fw-bold mb-0">{{ $college }}</p>
        </div>

        <div class="tw:mt-[4.5cqw]">
            <p class="fw-bold text-decoration-underline mb-0 tw:mb-[1cqw] tw:text-[2.35cqw]">Submitted by</p>
            <p class="fw-bold mb-0">{!! $field('title', 'Mr.') !!} {!! $field('name', 'Your name') !!}</p>
            <p class="mb-0">College ID: {!! $field('std_id', '—') !!}</p>
            <p data-preview-row="tu_roll" hidden class="mb-0">University Roll No.: {!! $field('tu_roll', '—') !!}</p>
            <p class="mb-0">Course: {!! $field('course', '—') !!}</p>
            <p class="mb-0">Semester: {!! $field('sem', '—') !!}</p>
            <p class="mb-0">Section: {!! $field('section', 'A') !!}</p>
            <p class="fw-bold mb-0">Department of {!! $field('std_dept', 'Department') !!}</p>
            <p class="fw-bold mb-0">{{ $college }}</p>
        </div>

        <p class="fw-bold mb-0 mt-auto tw:pt-[3cqw]">Date of Submission: {!! $field('dos', '—') !!}</p>
    </div>
</div>
