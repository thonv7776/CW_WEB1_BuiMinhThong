<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$count = is_array($messages) ? count($messages) : 0;
?>
<div class="page-head">
    <div>
        <h1>Messages</h1>
        <p class="lead">
            <?php if ($count > 0): ?>
                <?= $count ?> message<?= $count === 1 ? '' : 's' ?> sent through the contact form.
            <?php else: ?>
                Messages sent through the contact form will show up here.
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if ($error !== null): ?>
    <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
<?php endif; ?>

<?php if ($error === null && $count === 0): ?>
    <div class="empty">
        <h3>No messages yet</h3>
        <p>Nothing has been sent through the contact form.</p>
    </div>
<?php elseif ($count > 0): ?>
    <div class="table-wrap">
        <table class="data">
            <thead>
                <tr>
                    <th scope="col">From</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Received</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $m): ?>
                    <tr>
                        <td>
                            <a href="mailto:<?= $h($m['email']) ?>"><?= $h($m['name']) ?></a>
                        </td>
                        <td><?= $h($m['subject']) ?></td>
                        <td>
                            <?php if (!empty($m['created_at'])): ?>
                                <time datetime="<?= $h($m['created_at']) ?>">
                                    <?= $h(date('j M Y', strtotime($m['created_at']))) ?>
                                </time>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <form class="inline-form" method="post"
                                  action="admin-messages-delete.php"
                                  onsubmit="return confirm('Delete this message? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?= (int) $m['message_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"><p class="excerpt"><?= $h($m['body']) ?></p></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
