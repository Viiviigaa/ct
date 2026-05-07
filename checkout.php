<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$offer_id     = $_POST['offer_id']     ?? '';
$passenger_id = $_POST['passenger_id'] ?? '';
$precio       = $_POST['precio']       ?? '';
$moneda       = $_POST['moneda']       ?? 'EUR';
$origen       = $_POST['origen']       ?? '';
$destino      = $_POST['destino']      ?? '';

// Si no viene offer_id volvemos al buscador
if (empty($offer_id)) {
    header('Location: buscador.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canary Travel - Reserva</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">
<div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-200 w-full max-w-2xl">

    <h2 class="text-3xl font-black text-slate-800 mb-2 uppercase italic text-center">Completa tu reserva</h2>
    <p class="text-center text-slate-400 text-sm mb-8">
        <?php echo htmlspecialchars($origen); ?> ✈ <?php echo htmlspecialchars($destino); ?> —
        <span class="text-blue-600 font-bold">
            <?php echo number_format((float)$precio, 2, ',', '.'); ?> <?php echo htmlspecialchars($moneda); ?>
        </span>
    </p>

    <form action="confirmar.php" method="POST" class="space-y-5">
        <!-- Campos ocultos que viajan a confirmar.php -->
        <input type="hidden" name="offer_id"     value="<?php echo htmlspecialchars($offer_id); ?>">
        <input type="hidden" name="passenger_id" value="<?php echo htmlspecialchars($passenger_id); ?>">
        <input type="hidden" name="precio"       value="<?php echo htmlspecialchars($precio); ?>">
        <input type="hidden" name="moneda"       value="<?php echo htmlspecialchars($moneda); ?>">
        <input type="hidden" name="origen"       value="<?php echo htmlspecialchars($origen); ?>">
        <input type="hidden" name="destino"      value="<?php echo htmlspecialchars($destino); ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Nombre</label>
                <input type="text" name="nombre" required placeholder="María"
                    class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Apellidos</label>
                <input type="text" name="apellidos" required placeholder="García López"
                    class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" required
                    class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase mb-2">Género</label>
                <select name="genero" class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
                    <option value="m">Hombre</option>
                    <option value="f">Mujer</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase mb-2">Email</label>
            <input type="email" name="email" required placeholder="maria@email.com"
                class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase mb-2">Teléfono (con prefijo)</label>
            <input type="tel" name="telefono" required placeholder="+34600000000"
                class="w-full border-2 border-slate-100 p-4 rounded-2xl bg-slate-50 font-bold focus:border-blue-500 outline-none">
        </div>

        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-blue-100 transition-all text-lg uppercase tracking-wider">
            Confirmar y Reservar →
        </button>
    </form>

    <a href="javascript:history.back()" class="flex items-center justify-center mt-5 text-slate-400 text-sm hover:text-blue-500">
        ← Volver a los resultados
    </a>
</div>
</body>
</html>