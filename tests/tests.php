<?php

require_once __DIR__ . '/testframework.php';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$tests = new TestFramework();

// 1. DB connection
function testDbConnection() {
    global $config;
    $db = new Database($config["db"]["path"]);
    return assertExpression($db !== null);
}

// 2. Count
function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    return assertExpression($db->Count("page") >= 3);
}

// 3. Create
function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);

    $id = $db->Create("page", [
        "title" => "Test",
        "content" => "Test content"
    ]);

    return assertExpression($id > 0);
}

// 4. Read
function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);

    $data = $db->Read("page", 1);

    return assertExpression(isset($data['title']));
}

// 5. Update
function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);

    $id = $db->Create("page", [
        "title" => "Old",
        "content" => "Old content"
    ]);

    $db->Update("page", $id, [
        "title" => "New"
    ]);

    $data = $db->Read("page", $id);

    return assertExpression($data['title'] === "New");
}

// 6. Delete
function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);

    $id = $db->Create("page", [
        "title" => "To delete",
        "content" => "..."
    ]);

    $db->Delete("page", $id);

    $data = $db->Read("page", $id);

    return assertExpression($data === false);
}

// 7. Page render
function testPageRender() {
    $template = __DIR__ . '/../templates/index.tpl';

    file_put_contents($template, "<h1>{{title}}</h1>");

    $page = new Page($template);

    $result = $page->Render([
        "title" => "Hello"
    ]);

    return assertExpression(strpos($result, "Hello") !== false);
}


// добавление тестов
$tests->add('Database connection', 'testDbConnection');
$tests->add('Count', 'testDbCount');
$tests->add('Create', 'testDbCreate');
$tests->add('Read', 'testDbRead');
$tests->add('Update', 'testDbUpdate');
$tests->add('Delete', 'testDbDelete');
$tests->add('Page render', 'testPageRender');

// запуск
$tests->run();

echo $tests->getResult();