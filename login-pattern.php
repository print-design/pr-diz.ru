<?php
include 'include/topscripts.php';

// Если залогинен, перенаправляем на основную страницу
if(LoggedIn()) {
    header('Location: '.APPLICATION.'/');
}

// Карщика и ревизора перенаправляем в раздел car
if(IsInRole(array(ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/car/');
}

// Резчика по раскрою перенаправляем в раздел cut
if(IsInRole(ROLE_NAMES[ROLE_CUTTER])) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Маркиратора перенаправляем в раздел marker
if(IsInRole(ROLE_NAMES[ROLE_MARKER])) {
    header('Location: '.APPLICATION.'/marker/');
}

$user = filter_input(INPUT_GET, 'user');

if(empty($user)) {
    header('Location: login-user.php');
}

const LOGIN_USER_COLORS = array("av-pink", "av-blue", "av-purple", "av-violet", "av-orange", "av-yellow", "av-shrek", "av-green", "av-terracot", "av-brick");
$login_user_colors_count = count(LOGIN_USER_COLORS);
$login_user_colors_index = 0;
$sql = "select id, first_name, last_name, role_id from user where graph_key <> '' order by first_name, last_name";
$grabber = new Grabber($sql);
$error_message = $grabber->error;
$users = $grabber->result;

$users_ext = array();

foreach($users as $item) {
    if($login_user_colors_index >= $login_user_colors_count) {
        $login_user_colors_index = 0;
    }
    $item['login_user_color'] = $login_user_colors_index++;
    $users_ext[$item['id']] = $item;
}
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php
        $title_tail = "Вход по графическому ключу";
        include 'include/head.php';
        ?>
        <style>
            /* ─────────────────────────────────────────────────────────────────
            PAGE-LEVEL КАРКАС.
            Дублируем .auth-shell / .auth-hero / .auth-form из 03-login.html
            — это уникальный каркас экрана входа (RULES → «единственное
            допустимое page-level CSS — структурный каркас»). Когда экранов
            логина станет несколько, вынести каркас в overrides; пока — здесь.
            ───────────────────────────────────────────────────────────────── */
            html, body { height: 100%; }
            
            .auth-shell {
                display: flex;
                min-height: 100vh;
                align-items: stretch;
            }
            
            /* Hero фиксируем sticky-top на 100vh, чтобы картинка не растягивалась
            по высоте формы (см. одноимённый блок в 03-login.html). */
            .auth-hero {
                position: sticky;
                top: 0;
                flex: 2 1 0;
                align-self: flex-start;
                height: 100vh;
                min-height: 320px;
                background: var(--primary-dark);
                overflow: hidden;
            }
            .auth-hero__image {
                position: absolute; inset: 0;
                width: 100%; height: 100%;
                object-fit: cover;
                display: block;
            }
            .auth-hero__brand {
                position: absolute;
                left: calc(var(--size-xxxl) * 2);
                bottom: calc(var(--size-xxxl) * 2);
                display: flex;
                align-items: center;
                gap: var(--size-xxl);
                z-index: 1;
            }
            .auth-hero .flexim-logo__mark { width: 60px; height: 60px; }
            .auth-hero__brand-name {
                margin: 0;
                color: var(--text-contrast);
                white-space: nowrap;
            }
            
            /* Прижимаем карточку к верху + одинаковый top-отступ на всех экранах
            входа (03/04-users/04-pattern), чтобы карточки разной высоты не
            уезжали по вертикали. См. одноимённый блок в 03-login.html. */
            .auth-form {
                flex: 1 1 0;
                display: flex;
                align-items: flex-start;
                justify-content: center;
                padding: calc(var(--size-xxxl) * 4) calc(var(--size-xxxl) + var(--size-l));
                background: var(--background-bg);
            }
            .auth-form__card {
                width: 100%;
                max-width: 360px;
                display: flex;
                flex-direction: column;
                gap: var(--size-xxl);
            }
            
            /* Кнопка «← К списку» — вверху карточки, link-компонент
            (flexim-link-button--m). Прижата влево. */
            .auth-back {
                align-self: flex-start;
                margin: 0;
            }
            
            /* Шапка карточки: приветствие именованного пользователя.
            Имя/инициалы/роль — из выбранной карточки на 04-login-users.html. */
            .auth-greet {
                display: flex;
                align-items: center;
                gap: var(--size-m);
            }
            .auth-greet__avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--infographic-pink-20);
                color: var(--infographic-pink);
                font: 700 14px/16px var(--font-family);
                flex-shrink: 0;
            }
            .auth-greet__name {
                margin: 0;
                color: var(--text-primary);
            }
            .auth-greet__role {
                margin: 0;
                color: var(--text-secondary);
            }
            
            .auth-form__title { margin: 0; }
            .auth-form__subtitle {
                margin: var(--size-xs) 0 0;
                color: var(--text-secondary);
            }
            
            /* Палитра аватарок — берём из infographic, чтобы не вводить новых цветов. */
            .av-pink     { background: var(--infographic-pink-20);     color: var(--infographic-pink); }
            .av-blue     { background: var(--infographic-blue-20);     color: var(--infographic-blue); }
            .av-purple   { background: var(--infographic-purple-20);   color: var(--infographic-purple); }
            .av-violet   { background: var(--infographic-violet-20);   color: var(--infographic-violet); }
            .av-orange   { background: var(--infographic-orange-20);   color: var(--infographic-orange); }
            .av-yellow   { background: var(--infographic-yellow-20);   color: var(--infographic-yellow); }
            .av-shrek    { background: var(--infographic-shrek-20);    color: var(--infographic-shrek); }
            .av-green    { background: var(--infographic-green-20);    color: var(--infographic-green); }
            .av-terracot { background: var(--infographic-terracot-20); color: var(--infographic-terracot); }
            .av-brick    { background: var(--infographic-brick-20);    color: var(--infographic-brick); }
            
            /* ──────────────────────────────────────────────────────────────────
            ГРАФИЧЕСКИЙ КЛЮЧ — 3×3 сетка точек.
            Сам компонент пока живёт здесь page-level, потому что больше нигде
            не используется. Если он попадёт ещё в один экран — перенести в
            _bootstrap-flexim-overrides.css как .flexim-pattern-lock.
            ────────────────────────────────────────────────────────────────── */
            .pattern-lock {
                position: relative;
                width: 240px;
                height: 240px;
                align-self: flex-start;
                touch-action: none;
                user-select: none;
                -webkit-user-select: none;
            }
            .pattern-lock__svg {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
            }
            .pattern-lock__grid {
                position: absolute;
                inset: 0;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, 1fr);
            }
            .pattern-lock__cell {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .pattern-lock__dot {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: transparent;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 80ms ease;
            }
            .pattern-lock__dot::after {
                content: "";
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: var(--surface-controls);
                transition: background 80ms ease, width 80ms ease, height 80ms ease;
            }
            .pattern-lock__dot.is-active {
                background: var(--primary-50);
            }
            .pattern-lock__dot.is-active::after {
                width: 14px;
                height: 14px;
                background: var(--primary-main);
            }
            /* Состояния всей сетки */
            .pattern-lock.is-error .pattern-lock__dot.is-active { background: var(--error-50); }
            .pattern-lock.is-error .pattern-lock__dot.is-active::after { background: var(--error-main); }
            .pattern-lock.is-success .pattern-lock__dot.is-active { background: var(--success-20); }
            .pattern-lock.is-success .pattern-lock__dot.is-active::after { background: var(--success-main); }
            .pattern-lock.is-disabled .pattern-lock__dot::after { background: var(--text-disabled); }
            .pattern-lock.is-disabled { opacity: 0.6; cursor: not-allowed; }
            
            .pattern-lock__line {
                stroke: var(--primary-main);
                stroke-width: 4;
                stroke-linecap: round;
                stroke-linejoin: round;
                fill: none;
            }
            .pattern-lock.is-error .pattern-lock__line { stroke: var(--error-main); }
            .pattern-lock.is-success .pattern-lock__line { stroke: var(--success-main); }
            
            /* Подпись-хинт под сеткой — используем тот же стиль, что у Input hint.
            В idle пустая, появляется при ошибке/блокировке/успехе. */
            .pattern-hint {
                min-height: 16px;
                text-align: left;
                color: var(--text-secondary);
                font: 400 12px/16px var(--font-family);
                margin: 0;
            }
            .pattern-hint.is-error { color: var(--error-main); }
            .pattern-hint.is-success { color: var(--success-main); }
            
            /* Ряд вспомогательных ссылок снизу карточки. Прижат влево. */
            .auth-alt {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: var(--size-xs);
            }
            
            /* ──────────────────────────────────────────────────────────────────
            ДЕМО-ПАНЕЛЬ СОСТОЯНИЙ (для дизайн-сверки).
            В прод НЕ переносить — это инструмент для просмотра состояний без
            реального ввода ключа. Программист её удалит при интеграции.
            ────────────────────────────────────────────────────────────────── */
            .demo-states {
                position: fixed;
                left: 50%;
                bottom: var(--size-l);
                transform: translateX(-50%);
                z-index: 50;
                display: flex;
                align-items: center;
                gap: var(--size-xs);
                padding: var(--size-xs);
                background: var(--background-paper);
                border: 1px solid var(--other-lines);
                border-radius: var(--size-s);
                box-shadow: var(--shadow-m);
            }
            .demo-states__label {
                font: 700 12px/14px var(--font-family);
                letter-spacing: 0.04em;
                text-transform: uppercase;
                color: var(--text-tertiary);
                padding: 0 var(--size-xs);
            }
            
            /* Мобильный header (лого + бренд-имя) — на десктопе скрыт. */
            .auth-mobile-header { display: none; }
            
            /* Мобильный вид (Figma 6750:81405): без hero, лого+бренд сверху формы. */
            @media (max-width: 900px) {
                .auth-shell { flex-direction: column; }
                .auth-hero { display: none; }
                
                .auth-form {
                    flex: 1 1 auto;
                    padding: var(--size-xxl);   /* 32px по Figma */
                    align-items: stretch;
                }
                .auth-form__card {
                    max-width: 100%;
                    gap: var(--size-xxxl);      /* 40px между блоками card */
                }
                
                .auth-mobile-header {
                    display: flex;
                    align-items: center;
                    gap: var(--size-xl);        /* 24px между лого и текстом */
                    margin-bottom: var(--size-l); /* 20px + gap card 40 = 60 до title */
                }
                .auth-mobile-header__logo {
                    width: 40px;
                    height: 40px;
                    flex-shrink: 0;
                }
                .auth-mobile-header__brand-name {
                    margin: 0;
                    color: var(--text-primary);
                    white-space: nowrap;
                }
                
                /* Pattern grid центрируем на ширине экрана. */
                .pattern-lock { align-self: center; }
                
                .demo-states {
                    bottom: var(--size-xs);
                    flex-wrap: wrap;
                    justify-content: center;
                    max-width: calc(100% - var(--size-xl));
                }
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <!-- HERO: тот же ассет, что у 03-login.html (нода Figma 6750:79644). -->
            <section class="auth-hero" aria-hidden="true">
                <img class="auth-hero__image"
                     src="./assets/login-hero.png"
                     alt="">
                <div class="auth-hero__brand">
                    <span class="flexim-logo__mark" aria-hidden="true"></span>
                    <p class="t-h1 auth-hero__brand-name">Принт-Дизайн</p>
                </div>
            </section>
            
            <!-- ФОРМА: графический ключ -->
            <main class="auth-form">
                <!-- < ? php // POST /login_pattern.php — сравнение sha256(pattern) на бэке ? > -->
                <form class="auth-form__card" id="pattern-form" method="post" action="#" novalidate>
                    
                    <!-- Мобильный header (Figma 6750:81405) — на десктопе скрыт через CSS. -->
                    <header class="auth-mobile-header" aria-hidden="false">
                        <span class="flexim-logo__mark auth-mobile-header__logo" aria-hidden="true"></span>
                        <p class="t-h2 auth-mobile-header__brand-name">Принт-Дизайн</p>
                    </header>
                    
                    <!-- Кнопка возврата к списку юзеров. На общем устройстве это основной
                        путь «выбрал не того» — возвращает на 04-login-users.html. -->
                    <a href="login-users.php" class="btn btn-link flexim-link-button--m auth-back">
                        <span data-flexim-icon="arrow-left" data-size="24" aria-hidden="true"></span>
                        К списку пользователей
                    </a>
                    
                    <!-- Приветствие выбранного пользователя.
                        Имя/инициалы/роль — приходят query-параметром user_id с 04-login-users.html.
                        PHP: < ? = $user->name ? >, < ? = $user->initials() ? >, < ? = $user->role_label ? > -->
                    <?php if(key_exists($user, $users_ext)): ?>
                    <header class="auth-greet">
                        <span class="auth-greet__avatar <?= LOGIN_USER_COLORS[$users_ext[$user]['login_user_color']] ?>" aria-hidden="true"><?= (count_chars($users_ext[$user]['first_name']) == 0 ? '' : mb_substr($users_ext[$user]['first_name'], 0, 1)).(count_chars($users_ext[$user]['last_name']) == 0 ? '' : mb_substr($users_ext[$user]['last_name'], 0, 1)) ?></span>
                        <div>
                            <p class="t-h4 auth-greet__name"><?=$users_ext[$user]['first_name'] ?> <?=$users_ext[$user]['last_name'] ?></p>
                            <p class="t-label auth-greet__role"><?= ROLE_LOCAL_NAMES[$users_ext[$user]['role_id']] ?></p>
                        </div>
                    </header>
                    <?php endif; ?>
                    
                    <div>
                        <h1 class="t-h2-r auth-form__title">Введите ключ</h1>
                        <p class="t-body auth-form__subtitle">Соедините точки в правильном порядке.</p>
                    </div>
                    
                    <!-- Графический ключ: 3×3 сетка + SVG-линия.
                        data-pattern — эталонный ключ (для прод-интеграции хеш сравнивать на бэке,
                        тут — демо-проверка на клиенте). Формат: индексы 1..9 в порядке соединения. -->
                    <div class="pattern-lock"
                         id="pattern-lock"
                         data-pattern="1,5,9,6,3"
                         data-min-length="4"
                         data-max-attempts="3"
                         role="application"
                         aria-label="Графический ключ: соедините точки">
                        <!-- SVG-слой для линии-соединения -->
                        <svg class="pattern-lock__svg" viewBox="0 0 240 240" preserveAspectRatio="none">
                            <polyline class="pattern-lock__line" points=""></polyline>
                        </svg>
                        <!-- Сетка точек 3×3 (9 точек, индекс data-i = 1..9 слева-направо, сверху-вниз) -->
                        <div class="pattern-lock__grid">
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="1"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="2"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="3"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="4"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="5"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="6"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="7"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="8"></span></div>
                            <div class="pattern-lock__cell"><span class="pattern-lock__dot" data-i="9"></span></div>
                        </div>
                    </div>
                    
                    <!-- Подпись под ключом: пустая в idle, появляется при ошибке/блокировке/успехе -->
                    <p class="pattern-hint" id="pattern-hint"></p>
                    
                    <!-- Альтернатива ключу — вход по логину и паролю (для админа, нового
                        сотрудника или того, у кого ключ заблокирован). Видна во всех
                        состояниях, ведёт на 03-login.html. -->
                    <div class="auth-alt">
                        <a href="login.php" class="btn btn-link flexim-link-button--m">
                            Войти по логину и паролю
                        </a>
                    </div>
                    
                    <!-- Скрытое поле с собранным ключом — улетит в POST вместе с CSRF-токеном.
                        PHP должен сравнивать sha256($_POST['pattern']) с хешем в БД. -->
                    <input type="hidden" name="pattern" id="pattern-value" value="">
                </form>
            </main>
        </div>
        
        <!-- ДЕМО-ПАНЕЛЬ СОСТОЯНИЙ. Только для дизайн-сверки, в прод не переносится. -->
        <!--aside class="demo-states" aria-label="Демо-состояния экрана">
        <span class="demo-states__label">Демо</span>
        <button type="button" class="btn btn-outline-primary flexim-btn-s" data-demo="idle">Idle</button>
        <button type="button" class="btn btn-outline-primary flexim-btn-s" data-demo="error">Ошибка</button>
        <button type="button" class="btn btn-outline-primary flexim-btn-s" data-demo="last">Последняя попытка</button>
        <button type="button" class="btn btn-outline-primary flexim-btn-s" data-demo="locked">Заблокирован</button>
        <button type="button" class="btn btn-outline-primary flexim-btn-s" data-demo="success">Успех</button>
        </aside-->
    
        <!-- Скрипты — теми же версиями, что в проде -->
        <?php
        include 'include/footer.php';
        include 'include/footer_cut.php';
        ?>
        <script>
            if (window.fleximIcons) window.fleximIcons.renderAll();
        
            /* ──────────────────────────────────────────────────────────────────
                Графический ключ: рисование 3×3.

                Поток событий:
                pointerdown на любой точке → начинаем сбор;
                pointermove над контейнером → если попали в точку и её ещё нет в
                    последовательности — добавляем, перерисовываем линию;
                pointerup в любом месте → проверка ключа.

                Линия рисуется в SVG-слое поверх сетки координатами в системе viewBox
                0..240. Центр каждой точки = (col*80+40, row*80+40), индекс 1..9
                слева-направо, сверху-вниз.

                Минимальная длина — data-min-length (по умолчанию 4 как в Android).
                Эталон — data-pattern (CSV индексов). В проде заменить на сравнение
                хеша на сервере (поле #pattern-value уходит в POST).
                ────────────────────────────────────────────────────────────────── */
            (function () {
                var lock = document.getElementById('pattern-lock');
                if (!lock) return;
        
                var svg = lock.querySelector('.pattern-lock__svg');
                var line = lock.querySelector('.pattern-lock__line');
                var hint = document.getElementById('pattern-hint');
                var hiddenInput = document.getElementById('pattern-value');
                
                var EXPECTED = (lock.getAttribute('data-pattern') || '').split(',').map(Number);
                var MIN_LEN = parseInt(lock.getAttribute('data-min-length'), 10) || 4;
                var MAX_ATTEMPTS = parseInt(lock.getAttribute('data-max-attempts'), 10) || 3;
        
                var sequence = [];           // массив индексов 1..9
                var dragging = false;
                var attemptsLeft = MAX_ATTEMPTS;
                var resetTimer = null;
        
                // Координаты центров точек (в системе viewBox 240×240).
                function centerOf(i) {
                    var idx = i - 1;
                    var col = idx % 3;
                    var row = Math.floor(idx / 3);
                    return { x: col * 80 + 40, y: row * 80 + 40 };
                }
        
                // Перерисовать линию по текущей последовательности.
                function renderLine(extraPoint) {
                    if (!sequence.length) {
                        line.setAttribute('points', '');
                        return;
                    }
                    var pts = sequence.map(function (i) {
                        var c = centerOf(i);
                        return c.x + ',' + c.y;
                    });
                    if (extraPoint) pts.push(extraPoint.x + ',' + extraPoint.y);
                    line.setAttribute('points', pts.join(' '));
                }
        
                // Подсветить точку как активную / снять подсветку со всех.
                function activate(i) {
                    lock.querySelector('.pattern-lock__dot[data-i="' + i + '"]').classList.add('is-active');
                }
                function deactivateAll() {
                    lock.querySelectorAll('.pattern-lock__dot.is-active').forEach(function (d) {
                        d.classList.remove('is-active');
                    });
                }
        
                // Получить координаты события в системе viewBox 240×240.
                function eventToViewBox(e) {
                    var rect = lock.getBoundingClientRect();
                    var x = (e.clientX - rect.left) * (240 / rect.width);
                    var y = (e.clientY - rect.top) * (240 / rect.height);
                    return { x: x, y: y };
                }
        
                // Какая точка попадает под координату (или null).
                function hitDot(p) {
                    for (var i = 1; i <= 9; i++) {
                        var c = centerOf(i);
                        var dx = p.x - c.x;
                        var dy = p.y - c.y;
                        // Хит-радиус = 32 (чуть больше визуального dot 22 + ring 44/2).
                        if (dx * dx + dy * dy <= 32 * 32) return i;
                    }
                    return null;
                }
        
                function startDrag(e) {
                    if (lock.classList.contains('is-disabled')) return;
                    clearTimeout(resetTimer);
                    resetState();
                    dragging = true;
                    lock.setPointerCapture && lock.setPointerCapture(e.pointerId);
                    moveDrag(e);
                }
        
                function moveDrag(e) {
                    if (!dragging) return;
                    var p = eventToViewBox(e);
                    var i = hitDot(p);
                    if (i && sequence.indexOf(i) === -1) {
                        sequence.push(i);
                        activate(i);
                    }
                    renderLine(p);
                }
        
                function endDrag() {
                    if (!dragging) return;
                    dragging = false;
                    renderLine(); // финальная без «хвоста» к курсору
                    checkSequence();
                }
        
                function checkSequence() {
                    hiddenInput.value = sequence.join(',');
            
                    if (!sequence.length) {
                        setHint('');
                        return;
                    }
                    if (sequence.length < MIN_LEN) {
                        setError('Слишком короткий ключ — нужно минимум ' + MIN_LEN + ' точки.');
                        scheduleReset();
                        return;
                    }
            
                    var ok = sequence.length === EXPECTED.length &&
                            sequence.every(function (v, idx) { return v === EXPECTED[idx]; });
            
                    if (ok) {
                        lock.classList.add('is-success');
                        setHint('Вход выполнен.', 'is-success');
                        // В прод: redirect to /. PHP: header('Location: /').
                        // window.location.href = '/';
                    } else {
                        attemptsLeft--;
                        if (attemptsLeft <= 0) {
                            lockOut();
                        } else if (attemptsLeft === 1) {
                            setError('Неверный ключ. Осталась последняя попытка.');
                            scheduleReset();
                        } else {
                            setError('Неверный ключ. Осталось попыток: ' + attemptsLeft + '.');
                            scheduleReset();
                        }
                    }
                }
        
                function setHint(text, cls) {
                    hint.textContent = text;
                    hint.className = 'pattern-hint' + (cls ? ' ' + cls : '');
                }
                function setError(text) {
                    lock.classList.add('is-error');
                    setHint(text, 'is-error');
                }
                function lockOut() {
                    lock.classList.add('is-error', 'is-disabled');
                    // Хинт короткий — длинный fallback («или войдите по логину и паролю»)
                    // уже всегда показан ссылкой ниже в .auth-alt.
                    setHint('Вход заблокирован. Обратитесь к администратору, чтобы сбросить ключ.', 'is-error');
                }
                function scheduleReset() {
                    resetTimer = setTimeout(function () { resetState(); }, 1400);
                }
                function resetState() {
                    sequence = [];
                    deactivateAll();
                    lock.classList.remove('is-error', 'is-success');
                    line.setAttribute('points', '');
                    hiddenInput.value = '';
                    if (!lock.classList.contains('is-disabled')) {
                        setHint('');
                    }
                }
        
                // Pointer events покрывают мышь, тач и стилус.
                lock.addEventListener('pointerdown', startDrag);
                lock.addEventListener('pointermove', moveDrag);
                lock.addEventListener('pointerup', endDrag);
                lock.addEventListener('pointercancel', endDrag);
                lock.addEventListener('pointerleave', function (e) {
                    // Если палец/мышь ушли за пределы сетки — заканчиваем как «отпуск».
                    if (dragging) endDrag(e);
                });
        
                // ── Демо-панель состояний (только для дизайн-сверки) ──
                $('.demo-states [data-demo]').on('click', function () {
                    var state = $(this).data('demo');
                    clearTimeout(resetTimer);
                    resetState();
                    lock.classList.remove('is-disabled');
                    attemptsLeft = MAX_ATTEMPTS;
            
                    switch (state) {
                        case 'idle':
                            // Уже сброшено.
                            break;
                        case 'error':
                            [1, 5, 9].forEach(function (i) { sequence.push(i); activate(i); });
                            renderLine();
                            setError('Неверный ключ. Осталось попыток: 2.');
                            break;
                        case 'last':
                            [1, 5, 9].forEach(function (i) { sequence.push(i); activate(i); });
                            renderLine();
                            setError('Неверный ключ. Осталась последняя попытка.');
                            break;
                        case 'locked':
                            lockOut();
                            break;
                        case 'success':
                            EXPECTED.forEach(function (i) { sequence.push(i); activate(i); });
                            renderLine();
                            lock.classList.add('is-success');
                            setHint('Вход выполнен.', 'is-success');
                            break;
                    }
                });
        
                // Стартовый хинт.
                resetState();
            })();
        </script>
    </body>
</html>
