<?php

namespace VAJOFOWPPGNext\Shared\Exceptions;

/**
 * Exception thrown when attempting to update settings with no actual changes.
 *
 * This exception is thrown in situations where a settings update operation
 * was requested but no actual changes were detected in the settings values.
 * It helps prevent unnecessary database operations and distinguishes between
 * actual errors and no-change scenarios.
 *
 * @package VAJOFOWPPGNext\Shared\Exceptions
 * @since 1.0.0
 * 
 * Usage example:
 * ```php
 * if ($newSettings === $currentSettings) {
 *     throw new SettingsNoChangesException('No changes detected in settings');
 * }
 * ```
 */
class SettingsNoChangesException extends \Exception {}
