<?php
/**
 * Users list (admin). $users = rows from allUsersWithCounts() (question_count,
 * is_verified). $error = string|null. $flash = ?saved= key.
 */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$count = is_array($users) ? count($users) : 0;

$flashes = [
    'created' => ['success', 'User created.'],
    'updated' => ['success', 'User updated.'],
    'deleted' => ['success', 'User deleted.'],
    'inuse'   => ['error',   'This user still has questions, so they can’t be deleted. Reassign or remove those questions first.'],
    'error'   => ['error',   'Sorry — the user could not be deleted. Please try again.'],
];
$note = $flash !== null && isset($flashes[$flash]) ? $flashes[$flash] : null;
?>
<div class="page-head">
    <div>
        <h1>Users</h1>
        <p class="lead">
            <?php if ($count > 0): ?>
                <?= $count ?> author<?= $count === 1 ? '' : 's' ?> who can be tagged to a question.
            <?php else: ?>
                Users are the authors questions are tagged to.
            <?php endif; ?>
        </p>
    </div>
    <div class="btn-row">
        <a class="btn btn-primary" href="admin-users-add.php">New user</a>
    </div>
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
        <h3>No users yet</h3>
        <p>Add the first user so questions can be tagged to an author.</p>
        <a class="btn btn-primary" href="admin-users-add.php">Add a user</a>
    </div>
<?php elseif ($count > 0): ?>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Email</th>
                    <th scope="col">Account</th>
                    <th scope="col">Questions</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <?php $used = (int) ($u['question_count'] ?? 0); ?>
                    <tr>
                        <td><?= $h($u['username']) ?></td>
                        <td><?= $h($u['email']) ?></td>
                        <td><?= ((int) $u['is_verified'] === 1) ? 'Verified' : 'Pending verification' ?></td>
                        <td><?= $used ?></td>
                        <td class="actions">
                            <a class="btn btn-sm btn-secondary"
                               href="admin-users-edit.php?id=<?= (int) $u['user_id'] ?>">Edit</a>
                            <form class="inline-form" method="post"
                                  action="admin-users-delete.php"
                                  onsubmit="return confirm('Delete “<?= $h(addslashes($u['username'])) ?>”? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?= (int) $u['user_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
