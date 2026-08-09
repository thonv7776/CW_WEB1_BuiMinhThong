<?php
/**
 * validate.php — small shared validation helpers used across the add/edit
 * pages for every entity.
 */

function isBlank($value)
{
    return trim((string) $value) === '';
}

function validEmail($email)
{
    return filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL) !== false;
}

/** True if $e is an SQL integrity-constraint violation (duplicate key, FK RESTRICT, ...). */
function isConstraintViolation(PDOException $e)
{
    return isset($e->errorInfo[0]) && $e->errorInfo[0] === '23000';
}
