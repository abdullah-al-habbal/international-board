<?php

// filePath: app/Providers/Filament/CenterPanelProvider.php
// ── DIFF / PATCH — add middleware and widget registration ──────────────────
// This shows only the changes needed. Merge with your existing CenterPanelProvider.
//
// 1. In the panel() method, add the middleware:
//
//    ->middleware([
//        EncryptCookies::class,
//        AddQueuedCookiesToResponse::class,
//        StartSession::class,
//        AuthenticateSession::class,
//        ShareErrorsFromSession::class,
//        VerifyCsrfToken::class,
//        SubstituteBindings::class,
//        DisableBladeIconComponents::class,
//        DispatchServingFilamentEvent::class,
//        \App\Http\Middleware\EnsureCenterIsAccredited::class, // ← ADD THIS
//    ])
//
// 2. Add the widget to ->widgets():
//
//    ->widgets([
//        \App\Filament\Center\Widgets\AccreditationStatusBanner::class, // ← ADD THIS
//        \App\Filament\Center\Widgets\CenterStatsOverview::class,
//        \App\Filament\Center\Widgets\MonthlyCertificationsChart::class,
//    ])
//
// No other changes needed — resources auto-discover via ->discoverResources().
