<?php
declare(strict_types=1);

require __DIR__ . '/db.php';

$errors = [];
$carriers = TrackingService::carriers();
$id = (int) ($_GET['id'] ?? post('id'));

$stmt = db()->prepare('SELECT * FROM shipments WHERE id = ?');
$stmt->execute([$id]);
$shipment = $stmt->fetch();
if (!$shipment) {
    flash('error', 'Відправлення не знайдено.');
    redirect('index.php');
}

if (is_post()) {
    $action = post('action');

    if ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM shipments WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Відправлення видалено.');
        redirect('index.php');
    }

    if ($action === 'refresh') {
        try {
            $result = (new TrackingService())->track($shipment['carrier'], $shipment['tracking_number']);
            $stmt = db()->prepare('UPDATE shipments SET status = ?, events = ?::jsonb, checked_at = NOW() WHERE id = ?');
            $stmt->execute([$result->status, json_encode($result->events, JSON_UNESCAPED_UNICODE), $id]);
            flash('success', 'Статус оновлено: ' . STATUSES[$result->status] . '.');
        } catch (TrackingException $ex) {
            flash('error', $ex->getMessage());
        }
        redirect('shipment.php?id=' . $id);
    }

    if ($action === 'update') {
        $recipient = post('recipient');
        $description = post('description');
        if ($recipient === '') {
            $errors[] = 'Вкажіть одержувача.';
        } elseif (mb_strlen($recipient) > 120) {
            $errors[] = 'Назва одержувача не може перевищувати 120 символів.';
        }
        if (mb_strlen($description) > 200) {
            $errors[] = 'Опис не може перевищувати 200 символів.';
        }
        if (!$errors) {
            $stmt = db()->prepare('UPDATE shipments SET recipient = ?, description = ? WHERE id = ?');
            $stmt->execute([$recipient, $description, $id]);
            flash('success', 'Дані збережено.');
            redirect('shipment.php?id=' . $id);
        }
        $shipment['recipient'] = $recipient;
        $shipment['description'] = $description;
    }
}

$events = array_reverse(json_decode($shipment['events'], true) ?: []);

$title = $shipment['tracking_number'];
$active = 'shipments';
$back = ['index.php', 'Вантажі'];
require __DIR__ . '/header.php';
?>
<ul class="group">
    <li class="row static"><span class="row-title">Статус</span><?= status_pill($shipment['status']) ?></li>
    <li class="row static"><span class="row-title">Перевізник</span><span class="value"><?= e($carriers[$shipment['carrier']] ?? $shipment['carrier']) ?></span></li>
    <li class="row static"><span class="row-title">Оновлено</span><span class="value"><?= e(format_time($shipment['checked_at'])) ?></span></li>
</ul>

<form method="post" action="shipment.php">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="action" value="refresh">
    <button type="submit" class="btn">Оновити статус</button>
</form>

<h2 class="section">Історія</h2>
<?php if (!$events): ?>
    <p class="hint">Історія з'явиться після першого оновлення статусу.</p>
<?php else: ?>
    <ol class="timeline">
        <?php foreach ($events as $i => $event): ?>
            <li<?= $i === 0 ? ' class="latest"' : '' ?>>
                <span class="t-text"><?= e($event['text']) ?></span>
                <span class="t-meta"><?= e($event['place']) ?>, <?= e(date('d.m.Y H:i', strtotime($event['time']))) ?></span>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

<h2 class="section">Дані відправлення</h2>
<form method="post" action="shipment.php" class="form" novalidate>
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="action" value="update">
    <div class="group">
        <label class="field">
            <span>Одержувач</span>
            <input type="text" name="recipient" value="<?= e($shipment['recipient']) ?>" maxlength="120" required>
        </label>
        <label class="field">
            <span>Опис</span>
            <input type="text" name="description" value="<?= e($shipment['description']) ?>" maxlength="200">
        </label>
    </div>
    <button type="submit" class="btn secondary">Зберегти</button>
</form>

<form method="post" action="shipment.php" data-confirm="Видалити відправлення?">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="action" value="delete">
    <div class="group">
        <button type="submit" class="row-button danger">Видалити відправлення</button>
    </div>
</form>
<?php require __DIR__ . '/footer.php'; ?>
