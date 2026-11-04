<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$val = function ($field) use ($old, $module) {
    if (array_key_exists($field, $old)) { return $old[$field]; }
    if (is_array($module) && array_key_exists($field, $module)) { return $module[$field]; }
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
$action = $isEdit ? 'admin-modules-edit.php' : 'admin-modules-add.php';
?>
<div class="page-head">
    <div>
        <h1><?= $isEdit ? 'Edit module' : 'New module' ?></h1>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= $h($errors['general']) ?></div>
<?php endif; ?>

<form class="form" method="post" action="<?= $action ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $module['module_id'] ?>">
    <?php endif; ?>

    <div class="field<?= $hasErr('module_name') ?>">
        <label for="module_name">Module name <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="module_name" name="module_name" required maxlength="100"
               value="<?= $h($val('module_name')) ?>"
               placeholder="e.g. COMP1841 Web Programming 1"<?= $descBy('module_name') ?>>
        <?= $err('module_name') ?>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Save changes' : 'Create module' ?>
        </button>
        <a class="btn btn-ghost" href="admin-modules.php">Cancel</a>
    </div>
</form>
