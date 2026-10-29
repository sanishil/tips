<x-layouts.app title="Assignment cover page" :themeable="false" class="intake d-flex flex-column min-vh-100">
    <x-intro-loader />

    <x-site-header>
        <a href="{{ route('login') }}" class="btn btn-sm btn-primary flex-shrink-0">
            <i class="fa-solid fa-right-to-bracket me-1" aria-hidden="true"></i> Login
        </a>
    </x-site-header>

    <main id="main" class="container-xl py-4 py-lg-5 flex-grow-1">
        <h1 class="font-display h2 mb-1">Assignment cover page</h1>
        <p class="intake-muted mb-4 mb-lg-5">Fill in the form, check the preview, then print.</p>

        <div class="row g-4 g-xl-5">
            <div class="col-lg-7">
                <form method="POST" class="brand-form" data-print-form novalidate>
                    @csrf
                    <section class="form-part">
                        <h2 class="form-part-title"><i class="fa-solid fa-plus" aria-hidden="true"></i> Student details</h2>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label" for="std_id">College ID</label>
                                <input id="std_id" name="std_id" class="form-control" inputmode="numeric" pattern="\d{5,10}" required data-preview="std_id">
                                <x-field-error for="std_id">Enter your college ID (numbers only).</x-field-error>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="tu_roll">University roll no. <span class="intake-muted fw-normal">(optional)</span></label>
                                <input id="tu_roll" name="tu_roll" class="form-control" data-preview="tu_roll">
                            </div>
                            <div class="col-4 col-sm-3">
                                <label class="form-label" for="title">Title</label>
                                <select id="title" name="title" class="form-select" data-preview="title">
                                    @foreach ($options['titles'] as $title)
                                        <option>{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-8 col-sm-9">
                                <label class="form-label" for="name">Full name</label>
                                <input id="name" name="name" class="form-control" autocomplete="name" required data-preview="name">
                                <x-field-error for="name">Enter your name as it appears on your ID card.</x-field-error>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="std_dept">Department</label>
                                <select id="std_dept" name="std_dept" class="form-select" required data-preview="std_dept">
                                    <option value="">Choose…</option>
                                    @foreach ($departments as $department)
                                        <option>{{ $department['dept_name'] }}</option>
                                    @endforeach
                                </select>
                                <x-field-error for="std_dept">Choose your department.</x-field-error>
                            </div>
                        </div>
                    </section>

                    <section class="form-part">
                        <h2 class="form-part-title"><i class="fa-solid fa-plus" aria-hidden="true"></i> Course and subject</h2>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label" for="course">Course</label>
                                <select id="course" name="course" class="form-select" required data-preview="course">
                                    <option value="">Choose…</option>
                                    @foreach ($options['courses'] as $course)
                                        <option>{{ $course }}</option>
                                    @endforeach
                                </select>
                                <x-field-error for="course">Choose your course.</x-field-error>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="form-label" for="sem">Semester</label>
                                <select id="sem" name="sem" class="form-select" required data-preview="sem">
                                    <option value="">Choose…</option>
                                    @foreach ($options['semesters'] as $semester)
                                        <option>{{ $semester }}</option>
                                    @endforeach
                                </select>
                                <x-field-error for="sem">Choose a semester.</x-field-error>
                            </div>
                            <div class="col-6 col-sm-4">
                                <label class="form-label" for="section">Section</label>
                                <select id="section" name="section" class="form-select" data-preview="section">
                                    @foreach ($options['sections'] as $section)
                                        <option>{{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="case_presentation">Case presentation <span class="intake-muted fw-normal">(optional)</span></label>
                                <input id="case_presentation" name="case_presentation" class="form-control" data-preview="case_presentation" placeholder="e.g. Case of Bronchial Asthma">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="topic">Topic <span class="intake-muted fw-normal">(optional)</span></label>
                                <input id="topic" name="topic" class="form-control" data-preview="topic">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="ward">Ward <span class="intake-muted fw-normal">(optional)</span></label>
                                <input id="ward" name="ward" class="form-control" list="ward-list" autocomplete="off" data-preview="ward" placeholder="Choose or type a ward">
                                <datalist id="ward-list">
                                    @foreach ($options['wards'] as $ward)
                                        <option value="{{ $ward }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-sm-8">
                                <label class="form-label" for="subjects">Subject title</label>
                                <select id="subjects" name="subjects" class="form-select" required data-preview="subject" data-autofill="code"
                                        data-option-filter='{"dept":"std_dept","course":"course","sem":"sem"}'>
                                    <option value="" data-label="Choose…" data-empty="No subjects for this course and semester">Choose…</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject['subjects'] }}" data-value="{{ $subject['code'] }}" data-dept="{{ $subject['dept_name'] }}" data-course="{{ $subject['course'] }}" data-sem="{{ $subject['sem'] }}">{{ $subject['subjects'] }}</option>
                                    @endforeach
                                </select>
                                <x-field-error for="subjects">Choose a subject.</x-field-error>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label" for="code">Subject code</label>
                                <input id="code" name="code" class="form-control" readonly tabindex="-1" data-preview="code">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="page_type">This is a…</label>
                                <select id="page_type" name="page_type" class="form-select" data-preview="page_type">
                                    @foreach ($options['pageTypes'] as $pageType)
                                        <option>{{ $pageType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="dos">Submission date</label>
                                <input id="dos" name="dos" type="date" class="form-control" required data-preview="dos">
                                <x-field-error for="dos">Pick the date you're handing it in.</x-field-error>
                            </div>
                        </div>
                    </section>

                    <section class="form-part">
                        <h2 class="form-part-title"><i class="fa-solid fa-plus" aria-hidden="true"></i> Submitted to</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="f_dept">Teacher's department</label>
                                <select id="f_dept" name="f_dept" class="form-select" required data-preview="f_dept">
                                    <option value="">Choose…</option>
                                    @foreach ($departments as $department)
                                        <option>{{ $department['dept_name'] }}</option>
                                    @endforeach
                                </select>
                                <x-field-error for="f_dept">Choose the teacher's department.</x-field-error>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="f_name">Teacher</label>
                                <input id="f_name" name="f_name" class="form-control" list="teacher-list" autocomplete="off" required data-preview="f_name" data-autofill="f_designation">
                                <datalist id="teacher-list" data-option-filter='{"dept":"f_dept"}'>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher['teacher'] }}" data-value="{{ $teacher['designation'] }}" data-dept="{{ $teacher['dept_name'] }}"></option>
                                    @endforeach
                                </datalist>
                                <x-field-error for="f_name">Enter or choose the teacher's name.</x-field-error>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="f_designation">Designation</label>
                                <input id="f_designation" name="f_designation" class="form-control" list="designation-list" autocomplete="off" required data-preview="f_designation">
                                <datalist id="designation-list">
                                    @foreach ($options['designations'] as $designation)
                                        <option value="{{ $designation }}"></option>
                                    @endforeach
                                </datalist>
                                <x-field-error for="f_designation">Enter or choose the teacher's designation.</x-field-error>
                            </div>
                        </div>
                    </section>

                    <div class="form-actions">
                        <button type="reset" class="btn btn-danger">Clear</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-print me-1" aria-hidden="true"></i> Print cover page
                        </button>
                    </div>
                </form>
            </div>

            <aside class="col-lg-5" aria-label="Preview">
                <div class="preview-panel sticky-lg-top tw:lg:top-6">
                    <p class="preview-label">Preview</p>
                    @include('cover.partials.paper')
                </div>
            </aside>
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
