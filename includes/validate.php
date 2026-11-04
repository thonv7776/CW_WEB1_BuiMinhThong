<?php


function isBlank($value)
{
    return trim((string) $value) === '';
}

function validEmail($email)
{
    return filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL) !== false;
}

/** duplicate key, FK RESTRICT */
function isConstraintViolation(PDOException $e)
{
    return isset($e->errorInfo[0]) && $e->errorInfo[0] === '23000';
}
