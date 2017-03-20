<?php if (function_exists('posix_getpwuid')): ?>
    <?php
    $processUser = posix_getpwuid(posix_geteuid());
    $secure = true;
    ?>
    <h2>Security</h2>

    <?php if ($processUser['name'] == "root"): ?>
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Danger:</span>
            Webserver is running as root. This is a major security risk
        </div>
        <?php $secure = false; ?>
    <?php endif; ?>

    <?php if ($processUser['gecos'] == "root"): ?>
        <div class="alert alert-danger" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">Danger:</span>
            Webserver is user is part of superuser group. This is a major security risk
        </div>
        <?php $secure = false; ?>
    <?php endif; ?>

    <?php if ($secure): ?>
        <div class="alert alert-success" role="alert">
            <span class="glyphicon glyphicon glyphicon-ok" aria-hidden="true"></span>
            No security threats detected
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <span class="glyphicon glyphicon glyphicon-remove" aria-hidden="true"></span>
        Windows is not officialy supported
    </div>
<?php endif; ?>
