<?php
require_once "config.php";

// Searching all tables
function searchAll($pdo, $keyword) {
    $keyword = "%$keyword%";

    // Search users
    $stmt = $pdo->prepare("SELECT 'users' AS source, user_id, first_name, last_name
                           FROM users
                           WHERE first_name LIKE ? OR last_name LIKE ?");
    $stmt->execute([$keyword, $keyword]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search websites
    $stmt = $pdo->prepare("SELECT 'websites' AS source, site_id, site_name, url
                           FROM websites
                           WHERE site_name LIKE ? OR url LIKE ?");
    $stmt->execute([$keyword, $keyword]);
    $websites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Search account_credentials
    $stmt = $pdo->prepare("SELECT 'account_credentials' AS source, username,
                                  AES_DECRYPT(password, UNHEX(SHA2('my spectacular passphrase',256)), @init_vector) AS plain_password,
                                  email_address, comment
                           FROM account_credentials
                           WHERE username LIKE ? OR email_address LIKE ? OR comment LIKE ?");
    $stmt->execute([$keyword, $keyword, $keyword]);
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_merge($users, $websites, $accounts);
}

// Insert new entry
function insertEntry($pdo, $site_name, $url, $email, $username, $password, $comment) {
    $pdo->beginTransaction();
    try {
        // Insert site if not exists
        $stmt = $pdo->prepare("INSERT IGNORE INTO websites (site_name, url) VALUES (?, ?)");
        $stmt->execute([$site_name, $url]);

        // Get site_id
        $site_id = $pdo->lastInsertId();
        if ($site_id == 0) {
            $stmt = $pdo->prepare("SELECT site_id FROM websites WHERE url = ?");
            $stmt->execute([$url]);
            $site_id = $stmt->fetchColumn();
        }
        $user_id = 1;
        // Insert credentials
        $stmt = $pdo->prepare("INSERT INTO account_credentials
            (username, password, email_address, user_id, site_id, comment)
            VALUES (?, AES_ENCRYPT(?, UNHEX(SHA2('my spectacular passphrase',256)), @init_vector), ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $user_id, $site_id, $comment]);

        $pdo->commit();
        return "Entry inserted successfully.";
    } catch (Exception $e) {
        $pdo->rollBack();
        return "Insert failed: " . $e->getMessage();
    }
}

// Update entry
function updateEntry($pdo, $site_name, $new_url) {
    $stmt = $pdo->prepare("UPDATE websites SET url = ? WHERE site_name = ?");
    $stmt->execute([$new_url, $site_name]);
    return $stmt->rowCount() . " row(s) updated.";
}

// Delete entry
function deleteEntry($pdo, $username) {
    $stmt = $pdo->prepare("DELETE FROM account_credentials WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->rowCount() . " row(s) deleted.";
}
?>
