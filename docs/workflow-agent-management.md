# Payment Agent Management Documentation

## Agent Entity

### Model: `CertifiedCenterPaymentAgentPerson`

| Property | Value |
|----------|-------|
| Table | `certified_center_payment_agent_persons` |
| Fillable | `name`, `certified_center_id` |
| Casts | None |
| Timestamps | Yes |

### Database Schema

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | bigint | PK, auto-increment |
| `certified_center_id` | bigint | FK → `certified_centers.id`, cascade delete |
| `name` | string | unique with `certified_center_id` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### Relationships

| Relationship | Type | Target | FK | On Delete |
|-------------|------|--------|-----|-----------|
| `certifiedCenter` | BelongsTo | CertifiedCenter | `certified_center_id` | Cascade |
| `centerFinancialRequests` | HasMany | CertifiedCenterFinancialRequest | `agent_person_id` | — |
| `trainerFinancialRequests` | HasMany | TrainerFinancialRequest | `agent_person_id` | — |

---

## Creation Workflow

### Who Can Create?

**Admin only.** Neither Center nor Trainer panels have resources for agent persons.

### How to Create (Step-by-Step)

1. Admin navigates to `/admin/payment-agent-persons`
2. Clicks "Create" button
3. Fills in:
   - **Name** (required, max 255 chars)
   - **Certified Center** (required, searchable Select with preload)
4. Submits → record created → redirected to view page

### Association Rules

- Each agent belongs to **exactly one** certified center
- Multiple agents can belong to the same center
- Unique constraint: `(certified_center_id, name)` — same center cannot have two agents with the same name
- Agent is linked at creation time — cannot be reassigned to a different center

### What Data Is Stored?

Currently: **name only**. No phone, email, bank account, ID number, or status field.

---

## Usage in Financial Requests

### Center Financial Requests

When admin creates a center financial request:
1. Admin selects `certified_center_id` first (reactive Select)
2. `agent_person_id` dropdown filters to agents belonging to that center
3. Server-side validation ensures the selected agent belongs to the selected center
4. Agent is stored as `agent_person_id` on the financial request

### Trainer Financial Requests

When admin creates a trainer financial request:
1. Admin selects `trainer_id` first
2. `agent_person_id` dropdown shows ALL agents grouped by center name
3. No validation that the agent is related to the trainer's center
4. When trainer creates their own request, same agent selection applies

---

## Deletion Impact Analysis

### What Happens If an Agent Is Deleted?

| Dependent Entity | Impact |
|-----------------|--------|
| Center Financial Requests | `agent_person_id` set to NULL (nullOnDelete constraint) |
| Trainer Financial Requests | `agent_person_id` set to NULL (nullOnDelete constraint) |
| Certified Center | Agent deleted (cascade from center deletion) |

### Risk Assessment

- Financial requests ** survive ** agent deletion — but lose their agent reference
- Reports grouping by agent will show orphaned requests under "Unassigned"
- No soft delete — agent is permanently removed

---

## Current Limitations

| Limitation | Impact |
|-----------|--------|
| No phone/email field | Cannot contact agent directly from system |
| No bank account details | Cannot automate payments |
| No status field (active/inactive) | Cannot deactivate an agent without deleting |
| No audit trail | Cannot track who created/modified the agent |
| No center-side management | Center cannot manage their own agents |
