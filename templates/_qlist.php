<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$canManage = function ($q) {
    return !empty($_SESSION['admin_id']) || (!empty($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $q['user_id']);
};
?>
<ul class="qlist">
    <?php foreach ($questions as $q): ?>
        <li>
            <article class="qitem">
                <div class="qitem-main">
                    <h3><?= $h($q['title']) ?></h3>
                    <p class="excerpt"><?= $h($q['content']) ?></p>
                    <div class="qmeta">
                        <span class="tag"><?= $h($q['module_name']) ?></span>
                        <span>
                            Asked by
                            <?= $h($q['username']) ?>
                            (<a href="mailto:<?= $h($q['email']) ?>" aria-label="Email <?= $h($q['username']) ?>">email</a>)
                        </span>
                        <?php if (!empty($q['created_at'])): ?>
                            <time datetime="<?= $h($q['created_at']) ?>">
                                <?= $h(date('j M Y', strtotime($q['created_at']))) ?>
                            </time>
                        <?php endif; ?>
                    </div>

                    <?php if ($canManage($q)): ?>
                        <div class="qactions">
                            <a class="btn btn-sm btn-secondary"
                               href="questions-edit.php?id=<?= (int) $q['question_id'] ?>">Edit</a>
                            <form class="inline-form" method="post"
                                  action="questions-delete.php"
                                  onsubmit="return confirm('Delete this question? This cannot be undone.');">
                                <input type="hidden" name="id" value="<?= (int) $q['question_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($q['image'])): ?>
                    <div class="qgallery">
                        <img class="qthumb" src="images/<?= htmlspecialchars($q['image']) ?>" alt="Screenshot attached to <?= htmlspecialchars($q['title']) ?>">
                    </div>
                <?php endif; ?>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
