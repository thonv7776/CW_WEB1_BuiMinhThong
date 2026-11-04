<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$val = function ($field) use ($old, $user) {
    if (array_key_exists($field, $old)) { return $old[$field]; }
    if (is_array($user) && array_key_exists($field, $user)) { return $user[$field]; }
    return '';
};
$err = function ($field) use ($errors, $h) {
    return isset($errors[$field])
        ? '<p class="field-error" id="' . $h($field) . '-error" role="alert">' . $h($errors[$field]) . '</p>'
        : '';
};
$hasErr = function ($field) use ($errors) { return isset($errors[$field]) ? ' has-error' : ''; };
$descBy = function ($field) use ($errors, $h) {
    return isset($errors[$field]) ? ' aria-describedby="' . $h($field) . '-error"' : '';
};

$isEdit = ($mode === 'edit');
$action = $isEdit ? 'admin-users-edit.php' : 'admin-users-add.php';
?>
<div class="page-head">
    <div>
        <h1><?= $isEdit ? 'Edit user' : 'New user' ?></h1>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= $h($errors['general']) ?></div>
<?php endif; ?>

<form class="form" method="post" action="<?= $action ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $user['user_id'] ?>">
    <?php endif; ?>

    <div class="field<?= $hasErr('username') ?>">
        <label for="username">Username <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="username" name="username" required maxlength="50"
               value="<?= $h($val('username')) ?>"<?= $descBy('username') ?>>
        <?= $err('username') ?>
    </div>

    <div class="field<?= $hasErr('email') ?>">
        <label for="email">Email <span class="req" aria-hidden="true">*</span></label>
        <input type="email" id="email" name="email" required maxlength="120"
               value="<?= $h($val('email')) ?>"<?= $descBy('email') ?>>
        <?= $err('email') ?>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Save changes' : 'Create user' ?>
        </button>
        <a class="btn btn-ghost" href="admin-users.php">Cancel</a>
    </div>
</form>
