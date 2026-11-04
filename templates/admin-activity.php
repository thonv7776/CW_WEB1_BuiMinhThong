<?php
/** Activity feed (admin). $entries = rows from allActivityLog(). $error = string|null. */
$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$count = is_array($entries) ? count($entries) : 0;

$actionLabels = [
    'login'   => 'logged in',
    'logout'  => 'logged out',
    'create'  => 'created',
    'update'  => 'updated',
    'delete'  => 'deleted',
];
?>
<div class="page-head">
    <div>
        <h1>Activity log</h1>
        <p class="lead">The last <?= $count ?> admin action<?= $count === 1 ? '' : 's' ?> — logins and content changes.</p>
    </div>
</div>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php elseif ($count === 0): ?>
    <div class="empty">
        <h3>Nothing logged yet</h3>
        <p>Admin logins and content changes will appear here.</p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th scope="col">When</th>
                    <th scope="col">Admin</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entries as $e): ?>
                    <?php
                        $label = $actionLabels[$e['action']] ?? $e['action'];
                        $detail = $label;
                        if (!empty($e['target_entity'])) {
                            $detail .= ' ' . $e['target_entity'] . (!empty($e['target_id']) ? ' #' . (int) $e['target_id'] : '');
                        }
                    ?>
                    <tr>
                        <td><?= $h(date('j M Y H:i', strtotime($e['created_at']))) ?></td>
                        <td><?= $h($e['admin_username'] ?? 'Removed admin') ?></td>
                        <td><?= $h($detail) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
