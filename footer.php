</main>
<nav class="tabbar" aria-label="Навігація">
    <a href="index.php"<?= $active === 'shipments' ? ' class="active" aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><path d="M3 7.5 12 3l9 4.5v9L12 21l-9-4.5z"/><path d="M3 7.5l9 4.5 9-4.5M12 12v9"/></svg>
        <span>Вантажі</span>
    </a>
    <a href="services.php"<?= $active === 'services' ? ' class="active" aria-current="page"' : '' ?>>
        <svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/></svg>
        <span>Сервіси</span>
    </a>
</nav>
<script src="assets/app.js"></script>
</body>
</html>
