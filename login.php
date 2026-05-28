<?php
include 'include/topscripts.php';

// Если залогинен, перенаправляем на основную страницу
if(LoggedIn()) {
    header('Location: '.APPLICATION);
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
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        $title_tail = "Вход";
        include 'include/head.php';
        ?>
        <style>
            /* ─────────────────────────────────────────────────────────────────
                СТРУКТУРНЫЙ КАРКАС СТРАНИЦЫ — hero слева 2/3 + форма справа 1/3
                (соотношение из Figma: 960×840 / 480×840 = 2:1).
                Это не «компонент», а скелет page-level (RULES → «единственное
                допустимое page-level CSS — структурный каркас»). Контент внутри —
                только из компонентов DS, типографика — классы .t-* из tokens.css.
                ───────────────────────────────────────────────────────────────── */
            /* Базовые стили body (шрифт, цвет, фон) — в _bootstrap-flexim-overrides.css.
                Здесь только специфика логина: чтобы .auth-shell `min-height:100vh` рассчитался
                корректно, html/body тянем на полную высоту. */
            html, body { height: 100%; }

            .auth-shell {
                display: flex;
                min-height: 100vh;
                align-items: stretch;
            }

            /* HERO — левая колонка 2/3 ширины */
            .auth-hero {
                position: relative;
                flex: 2 1 0;
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
                /* В Figma: left 80, bottom 80 (= двойной size-xxxl) */
                left: calc(var(--size-xxxl) * 2);
                bottom: calc(var(--size-xxxl) * 2);
                display: flex;
                align-items: center;
                gap: var(--size-xxl);
                z-index: 1;
            }
            /* Знак из DS (.flexim-logo__mark, секция #flexim-logo). По дефолту 32×32,
                в Figma макете логина — 60×60 (локальный размер под этот экран). */
            .auth-hero .flexim-logo__mark { width: 60px; height: 60px; }
            /* Бренд-имя — типографика из DS (.t-h1), здесь только цвет и nowrap для каркаса */
            .auth-hero__brand-name {
                margin: 0;
                color: var(--text-contrast);
                white-space: nowrap;
            }

            /* ФОРМА — правая колонка 1/3 ширины */
            .auth-form {
                flex: 1 1 0;
                display: flex;
                align-items: center;
                justify-content: center;
                /* В Figma padding 60px по горизонтали = size-xxxl + size-l */
                padding: var(--size-xxxl) calc(var(--size-xxxl) + var(--size-l));
                background: var(--background-bg);
            }
            .auth-form__card {
                width: 100%;
                max-width: 360px;
                display: flex;
                flex-direction: column;
                gap: var(--size-xxxl);
            }
            .auth-form__title { margin: 0; }
            .auth-form__fields {
                display: flex;
                flex-direction: column;
                gap: var(--size-m);
            }
            /* В каталоге .flexim-input-field фикс 316px — для карточки логина 360px растягиваем */
            .auth-form .flexim-input-field { width: 100%; max-width: 100%; }

            /* Мобильный вид — стек: hero сверху, форма снизу */
            @media (max-width: 900px) {
                .auth-shell { flex-direction: column; }
                .auth-hero {
                    flex: 0 0 280px;
                    min-height: 280px;
                }
                .auth-hero__brand {
                    left: var(--size-l);
                    bottom: var(--size-l);
                    gap: var(--size-m);
                }
                .auth-hero .flexim-logo__mark { width: 40px; height: 40px; }
                .auth-form {
                    flex: 1 1 auto;
                    padding: var(--size-xl);
                }
            }
        </style>
    </head>
    <body>
        <div class="auth-shell">
            <?php
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger mt-3'>$error_message</div>";
            }
            ?>
            <!-- HERO: ассет из макета Figma (нода 6750:79644), скачан в assets/. -->
            <section class="auth-hero" aria-hidden="true">
                <img class="auth-hero__image"
                     src="<?= APPLICATION ?>/assets/login-hero.png"
                     alt="">
                <div class="auth-hero__brand">
                    <!-- BrandLogo: знак из библиотеки (.flexim-logo__mark, каталог #flexim-logo) -->
                    <span class="flexim-logo__mark" aria-hidden="true"></span>
                    <p class="t-h1 auth-hero__brand-name">Принт-Дизайн</p>
                </div>
            </section>
            
            <!-- ФОРМА ВХОДА -->
            <main class="auth-form">
                <form class="auth-form__card" method="post" novalidate>
                    <h1 class="t-h2-r auth-form__title">Вход</h1>
                    <div class="auth-form__fields">
                        <!-- Логин или email — Input (рабочая форма, разметка из #forms «Рабочий пример») -->
                        <div class="flexim-input-field">
                            <label class="flexim-input-field__label" for="login-identifier">Логин</label>
                            <div class="flexim-input">
                                <div class="flexim-input__content">
                                    <input id="login_username"
                                           name="login_username"
                                           class="flexim-input__field<?=$login_username_valid ?>"
                                           type="text"
                                           placeholder="Логин" 
                                           value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username']) ? $_POST['login_username'] : '' ?>" 
                                           required="required" 
                                           autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <!-- Пароль — Input + eye-toggle в action-slot (.flexim-input__action). -->
                        <div class="flexim-input-field">
                            <label class="flexim-input-field__label" for="login-password">Пароль</label>
                            <div class="flexim-input">
                                <div class="flexim-input__content">
                                    <input id="login_password"
                                           name="login_password"
                                           class="flexim-input__field<?=$login_password_valid ?>"
                                           type="password"
                                           autocomplete="current-password"
                                           placeholder="Пароль" 
                                           required="required">
                                </div>
                                <button type="button" 
                                        class="flexim-input__action"
                                        data-flexim-icon="eye-closed"
                                        data-size="24"
                                        data-toggle-password
                                        aria-pressed="false"
                                        aria-label="Показать пароль"></button>
                            </div>
                        </div>
                    </div>
                    <!-- Primary Button «Войти» на всю ширину -->
                    <button type="submit" class="btn btn-primary btn-block" id="login_submit" name="login_submit">Войти</button>
                </form>
            </main>
        </div>
        <?php
        include 'include/footer.php';
        include 'include/footer_cut.php';
        ?>
    </body>
</html>