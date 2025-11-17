<?php
require_once "helpers.php";
$result = "";
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["search"])) {
        $keyword = $_POST["search_keyword"];
        $data = searchAll($pdo, $keyword);
        if (empty($data)) {
            $result = "No results found for '$keyword'.";
        }
    }
    if (isset($_POST["insert"])) {
        $result = insertEntry($pdo, $_POST["site_name"], $_POST["url"], $_POST["email"], $_POST["username"], $_POST["password"], $_POST["comment"]);
    }
    if (isset($_POST["update"])) {
        $result = updateEntry($pdo, $_POST["site_name"], $_POST["new_url"]);
    }
    if (isset($_POST["delete"])) {
        $result = deleteEntry($pdo, $_POST["username"]);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Passwords Database</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Password Interface</h1>

<!-- Search -->
<form method="post">
    <fieldset>
        <legend>Search</legend>
        <p><input type="text" name="search_keyword" placeholder="Enter keyword"></p>
        <p><input type="submit" name="search" value="Search"></p>
    </fieldset>
</form>

<!-- Insert -->
<form method="post">
    <fieldset>
        <legend>Insert</legend>
        <p>Site/App Name: <input type="text" name="site_name"></p>
        <p>URL: <input type="text" name="url"></p>
        <p>Email: <input type="email" name="email"></p>
        <p>Username: <input type="text" name="username"></p>
        <p>Password: <input type="text" name="password"></p>
        <p>Comment: <textarea name="comment"></textarea></p>
        <p><input type="submit" name="insert" value="Insert"></p>
    </fieldset>
</form>

<!-- Update -->
<form method="post">
    <fieldset>
        <legend>Update</legend>
        <p>Site/App Name: <input type="text" name="site_name"></p>
        <p>New URL: <input type="text" name="new_url"></p>
        <p><input type="submit" name="update" value="Update"></p>
    </fieldset>
</form>

<!-- Delete -->
<form method="post">
    <fieldset>
        <legend>Delete</legend>
        <p>Username: <input type="text" name="username"></p>
        <p><input type="submit" name="delete" value="Delete"></p>
    </fieldset>
</form>

<!-- Results -->
<h2>Results</h2>
<p><?php echo $result; ?></p>
<?php if (!empty($data)): ?>
    <table>
        <thead>
            <tr>
                <?php foreach (array_keys($data[0]) as $col): ?>
                    <th><?php echo htmlspecialchars($col); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $val): ?>
                        <td><?php echo htmlspecialchars($val); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>
