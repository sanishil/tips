<x-layouts.app title="Assignment cover page" :themeable="false" class="d-flex flex-column min-vh-100">
    <header class="bg-body border-bottom">
        <div class="container-xl d-flex align-items-center justify-content-between gap-3 py-2">
            <a href="{{ route('cover.form') }}" class="text-decoration-none"><x-brand /></a>
            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Staff sign in</a>
            </div>
        </div>
    </header>

    <main id="main" class="container-xl py-4 py-lg-5 flex-grow-1">
        <div class="mb-4 tw:max-w-2xl">
            <h1 class="font-display h2 mb-2">Assignment cover page</h1>
            <p class="text-body-secondary mb-0">
                Fill in the form and check the preview. When it looks right, print it — or choose
                <em>Save as PDF</em> in the print window to keep a copy. The page is set up for A4.
            </p>
        </div>

        <div class="row g-4 g-xl-5">
            <div class="col-lg-7">
                <form method="POST" class="card shadow-sm" data-print-form novalidate>
                    @csrf
                    <div class="card-body p-3 p-sm-4">
                        <fieldset>
                            <legend class="h6 font-display fs-5 mb-3">1. About you</legend>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label" for="std_id">College ID</label>
                                    <input id="std_id" name="std_id" class="form-control" inputmode="numeric" pattern="\d{5,10}" required data-preview="std_id">
                                    <x-field-error for="std_id">Enter your college ID (numbers only).</x-field-error>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="tu_roll">University roll no. <span class="text-body-secondary fw-normal">(optional)</span></label>
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
                        </fieldset>

                        <hr class="my-4">

                        <fieldset>
                            <legend class="h6 font-display fs-5 mb-3">2. Course and subject</legend>
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
                                <div class="col-sm-8">
                                    <label class="form-label" for="subjects">Subject</label>
                                    <select id="subjects" name="subjects" class="form-select" required data-preview="subject" data-autofill="code"
                                            data-option-filter='{"dept":"std_dept","course":"course","sem":"sem"}'>
                                        <option value="" data-label="Choose…" data-empty="No subjects for this course and semester">Choose…</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject['subjects'] }}" data-value="{{ $subject['code'] }}" data-dept="{{ $subject['dept_name'] }}" data-course="{{ $subject['course'] }}" data-sem="{{ $subject['sem'] }}">{{ $subject['subjects'] }}</option>
                                        @endforeach
                                    </select>
                                    <x-field-error for="subjects">Choose a subject.</x-field-error>
                                    <div class="form-text">Only subjects for your department, course and semester are listed.</div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label" for="code">Subject code</label>
                                    <input id="code" name="code" class="form-control font-monospace" readonly tabindex="-1" data-preview="code">
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
                        </fieldset>

                        <hr class="my-4">

                        <fieldset>
                            <legend class="h6 font-display fs-5 mb-3">3. Submitted to</legend>
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
                                    <select id="f_name" name="f_name" class="form-select" required data-preview="f_name" data-autofill="f_designation"
                                            data-option-filter='{"dept":"f_dept"}'>
                                        <option value="" data-label="Choose…" data-empty="No teachers in this department">Choose…</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher['teacher'] }}" data-value="{{ $teacher['designation'] }}" data-dept="{{ $teacher['dept_name'] }}">{{ $teacher['teacher'] }}</option>
                                        @endforeach
                                    </select>
                                    <x-field-error for="f_name">Choose a teacher.</x-field-error>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="f_designation">Designation</label>
                                    <input id="f_designation" name="f_designation" class="form-control" readonly tabindex="-1" data-preview="f_designation">
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="card-footer d-flex flex-column-reverse flex-sm-row justify-content-end gap-2 py-3">
                        <button type="reset" class="btn btn-outline-secondary">Clear form</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-print me-1" aria-hidden="true"></i> Print or save as PDF
                        </button>
                    </div>
                </form>
            </div>

            <aside class="col-lg-5" aria-label="Preview">
                <div class="sticky-lg-top tw:lg:top-6">
                    <p class="small text-body-secondary mb-2">Preview — updates as you type</p>
                    @include('cover.partials.paper')
                </div>
            </aside>
        </div>
    </main>

    <x-site-footer />
</x-layouts.app>
