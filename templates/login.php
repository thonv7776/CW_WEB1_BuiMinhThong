<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>
<div class="page-head">
    <div>
        <h1>Log in</h1>
        <p class="lead">Log in to post questions under your own account.</p>
    </div>
</div>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php endif; ?>

<form class="form" method="post" action="login.php">
    <div class="field">
        <label for="identifier">Username or email <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="identifier" name="identifier" required
               value="<?= $h($old['identifier'] ?? '') ?>">
    </div>
    <div class="field">
        <label for="password">Password <span class="req" aria-hidden="true">*</span></label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Log in</button>
        <a class="btn btn-ghost" href="register.php">Create an account</a>
    </div>
</form>
