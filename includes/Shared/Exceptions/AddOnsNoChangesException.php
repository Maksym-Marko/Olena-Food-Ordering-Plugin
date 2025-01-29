<?php

namespace VAJOFOWPPGNext\Shared\Exceptions;

/**
 * Exception thrown when attempting to update add-ons with no actual changes.
 *
 * This exception is thrown in situations where an add-ons update operation
 * was requested but no actual changes were detected in the add-ons values.
 * It helps prevent unnecessary database operations and distinguishes between
 * actual errors and no-change scenarios.
 *
 * @package VAJOFOWPPGNext\Shared\Exceptions
 * @since 1.0.0
 * 
 * Usage example:
 * ```php
 * if ($newAddOns === $savedAddOns) {
 *     throw new AddOnsNoChangesException('Add-ons already set.');
 * }
 * ```
 */
class AddOnsNoChangesException extends \Exception {}
