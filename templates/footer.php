<?php
/**
 * FabX ERP - Footer
 */
?>

<footer class="main-footer">
    <div class="footer-left">
        <span>&copy; <?= date('Y') ?> <?= COMPANY_NAME ?>. All rights reserved.</span>
        <span class="d-none d-md-inline"> | </span>
        <span class="d-none d-md-inline">ISO 9001:2015 Certified</span>
    </div>
    <div class="footer-right">
        <span><?= APP_NAME ?> v<?= APP_VERSION ?></span>
        <span class="session-timer" id="sessionTimer" title="Session expires in">
            <i class="bi bi-clock"></i> <span id="timerDisplay">--:--</span>
        </span>
    </div>
</footer>
