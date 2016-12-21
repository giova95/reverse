<!DOCTYPE html>

<html>

    <head>

        <link href="/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
        <link href="/css/styles.css" rel="stylesheet"/>
        <meta charset="UTF-8">
        
        <?php if (isset($title) && $title != ""): ?>
            <title><?= SITE_TITLE ?>: <?= htmlspecialchars($title) ?></title>
        <?php else: ?>
            <title><?= SITE_TITLE ?></title>
        <?php endif ?>

        <script src="js/jquery-2.1.4.min.js" defer></script>
        <script src="/js/bootstrap.min.js" defer></script>
        <script src="/js/scripts.js" defer></script>

    </head>

    <body>

        <div class="container">

            <div id="top">
                <!-- <h1 id="logo"><a href="/">Яeverse</a></h1> -->
                <nav class="navbar navbar-default">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a id="logo" class="navbar-brand" href="/"><span style="position: relative">Я R<span id="R">R</span></span>everse</a>
                        </div>
                        <div>
                            <ul class="nav navbar-nav navbar-right">
                                <?php if (isset($_SESSION["id"])): ?>
                                    <li><a href="/profile.php"><span class="glyphicon glyphicon-user"></span> <?= $username ?> <span class="badge">Level: <span id="level"><?= $level ?></span></span></a></li>
                                    <li><a href="/highscores.php"><span class="glyphicon glyphicon-signal"></span> Highscores</a></li>
                                    <li><a href="/settings.php"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
                                    <li><a href="/logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                                <?php else: ?>
                                    <li><a href="/register.php"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
                                    <li><a href="/login.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                    </div>
                </nav>
            </div>

            <div id="middle">
