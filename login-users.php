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
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php
        $title_tail = "Вход";
        include 'include/head.php';
        ?>
        <style>
            /* ─────────────────────────────────────────────────────────────────
                PAGE-LEVEL КАРКАС — дублируем .auth-shell / .auth-hero / .auth-form
                из 03-login.html. Когда экранов входа станет 3+, вынести каркас
                в overrides как .flexim-auth-* (RULES → «Экран входа»).
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
            .auth-form__title { margin: 0; }
            .auth-form__subtitle {
                margin: var(--size-xs) 0 0;
                color: var(--text-secondary);
            }
            
            /* ──────────────────────────────────────────────────────────────────
                СПИСОК ЮЗЕРОВ.
                Каждая карточка — кликабельная строка с аватаром, именем, ролью и
                стрелкой справа. Это специфика экрана входа на общем устройстве;
                пока живёт page-level, при появлении других экранов с подобным
                списком (например, «выбрать получателя») — вынести в overrides
                как `.flexim-user-card` и добавить в каталог.
                ────────────────────────────────────────────────────────────────── */
            .user-list {
                display: flex;
                flex-direction: column;
                gap: var(--size-xs);
                margin: 0;
                padding: 0;
                list-style: none;
            }
            .user-card {
                display: flex;
                align-items: center;
                gap: var(--size-m);
                padding: var(--size-s) var(--size-m);
                background: var(--background-paper);
                border: 1px solid var(--other-lines);
                border-radius: var(--size-s);
                cursor: pointer;
                transition: background 80ms ease, border-color 80ms ease, box-shadow 80ms ease;
                text-align: left;
                width: 100%;
            }
            .user-card:hover {
                background: var(--primary-50);
                border-color: transparent;
                box-shadow: var(--shadow-s);
            }
            .user-card:focus-visible {
                outline: 2px solid var(--primary-main);
                outline-offset: 2px;
            }
            .user-card__avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font: 700 14px/16px var(--font-family);
                flex-shrink: 0;
            }
            .user-card__body {
                flex: 1 1 auto;
                min-width: 0;
            }
            .user-card__name {
                margin: 0;
                color: var(--text-primary);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .user-card__role {
                margin: 0;
                color: var(--text-secondary);
            }
            .user-card__arrow {
                flex-shrink: 0;
                color: var(--text-tertiary);
                transition: color 80ms ease, transform 80ms ease;
            }
            .user-card:hover .user-card__arrow {
                color: var(--primary-main);
                transform: translateX(2px);
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
            
            /* Мелкий fallback внизу — для случая, когда нового сотрудника ещё нет
                в списке устройства. Виден намеренно тускло. */
            .auth-fallback {
                text-align: left;
                color: var(--text-secondary);
                /* +20px к 32px gap карточки = 52px до списка (кратно 4px) */
                margin-top: var(--size-l);
            }
            /* «Нет в списке?» — отдельной строкой над ссылкой */
            .auth-fallback__text { display: block; }
            
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
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <section class="auth-hero" aria-hidden="true">
                <img class="auth-hero__image"
                     src="./assets/login-hero.png"
                     alt="">
                <div class="auth-hero__brand">
                    <span class="flexim-logo__mark" aria-hidden="true"></span>
                    <p class="t-h1 auth-hero__brand-name">Принт-Дизайн</p>
                </div>
            </section>
            <main class="auth-form">
                <div class="auth-form__card">
                    <!-- Мобильный header (Figma 6750:81405) — на десктопе скрыт через CSS. -->
                    <header class="auth-mobile-header" aria-hidden="false">
                        <span class="flexim-logo__mark auth-mobile-header__logo" aria-hidden="true"></span>
                        <p class="t-h2 auth-mobile-header__brand-name">Принт-Дизайн</p>
                    </header>
                    <header>
                        <h1 class="t-h2-r auth-form__title">Кто входит?</h1>
                        <p class="t-body auth-form__subtitle">Выберите свой профиль, чтобы продолжить.</p>
                    </header>
                    <!-- Список юзеров, привязанных к этому устройству.
                        PHP: < ? php foreach ($device->users() as $u): ? > ... < ? php endforeach; ? >
                        Цвет аватарки (av-*) — детерминированно по user_id (хелпер на бэке).
                        Клик ведёт на 04-login-pattern.html?user=ID — ключ конкретного юзера. -->
                    <ul class="user-list" role="list">
                        <?php
                        $sql = "select id, first_name, last_name, role_id from user where graph_key <> '' order by first_name, last_name";
                        $fetcher = new Fetcher($sql);
                        while($row = $fetcher->Fetch()):
                        ?>
                        <li>
                            <button type="button" class="user-card" data-user="<?=$row['id'] ?>" data-href="./login-pattern.php?user=<?=$row['id'] ?>">
                                <span class="user-card__avatar av-pink" aria-hidden='true'><?= (count_chars($row['first_name']) == 0 ? '' : mb_substr($row['first_name'], 0, 1)).(count_chars($row['last_name']) == 0 ? '' : mb_substr($row['last_name'], 0, 1)) ?></span>
                                <div class="user-card__body">
                                    <p class="t-h4 user-card__name"><?=$row['first_name'] ?> <?=$row['last_name'] ?></p>
                                    <p class="t-label user-card__role"><?= ROLE_LOCAL_NAMES[$row['role_id']] ?></p>
                                </div>
                                <span class="user-card__arrow" data-flexim-icon="arrow-right" data-size="24" aria-hidden="true"></span>
                            </button>
                        </li>
                        <?php endwhile; ?>
                        <li>
                            <button type="button" class="user-card" data-user="42" data-href="./04-login-pattern.html?user=42">
                                <span class="user-card__avatar av-pink" aria-hidden="true">СП</span>
                                <div class="user-card__body">
                                    <p class="t-h4 user-card__name">Сергей Пономарёв</p>
                                    <p class="t-label user-card__role">Технолог</p>
                                </div>
                                <span class="user-card__arrow" data-flexim-icon="arrow-right" data-size="24" aria-hidden="true"></span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="user-card" data-user="51" data-href="./04-login-pattern.html?user=51">
                                <span class="user-card__avatar av-blue" aria-hidden="true">ИВ</span>
                                <div class="user-card__body">
                                    <p class="t-h4 user-card__name">Ирина Власова</p>
                                    <p class="t-label user-card__role">Упаковщица</p>
                                </div>
                                <span class="user-card__arrow" data-flexim-icon="arrow-right" data-size="24" aria-hidden="true"></span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="user-card" data-user="63" data-href="./04-login-pattern.html?user=63">
                                <span class="user-card__avatar av-shrek" aria-hidden="true">ОБ</span>
                                <div class="user-card__body">
                                    <p class="t-h4 user-card__name">Олег Бойков</p>
                                    <p class="t-label user-card__role">Кладовщик</p>
                                </div>
                                <span class="user-card__arrow" data-flexim-icon="arrow-right" data-size="24" aria-hidden="true"></span>
                            </button>
                        </li>
                        <li>
                            <button type="button" class="user-card" data-user="74" data-href="./04-login-pattern.html?user=74">
                                <span class="user-card__avatar av-orange" aria-hidden="true">АН</span>
                                <div class="user-card__body">
                                    <p class="t-h4 user-card__name">Алексей Никитин</p>
                                    <p class="t-label user-card__role">Печатник</p>
                                </div>
                                <span class="user-card__arrow" data-flexim-icon="arrow-right" data-size="24" aria-hidden="true"></span>
                            </button>
                        </li>
                    </ul>
                    <!-- Fallback для админа / нового сотрудника, которого ещё нет в списке.
                        Малозаметный — это аварийный сценарий, а не основной поток. -->
                    <p class="t-body auth-fallback">
                        <span class="auth-fallback__text">Нет в списке?</span>
                        <a class="btn btn-link flexim-link-button--m" id="alt-password" href="./login.php">Войти по логину и паролю</a>
                    </p>
                </div>
            </main>
        </div>
        <?php
        include 'include/footer.php';
        include 'include/footer_cut.php';
        ?>
    </body>
</html>