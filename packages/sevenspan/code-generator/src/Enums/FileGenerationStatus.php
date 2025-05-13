<?php

namespace Sevenspan\CodeGenerator\Enums;

/**
 * Enum FileGenerationStatus
 *
 * Represents the status of file generation operations.
 */
enum FileGenerationStatus: string
{
/**
     * Indicates that the file generation was successful.
     */
    case SUCCESS = 'success';

/**
     * Indicates that the file generation encountered an error.
     */
    case ERROR = 'error';
}
