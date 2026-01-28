<?php

declare(strict_types=1);

namespace App\Foundation\Theme;

enum ThemeType: string
{
    case LEGACY = 'legacy';
    case FSE = 'fse';
    case NATIVE = 'native';
}
