<?php
require_once 'db.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO students (fullname, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$fullname, $email, $hashed_password])) {
                $success = "Registration successful! You can now <a href='login.php' class='underline font-bold text-white'>Login here</a>.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 font-sans text-slate-100 flex items-center justify-center min-h-screen px-4">

    <!-- Card with a distinct glowing indigo border -->
    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border-2 border-indigo-500/40 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
        <!-- Glow effect decoration -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header -->
        <div class="text-center mb-8 relative z-10">
            <div class="inline-flex bg-indigo-600/30 border border-indigo-400/50 p-3.5 rounded-2xl text-indigo-300 text-2xl mb-3 shadow-inner">✨</div>
            <h1 class="text-2xl font-black tracking-tight text-white">Create Account</h1>
            <p class="text-xs text-slate-400 mt-1">Join StudyFlow and plan your academic goals</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/40 text-rose-400 text-xs rounded-2xl font-medium flex items-center space-x-2">
                <span>⚠️</span>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/40 text-emerald-400 text-xs rounded-2xl font-medium flex items-center space-x-2">
                <span>✅</span>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4 relative z-10">
            <div>
                <label class="block text-xs font-bold text-indigo-300 mb-1.5 uppercase tracking-wider">Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">👤</span>
                    <input type="text" name="fullname" required placeholder="e.g. Juan Dela Cruz" class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-950 border-2 border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/20 transition text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-indigo-300 mb-1.5 uppercase tracking-wider">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">📧</span>
                    <input type="email" name="email" required placeholder="student@example.com" class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-950 border-2 border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/20 transition text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-indigo-300 mb-1.5 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">🔒</span>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full pl-11 pr-4 py-3 rounded-2xl bg-slate-950 border-2 border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/20 transition text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 active:scale-[0.99] text-white font-bold py-3.5 rounded-2xl transition duration-200 shadow-lg shadow-indigo-600/40 text-sm mt-3 border border-indigo-400/30">
                Register Account ✨
            </button>
        </form>

        <div class="text-center mt-8 pt-6 border-t border-slate-800 relative z-10">
            <p class="text-xs text-slate-400">Already have an account? <a href="login.php" class="text-indigo-400 font-bold hover:underline">Log in here</a></p>
        </div>
    </div>

</body>
</html>