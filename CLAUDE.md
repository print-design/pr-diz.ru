# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Проект

ERP-система «Принт-Дизайн» — управление производством гибкой упаковки (флексопечать → ламинация → резка → упаковка → отгрузка) плюс складской учёт плёнки в рулонах и паллетах. Классический PHP + MySQL без фреймворка и без сборки: каждый `.php` — самостоятельная страница. Весь UI и все комментарии — на русском.

## Запуск и окружение

Приложение работает под XAMPP из `C:\xampp\htdocs\pr-diz.ru`, доступно по `http://localhost/pr-diz.ru/`. Базовый префикс URL зашит константой `APPLICATION` в [include/define.php](include/define.php) и **обязателен** во всех ссылках и путях к статике: `<?=APPLICATION ?>/css/main.css`.

- Сборки нет — правки в `.php`/`.js`/`.css` подхватываются перезагрузкой страницы.
- Тестов, линтера и CI в проекте нет. Проверка изменений — открыть страницу в браузере.
- Зависимости: `composer install` (PhpSpreadsheet, chillerlan/php-qrcode, ext-zip). Каталог `vendor/` в `.gitignore`, но нужен для Excel-выгрузок, печатных форм и QR.
- PHPMailer подключён отдельной вендорённой копией в `PHPMailer/` (не через composer), используется для двухфакторного кода на почту.
- База: MySQL `erp`, доступ через `root` без пароля — настройки в [include/define.php](include/define.php). Дампа схемы в репозитории нет; структура таблиц узнаётся из SQL-запросов в коде или из живой БД.
- `nbproject/` — проект NetBeans, к работе кода отношения не имеет.

## Что игнорировать

- **`_old_versions/`** — личный архив старых реализаций, который владелец хранит как образцы для будущих задач. Исключать из поиска, анализа, рефакторинга и аудита; никогда не предлагать к удалению. Искать там имеет смысл только тогда, когда явно нужен образец «как это делалось раньше».
- `vendor/`, `PHPMailer/`, `fontawesome-free-5.15.1-web/`, минифицированные `js/*.min.js` и `css/*.min.css` — сторонний код.
- `content/` (вне git) — загруженные пользователями макеты и картинки: `content/printing/`, `content/stream/`, `content/dialog/`.

## Архитектура

### Цепочка бутстрапа

Каждая страница начинается с `include '../include/topscripts.php'` (в корне — `include 'include/topscripts.php'`). [include/topscripts.php](include/topscripts.php) подтягивает [define.php](include/define.php) и [constants.php](include/constants.php), стартует сессию, генерирует CSRF-токен, объявляет глобальные массивы дат, все хелперы, три класса доступа к БД — и попутно **исполняет** общие обработчики POST (выход из системы, скачивание картинки, двухфакторная аутентификация, принудительный разлогин при смене пароля). Поэтому его нельзя подключать после вывода HTML.

### Скелет типичной страницы

```php
<?php
include '../include/topscripts.php';

// 1. Авторизация — при отказе _unauthorized.php печатает страницу и делает exit()
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))) {
    include '../include/_unauthorized.php';
}

// 2. Обработка POST-форм — по имени сабмит-кнопки
if(null !== filter_input(INPUT_POST, 'delete-roll-submit')) { ... }

// 3. Сбор фильтров из GET в строку $where, затем запросы
?>
<!DOCTYPE html>
<html lang="ru">
    <head><?php include '../include/head.php'; ?></head>
    <body>
        <?php include '../include/header.php'; ?>
        <div class="container-fluid"> ... </div>
        <?php include '../include/footer.php'; ?>
    </body>
</html>
```

Разметочные include-файлы: [head.php](include/head.php) (Bootstrap 4, FontAwesome, `main.css`), [header.php](include/header.php) → [left_bar.php](include/left_bar.php) (вертикальное меню, пункты которого показываются по ролям) + `find.php` текущего раздела + [header_right.php](include/header_right.php), [footer.php](include/footer.php) (jQuery, Bootstrap JS, `calculation.js` и общие скрипты). Разделы со своей шапкой добавляют `header_sklad.php` / `header_cut.php` / `header_admin.php` / `header_plan.php` / `header_buh.php` / `header_pack.php`.

### Соглашения об именах файлов

| Имя | Роль |
| --- | --- |
| `index.php` | список с фильтрами и постраничной навигацией |
| `new.php` / `create.php` / `edit.php` | формы создания и редактирования |
| `details.php`, `<сущность>.php` | карточка объекта |
| `find.php` | блок поиска раздела; подключается автоматически из `header.php`, если файл существует в каталоге (парная логика в `footer.php` подключает `footer_find.php`) |
| `print.php`, `_print.php` | печатные формы |
| `excel*.php` | выгрузки в XLSX через PhpSpreadsheet |
| `_имя.php` | **AJAX-эндпоинт или партиал**: сам подключает `topscripts.php`, что-то делает и `echo`-ит результат (обычно кусок HTML или текст), вызывается через `$.ajax` из родительской страницы |

`_`-префикс — самая важная конвенция: это не «приватный include», а отдельная точка входа. Пример: [roll/_edit_comment.php](roll/_edit_comment.php) вызывается из [roll/index.php:377](roll/index.php:377).

### Доступ к БД

Три класса в `topscripts.php`, каждый открывает и закрывает собственное соединение mysqli:

- `Executer($sql, $params)` — INSERT/UPDATE/DELETE, отдаёт `->error` и `->insert_id`
- `Grabber($sql, $params)` — SELECT, отдаёт `->result` как массив ассоциативных массивов
- `Fetcher($sql, $params)` — SELECT с построчным чтением через `->Fetch()`

**Передан второй аргумент `$params` → используется prepared statement** (типы для `bind_param` выводятся автоматически трейтом `BindParamsTrait`). Не передан → запрос уходит как есть. Кодовая база в середине миграции: значительная часть страниц всё ещё склеивает SQL из значений `filter_input(...)`. Весь новый и правленый код должен передавать `$params`.

Ошибки не бросаются исключениями: результат кладётся в `->error`, страница обычно копит его в `$error_message` и выводит алертом.

### Роли и авторизация

14 ролей объявлены в [include/constants.php:3](include/constants.php:3) тремя параллельными массивами: `ROLE_NAMES` (машинный ключ), `ROLE_LOCAL_NAMES` (подпись), `ROLE_TWOFACTOR` (нужен ли код на почту; сейчас всё выключено, боевой вариант закомментирован рядом).

Сессия пользователя живёт **в куках с обфусцированными именами** (`USER_ID`, `USERNAME`, `ROLE` и т. д. — константы в `define.php`). `IsInRole($role)` сравнивает куку `ROLE` со строкой или массимом строк и знает одно унаследование: `manager-senior` автоматически получает права `manager`. `LoggedIn()`, `GetUserId()` читают те же куки. При каждой загрузке страницы `topscripts.php` сверяет `username` + первые 5 символов хеша пароля с БД и разлогинивает, если пользователя удалили, деактивировали или сменили ему пароль.

Отдельная категория — **цеховые терминалы**: `cutter/` и `marker/` работают под техническими учётками резательных машин (`CUTTER_USERS`), имеют собственные `_head.php` / `_footer.php` / `logout.php` и не используют общий layout. Вход на планшетах — по графическому ключу через [mobile.php](mobile.php).

Пароли в БД хранятся через устаревшую MySQL-функцию `password()`.

### Разделы

| Каталог | Назначение |
| --- | --- |
| `calculation/` | ядро системы: расчёт заказа, техкарта, статусы, печатные формы, Excel. Самые крупные файлы проекта — [create.php](calculation/create.php), [calculation.php](calculation/calculation.php), [techmap.php](calculation/techmap.php) |
| `plan/` | план производства по сменам для печати / ламинации / резки, drag-and-drop очереди, назначение сотрудников |
| `cut/`, `cutter/` | участок резки: `cut/` — управление для технолога и начальника участка, `cutter/` — терминал резчика |
| `roll/`, `pallet/`, `cut_source/`, `utilized/` | склад: рулоны, паллеты, источники раскроя, сработанное |
| `car/` | интерфейс карщика и ревизора (перемещение по ячейкам, сканирование) |
| `marker/` | терминал маркировщика, печать этикеток |
| `pack/`, `buh/` | упаковка и готовая продукция / оплата |
| `supplier/`, `admin/` | справочники: поставщики, плёнки и цены, нормативы машин, наценки, клише, валюты |
| `user/`, `personal/` | пользователи и личный кабинет |
| `improvement/`, `okto/` | предложения по улучшению; `okto/` — внутренний чат (скрыт в меню классом `d-none`) |
| `migration/` | **одноразовые скрипты разовых миграций данных**, запускавшиеся вручную парами `*_migration.php` (страница с прогрессом) + `*_migration_ajax.php` (шаг). Это исторический слой, а не рабочий функционал |

### Доменная модель

Заказ (`calculation`) проходит статусы из `ORDER_STATUS_*` ([constants.php](include/constants.php)): черновик → расчёт → ждём подтверждения → ждём постановки в план → техкарта → в плане печати/ламинации/резки → приладка → режется → готово к упаковке → ждёт отгрузки → отгружено. У статусов заданы подписи, цвета и иконки FontAwesome в параллельных массивах — при добавлении статуса нужно дописать во все.

Смена статуса идёт только через `SetCalculationStatus($calculation_id, $status_id, $comment)` из `topscripts.php`: она пишет строку в `calculation_status_history` **и** дублирует последнее значение в денормализованные поля `calculation.duplicate_status_id/_comment/_date` (это нужно для быстрой фильтрации списков). Обратная операция — `RemoveCalculationStatus`.

Тот же приём «история + последняя запись» применяется на складе: `roll_status_history`, `pallet_roll_status_history`, `roll_cell_history`, `pallet_cell_history`. Текущее состояние рулона достаётся подзапросом вида `where id in (select max(id) from roll_status_history group by roll_id)`.

Основные таблицы: `calculation`, `calculation_quantity`, `calculation_stream`, `calculation_result`, `calculation_status_history`, `plan_edition`, `plan_event`, `plan_continuation`, `plan_workshift`, `plan_employee`, `roll`, `pallet`, `pallet_roll`, `film`, `film_variation`, `film_price`, `supplier`, `customer`, `cutting`, `cutting_stream`, `cutting_source`, `user`, справочники норм `norm_*`.

### Списки: фильтры, сортировка, страницы

Состояние списка полностью живёт в query string. Хелперы `BuildQuery`, `BuildQueryRemove`, `BuildQueryAddRemove`, `BuildQueryAddRemoveArray` в `topscripts.php` пересобирают текущий `$_GET` с нужной правкой — ими строятся ссылки фильтров, сортировки и пагинации, чтобы остальные параметры не терялись. Постранично: [pager_top.php](include/pager_top.php) (по 50 записей, вычисляет `$pager_skip`/`$pager_take`) до запроса и [pager_bottom.php](include/pager_bottom.php) после. Сортировка — локальная функция `OrderLink($param)`, объявляемая в самой странице.

### Формы

Валидация серверная и разложена по переменным на каждое поле: `$field_valid` получает константу `ISINVALID` (`' is-invalid'`, дописывается в класс Bootstrap), `$field_message` — текст ошибки, общий флаг `$form_valid` решает, выполнять ли запись. Обработчик выбирается по `filter_input(INPUT_POST, '<имя_кнопки>_submit')`. В форму добавляется скрытое поле `<input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />` — токен кладётся, но проверяется не везде.

### Excel, печать, QR

Выгрузка: `require '../vendor/autoload.php'`, сборка `Spreadsheet` вручную по ячейкам, отдача через `Xlsx`-writer с заголовками из `DownloadSendHeaders($filename)`. Печатные формы — отдельные `print*.php` со своим минимальным HTML без общего layout. QR-коды — chillerlan/php-qrcode в [improvement/qr.php](improvement/qr.php).

## Особенности, о которых стоит помнить

- Кодировка везде UTF-8, соединение выставляет `set names utf8`; идентификаторы английские, весь текст — русский. Файлы правятся без BOM.
- Никакого автозагрузчика для своего кода нет — только `include`/`require` с относительными путями, поэтому глубина вложенности каталога влияет на путь к `include/`.
- HTML и SQL живут в одном файле; ряд страниц перевалил за 1000–5000 строк. Прежде чем править такой файл, стоит найти нужный блок поиском по имени сабмита или по тексту на странице.
- В `constants.php` много «жёстко забитого» производственного справочника (печатные машины, резки, коэффициенты, ламинаторы). Новое оборудование добавляется правкой этих массивов, а не через админку.
- В [left_bar.php](include/left_bar.php) есть блоки, помеченные комментарием `ВРЕМЕННО` — временные костыли доступа под конкретных пользователей.
