<?php
declare(strict_types=1);

require __DIR__ . '/db.php';

$errors = [];
$service = new TrackingService();

if (is_post()) {
    $carrier = post('carrier');
    try {
        $service->track($carrier, 'CHECK000001');
        flash('success', 'Сервіс ' . TrackingService::carriers()[$carrier] . ' відповідає.');
    } catch (TrackingException $ex) {
        flash('error', $ex->getMessage());
    }
    redirect('services.php');
}

$counts = db()->query('SELECT carrier, COUNT(*) AS total FROM shipments GROUP BY carrier')->fetchAll(PDO::FETCH_KEY_PAIR);

$title = 'Сервіси';
$active = 'services';
require __DIR__ . '/header.php';
?>
<p class="hint">Кожен сервіс віддає дані у власному форматі. Адаптери приводять їх до єдиного вигляду.</p>
<?php foreach (TrackingService::describe() as $key => $info): ?>
    <form method="post" action="services.php">
        <input type="hidden" name="carrier" value="<?= e($key) ?>">
        <ul class="group">
            <li class="row static">
                <span class="row-main">
                    <span class="row-title"><?= e($info['name']) ?></span>
                    <span class="row-sub">Формат: <?= e($info['format']) ?>, адаптер <?= e($info['class']) ?></span>
                </span>
                <span class="value"><?= (int) ($counts[$key] ?? 0) ?></span>
            </li>
            <li><button type="submit" class="row-button">Перевірити з'єднання</button></li>
        </ul>
    </form>
<?php endforeach; ?>
<?php require __DIR__ . '/footer.php'; ?>
