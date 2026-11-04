<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$val = function ($field) use ($old) { return $old[$field] ?? ''; };
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
        <h1>Create an account</h1>
        <p class="lead">Register to post questions under your own name — we’ll email you a one-time code to confirm your address.</p>
    </div>
</div>

<?php if (!empty($errors['general'])): ?>
    <div class="alert alert-error" role="alert"><?= $h($errors['general']) ?></div>
<?php endif; ?>

<form class="form" method="post" action="register.php">
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

    <div class="field<?= $hasErr('password') ?>">
        <label for="password">Password <span class="req" aria-hidden="true">*</span> <span class="hint">(min. 8 characters)</span></label>
        <input type="password" id="password" name="password" required minlength="8"<?= $descBy('password') ?>>
        <?= $err('password') ?>
    </div>

    <div class="field<?= $hasErr('confirm_password') ?>">
        <label for="confirm_password">Confirm password <span class="req" aria-hidden="true">*</span></label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8"<?= $descBy('confirm_password') ?>>
        <?= $err('confirm_password') ?>
    </div>

    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Create account</button>
        <a class="btn btn-ghost" href="login.php">I already have an account</a>
    </div>
</form>
