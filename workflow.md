# TCEA EduCover — Full Project Workflow

> **Project:** Assignment Front Page Generator for Techno College of Engineering Agartala
> **Author:** Sani Shil
> **Stack:** PHP · MySQL · AngularJS 1.x · Bootstrap 5 · Chart.js · jsPDF + html2canvas · PHPMailer
> **Server:** XAMPP (Apache + MySQL) — local at `localhost/college/`, live at `myassignment.infinityfreeapp.com`

---

## 1. High-Level Architecture

```mermaid
flowchart LR
    subgraph Browser
        A["index.html<br>(Public Form)"]
        B["login.html"]
        C["admin/index.php<br>(Admin SPA)"]
        D["tech/index.php<br>(Student SPA)"]
    end

    subgraph Server["Apache + PHP"]
        E["login.php"]
        F["session_check.php"]
        G["captcha.php"]
        H["session_ping.php"]
        I["logout.php"]
        J["forgot_password.php"]
        K["form/std_entry.php"]
        L["form/form.php"]
        M["admin/api/*"]
        N["tech/api/*"]
    end

    subgraph Database["MySQL (assignment)"]
        DB["Tables:<br>login, student, dept,<br>teacher, subject,<br>print_log, rate_limit,<br>password_otp"]
    end

    subgraph Mail["SMTP (PHPMailer)"]
        SMTP["Account emails<br>Password resets"]
    end

    A --> K
    A --> L
    B --> E
    B --> G
    B --> J
    C --> M
    D --> N
    C & D --> H
    C & D --> I
    E --> DB
    F --> DB
    K --> DB
    M --> DB
    N --> DB
    J --> SMTP
    M --> SMTP
```

---

## 2. User Roles & Access Matrix

| Role | Entry Point | Dashboard | Manages |
|------|------------|-----------|---------|
| **Public (Guest)** | `index.html` → `form/form.php` | ✗ | Fill form → download PDF cover page |
| **Admin** | `login.html` → `admin/index.php` | ✓ (Chart.js) | Departments, Subjects, Teachers, Users (create/deactivate/reset), Students (view/delete), Active users, Profile |
| **Student** | `login.html` → `tech/index.php` | ✓ (Chart.js) | Departments, Subjects, Teachers (view/add/edit/delete), Students (view/delete), Profile |

> [!NOTE]
> Admin and Student portals share the same sidebar layout and nearly identical API endpoints under `admin/api/` and `tech/api/` respectively. The admin portal additionally has a **User Section** for creating accounts, deactivating users, and resetting passwords.

---

## 3. Complete Request Flow

### 3.1 Public Form — Cover Page Generation

```mermaid
sequenceDiagram
    participant U as Student (Browser)
    participant F as form/form.php
    participant API as form/* APIs
    participant DB as MySQL
    participant PDF as jsPDF + html2canvas

    U->>F: Opens index.html (AngularJS loads form.php into ng-view)
    F->>DB: Query dept table for department <select> options
    F-->>U: Render form (3 sections)

    Note over U: Section 1: Student Details
    U->>API: checkStudentID() → check_student.php
    API->>DB: SELECT * FROM student WHERE std_id = ?
    API-->>U: Auto-fill name, dept, TU roll if returning student

    Note over U: Section 2: Course & Submission
    U->>API: subjects_view() → subject_gets.php
    API->>DB: SELECT subjects WHERE dept + sem + course match
    API-->>U: Populate subject dropdown
    U->>API: subjects_code_view() → subject_code_gets.php
    API-->>U: Auto-fill subject code

    Note over U: Section 3: Submitted To (Faculty)
    U->>API: tech_view() → get_faculty.php
    API->>DB: SELECT teacher WHERE dept_name = ?
    API-->>U: Populate faculty dropdown
    U->>API: designation_view() → get_faculty_designation.php
    API-->>U: Auto-fill designation

    U->>U: Click "Download cover page"
    U->>API: executeFunctions() → std_entry.php
    API->>DB: INSERT or UPDATE student + INSERT print_log
    API-->>U: { status: "success" }
    U->>PDF: printPDF() — show hidden A4 div, html2canvas → jsPDF → save PDF
    U-->>U: PDF downloaded
```

### 3.2 Authentication Flow

```mermaid
sequenceDiagram
    participant U as User (Browser)
    participant L as login.html
    participant C as captcha.php
    participant LP as login.php
    participant DB as MySQL
    participant S as PHP Session

    U->>L: Navigate to login.html
    L->>C: Load captcha image (math: a ± b = ?)
    C->>S: Store captcha_answer + captcha_time in session
    C-->>L: PNG image of math question

    U->>LP: POST {user, pass, captcha}
    LP->>DB: Check rate_limit (5 fails / 15 min per IP)
    alt Rate limited
        LP-->>U: Redirect → login.html?error=locked&wait=X
    end
    LP->>S: Verify captcha answer (within 5 min, single use)
    alt Wrong captcha
        LP-->>U: Redirect → login.html?error=captcha
    end
    LP->>DB: SELECT * FROM login WHERE user_id + pass match
    alt No match
        LP->>DB: INSERT rate_limit row (rate_hit)
        LP-->>U: Redirect → login.html?error=mismatch
    end
    LP->>DB: Check is_active (token=1 AND last_activity < 10 min)
    alt Already active elsewhere
        LP-->>U: Redirect → login.html?error=active
    end
    LP->>DB: UPDATE login SET token=1, last_activity=NOW()
    LP->>S: Set session: user_id, type, name, last_activity
    LP->>DB: Clear rate_limit for this IP

    alt type = Admin
        LP-->>U: Redirect → admin/
    else type = Student
        LP-->>U: Redirect → tech/
    end
```

### 3.3 Session Management & Idle Logout

```mermaid
flowchart TD
    A["Page loads (admin/ or tech/)"] --> B["session_check.php runs"]
    B --> C{"Session exists?"}
    C -->|No| D["Destroy session → login.html"]
    C -->|Yes| E{"last_activity > 10 min ago?"}
    E -->|Yes| F["UPDATE login SET token=0<br>Destroy session → login.html?timeout=1"]
    E -->|No| G["UPDATE last_activity = NOW()<br>Page renders"]

    G --> H["idle_logout.js starts"]
    H --> I["Monitors mouse/keyboard/touch events"]
    I --> J{"Activity detected?"}
    J -->|Yes| K["Update localStorage timestamp<br>Ping session_ping.php (every 60s max)"]
    K --> L["Server refreshes last_activity"]
    J -->|No| M{"10 min elapsed?"}
    M -->|Yes| N["Redirect → logout.php?timeout=1"]
    M -->|No| J

    style D fill:#fee2e2,color:#991b1b
    style F fill:#fee2e2,color:#991b1b
    style N fill:#fee2e2,color:#991b1b
```

> [!IMPORTANT]
> **Dual-layer idle timeout:** The server enforces a 10-minute session timeout (`session_check.php` + `session_ping.php`), and the browser independently tracks idle time via `idle_logout.js` with `localStorage` sharing across tabs. Both must agree the session is alive.

### 3.4 Forgot Password Flow

```mermaid
sequenceDiagram
    participant U as User
    participant FP as forgot_password.php
    participant DB as MySQL
    participant M as PHPMailer (SMTP)

    U->>FP: POST {op: "send_otp", email: "..."}
    FP->>DB: Rate limit check (10 requests/IP/hour)
    FP->>DB: Verify user_id = email exists in login table
    FP->>DB: Throttle per account (1 code/min, 5/hour)
    FP->>FP: Generate 6-digit OTP → password_hash()
    FP->>DB: Invalidate old codes → INSERT password_otp
    FP->>M: Send OTP email (HTML + plain text)
    FP-->>U: {status: "success", masked: "s****@gmail.com", wait: 60}

    U->>FP: POST {op: "verify_otp", otp: "123456"}
    FP->>DB: Rate limit check (10 wrong/IP/15 min)
    FP->>DB: SELECT latest unused code for user
    FP->>FP: password_verify(otp, hash)
    alt Correct
        FP->>DB: Mark code as used
        FP-->>U: {status: "success"} → show new password form
    else Wrong
        FP->>DB: Increment attempts (max 5)
        FP-->>U: {status: "error", message: "X attempts left"}
    end

    U->>FP: POST {op: "reset", pass: "...", cpass: "..."}
    FP->>DB: UPDATE login SET pass = ?, token = 0
    FP-->>U: {status: "success"} → redirect to login
```

---

## 4. Admin SPA Workflow (AngularJS)

### 4.1 Routing (`admin/controller/main.js`)

```mermaid
flowchart LR
    R["ngRoute"] --> D["/ or /dash → dash.php<br>(dashCtrl)"]
    R --> S["/student → student.html<br>(dashCtrl)"]
    R --> DE["/dept → dept.html<br>(dashCtrl)"]
    R --> SU["/subjects → subjects.php<br>(dashCtrl)"]
    R --> T["/teacher → teacher.php<br>(dashCtrl)"]
    R --> US["/user_section → user_section.html<br>(dashCtrl)"]
    R --> AU["/active_user → active_user.html<br>(dashCtrl)"]
    R --> P["/profile → profile.html<br>(profileCtrl)"]
    R --> X["otherwise → inline 404"]
```

### 4.2 CRUD Operations

Each management module follows the same pattern:

```mermaid
flowchart TD
    V["View (on page load)"] -->|"$http POST → api/.../xxx_view.php"| DB[(MySQL)]
    DB --> T["Populate $scope.xxx_data"]
    T --> TBL["Render table with ng-repeat + pagination"]

    ADD["Add Form Submit"] -->|"$http POST → api/.../xxx_entry.php"| DB
    DB --> R1["Refresh view"]

    EDIT["Edit Button"] -->|"$http POST → api/.../xxx_update.php?op=select_condition"| DB
    DB --> FILL["Fill modal form"]
    FILL --> SAVE["Save Changes"]
    SAVE -->|"$http POST → api/.../xxx_update.php?op=edit"| DB
    DB --> R2["Refresh view + close modal"]

    DEL["Delete Button"] -->|"confirm() → $http POST → api/.../xxx_delete.php"| DB
    DB --> R3["Refresh view"]
```

**API endpoints by module:**

| Module | Entry | View | Edit/Update | Delete |
|--------|-------|------|-------------|--------|
| **Department** | `Dept/dept_entry.php` | `Dept/dept_view.php` | `Dept/edit_update.php` | `Dept/dept_delete.php` |
| **Subject** | `subject/subject_entry.php` | `subject/subject_view.php` | `subject/subject_update.php` | `subject/subject_delete.php` |
| **Teacher** | `teacher/teacher_entry.php` | `teacher/teacher_view.php` | `teacher/teacher_update.php` | `teacher/teacher_delete.php` |
| **Student** | *(from public form)* | `std/std_view.php` | — | `std/std_delete.php` |
| **User** | `user/user_entry.php` | `user/user_view.php` | `user/user_edit_update.php` | `user/user_delete.php` |

> [!TIP]
> The **Student (tech)** portal has the same API structure under `tech/api/` without the User management section.

### 4.3 Dashboard Features

```mermaid
flowchart TD
    DASH["Dashboard Page"] --> CHART["Department Summary Chart"]
    DASH --> PRINTS["Today's Prints Card"]

    CHART -->|"api/dash/dept_summary.php"| C1["Bar chart (Chart.js)<br>Students / Teachers / Subjects per dept"]
    C1 --> THEME["Responds to dark/light theme toggle"]

    PRINTS -->|"api/dash/today_prints.php"| P1["Modal with paginated table<br>of today's cover page prints"]
```

---

## 5. Database Schema (Inferred)

```mermaid
erDiagram
    login {
        varchar user_id PK "email address"
        varchar pass
        varchar name
        varchar type "Admin | Student"
        int token "1=active, 0=inactive"
        datetime last_activity
        varchar photo "nullable, filename"
        datetime dou "date of update"
    }

    dept {
        int id PK
        varchar dept_name
        varchar hod
    }

    teacher {
        int id PK
        varchar teacher
        varchar designation
        varchar dept_name FK
    }

    subject {
        int id PK
        varchar subjects "subject title"
        varchar code "subject code"
        varchar sem "1st–8th"
        varchar course "B.Tech | Diploma"
        varchar dept_name FK
    }

    student {
        varchar std_id PK "student ID number"
        varchar tu_roll "Tripura Univ roll"
        varchar title "Mr./Miss./etc."
        varchar name
        varchar sem
        varchar std_dept FK
        varchar course
        varchar subjects
        varchar code
        varchar page_type
        varchar section "A/B/C/D"
        date dos "date of submission"
        varchar f_dept
        varchar f_name
        varchar f_designation
        int reprint "counter, starts at 0"
    }

    print_log {
        int id PK
        varchar std_id FK
        datetime created_at "auto"
    }

    rate_limit {
        int id PK
        varchar ip
        varchar action "login | otp_send | otp_wrong"
        datetime created_at
    }

    password_otp {
        int id PK
        varchar user_id FK
        varchar otp_hash
        tinyint attempts
        tinyint used
        datetime expires_at
        datetime created_at
    }

    dept ||--o{ teacher : "has"
    dept ||--o{ subject : "has"
    dept ||--o{ student : "enrolled in"
    login ||--o{ password_otp : "resets"
    student ||--o{ print_log : "generates"
```

---

## 6. Security Mechanisms

```mermaid
flowchart TD
    subgraph Login
        A["Math CAPTCHA (GD image)"] --> B["Single-use, 5-min expiry"]
        C["Rate Limiting"] --> D["5 wrong passwords / 15 min per IP"]
        E["Single Active Session"] --> F["token=1 + last_activity check<br>Prevents concurrent logins"]
    end

    subgraph "Forgot Password"
        G["OTP hashed with password_hash()"] --> H["5 wrong guesses / code"]
        I["10 requests / IP / hour"] --> J["5 codes / account / hour"]
        K["60-second resend cooldown"]
        L["10-minute OTP expiry"]
        M["10-minute reset window after verification"]
    end

    subgraph "Session"
        N["session_check.php on every page"] --> O["10-min server-side timeout"]
        P["idle_logout.js"] --> Q["10-min client-side timeout"]
        R["session_ping.php"] --> S["Keep-alive every 60s on activity"]
        T["localStorage"] --> U["Shared activity timestamp across tabs"]
    end

    subgraph "Upload"
        V["uploads/profile/.htaccess"] --> W["Restricts executable file access"]
    end
```

> [!WARNING]
> Passwords in the `login` table are stored in **plain text** (compared directly in `login.php`). The OTP codes use `password_hash()`, but login credentials do not. This is a known security concern.

---

## 7. File Structure & Responsibilities

```
college/
├── index.html                 # Public entry → AngularJS SPA shell
├── login.html                 # Login page (standalone, no framework)
├── login.php                  # Auth handler: captcha + rate limit + session
├── logout.php                 # Clear token + destroy session
├── captcha.php                # Math CAPTCHA image generator (GD)
├── session_check.php          # Guard: included at top of admin/ & tech/
├── session_ping.php           # AJAX keep-alive for idle_logout.js
├── forgot_password.php        # OTP send → verify → reset password
├── mail_config.php            # SMTP credentials (gitignored)
├── 404.html                   # Custom 404 page
├── .htaccess                  # ErrorDocument 404 routing
│
├── form/                      # Public form APIs
│   ├── form.php               # Main form template (injected via ng-view)
│   ├── std_entry.php           # Save/update student + print_log
│   ├── check_student.php       # Auto-fill returning student
│   ├── subject_gets.php        # Subjects by dept+sem+course
│   ├── subject_code_gets.php   # Subject code by name
│   ├── get_faculty.php         # Teachers by department
│   └── get_faculty_designation.php  # Designation by teacher name
│
├── admin/                     # Admin portal (protected)
│   ├── index.php              # SPA shell (session_check + sidebar + ng-view)
│   ├── controller/main.js     # AngularJS routes + all CRUD controllers
│   ├── pages/                 # Partial templates
│   │   ├── dash.php           # Dashboard (chart + today's prints)
│   │   ├── dept.html          # Department management
│   │   ├── student.html       # Student records viewer
│   │   ├── subjects.php       # Subject management
│   │   ├── teacher.php        # Teacher management
│   │   ├── user_section.html  # Create/edit/delete users
│   │   ├── active_user.html   # Currently active users
│   │   └── profile.html       # My profile + change password
│   └── api/                   # JSON APIs
│       ├── connect.php        # DB connection (shared)
│       ├── profile.php        # Profile photo upload
│       ├── change_pass.php    # Change own password
│       ├── dash/              # dept_summary.php, today_prints.php
│       ├── Dept/              # dept_entry, dept_view, dept_delete, edit_update
│       ├── std/               # std_view, std_delete
│       ├── subject/           # subject_entry, subject_view, subject_update, subject_delete
│       ├── teacher/           # teacher_entry, teacher_view, teacher_update, teacher_delete
│       └── user/              # user_entry, user_view, user_edit_update, user_delete,
│                              # user_reset_pass, deactivate, account_mail
│
├── tech/                      # Student portal (protected, same structure as admin)
│   ├── index.php
│   ├── controller/main.js
│   ├── pages/                 # Same partials minus user_section & active_user
│   └── api/                   # Same API structure minus user/
│
├── assets/
│   ├── css/
│   │   ├── modern.css         # Shared theme (light/dark, sidebar, cards, tables)
│   │   └── form.css           # Public form styles
│   ├── js/
│   │   ├── angular.min.js     # AngularJS 1.x
│   │   ├── angular-router.min.js
│   │   ├── angular-cookies.min.js
│   │   ├── theme.js           # Light/dark toggle (localStorage persistence)
│   │   ├── toast.js           # Toast notification utility
│   │   ├── idle_logout.js     # Client-side idle timeout
│   │   └── profile_photo.js   # Profile photo upload/change handler
│   ├── main.js                # Public form AngularJS app (routes + PDF logic)
│   └── TCEA_Format_Paper.pdf  # Downloadable format paper
│
├── lib/
│   ├── mailer.php             # Reusable send_mail() + reply_now()
│   ├── profile_photo.php      # Photo validation helper
│   ├── rate_limit.php         # IP-based rate limiting (rate_wait, rate_hit, rate_clear)
│   └── PHPMailer/             # PHPMailer library (Exception, PHPMailer, SMTP)
│
├── img/                       # Static images
│   ├── campus.jpg / .webp     # Hero photo (login + form pages)
│   └── final_tcea.jpg         # College logo / favicon
│
└── uploads/
    └── profile/               # User profile photos
        └── .htaccess          # Security: restrict script execution
```

---

## 8. Theme System

```mermaid
flowchart LR
    TJS["theme.js (runs first)"] -->|Read| LS["localStorage<br>'tcea_theme'"]
    LS -->|"'dark'"| DARK["Set data-theme='dark' on <html>"]
    LS -->|"'light' or absent"| LIGHT["Default light theme"]

    BTN["Theme toggle button<br>(topbar)"] -->|Click| TJS2["Toggle attribute + localStorage"]
    TJS2 -->|Dispatch| EVT["'themechange' event"]
    EVT --> CHART["Chart.js recolors<br>via CSS custom properties"]
    EVT --> CSS["modern.css applies<br>dark mode variables"]
```

---

## 9. PDF Generation Workflow

```mermaid
flowchart TD
    A["User clicks 'Download cover page'"] --> B["executeFunctions()"]
    B --> C["Validate all required fields"]
    C -->|Missing| D["Toast error: fill required fields"]
    C -->|OK| E["POST to form/std_entry.php"]
    E --> F["Save student record + print_log"]
    F --> G["printPDF()"]
    G --> H["Show hidden A4 div (#contentToPrint)<br>AngularJS bindings render data"]
    H --> I["html2canvas captures div as canvas"]
    I --> J["jsPDF creates A4 PDF from canvas image"]
    J --> K["Save as: Name_Subject_Assignment.pdf"]
    K --> L["Hide A4 div again"]
```

---

## 10. Email System

| Feature | Trigger | Template |
|---------|---------|----------|
| **Password Reset OTP** | "Forgot password?" on login page | 6-digit code, 10-min expiry, HTML email |
| **New Account Credentials** | Admin creates user (`user/user_entry.php`) | Welcome email with login ID + generated password |
| **Password Reset by Admin** | Admin resets user password (`user/user_reset_pass.php`) | New password notification email |

> [!NOTE]
> All emails are sent via SMTP using **PHPMailer**. Configuration lives in `mail_config.php` (gitignored). The `lib/mailer.php` helper provides `reply_now()` to send the HTTP response immediately and continue sending the email in the background, so the user doesn't wait for SMTP.

---

## 11. Deployment

```mermaid
flowchart LR
    DEV["Local Development<br>XAMPP: localhost/college/"] --> PROD["Production<br>InfinityFree Hosting"]

    subgraph Config
        C1["connect.php<br>DB: localhost / root / assignment"]
        C2["mail_config.php<br>SMTP credentials"]
        C3[".htaccess<br>404 routing (detects /college/ prefix)"]
        C4["Timezone: +05:30 (IST)<br>Set in MySQL + PHP"]
    end

    DEV --- Config
    PROD --- Config
```

**Setup steps:**
1. Clone repo → XAMPP `htdocs/college/`
2. Start Apache + MySQL
3. Import database schema into `assignment` database
4. Copy `mail_config.example.php` → `mail_config.php` and fill SMTP credentials
5. Access at `http://localhost/college/`
