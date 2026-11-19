<?php
declare(strict_types=1);

require __DIR__ . '/db.php';

$errors = [];
$form = ['tracking_number' => '', 'carrier' => '', 'recipient' => '', 'description' => ''];
$carriers = TrackingService::carriers();

if (is_post()) {
    $form = [
        'tracking_number' => strtoupper(post('tracking_number')),
        'carrier' => post('carrier'),
        'recipient' => post('recipient'),
        'description' => post('description'),
    ];

    if ($form['tracking_number'] === '') {
        $errors[] = 'Вкажіть номер відправлення.';
    } elseif (!preg_match('/^[A-Z0-9]{8,20}$/', $form['tracking_number'])) {
        $errors[] = 'Номер має містити від 8 до 20 латинських літер або цифр.';
    }
    if (!isset($carriers[$form['carrier']])) {
        $errors[] = 'Оберіть перевізника.';
    }
    if ($form['recipient'] === '') {
        $errors[] = 'Вкажіть одержувача.';
    } elseif (mb_strlen($form['recipient']) > 120) {
        $errors[] = 'Назва одержувача не може перевищувати 120 символів.';
    }
    if (mb_strlen($form['description']) > 200) {
        $errors[] = 'Опис не може перевищувати 200 символів.';
    }

    if (!$errors) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO shipments (tracking_number, carrier, recipient, description) VALUES (?, ?, ?, ?) RETURNING id'
            );
            $stmt->execute([$form['tracking_number'], $form['carrier'], $form['recipient'], $form['description']]);
            flash('success', 'Відправлення додано.');
            redirect('shipment.php?id=' . (int) $stmt->fetchColumn());
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23505') {
                $errors[] = 'Відправлення з таким номером уже існує.';
            } else {
                throw $ex;
            }
        }
    }
}

$status = (string) ($_GET['status'] ?? '');
if (!isset(STATUSES[$status])) {
    $status = '';
}
$q = trim((string) ($_GET['q'] ?? ''));

$sql = 'SELECT id, tracking_number, carrier, recipient, status FROM shipments WHERE 1 = 1';
$params = [];
if ($status !== '') {
    $sql .= ' AND status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $sql .= ' AND (tracking_number ILIKE ? OR recipient ILIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
$sql .= ' ORDER BY id DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

$filters = ['' => 'Усі'] + STATUSES;
$title = 'Вантажі';
$active = 'shipments';
require __DIR__ . '/header.php';
?>
<form method="get" action="index.php" class="search">
    <input type="hidden" name="status" value="<?= e($status) ?>">
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/></svg>
    <label class="sr" for="q">Пошук</label>
    <input id="q" type="search" name="q" value="<?= e($q) ?>" placeholder="Номер або одержувач">
</form>

<nav class="segmented" aria-label="Фільтр за статусом">
    <?php foreach ($filters as $key => $label): ?>
        <a href="index.php?<?= e(http_build_query(array_filter(['status' => $key, 'q' => $q]))) ?>"<?= $status === $key ? ' class="on" aria-current="true"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
</nav>

<?php if (!$items): ?>
    <p class="hint center">Відправлень не знайдено.</p>
<?php else: ?>
    <ul class="group">
        <?php foreach ($items as $row): ?>
            <li>
                <a class="row" href="shipment.php?id=<?= (int) $row['id'] ?>">
                    <span class="row-main">
                        <span class="row-title"><?= e($row['tracking_number']) ?></span>
                        <span class="row-sub"><?= e($row['recipient']) ?>, <?= e($carriers[$row['carrier']] ?? $row['carrier']) ?></span>
                    </span>
                    <?= status_pill($row['status']) ?>
                    <svg class="chev" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<h2 class="section">Нове відправлення</h2>
<form method="post" action="index.php" class="form" novalidate>
    <div class="group">
        <label class="field">
            <span>Номер</span>
            <input type="text" name="tracking_number" value="<?= e($form['tracking_number']) ?>" maxlength="20" placeholder="FP20260010" autocapitalize="characters" required>
        </label>
        <label class="field">
            <span>Перевізник</span>
            <select name="carrier" required>
                <option value="">Оберіть</option>
                <?php foreach ($carriers as $key => $name): ?>
                    <option value="<?= e($key) ?>"<?= $form['carrier'] === $key ? ' selected' : '' ?>><?= e($name) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field">
            <span>Одержувач</span>
            <input type="text" name="recipient" value="<?= e($form['recipient']) ?>" maxlength="120" required>
        </label>
        <label class="field">
            <span>Опис</span>
            <input type="text" name="description" value="<?= e($form['description']) ?>" maxlength="200">
        </label>
    </div>
    <p class="hint">Статус підтягується із сервісу перевізника на сторінці відправлення.</p>
    <button type="submit" class="btn">Додати відправлення</button>
</form>
<?php require __DIR__ . '/footer.php'; ?>
