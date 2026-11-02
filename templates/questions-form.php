<?php
/**
 * Shared question form (add + edit). In scope: $mode ('add'|'edit'),
 * $question, $modules, $errors, $old, $sessionUsername (add mode),
 * $isAdminEditing + $users (edit mode, admin only). Every dynamic value
 * is escaped; ids in value attributes are cast to int.
 */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$val = function ($field) use ($old, $question) {
    if (array_key_exists($field, $old)) { return $old[$field]; }
    if (is_array($question) && array_key_exists($field, $question)) { return $question[$field]; }
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
$action = $isEdit ? 'questions-edit.php' : 'questions-add.php';
$curModule = (int) $val('module_id');
$curUser = $isEdit ? (int) $val('user_id') : 0;
$showUserDropdown = $isEdit && !empty($isAdminEditing);
$noOptions = empty($modules) || ($showUserDropdown && empty($users));
?>

<div class="page-head">
    <div>
        <h1><?= $isEdit ? 'Edit question' : 'Ask a question' ?></h1>
        <p class="lead">
            <?= $isEdit
                ? 'Update the details, change the module, or replace the screenshots.'
                : 'Post a new question, tag it to your module, and add screenshots if it helps.' ?>
        </p>
    </div>
    <?php if ($isEdit): ?>
        <a class="btn btn-ghost" href="questions.php">Back to questions</a>
    <?php endif; ?>
</div>

<?php if (!$isEdit): ?>
    <p class="hint">Posting as <strong><?= $h($sessionUsername) ?></strong>.</p>
<?php elseif (!$showUserDropdown): ?>
    <p class="hint">Author: <strong><?= $h($ownerUsername) ?></strong></p>
<?php endif; ?>

<?php if ($isEdit && !empty($question['image'])): ?>
    <figure class="image-preview">
        <img src="images/<?= htmlspecialchars($question['image']) ?>" alt="Current screenshot for “<?= $h($question['title']) ?>”">
        <figcaption>Current cover screenshot</figcaption>
    </figure>
<?php endif; ?>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= $h($errors['general']) ?></div>
<?php endif; ?>

<?php if ($noOptions): ?>
    <div class="alert alert-info">
        <?= $showUserDropdown
            ? 'You need at least one author and one module before you can post a question.'
            : 'There are no modules yet.' ?>
        An admin can add them from the admin area.
    </div>
<?php endif; ?>

<form class="form" method="post" action="<?= $action ?>" enctype="multipart/form-data">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $question['question_id'] ?>">
    <?php endif; ?>

                          
    
    
    
    
    
    <!-- title -->
    <div class="field<?= $hasErr('title') ?>">
        <label for="title">Title <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="title" name="title" required maxlength="150"  
               value="<?= $h($val('title')) ?>"
               placeholder="e.g. Why does my PDO query return no rows?"<?= $descBy('title') ?>>
        <?= $err('title') ?>
    </div>

    <div class="field<?= $hasErr('module_id') ?>">
        <label for="module_id">Module <span class="req" aria-hidden="true">*</span></label>



        <!-- module_id -->
        <select id="module_id" name="module_id" required<?= $descBy('module_id') ?>>
            <option value="">Choose a module…</option>
            <?php foreach ($modules as $m): ?>
                <option value="<?= (int) $m['module_id'] ?>"
                    <?= $curModule === (int) $m['module_id'] ? 'selected' : '' ?>>
                    <?= $h($m['module_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?= $err('module_id') ?>
    </div>

    
    
    
    
    <!-- author based tracking -->
    <?php if ($showUserDropdown): ?>          
        <div class="field<?= $hasErr('user_id') ?>">
            <label for="user_id">Author <span class="req" aria-hidden="true">*</span></label>
            <select id="user_id" name="user_id" required<?= $descBy('user_id') ?>>
                <option value="">Choose an author…</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= (int) $u['user_id'] ?>"
                        <?= $curUser === (int) $u['user_id'] ? 'selected' : '' ?>>
                        <?= $h($u['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?= $err('user_id') ?>
        </div>
    <?php endif; ?>

    <div class="field<?= $hasErr('content') ?>">
        <label for="content">Your question <span class="req" aria-hidden="true">*</span></label>
        <textarea id="content" name="content" required rows="8"
                  placeholder="Describe what you’re trying to do, what happens, and what you’ve tried."<?= $descBy('content') ?>><?= $h($val('content')) ?></textarea>
        <?= $err('content') ?>
    </div>

    <div class="field<?= $hasErr('image') ?>">
        <label for="image">Cover screenshot <span class="hint">(optional — JPG, PNG or GIF, up to 2&nbsp;MB)</span></label>


        
        <input type="file" id="image" name="image"
               accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif"<?= $descBy('image') ?>>
        <?php if ($isEdit && !empty($question['image'])): ?>
            <p class="hint" style="margin-top:.5rem">Uploading a new image replaces the current cover.</p>
        <?php endif; ?>
        <?= $err('image') ?>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Save changes' : 'Publish question' ?>
        </button>
        <a class="btn btn-ghost" href="questions.php">Cancel</a>
    </div>
</form>
