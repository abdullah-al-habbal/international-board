# Decision: Dynamic Labels for Certification Creator Selection

## Context
When creating a certification, the form asks for "Issued By" (`creator_type`) and "Select Issuing Authority" (`creator_id`).
When "Trainer" is selected as the issuer, the generic label "Select Issuing Authority" was confusing for users, leading to requests to remove the field entirely.

## Decision
Instead of removing the `creator_id` field (which is required for data integrity to know *which* trainer issued the certificate), we implemented **dynamic labeling** using Filament's reactive `callable` label feature.

- If `creator_type` == `User` → Label: "مسؤول النظام" (Board Admin)
- If `creator_type` == `CertifiedCenter` → Label: "المركز المعتمد" (Certified Center)
- If `creator_type` == `Trainer` → Label: "اختر المدرب" (Select Trainer)

## Consequences
- The form remains technically sound and passes all validation rules.
- The UX is significantly improved, as the field label now directly matches the user's mental model based on their previous selection.
- No database schema changes were required.
