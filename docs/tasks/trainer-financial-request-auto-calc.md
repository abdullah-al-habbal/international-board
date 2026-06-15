# Auto‑Calculate `remaining_amount` & Validation for Trainer Financial Requests

**Type:** Improvement / Bug Fix  
**Status:** Ready for implementation  
**Owner:** Web Engineering  
**Target Release:** v2.4.0  

---

## 1. Overview

When creating or editing a **TrainerFinancialRequest**, the `remaining_amount` must be **auto‑calculated** (not manually entered) and displayed in the Filament form.  
Additionally, proper **validation** must ensure `amount_paid` ≤ `total_payment`.

---

## 2. User Story

- As a **trainer** using the panel  
  I want to see the remaining balance automatically computed when I enter total and paid amounts  
  so that I don’t have to calculate it myself and I’m prevented from entering an overpayment.

---

## 3. Current Behaviour

- The `TrainerFinancialRequest` model has an accessor `getRemainingAmountAttribute()` that computes `total_payment - amount_paid`.  
- The `infolist` already displays this attribute correctly.  
- The `form` (create / edit) does **not** include a `remaining_amount` field, and **no validation** checks that `amount_paid ≤ total_payment`.

---

## 4. Acceptance Criteria

### 4.1 Create / Edit Form

- [ ] Add a **read‑only** field for `remaining_amount` that updates **reactively** when `total_payment` or `amount_paid` changes.  
- [ ] The field should be disabled and visually distinct (e.g., greyed out).  
- [ ] The calculation must work both on page load and while typing.

### 4.2 Validation

- [ ] `amount_paid` must be **numeric** and **≥ 0**.  
- [ ] `total_payment` must be **numeric** and **≥ 0**.  
- [ ] `amount_paid` ≤ `total_payment` (before mutation).  
- [ ] If overpayment, show a clear error message.

### 4.3 No Manual Insertion

- [ ] `remaining_amount` is **not** part of the database `$fillable` (already true).  
- [ ] It is **never** taken from request input; it remains purely computed.

---

## 5. Technical Implementation Plan

### 5.1 Form Schema Update

File: `App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestForm.php`

- Add a `TextInput` for `remaining_amount`:
  ```php
  TextInput::make('remaining_amount')
      ->label(__('app.remaining_amount'))
      ->numeric()
      ->disabled()
      ->dehydrated(false)            // do not send to model
      ->afterStateHydrated(function ($component, $state, $record) {
          // Initial value when editing an existing record
          if ($record) {
              $component->state($record->remaining_amount);
          }
      })
      ->reactive()
      ->afterStateUpdated(function ($set, $get) {
          $total = (float) $get('total_payment');
          $paid  = (float) $get('amount_paid');
          $set('remaining_amount', $total - $paid);
      })
      ->live(afterStateUpdated: function ($set, $get) {
          $total = (float) $get('total_payment');
          $paid  = (float) $get('amount_paid');
          $set('remaining_amount', $total - $paid);
      }),
  ```
  *Alternatively, use `->lazy()` on the source fields and update the remaining via `->afterStateUpdated` on those fields.*

### 5.2 Validation Rules

Add a custom validation method or use `->rules()` on the form:

```php
TextInput::make('amount_paid')
    ->required()
    ->numeric()
    ->minValue(0)
    ->rules([
        function ($get) {
            return function (string $attribute, $value, \Closure $fail) use ($get) {
                $total = (float) $get('total_payment');
                if ($value > $total) {
                    $fail(__('validation.amount_paid_exceeds_total'));
                }
            };
        },
    ]);
```

### 5.3 Mutation (No Change Needed)

- The current `mutateFormDataBeforeCreate` in `CreateTrainerFinancialRequest` only adds `trainer_id`.  
- `remaining_amount` is never stored in the database – it continues to use the accessor.  
- No additional mutation is required unless you decide to persist the column (see **Out of Scope**).

---

## 6. Search & Apply the Same Pattern

The AI agent must **search the codebase** for other models/resources that exhibit the same behaviour (i.e., a computed field based on two stored numeric columns, often named `total_*` and `*_paid`). Examples to look for:

- `CertifiedCenterFinancialRequest`  
- Any other `*FinancialRequest` models  
- Any resource with fields like `total_fees` / `paid_amount`  

For each found instance, apply the same steps:  
1. Add a reactive read‑only `remaining_amount` field to the form.  
2. Add `amount_paid ≤ total_*` validation.  
3. Update the infolist if missing.

---

## 7. File Changes Summary

| File | Action |
|------|--------|
| `App\Filament\Trainer\Resources\TrainerFinancialRequests\Schemas\TrainerFinancialRequestForm.php` | Modify – add reactive `remaining_amount` field and validation rules |
| Language files (e.g., `lang/en/validation.php`) | Add `'amount_paid_exceeds_total' => 'The paid amount cannot exceed the total amount.'` |
| *(Optionally)* `App\Filament\Trainer\Resources\TrainerFinancialRequests\Pages\CreateTrainerFinancialRequest.php` | No change needed |

---

## 8. Out of Scope

- Persisting `remaining_amount` to the database – currently it remains a computed attribute. If later performance demands it, a migration + trigger would be required.  
- Real‑time validation feedback using `->live()` on the form itself is acceptable, but the main validation rule will also be enforced server‑side.

---

## 9. Additional Notes

- The `dehydrated(false)` on the `remaining_amount` field ensures it never ends up in the model’s attributes array.  
- The accessor on the model is already in place and should remain untouched.  
- If any other resource uses the same pattern, create a dedicated section in this PRD or spawn a sub‑task.  
```