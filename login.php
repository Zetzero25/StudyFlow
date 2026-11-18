<?php
require_once 'db.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['fullname'] = $student['fullname'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 font-sans text-slate-100 flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border-2 border-indigo-500/40 p-8 rounded-3xl shadow-2xl">
        <h1 class="text-2xl font-black text-white text-center mb-6">Welcome Back</h1>
        <?php if(!empty($error)): ?>
            <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/40 text-rose-400 text-xs rounded-xl font-medium">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-indigo-300 mb-1 uppercase">Email Address</label>
                <input type="email" name="email" required placeholder="student@example.com" class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <div>
                <label class="block text-xs font-bold text-indigo-300 mb-1 uppercase">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-indigo-400">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-lg">Sign In</button>
        </form>
        <p class="text-xs text-slate-400 text-center mt-6">Don't have an account? <a href="register.php" class="text-indigo-400 font-bold hover:underline">Register here</a></p>
    </div>
</body>
</html>