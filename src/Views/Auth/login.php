<?php
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SOS-Nómina | Iniciar sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center
             bg-gradient-to-br from-slate-900 via-teal-950 to-teal-900">

    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl w-full max-w-md overflow-hidden
                border border-white/20">

        <!-- Cabecera -->
        <div class="bg-gradient-to-r from-teal-700 to-teal-600 px-8 py-7 text-center">
            <div class="w-14 h-14 bg-white/15 rounded-full flex items-center justify-center
                        mx-auto mb-3 text-white font-bold text-lg shadow-lg border border-white/25">
                SN
            </div>
            <h1 class="text-xl font-bold text-white tracking-wide">SOS-Nómina</h1>
            <p class="text-teal-200 text-sm mt-0.5 font-light">Sistema de Gestión de Nómina</p>
        </div>

        <div class="px-8 py-7">

            <?php if (!empty($errors)): ?>
                <div class="mb-4 p-3 bg-red-50 border border-red-100 text-red-600 rounded-xl text-sm">
                    <?php foreach ($errors as $e): ?>
                        <p>⚠ <?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="mb-4 p-3 bg-teal-50 border border-teal-100 text-teal-700 rounded-xl text-sm">
                    ✔ <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login">

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                        Correo electrónico
                    </label>
                    <input type="email" name="email" required autofocus
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-700
                               bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400
                               focus:border-teal-400 transition-all placeholder-slate-300"
                        placeholder="usuario@correo.com">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-widest mb-1.5">
                        Contraseña
                    </label>
                    <input type="password" name="password" required
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-700
                               bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-400
                               focus:border-teal-400 transition-all placeholder-slate-300"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-600 to-teal-500 hover:from-teal-700 hover:to-teal-600
                           text-white font-semibold py-2.5 rounded-xl transition-all duration-200 text-sm
                           shadow-md hover:shadow-lg active:scale-[0.99]">
                    Ingresar
                </button>

                <p class="text-center text-sm text-slate-400 mt-4">
                    <a href="/forgot-password" class="text-teal-500 hover:text-teal-700 hover:underline transition-colors">
                        ¿Olvidaste tu contraseña?
                    </a>
                </p>

            </form>
        </div>
    </div>

</body>
</html>
