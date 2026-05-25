# Financial Request Module — Implementation Plan

## Overview
Complete the Financial Request module across Admin, Center, and Trainer panels with proper structure, validation, and consistency.

## Execution Order

### Phase 1: Model Layer
1. Add `financialRequests()` HasMany relation to `CertifiedCenter` model
2. Remove float accessor overrides from both models (keep `remaining_amount` accessor)

### Phase 2: File Structure Fixes
3. Move `Admin/Resources/CertifiedCenterFinancialRequestResource.php` → `Admin/Resources/CertifiedCenterFinancialRequests/` + update namespace
4. Move `Admin/Resources/PaymentAgentPersonResource.php` → `Admin/Resources/PaymentAgentPersons/` + update namespace
5. Move `Center/Resources/CenterFinancialRequestResource.php` → `Center/Resources/CenterFinancialRequests/` + update namespace
6. Move `Trainer/Resources/TrainerFinancialRequestResource.php` → `Trainer/Resources/TrainerFinancialRequests/` + update namespace
7. Update all Page imports referencing the old flat resource FQCNs

### Phase 3: New Resources & Pages
8. Create `Admin/Resources/TrainerFinancialRequests/` full resource (Resource, Pages, Schemas, Tables)
9. Create `Center/Resources/CenterFinancialRequests/ViewCenterFinancialRequest` page + Infolist schema
10. Create `Trainer/Resources/TrainerFinancialRequests/ViewTrainerFinancialRequest` page + Infolist schema

### Phase 4: Validation & Business Logic
11. Harden `CertifiedCenterFinancialRequestForm` (Admin) — agent_person_id ownership, min/gte/lte constraints, date rules
12. Harden new `TrainerFinancialRequestForm` (Admin) — same rules applied

### Phase 5: Auth & Middleware
13. Fix auth guards in `getEloquentQuery()` — use explicit `auth('certified_center')` / `auth('trainer')`
14. Change Center navigation group → `__('app.financial_management')`
15. Add financial route whitelist to `EnsureCenterIsAccredited`
16. Add financial route whitelist to `EnsureTrainerIsAccredited`

### Phase 6: Admin Context Views
17. Create `FinancialRequestsRelationManager` for `CertifiedCenterResource`
18. Create `FinancialRequestsRelationManager` for `TrainerResource`
19. Create `CenterFinancialRequestsRelationManager` & `TrainerFinancialRequestsRelationManager` for `PaymentAgentPersonResource`
