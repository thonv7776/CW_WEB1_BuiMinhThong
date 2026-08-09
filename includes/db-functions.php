<?php
/**
 * db-functions.php — all data access. One function per operation, $pdo
 * always first, every SQL statement via named :placeholders through query().
 * No SQL anywhere else in the codebase (pages or templates).
 */

function query($pdo, $sql, $parameters = [])
{
    $stmt = $pdo->prepare($sql);
    $stmt->execute($parameters);
    return $stmt;
}

/* --- questions ---------------------------------------------------------- */

/** Every question joined to its author and module, newest first. */
function allQuestions($pdo)
{
    $sql = 'SELECT q.question_id, q.title, q.content, q.image, q.created_at,
                   u.user_id, u.username, u.email,
                   m.module_id, m.module_name
            FROM questions q
            INNER JOIN users   u ON q.user_id   = u.user_id
            INNER JOIN modules m ON q.module_id = m.module_id
            ORDER BY q.created_at DESC, q.question_id DESC';
    return query($pdo, $sql)->fetchAll();
}

/** One question by id (raw row). Returns false if missing. */
function getQuestion($pdo, $id)
{
    return query(
        $pdo,
        'SELECT question_id, title, content, image, user_id, module_id, created_at
         FROM questions WHERE question_id = :id',
        [':id' => (int) $id]
    )->fetch();
}

/** Insert a question. Returns the new question_id. */
function insertQuestion($pdo, $title, $content, $image, $userId, $moduleId)
{
    query(
        $pdo,
        'INSERT INTO questions (title, content, image, user_id, module_id)
         VALUES (:title, :content, :image, :user_id, :module_id)',
        [
            ':title'     => $title,
            ':content'   => $content,
            ':image'     => $image,
            ':user_id'   => $userId,
            ':module_id' => $moduleId,
        ]
    );
    return (int) $pdo->lastInsertId();
}

function updateQuestion($pdo, $id, $title, $content, $image, $userId, $moduleId)
{
    query(
        $pdo,
        'UPDATE questions
            SET title = :title, content = :content, image = :image,
                user_id = :user_id, module_id = :module_id
          WHERE question_id = :id',
        [
            ':title'     => $title,
            ':content'   => $content,
            ':image'     => $image,
            ':user_id'   => $userId,
            ':module_id' => $moduleId,
            ':id'        => (int) $id,
        ]
    );
}

function deleteQuestion($pdo, $id)
{
    query($pdo, 'DELETE FROM questions WHERE question_id = :id', [':id' => (int) $id]);
}

/* --- users ---------------------------------------------------------------- */

/** Every author, alphabetically — for dropdowns. */
function allUsers($pdo)
{
    return query($pdo, 'SELECT user_id, username, email FROM users ORDER BY username')->fetchAll();
}

/** Every author, alphabetically, with how many questions reference them. */
function allUsersWithCounts($pdo)
{
    $sql = 'SELECT u.user_id, u.username, u.email, u.is_verified, COUNT(q.question_id) AS question_count
            FROM users u
            LEFT JOIN questions q ON q.user_id = u.user_id
            GROUP BY u.user_id, u.username, u.email, u.is_verified
            ORDER BY u.username';
    return query($pdo, $sql)->fetchAll();
}

/** One user by id. Returns false if missing. */
function getUser($pdo, $id)
{
    return query(
        $pdo,
        'SELECT * FROM users WHERE user_id = :id',
        [':id' => (int) $id]
    )->fetch();
}

function insertUser($pdo, $username, $email)
{
    query(
        $pdo,
        'INSERT INTO users (username, email, is_verified) VALUES (:username, :email, 1)',
        [':username' => $username, ':email' => $email]
    );
    return (int) $pdo->lastInsertId();
}

function updateUser($pdo, $id, $username, $email)
{
    query(
        $pdo,
        'UPDATE users SET username = :username, email = :email WHERE user_id = :id',
        [':username' => $username, ':email' => $email, ':id' => (int) $id]
    );
}

/** Throws PDOException (SQLSTATE 23000) if the user still has questions. */
function deleteUser($pdo, $id)
{
    query($pdo, 'DELETE FROM users WHERE user_id = :id', [':id' => (int) $id]);
}

/* --- self-service accounts (register / OTP verify / login) ---------------- */

function findUserByEmail($pdo, $email)
{
    return query($pdo, 'SELECT * FROM users WHERE email = :email', [':email' => $email])->fetch();
}

function findUserByUsername($pdo, $username)
{
    return query($pdo, 'SELECT * FROM users WHERE username = :username', [':username' => $username])->fetch();
}

/** Look up a user by email OR username, for the login form. */
function findUserForLogin($pdo, $identifier)
{
    return query(
        $pdo,
        'SELECT * FROM users WHERE email = :id1 OR username = :id2',
        [':id1' => $identifier, ':id2' => $identifier]
    )->fetch();
}

/** Create an unverified account with a hashed password and a fresh OTP. */
function insertPendingUser($pdo, $username, $email, $passwordHash, $otp, $otpExpiresAt)
{
    query(
        $pdo,
        'INSERT INTO users (username, email, password_hash, otp_code, otp_expires_at, is_verified)
         VALUES (:username, :email, :password_hash, :otp_code, :otp_expires_at, 0)',
        [
            ':username'       => $username,
            ':email'          => $email,
            ':password_hash'  => $passwordHash,
            ':otp_code'       => $otp,
            ':otp_expires_at' => $otpExpiresAt,
        ]
    );
    return (int) $pdo->lastInsertId();
}

/** Issue a new OTP (initial send or "resend code"). */
function setUserOtp($pdo, $userId, $otp, $otpExpiresAt)
{
    query(
        $pdo,
        'UPDATE users SET otp_code = :otp_code, otp_expires_at = :otp_expires_at WHERE user_id = :id',
        [':otp_code' => $otp, ':otp_expires_at' => $otpExpiresAt, ':id' => (int) $userId]
    );
}

function markUserVerified($pdo, $userId)
{
    query(
        $pdo,
        'UPDATE users SET is_verified = 1, otp_code = NULL, otp_expires_at = NULL WHERE user_id = :id',
        [':id' => (int) $userId]
    );
}

/* --- modules -------------------------------------------------------------- */

/** Every module, alphabetically, with how many questions reference it. */
function allModulesWithCounts($pdo)
{
    $sql = 'SELECT m.module_id, m.module_name, COUNT(q.question_id) AS question_count
            FROM modules m
            LEFT JOIN questions q ON q.module_id = m.module_id
            GROUP BY m.module_id, m.module_name
            ORDER BY m.module_name';
    return query($pdo, $sql)->fetchAll();
}

/** Plain list for the question-form dropdown. */
function allModules($pdo)
{
    return query($pdo, 'SELECT module_id, module_name FROM modules ORDER BY module_name')->fetchAll();
}

function getModule($pdo, $id)
{
    return query(
        $pdo,
        'SELECT module_id, module_name FROM modules WHERE module_id = :id',
        [':id' => (int) $id]
    )->fetch();
}

function insertModule($pdo, $moduleName)
{
    query($pdo, 'INSERT INTO modules (module_name) VALUES (:module_name)', [':module_name' => $moduleName]);
    return (int) $pdo->lastInsertId();
}

function updateModule($pdo, $id, $moduleName)
{
    query(
        $pdo,
        'UPDATE modules SET module_name = :module_name WHERE module_id = :id',
        [':module_name' => $moduleName, ':id' => (int) $id]
    );
}

/** Throws PDOException (SQLSTATE 23000) if the module still has questions. */
function deleteModule($pdo, $id)
{
    query($pdo, 'DELETE FROM modules WHERE module_id = :id', [':id' => (int) $id]);
}

/* --- messages --------------------------------------------------------------- */

function allMessages($pdo)
{
    return query($pdo, 'SELECT * FROM messages ORDER BY created_at DESC, message_id DESC')->fetchAll();
}

function insertMessage($pdo, $name, $email, $subject, $body)
{
    query(
        $pdo,
        'INSERT INTO messages (name, email, subject, body) VALUES (:name, :email, :subject, :body)',
        [':name' => $name, ':email' => $email, ':subject' => $subject, ':body' => $body]
    );
    return (int) $pdo->lastInsertId();
}

function deleteMessage($pdo, $id)
{
    query($pdo, 'DELETE FROM messages WHERE message_id = :id', [':id' => (int) $id]);
}

/* --- admins ------------------------------------------------------------------- */

/** One admin row by username, or false if not found. Use with password_verify(). */
function findAdminByUsername($pdo, $username)
{
    return query(
        $pdo,
        'SELECT admin_id, username, password_hash FROM admins WHERE username = :username',
        [':username' => $username]
    )->fetch();
}

/* --- admin_activity_log --------------------------------------------------- */

function logAdminActivity($pdo, $adminId, $action, $entity, $targetId = null)
{
    query(
        $pdo,
        'INSERT INTO admin_activity_log (admin_id, action, target_entity, target_id)
         VALUES (:admin_id, :action, :entity, :target_id)',
        [
            ':admin_id'  => $adminId !== null ? (int) $adminId : null,
            ':action'    => $action,
            ':entity'    => $entity,
            ':target_id' => $targetId !== null ? (int) $targetId : null,
        ]
    );
}

/** Most recent activity entries, newest first, for the activity feed view. */
function allActivityLog($pdo, $limit = 50)
{
    $limit = (int) $limit;
    $sql = "SELECT l.log_id, l.action, l.target_entity, l.target_id, l.created_at,
                   a.username AS admin_username
            FROM admin_activity_log l
            LEFT JOIN admins a ON a.admin_id = l.admin_id
            ORDER BY l.created_at DESC, l.log_id DESC
            LIMIT $limit";
    return query($pdo, $sql)->fetchAll();
}
