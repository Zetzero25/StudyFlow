<?php
require_once 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$student_id = $_SESSION['student_id'];
$action = $_GET['action'] ?? '';

if ($action === 'get_tasks') {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE student_id = ? ORDER BY completed ASC, deadline ASC");
    $stmt->execute([$student_id]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit();
}

if ($action === 'add_task' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $time = trim($data['time'] ?? ''); // Properly captures the time string from the form
    $subject = trim($data['subject'] ?? '');
    $deadline = $data['deadline'] ?? '';
    $difficulty = $data['difficulty'] ?? 'Medium';
    $hours = intval($data['hours'] ?? 1);

    // Added 'time' into the SQL query columns and parameters
    $stmt = $pdo->prepare("INSERT INTO tasks (student_id, name, time, subject, deadline, difficulty, hours, completed) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
    
    if ($stmt->execute([$student_id, $name, $time, $subject, $deadline, $difficulty, $hours])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

if ($action === 'toggle_task' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT completed FROM tasks WHERE id = ? AND student_id = ?");
    $stmt->execute([$id, $student_id]);
    $task = $stmt->fetch();

    if ($task) {
        $newStatus = $task['completed'] ? 0 : 1;
        $update = $pdo->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND student_id = ?");
        $update->execute([$newStatus, $id, $student_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}

if ($action === 'delete_task' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = intval($data['id'] ?? 0);

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND student_id = ?");
    if ($stmt->execute([$id, $student_id])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}
?>