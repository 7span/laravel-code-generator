<?php

namespace Sevenspan\CodeGenerator\Enums;

enum FileGenerationStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}
