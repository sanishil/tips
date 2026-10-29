<?php

namespace App\Support;

/**
 * Static sample data for the design-only build.
 *
 * Replace each method with Eloquent queries once the backend is wired up.
 */
class DemoData
{
    /**
     * Shared dropdown options.
     *
     * @return array{semesters: list<string>, courses: list<string>, titles: list<string>, sections: list<string>, pageTypes: list<string>, designations: list<string>, wards: list<string>}
     */
    public static function options(): array
    {
        return [
            'semesters' => ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'],
            'courses' => ['B.Sc', 'BPT', 'B.Optom', 'Diploma'],
            'titles' => ['Mr.', 'Miss.', 'Ms.', 'Mrs.'],
            'sections' => ['A', 'B', 'C', 'D'],
            'pageTypes' => ['Assignment', 'Case Presentation', 'Practical Record', 'Case Study', 'Project Report'],
            'designations' => ['Principal', 'Professor', 'Associate Professor', 'Assistant Professor', 'Lecturer', 'Tutor', 'HOD'],
            'wards' => [
                'Female Medical Ward',
                'Male Medical Ward',
                'Female Surgical Ward',
                'Male Surgical Ward',
                'Pediatric Ward',
                'Orthopedic Ward',
                'Maternity Ward',
                'Gynecology Ward',
                'ICU',
                'Emergency Ward',
                'Post-Operative Ward',
                'Physiotherapy OPD',
            ],
        ];
    }

    /**
     * @return list<array{id: int, dept_name: string, hod: string, students: int, teachers: int, subjects: int}>
     */
    public static function departments(): array
    {
        return [
            ['id' => 1, 'dept_name' => 'Medical Laboratory Technology', 'hod' => 'Dr. Sutapa Chakraborty', 'students' => 168, 'teachers' => 12, 'subjects' => 34],
            ['id' => 2, 'dept_name' => 'Radiology & Imaging Technology', 'hod' => 'Dr. Abhijit Debnath', 'students' => 121, 'teachers' => 9, 'subjects' => 29],
            ['id' => 3, 'dept_name' => 'Operation Theatre Technology', 'hod' => 'Dr. Rajib Saha', 'students' => 96, 'teachers' => 8, 'subjects' => 26],
            ['id' => 4, 'dept_name' => 'Physiotherapy', 'hod' => 'Dr. Moumita Bhowmik', 'students' => 142, 'teachers' => 11, 'subjects' => 38],
            ['id' => 5, 'dept_name' => 'Optometry', 'hod' => 'Partha Sarathi Deb', 'students' => 74, 'teachers' => 6, 'subjects' => 24],
        ];
    }

    /**
     * @return list<array{id: int, teacher: string, designation: string, dept_name: string}>
     */
    public static function teachers(): array
    {
        return [
            ['id' => 1, 'teacher' => 'Dr. Sutapa Chakraborty', 'designation' => 'HOD', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 2, 'teacher' => 'Mousumi Paul', 'designation' => 'Assistant Professor', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 3, 'teacher' => 'Biplab Nath', 'designation' => 'Lecturer', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 4, 'teacher' => 'Dr. Abhijit Debnath', 'designation' => 'HOD', 'dept_name' => 'Radiology & Imaging Technology'],
            ['id' => 5, 'teacher' => 'Tanushree Sen', 'designation' => 'Tutor', 'dept_name' => 'Radiology & Imaging Technology'],
            ['id' => 6, 'teacher' => 'Dr. Rajib Saha', 'designation' => 'HOD', 'dept_name' => 'Operation Theatre Technology'],
            ['id' => 7, 'teacher' => 'Kaushik Bhowmik', 'designation' => 'Associate Professor', 'dept_name' => 'Physiotherapy'],
            ['id' => 8, 'teacher' => 'Partha Sarathi Deb', 'designation' => 'HOD', 'dept_name' => 'Optometry'],
        ];
    }

    /**
     * @return list<array{id: int, subjects: string, code: string, sem: string, course: string, dept_name: string}>
     */
    public static function subjects(): array
    {
        return [
            ['id' => 1, 'subjects' => 'Human Anatomy', 'code' => 'MLT-101', 'sem' => '1st', 'course' => 'B.Sc', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 2, 'subjects' => 'Clinical Biochemistry', 'code' => 'MLT-302', 'sem' => '3rd', 'course' => 'B.Sc', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 3, 'subjects' => 'Haematology & Blood Banking', 'code' => 'MLT-503', 'sem' => '5th', 'course' => 'B.Sc', 'dept_name' => 'Medical Laboratory Technology'],
            ['id' => 4, 'subjects' => 'Physics of Medical Imaging', 'code' => 'RIT-202', 'sem' => '2nd', 'course' => 'B.Sc', 'dept_name' => 'Radiology & Imaging Technology'],
            ['id' => 5, 'subjects' => 'Radiographic Positioning', 'code' => 'RIT-301', 'sem' => '3rd', 'course' => 'B.Sc', 'dept_name' => 'Radiology & Imaging Technology'],
            ['id' => 6, 'subjects' => 'Anaesthesia Technology', 'code' => 'OTT-401', 'sem' => '4th', 'course' => 'Diploma', 'dept_name' => 'Operation Theatre Technology'],
            ['id' => 7, 'subjects' => 'Exercise Therapy', 'code' => 'BPT-302', 'sem' => '3rd', 'course' => 'BPT', 'dept_name' => 'Physiotherapy'],
            ['id' => 8, 'subjects' => 'Ocular Anatomy', 'code' => 'OPT-101', 'sem' => '1st', 'course' => 'B.Optom', 'dept_name' => 'Optometry'],
        ];
    }

    /**
     * @return list<array{std_id: string, tu_roll: string, title: string, name: string, sem: string, std_dept: string, course: string, subjects: string, code: string, page_type: string, section: string, dos: string, f_name: string, reprint: int}>
     */
    public static function students(): array
    {
        return [
            ['std_id' => '2401021', 'tu_roll' => '24-1145', 'title' => 'Miss.', 'name' => 'Riya Debnath', 'sem' => '3rd', 'std_dept' => 'Medical Laboratory Technology', 'course' => 'B.Sc', 'subjects' => 'Clinical Biochemistry', 'code' => 'MLT-302', 'page_type' => 'Practical Record', 'section' => 'A', 'dos' => '2026-10-29', 'f_name' => 'Mousumi Paul', 'reprint' => 0],
            ['std_id' => '2401034', 'tu_roll' => '24-1158', 'title' => 'Mr.', 'name' => 'Ankit Sarkar', 'sem' => '3rd', 'std_dept' => 'Medical Laboratory Technology', 'course' => 'B.Sc', 'subjects' => 'Clinical Biochemistry', 'code' => 'MLT-302', 'page_type' => 'Assignment', 'section' => 'B', 'dos' => '2026-10-29', 'f_name' => 'Biplab Nath', 'reprint' => 2],
            ['std_id' => '2402011', 'tu_roll' => '24-0871', 'title' => 'Mr.', 'name' => 'Sourav Dey', 'sem' => '3rd', 'std_dept' => 'Radiology & Imaging Technology', 'course' => 'B.Sc', 'subjects' => 'Radiographic Positioning', 'code' => 'RIT-301', 'page_type' => 'Case Study', 'section' => 'A', 'dos' => '2026-10-28', 'f_name' => 'Tanushree Sen', 'reprint' => 1],
            ['std_id' => '2503007', 'tu_roll' => '25-0412', 'title' => 'Miss.', 'name' => 'Priyanka Deb', 'sem' => '1st', 'std_dept' => 'Optometry', 'course' => 'B.Optom', 'subjects' => 'Ocular Anatomy', 'code' => 'OPT-101', 'page_type' => 'Assignment', 'section' => 'A', 'dos' => '2026-10-28', 'f_name' => 'Partha Sarathi Deb', 'reprint' => 0],
            ['std_id' => '2404019', 'tu_roll' => '24-0933', 'title' => 'Mr.', 'name' => 'Rahul Majumder', 'sem' => '3rd', 'std_dept' => 'Physiotherapy', 'course' => 'BPT', 'subjects' => 'Exercise Therapy', 'code' => 'BPT-302', 'page_type' => 'Practical Record', 'section' => 'A', 'dos' => '2026-10-27', 'f_name' => 'Kaushik Bhowmik', 'reprint' => 3],
            ['std_id' => '2305002', 'tu_roll' => '23-1002', 'title' => 'Miss.', 'name' => 'Ananya Bhattacharjee', 'sem' => '4th', 'std_dept' => 'Operation Theatre Technology', 'course' => 'Diploma', 'subjects' => 'Anaesthesia Technology', 'code' => 'OTT-401', 'page_type' => 'Project Report', 'section' => 'B', 'dos' => '2026-10-26', 'f_name' => 'Dr. Rajib Saha', 'reprint' => 0],
        ];
    }

    /**
     * @return list<array{name: string, email: string, type: string, active: bool, online: bool, last_activity: string}>
     */
    public static function users(): array
    {
        return [
            ['name' => 'Sani Shil', 'email' => 'sani.shil@example.com', 'type' => 'Admin', 'active' => true, 'online' => true, 'last_activity' => '1 min ago'],
            ['name' => 'Riya Debnath', 'email' => 'riya.debnath@example.com', 'type' => 'Student', 'active' => true, 'online' => true, 'last_activity' => '3 min ago'],
            ['name' => 'Ankit Sarkar', 'email' => 'ankit.sarkar@example.com', 'type' => 'Student', 'active' => true, 'online' => true, 'last_activity' => '7 min ago'],
            ['name' => 'Sourav Dey', 'email' => 'sourav.dey@example.com', 'type' => 'Student', 'active' => true, 'online' => false, 'last_activity' => '2 hours ago'],
            ['name' => 'Priyanka Deb', 'email' => 'priyanka.deb@example.com', 'type' => 'Student', 'active' => false, 'online' => false, 'last_activity' => '12 days ago'],
        ];
    }

    /**
     * @return list<array{time: string, std_id: string, name: string, subject: string, page_type: string}>
     */
    public static function todayPrints(): array
    {
        return [
            ['time' => '10:42', 'std_id' => '2401021', 'name' => 'Riya Debnath', 'subject' => 'Clinical Biochemistry', 'page_type' => 'Practical Record'],
            ['time' => '10:15', 'std_id' => '2401034', 'name' => 'Ankit Sarkar', 'subject' => 'Clinical Biochemistry', 'page_type' => 'Assignment'],
            ['time' => '09:58', 'std_id' => '2401047', 'name' => 'Moumita Das', 'subject' => 'Haematology & Blood Banking', 'page_type' => 'Assignment'],
            ['time' => '09:31', 'std_id' => '2402011', 'name' => 'Sourav Dey', 'subject' => 'Radiographic Positioning', 'page_type' => 'Case Study'],
            ['time' => '09:02', 'std_id' => '2404019', 'name' => 'Rahul Majumder', 'subject' => 'Exercise Therapy', 'page_type' => 'Practical Record'],
        ];
    }
}
