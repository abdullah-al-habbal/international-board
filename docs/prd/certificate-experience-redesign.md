# PRD — Certification Experience Redesign (Laravel + Blade + Bootstrap)
Version: `1.0`
Status: `Approved for implementation`
Priority: `High`
Type: `UI/UX Refactoring`
Database changes: `None`
Backend changes: `Minimal`
Breaking changes: `No`
---
# 1. Objective
Transform the existing certification verification page from an administrative table-based interface into a professional digital certificate experience.
The final result should feel closer to:
* Coursera
* Credly
* LinkedIn Learning
* Microsoft Learn
* Google Certificates
The implementation must:
* preserve the current data model;
* avoid any database migrations;
* support RTL and LTR layouts;
* support printing;
* support browser-generated PDFs;
* support QR verification;
* remain compatible with Bootstrap;
* remain fully server-side rendered (Blade).
---
# 2. Existing problems
| Problem                 | Impact                     |
| ----------------------- | -------------------------- |
| Excessive badge usage   | Visual clutter             |
| Table-based layout      | Looks administrative       |
| Inconsistent colors     | Weak visual identity       |
| No visual hierarchy     | Difficult scanning         |
| Mixed spacing           | Poor readability           |
| No print support        | Bad certificate experience |
| No verification QR      | Reduced authenticity       |
| No responsive structure | Mobile experience suffers  |
---
# 3. AI Agent Skill Requirements
Any AI agent implementing this PRD should possess the following capabilities.
---
### UI/UX skills
* Information architecture
* Visual hierarchy design
* Responsive design
* Typography systems
* Color systems
* Grid systems
* Spacing systems
* Accessibility (WCAG)
* RTL support
* Mobile-first design
---
### Frontend skills
* HTML5
* SCSS
* Bootstrap 5
* CSS Grid
* Flexbox
* Print stylesheets
* SVG
---
### Laravel skills
* Blade components
* View composers
* Localization
* Asset pipelines
* Route generation
---
### Design system skills
* Atomic design
* Component-driven development
* Design tokens
* Consistent state management
---
### Experience design skills
* Certificate design
* Credential verification UX
* Information density optimization
* Cognitive load reduction
---
# 4. Design philosophy
Current state:
```text
Database table
        │
        ▼
Administrative UI
        │
        ▼
Poor experience
```
Target state:
```text
Certification data
         │
         ▼
Digital certificate
         │
         ▼
Trust
         │
         ▼
Verification
```
---
# 5. Final wireframe
```text
┌───────────────────────────────────────────────────────┐
│ ✔ VERIFIED CERTIFICATE                               │
├───────────────────────────────────────────────────────┤
│                                                       │
│                       LOGO                            │
│                                                       │
│                 CERTIFICATE                           │
│                                                       │
│           This certifies that                        │
│                                                       │
│                ABDULLAH ALHABAL                      │
│                                                       │
│         successfully completed                       │
│                                                       │
│         Advanced Pilates Training                    │
│                                                       │
│                 IBVTQ2026072490434                   │
│                                                       │
├───────────────────────────────────────────────────────┤
│                                                       │
│  Trainer                Country                      │
│  John Doe               Morocco                      │
│                                                       │
│  Issued by              Issue date                  │
│  International Board    2025-03-14                  │
│                                                       │
├───────────────────────────────────────────────────────┤
│                                                       │
│ Notes                                                 │
│ Lorem ipsum dolor sit amet...                         │
│                                                       │
├───────────────────────────────────────────────────────┤
│                                                       │
│ QR CODE          Verify authenticity                 │
│                                                       │
├───────────────────────────────────────────────────────┤
│ Print      Save PDF      Share                       │
└───────────────────────────────────────────────────────┘
```
---
# 6. Component architecture
```text
resources/views/web/certifications/
├── show.blade.php
├── search.blade.php
├── partials
│
├── certificate.blade.php
├── certificate_header.blade.php
├── certificate_body.blade.php
├── certificate_meta.blade.php
├── certificate_notes.blade.php
├── certificate_qr.blade.php
├── certificate_actions.blade.php
│
└── styles
    └── certificate.scss
```
---
# 7. Dependency graph
```text
TASK-001
    │
    ├── TASK-002
    │       │
    │       ├── TASK-003
    │       │       │
    │       │       ├── TASK-004
    │       │       │       │
    │       │       │       └── TASK-005
    │       │       │
    │       │       └── TASK-006
    │       │
    │       └── TASK-007
    │
    └── TASK-008
```
---
# TASK-001
## Design system creation
### Objective
Create a unified visual language.
---
### Deliverables
* typography scale;
* spacing system;
* shadows;
* border radius;
* grid system;
* responsive breakpoints.
---
### Color tokens
```scss
$primary: #1e7a46;
$accent: #c89f3d;
$text: #1f2937;
$border: #e5e7eb;
$background: #fafafa;
```
---
### Dependencies
None.
---
# TASK-002
## Certificate component construction
---
### Objective
Replace the existing table.
---
### Current structure
```blade
<table>
```
---
### New structure
```blade
<div class="certificate-card">
    <div class="certificate-header"></div>
    <div class="certificate-body"></div>
    <div class="certificate-meta"></div>
    <div class="certificate-footer"></div>
</div>
```
---
### Remove
* badges;
* pills;
* bordered tables;
* opacity classes.
---
### Depends on
TASK-001
---
# TASK-003
## Responsive layout system
---
### Objective
Create:
* desktop layout;
* tablet layout;
* mobile layout;
* print layout.
---
### Desktop
```text
meta   meta
meta   meta
```
---
### Mobile
```text
meta
meta
meta
meta
```
---
### Depends on
TASK-002
---
# TASK-004
## QR implementation
---
### Objective
Create certificate verification support.
---
### Constraints
* no migrations;
* no database changes;
* use existing routes.
---
### Source
```php
$url = route(
    'web.certifications.show',
    $certification->accreditation_number
);
```
---
### Library
```bash
composer require simplesoftwareio/simple-qrcode
```
---
### Output
```text
+------------------+
|                  |
|       QR         |
|                  |
+------------------+
```
---
### Depends on
TASK-003
---
# TASK-005
## Print support
---
### Objective
Allow the user to print the certificate.
---
### Implementation
```javascript
window.print();
```
---
### Hidden elements
* navigation;
* footer;
* search section;
* controls;
* breadcrumbs;
* action buttons.
---
### CSS
```css
@media print {
}
```
---
### Depends on
TASK-004
---
# TASK-006
## Browser PDF support
---
### Objective
Allow users to export PDFs.
---
### Constraints
No packages.
No Puppeteer.
No DomPDF.
No Browsershot.
---
### Flow
```text
Print
   │
   ▼
window.print()
   │
   ▼
Save as PDF
```
---
### Depends on
TASK-005
---
# TASK-007
## Certificate actions area
---
### Objective
Add interaction controls.
---
### Components
```text
Print
Save PDF
Share
Copy link
```
---
### Depends on
TASK-003
---
# TASK-008
## Optional future improvements
---
### Explicitly excluded
* database migrations;
* signature system;
* signature storage;
* approval workflow.
---
### Possible future additions
* SVG seals;
* cryptographic signatures;
* Open Badge compatibility;
* blockchain verification;
* multilingual PDFs.
---
# 8. Technical restrictions
| Item                 | Status    |
| -------------------- | --------- |
| Bootstrap            | Required  |
| Blade                | Required  |
| Laravel              | Required  |
| JavaScript framework | Forbidden |
| Database changes     | Forbidden |
| Livewire             | Optional  |
| Alpine.js            | Optional  |
---
# 9. Acceptance criteria
### User experience
* Certificate feels official.
* Certificate is readable.
* Certificate supports RTL.
* Certificate supports mobile devices.
* Certificate supports printing.
* QR verification works.
---
### Performance
* No additional queries.
* No N+1 issues.
* No database changes.
---
### Accessibility
* Keyboard navigation.
* Semantic HTML.
* Correct contrast ratio.
---
### Code quality
* Component-based architecture.
* No duplicated styles.
* Strict separation of concerns.
---