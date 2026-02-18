---
name: RMS Modules Implementation Plan
overview: "Implement all 20 pending modules for the Research Management System following the established patterns from Publications, Grants, RTN, and Bonus modules. All modules will include faculty submission forms, admin management interfaces, workflow integration, scoring (bonus points), and evidence file handling. Timeline: 4 weeks maximum."
todos: []
---

# RMS Modules Implementation Plan

## Overview

Implement 20 pending modules following the established architecture pattern from completed modules (Publications, Grants, RTN Submissions, Bonus Recognitions). All modules require:

- Faculty submission forms (create/edit/show)
- Admin management interfaces (index/show/approve/reject)
- Workflow integration (using `WorkflowService` and `ApprovalWorkflow` model)
- Scoring integration (bonus points via `ScoringService`)
- Evidence file handling (using `FileUploadService` and `EvidenceFile` model)
- Routes (public faculty routes + admin routes)

## Architecture Pattern

Each module follows this structure:

```
app/
├── Models/{Module}.php
├── Http/Controllers/{Module}Controller.php (public)
├── Http/Controllers/Admin/{Module}Controller.php (admin)
└── Services/ (reuse WorkflowService, ScoringService, FileUploadService)

resources/views/
├── {module}/create.blade.php
├── {module}/show.blade.php
├── {module}/index.blade.php
└── admin/{module}/index.blade.php
    admin/{module}/show.blade.php

routes/web.php (add public + admin routes)
database/migrations/ (already exist for most modules)
```

## Implementation Phases

### Phase 1: Modules with Client Responses (Week 1-1.5)

#### 1.1 Partnerships & MOUs (2-3 days)

**Requirements from client:**

- Any faculty can submit (like publications)
- Needs approval workflow (like publications)
- Counted under bonus (no specific score yet)
- Evidence: link + description + file uploads (word, PDF, zip)

**Implementation:**

- Model: Create `PartnershipMou` model (migration exists: `2026_01_15_100026_create_partnerships_mous_table.php`)
- Add workflow fields: `status`, `submitted_by`, `submitted_at`, `approved_at`, `approver_id`
- Controllers: `PartnershipController` (public) + `Admin\PartnershipController` (admin)
- Views: Create forms following `publications/create.blade.php` pattern
- Workflow: Add 'mou' to `approval_workflows.submission_type` enum
- Scoring: Add to bonus scoring (default 5 points)
- Routes: Add public routes + admin routes

**Files to create/modify:**

- `app/Models/PartnershipMou.php`
- `app/Http/Controllers/PartnershipController.php`
- `app/Http/Controllers/Admin/PartnershipController.php`
- `resources/views/partnerships/create.blade.php`
- `resources/views/partnerships/show.blade.php`
- `resources/views/partnerships/index.blade.php`
- `resources/views/admin/partnerships/index.blade.php`
- `resources/views/admin/partnerships/show.blade.php`
- Update `routes/web.php`
- Update `database/migrations/2026_01_15_100003_create_approval_workflows_table.php` (add 'mou' to enum)

#### 1.2 Adjunct Professors / Agent-Professors (2-3 days)

**Requirements from client:**

- Research coordinator/dean/research director can manage
- Can assign agent-professor coordinator (faculty)
- Faculty proposes agent-professor → workflow: research coordinator → dean → research director
- Scoring: under bonus (dynamic, maybe 5-10 points)
- Need to track agent vs normal faculty vs fellowship publications

**Implementation:**

- Model: `AdjunctProfessor` exists, add workflow fields
- Add proposal workflow: `proposed_by`, `status`, `workflow_id`
- Controllers: Admin management + faculty proposal form
- Views: Admin CRUD + faculty proposal form
- Workflow: Custom workflow for agent-professor proposals
- Link to publications: Add `adjunct_professor_id` to publications table (migration needed)

**Files to create/modify:**

- Update `app/Models/AdjunctProfessor.php` (add workflow fields)
- `app/Http/Controllers/Admin/AdjunctProfessorController.php`
- `app/Http/Controllers/AdjunctProfessorController.php` (faculty proposal)
- `resources/views/adjunct-professors/propose.blade.php`
- `resources/views/admin/adjunct-professors/index.blade.php`
- `resources/views/admin/adjunct-professors/show.blade.php`
- Create migration: `add_adjunct_professor_proposal_fields.php`
- Create migration: `add_adjunct_professor_id_to_publications.php`

#### 1.3 Commercializations (2-3 days)

**Requirements from client:**

- Faculty (including research coordinator & dean) can submit
- Needs approval workflow (like publications)
- Under bonus (no specific score yet)

**Implementation:**

- Model: Create `Commercialization` model (migration exists)
- Controllers: Public + Admin
- Views: Create/show/index forms
- Workflow: Add 'commercialization' to workflow enum
- Scoring: Bonus points (default 5)

**Files to create/modify:**

- `app/Models/Commercialization.php`
- `app/Http/Controllers/CommercializationController.php`
- `app/Http/Controllers/Admin/CommercializationController.php`
- Views (create/show/index for public + admin)
- Update workflow enum

#### 1.4 Consultancies & KT (2-3 days)

**Requirements from client:**

- Faculty can submit
- Needs approval workflow
- Under bonus (default 5 points)

**Implementation:**

- Model: Create `Consultancy` model (migration exists: `consultancies_kts`)
- Controllers: Public + Admin
- Views: Forms following publication pattern
- Workflow: Add 'consultancy' to enum
- Scoring: Bonus (5 points default)

**Files to create/modify:**

- `app/Models/Consultancy.php`
- `app/Http/Controllers/ConsultancyController.php`
- `app/Http/Controllers/Admin/ConsultancyController.php`
- Views

#### 1.5 Awards (2-3 days)

**Requirements from client:**

- Separate module (under bonus)
- Faculty can submit
- Needs workflow approval
- Contributes to scoring

**Implementation:**

- Model: Create `Award` model (migration needed)
- Controllers: Public + Admin
- Views: Forms
- Workflow: Add 'award' to enum
- Scoring: Bonus points

**Files to create/modify:**

- Create migration: `create_awards_table.php`
- `app/Models/Award.php`
- `app/Http/Controllers/AwardController.php`
- `app/Http/Controllers/Admin/AwardController.php`
- Views

#### 1.6 TRN Course Details (2-3 days)

**Requirements from client:**

- Faculty assigns RTN type to their course (RTN-3, RTN-4, dynamic for future)
- Evidence: link + description + file upload
- Two scenarios: coordinator uploads Excel OR faculty adds directly
- Course info as per Excel format

**Implementation:**

- Model: Create `RtnCourseDetail` model (migration needed)
- Fields: course_code, course_name, rtn_type (RTN_3/RTN_4), faculty_id, year, evidence
- Controllers: Faculty submission + Admin management + Coordinator Excel upload
- Views: Course form + Excel upload form
- Excel import: Use Laravel Excel package (if available) or manual parsing

**Files to create/modify:**

- Create migration: `create_rtn_course_details_table.php`
- `app/Models/RtnCourseDetail.php`
- `app/Http/Controllers/RtnCourseDetailController.php`
- `app/Http/Controllers/Admin/RtnCourseDetailController.php`
- Views + Excel import functionality

### Phase 2: Standard Implementation Modules (Week 2-3)

#### 2.1 Research Investments (2-3 days)

- Model exists (migration: `research_investments`)
- Add workflow fields
- Controllers + Views

#### 2.2 Conference Activities (2-3 days)

- Model: Create `ConferenceActivity` (migration exists)
- Controllers + Views

#### 2.3 Supervision & Exams (2-3 days)

- Model: `SupervisionExam` exists
- Controllers + Views

#### 2.4 Editorial Appointments (2-3 days)

- Model: `EditorialAppointment` exists
- Controllers + Views

#### 2.5 Student Involvements (2-3 days)

- Model: `StudentInvolvement` exists
- Controllers + Views

#### 2.6 Internal Fundings (2-3 days)

- Create model + migration if needed
- Controllers + Views

#### 2.7 Block Fundings (2-3 days)

- Create model + migration if needed
- Controllers + Views

#### 2.8 SDG Contributions (2-3 days)

- Model exists (migration exists)
- Controllers + Views

#### 2.9 SDG Mappings (2-3 days)

- Create model + migration if needed
- Controllers + Views

#### 2.10 SDG List (2-3 days)

- Create model + migration
- Controllers + Views

#### 2.11 Research Fellows (2-3 days)

- Model: `ResearchFellow` exists
- Controllers + Views

### Phase 3: Additional Features (Week 3-4)

#### 3.1 Faculty Profile (2-3 days)

- Profile edit form
- Photo upload
- Display profile information

#### 3.2 Reporting System Enhancements (3-4 days)

- Advanced analytics dashboards
- CV-style exports
- Management overview charts
- Breakdowns by college/department/faculty

## Common Implementation Tasks

### For Each Module:

1. **Model Setup:**

   - Add workflow fields: `status`, `submitted_by`, `submitted_at`, `approved_at`, `approver_id`
   - Add scoring fields: `points_allocated`, `points_locked`, `policy_version_id`
   - Add evidence fields: `evidence_required`, `evidence_uploaded`
   - Implement relationships: `submitter()`, `approve