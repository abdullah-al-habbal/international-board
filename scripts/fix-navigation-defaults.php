<?php

// scripts/fix-navigation-defaults.php
declare(strict_types=1);

$root = realpath(__DIR__.'/..');
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/app'));
$filesPatched = [];

foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    if (! str_ends_with($path, '.php')) {
        continue;
    }

    $contents = file_get_contents($path);
    $original = $contents;
    $patched = $contents;

    $patched = preg_replace(
        '/(protected\s+static\s+[^\n=]+=\s*)([A-Za-z0-9_\\\\]+::[A-Za-z0-9_]+)\s*;/m',
        '$1 null;',
        $patched
    );

    $patched = preg_replace(
        '/(protected\s+static\s+[^\n=]+=\s*)__\([^\;]+\)\s*;/m',
        '$1 null;',
        $patched
    );

    if ($patched !== $original) {
        if (preg_match('/class\s+([A-Za-z0-9_]+)\s+extends\s+[A-Za-z0-9_\\\\]+/m', $patched, $classMatches)) {
            if (preg_match('/navigationIcon\s*=\s*([A-Za-z0-9_\\\\:]+)::([A-Za-z0-9_]+)/m', $original, $m)) {
                $icon = $m[1].'::'.$m[2];
            } else {
                $icon = 'null';
            }

            if (
                preg_match('/protected\s+static\s+[^\n=]*navigationIcon/m', $original) &&
                ! preg_match('/public\s+static\s+function\s+getNavigationIcon\s*\(/m', $patched)
            ) {
                $stub = "\n    public static function getNavigationIcon(): string|\\BackedEnum|null\n    {\n        return {$icon};\n    }\n";
                $patched = preg_replace('/}\s*$/', $stub."}\n", $patched);
            }

            if (
                preg_match('/protected\s+static\s+[^\n=]*navigationGroup/m', $original) &&
                ! preg_match('/public\s+static\s+function\s+getNavigationGroup\s*\(/m', $patched)
            ) {
                if (preg_match('/navigationGroup\s*=\s*__\(\s*[\'\"]([^\'\"]+)[\'\"]\s*\)/m', $original, $m2)) {
                    $groupKey = var_export($m2[1], true);
                    $stub = "\n    public static function getNavigationGroup(): ?string\n    {\n        return __({$groupKey});\n    }\n";
                } else {
                    $stub = "\n    public static function getNavigationGroup(): ?string\n    {\n        return null;\n    }\n";
                }
                $patched = preg_replace('/}\s*$/', $stub."}\n", $patched);
            }
        }

        file_put_contents($path.'.bak', $original);
        file_put_contents($path, $patched);
        $filesPatched[] = $path;
    }
}

if (count($filesPatched) === 0) {
    echo "No navigation defaults needed patching.\n";
} else {
    echo "Patched files:\n";
    foreach ($filesPatched as $f) {
        echo " - $f\n";
    }
    echo "Backups created with .bak extension. Review changes before committing.\n";
}
