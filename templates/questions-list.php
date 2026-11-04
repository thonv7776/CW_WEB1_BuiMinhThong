<?php
/**
 * Questions list. Presentation only — data comes in as $questions (rows from
 * allQuestions()) and $error (string|null). Every dynamic value is escaped.
 */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$count = is_array($questions) ? count($questions) : 0;
?>
<div class="page-head">
    <div>
        <h1>Questions</h1>
        <p class="lead">
            <?php if ($count > 0): ?>
                <?= $count ?> question<?= $count === 1 ? '' : 's' ?> from across the modules.
            <?php else: ?>
                Everything students have asked will show up here.
            <?php endif; ?>
        </p>
    </div>
    <a class="btn btn-primary" href="questions-add.php">Ask a question</a>
</div>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php endif; ?>

<?php if ($error === null && $count === 0): ?>
    <div class="empty">
        <h3>No questions yet</h3>
        <p>Be the first to post one — pick your module, describe what you’re stuck on, and add a screenshot if it helps.</p>
        <a class="btn btn-primary" href="questions-add.php">Ask the first question</a>
    </div>
<?php elseif ($count > 0): ?>
    <?php require TEMPLATES_PATH . '/_qlist.php'; ?>
<?php endif; ?>
