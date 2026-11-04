<?php


const QUESTION_IMAGE_ALLOWED_EXT  = ['jpg', 'jpeg', 'png', 'gif'];
const QUESTION_IMAGE_ALLOWED_TYPE = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
const QUESTION_IMAGE_MAX_BYTES    = 2097152; // 2 MB


function saveQuestionImage($file, &$error = null)
{
    $error = null;

    if (!is_array($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no file chosen — fine, the image is optional
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = 'The image must be 2 MB or smaller.';
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'The image could not be uploaded. Please try again.';
        return null;
    }
    if ($file['size'] > QUESTION_IMAGE_MAX_BYTES) {
        $error = 'The image must be 2 MB or smaller.';
        return null;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, QUESTION_IMAGE_ALLOWED_EXT, true)) {
        $error = 'Only JPG, PNG or GIF images are allowed.';
        return null;
    }

    // Confirm the bytes really are an allowed image, not a renamed file.
    $info = @getimagesize($file['tmp_name']);
    if ($info === false || !in_array($info[2], QUESTION_IMAGE_ALLOWED_TYPE, true)) {
        $error = 'That file is not a valid image.';
        return null;
    }

    if (!is_dir(UPLOAD_PATH)) {
        @mkdir(UPLOAD_PATH, 0777, true);
    }

    $name = 'q_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $name)) {
        $error = 'The image could not be saved. Please try again.';
        return null;
    }

    return $name;
}

/* Delete a stored image file by name */
function deleteQuestionImage($name)
{
    $path = UPLOAD_PATH . '/' . basename($name);
    if (is_file($path)) {
        @unlink($path);
    }
}
