<?php
/**
 * Contact form. In scope: $errors, $old, $sent (bool — true after a
 * successful submission, via ?sent=1).
 */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };

$val = function ($field) use ($old) {
    return array_key_exists($field, $old) ? $old[$field] : '';
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
?>
<div class="page-head">
    <div>
        <h1>Contact the team</h1>
        <p class="lead">Spotted a problem or need a hand? Send a message straight to the site admins.</p>
    </div>
</div>

<?php if ($sent): ?>
    <div class="alert alert-success" role="status">Thanks — your message has been sent.</div>
<?php endif; ?>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= $h($errors['general']) ?></div>
<?php endif; ?>

<form class="form" method="post" action="contact.php">
    <div class="field<?= $hasErr('name') ?>">
        <label for="name">Your name <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="name" name="name" required maxlength="100"
               value="<?= $h($val('name')) ?>"<?= $descBy('name') ?>>
        <?= $err('name') ?>
    </div>

    <div class="field<?= $hasErr('email') ?>">
        <label for="email">Your email <span class="req" aria-hidden="true">*</span></label>
        <input type="email" id="email" name="email" required maxlength="120"
               value="<?= $h($val('email')) ?>"<?= $descBy('email') ?>>
        <?= $err('email') ?>
    </div>

    <div class="field<?= $hasErr('subject') ?>">
        <label for="subject">Subject <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="subject" name="subject" required maxlength="150"
               value="<?= $h($val('subject')) ?>"<?= $descBy('subject') ?>>
        <?= $err('subject') ?>
    </div>

    <div class="field<?= $hasErr('body') ?>">
        <label for="body">Message <span class="req" aria-hidden="true">*</span></label>
        <textarea id="body" name="body" required rows="8"<?= $descBy('body') ?>><?= $h($val('body')) ?></textarea>
        <?= $err('body') ?>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Send message</button>
    </div>
</form>
