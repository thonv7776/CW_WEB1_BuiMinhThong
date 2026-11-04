<?php
/** OTP entry form. In scope: $email, $error, $notice. */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
?>
<div class="page-head">
    <div>
        <h1>Verify your email</h1>
        <p class="lead">Enter the 6-digit code we emailed to <strong><?= $h($email) ?></strong>.
            The code expires <?= (int) OTP_TTL_MINUTES ?> minutes after it's sent.</p>
    </div>
</div>

<?php if ($notice !== null): ?>
    <div class="alert alert-success" role="status"><?= $h($notice) ?></div>
<?php endif; ?>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php endif; ?>

<form class="form" method="post" action="verify.php">
    <input type="hidden" name="email" value="<?= $h($email) ?>">
    <input type="hidden" name="action" value="verify">
    <div class="field">
        <label for="otp">Verification code <span class="req" aria-hidden="true">*</span></label>
        <input type="text" id="otp" name="otp" required maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
               autocomplete="one-time-code" placeholder="123456">
    </div>
    <div class="btn-row">
        <button type="submit" class="btn btn-primary">Verify</button>
    </div>
</form>

<form class="form" method="post" action="verify.php" style="margin-top:1rem">
    <input type="hidden" name="email" value="<?= $h($email) ?>">
    <input type="hidden" name="action" value="resend">
    <button type="submit" class="btn btn-ghost">Resend code</button>
</form>
