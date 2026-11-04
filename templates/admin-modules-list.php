<?php
/**
 * Modules list (admin). $modules = rows from allModulesWithCounts(), each
 * with a question_count. $error = string|null. $flash = the ?saved= key.
 */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$count = is_array($modules) ? count($modules) : 0;

$flashes = [
    'created' => ['success', 'Module created.'],
    'updated' => ['success', 'Module updated.'],
    'deleted' => ['success', 'Module deleted.'],
    'inuse'   => ['error',   'That module still has questions tagged to it, so it can’t be deleted. Reassign or remove those questions first.'],
    'error'   => ['error',   'Sorry — the module could not be deleted. Please try again.'],
];
$note = $flash !== null && isset($flashes[$flash]) ? $flashes[$flash] : null;
?>
<div class="page-head">
    <div>
        <h1>Modules</h1>
        <p class="lead">
            <?php if ($count > 0): ?>
                <?= $count ?> module<?= $count === 1 ? '' : 's' ?> that questions can be tagged to.
            <?php else: ?>
                Modules are the categories questions are tagged to.
            <?php endif; ?>
        </p>
    </div>
    <a class="btn btn-primary" href="admin-modules-add.php">New module</a>
</div>

<?php if ($note !== null): ?>
    <div class="alert alert-<?= $h($note[0]) ?>" role="<?= $note[0] === 'error' ? 'alert' : 'status' ?>">
        <?= $h($note[1]) ?>
    </div>
<?php endif; ?>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php endif; ?>

<?php if ($error === null && $count === 0): ?>
    <div class="empty">
        <h3>No modules yet</h3>
        <p>Add the first module so students can tag their questions to it.</p>
        <a class="btn btn-primary" href="admin-modules-add.php">Add a module</a>
    </div>
<?php elseif ($count > 0): ?>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th scope="col">Module</th>
                    <th scope="col">Questions</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modules as $m): ?>
                    <?php $used = (int) $m['question_count']; ?>
                    <tr>
                        <td><?= $h($m['module_name']) ?></td>
                        <td><?= $used ?></td>
                        <td class="actions">
                            <a class="btn btn-sm btn-secondary"
                               href="admin-modules-edit.php?id=<?= (int) $m['module_id'] ?>">Edit</a>
                            <form class="inline-form" method="post"
                                  action="admin-modules-delete.php"
                                  onsubmit="return confirm('Delete “<?= $h(addslashes($m['module_name'])) ?>”? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?= (int) $m['module_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"
                                    <?= $used > 0 ? 'disabled title="In use by ' . $used . ' question' . ($used === 1 ? '' : 's') . '"' : '' ?>>
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
