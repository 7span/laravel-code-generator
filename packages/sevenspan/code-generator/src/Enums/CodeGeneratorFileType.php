<?php

namespace Sevenspan\CodeGenerator\Enums;

/**
 * Enum CodeGeneratorFileType
 *
 * This enum represents the different types of files that can be generated
 * within the Code Generator package. Each case corresponds to
 * a specific type of file that can be created during the code generation process.
 */
enum CodeGeneratorFileType: string
{
    /**
     * Represents a Controller file.
     */
    case CONTROLLER = 'Controller';

    /**
     * Represents a Service file.
     */
    case SERVICE = 'Service';

    /**
     * Represents a Model file.
     */
    case MODEL = 'Model';

    /**
     * Represents a Factory file.
     */
    case FACTORY = 'Factory';

    /**
     * Represents a Migration file.
     */
    case MIGRATION = 'Migration';

    /**
     * Represents an Observer file.
     */
    case OBSERVER = 'Observer';

    /**
     * Represents a Policy file.
     */
    case POLICY = 'Policy';

    /**
     * Represents a Resource file.
     */
    case RESOURCE = 'Resource';

    /**
     * Represents a Resource file.
     */
    case RESOURCE_COLLECTION = 'Resource-Collection';

    /**
     * Represents a Trait file.
     */
    case TRAIT = 'Trait';

    /**
     * Represents a Request file.
     */
    case REQUEST = 'Request';

    /**
     * Represents a Notification file.
     */
    case NOTIFICATION = 'Notification';

    /**
     * Represents a Route file.
     */
    case ROUTE = 'Route';
}
