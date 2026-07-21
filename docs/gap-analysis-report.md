# Gap Analysis Report

## Critical Issues

### 1. Infolist references non-existent `status` column

**Severity:** 🔴 Critical
**Location:**
- `app/Filament/Admin/Resources/CertifiedCenterFinancialRequests/Schemas/CertifiedCenterFinancialRequestInfolist.php`
- `app/Filament/Admin/Resources/TrainerFinancialRequests/Schemas/TrainerFinancialRequestInfolist.php`

**Problem:** Both infolists render `TextEntry::make('status')` with badge colors for `pending`/`approved`/`rejected`. However:
- The `status` column does not exist in either migration
- The `status` field is not in either model's `$fillable`
- The View page will show a blank/broken status badge

**Evidence:**
```php
// CertifiedCenterFinancialRequestInfolist.php line 27-36
TextEntry::make('status')
    ->label(__('app.status'))
    ->badge()
    ->color(fn (string $state): string => match ($state) {
        'pending'  => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        default    => 'gray',
    })
```

**Fix:** Either add `status` column to both tables + models, or remove the `status` entry from both infolists.

---

### 2. Infolist references non-existent `amount` column

**Severity:** 🔴 Critical
**Location:** Same infolists as above

**Problem:** Both infolists render `TextEntry::make('amount')` with `money('SAR')`. The model has `total_payment` and `amount_paid` — there is no `amount` attribute.

**Evidence:**
```php
// CertifiedCenterFinancialRequestInfolist.php line 23-26
TextEntry::make('amount')
    ->label(__('app.amount'))
    ->money('SAR')
    ->columnSpan(1),
```

**Fix:** Replace `amount` with `total_payment` in both infolists.

---

### 3. Infolist references non-existent `notes` column

**Severity:** 🔴 Critical
**Location:** Same infolists as above

**Problem:** Both infolists render `TextEntry::make('notes')`. The model column is `reason`, not `notes`.

**Evidence:**
```php
// CertifiedCenterFinancialRequestInfolist.php line 37-40
TextEntry::make('notes')
    ->label(__('app.notes'))
    ->markdown()
    ->columnSpanFull(),
```

**Fix:** Replace `notes` with `reason` in both infolists.

---

### 4. No `status` field — no financial lifecycle

**Severity:** 🔴 Critical
**Location:** Both financial request models + migrations

**Problem:** Financial requests have no status field, no enum, and no workflow. Records are created with `total_payment` and `amount_paid` set simultaneously. There is no concept of:
- Pending/approved/rejected states
- Payment scheduling
- Partial payments over time
- Admin approval

**Impact:** The system cannot track whether a payment is pending, approved, or rejected. The infolist badge code is dead code.

**Fix:** Add `status` enum + column to both tables, or remove all status-related code from infolists.

---

## Warnings

### 5. Currency mismatch: SAR vs USD

**Severity:** 🟡 Warning
**Location:**
- Infolists use `money('SAR')` on `amount`
- Tables use `money('USD')` on `total_payment`, `amount_paid`, `remaining_amount`

**Problem:** Inconsistent currency display. The infolist shows Saudi Riyals, tables show US Dollars.

**Fix:** Standardize on one currency across all financial displays.

---

### 6. Trainer form has no `maxValue` on `amount_paid`

**Severity:** 🟡 Warning
**Location:** `app/Filament/Trainer/Resources/TrainerFinancialRequests/Schemas/TrainerFinancialRequestForm.php`

**Problem:** Admin form constrains `amount_paid` to `maxValue(fn ($get) => $get('total_payment'))`. Trainer form has no such constraint — trainer can set `amount_paid` greater than `total_payment`.

**Evidence:**
```php
// Admin form (correct)
TextInput::make('amount_paid')
    ->maxValue(fn (callable $get): float => (float) ($get('total_payment') ?? 0))

// Trainer form (missing constraint)
TextInput::make('amount_paid')
    // no maxValue
```

**Fix:** Add `->maxValue(fn (callable $get): float => (float) ($get('total_payment') ?? 0))` to trainer form.

---

### 7. No permission checks on financial resources

**Severity:** 🟡 Warning
**Location:** All three financial resources

**Problem:** No `canCreate()`, `canEdit()`, `canDelete()` overrides. Access is gated only by panel middleware (auth guard). Any authenticated admin can create/edit/delete any financial request.

**Impact:** No role-based restrictions within the admin panel.

**Fix:** Consider adding policy-based authorization or `canCreate()`/`canEdit()` overrides if different admin roles need different permissions.

---

### 8. Agent has only `name` — no contact or bank details

**Severity:** 🟡 Warning
**Location:** `CertifiedCenterPaymentAgentPerson` model + migration

**Problem:** Agent persons store only `name` and `certified_center_id`. No phone, email, bank account, or ID number. This limits the system's ability to:
- Contact agents
- Process automated payments
- Verify agent identity

**Fix:** Add fields like `phone`, `email`, `bank_account_number`, `is_active` to the agent model.

---

## Improvements

### 9. No date range filter on financial request tables

**Severity:** 🟢 Info
**Location:** All financial request tables

**Problem:** Tables have no date range filter. Admin cannot filter requests by date period.

**Fix:** Add `Filter::make('date')` with `DatePicker` from/to fields.

---

### 10. No export functionality

**Severity:** 🟢 Info
**Location:** All financial request resources

**Problem:** No export to CSV/PDF for financial reports.

**Fix:** Add `ExportAction` or `Filament\Actions\ExportAction` to table bulk actions.

---

### 11. No audit trail for financial modifications

**Severity:** 🟢 Info
**Location:** All financial request models

**Problem:** No `edited_at` / `edited_by` tracking. When admin edits a financial request, there is no record of who changed what.

**Fix:** Add `spatie/laravel-activitylog` or manual audit columns.

---

### 12. Missing translation keys

**Severity:** 🟢 Info
**Location:** `lang/ar/app.php` and `lang/en/app.php`

**Problem:** Some financial terms may be missing translations. The infolist references `app.amount`, `app.notes`, `app.status` which exist but may not match the actual model fields.

**Fix:** Audit all `__()` calls in financial resources against lang files.

---

## Summary

| Severity | Count | Action Required |
|----------|-------|-----------------|
| 🔴 Critical | 4 | Fix immediately — broken View pages, dead code |
| 🟡 Warning | 4 | Fix soon — logic gaps, missing constraints |
| 🟢 Info | 4 | Improve over time — nice-to-have features |
