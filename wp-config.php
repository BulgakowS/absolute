<?php
/**
 * Основные параметры WordPress.
 *
 * Этот файл содержит следующие параметры: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Дополнительную информацию можно найти
 * на странице {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Кодекса. Настройки MySQL можно узнать у хостинг-провайдера.
 *
 * Этот файл используется сценарием создания wp-config.php в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать этот файл
 * с именем "wp-config.php" и заполнить значения.
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'WPtest');

/** Имя пользователя MySQL */
define('DB_USER', 'root');

/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'root');

/** Имя сервера MySQL */
define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется снова авторизоваться.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '61_a8ENX!Q@_a5uZ=m}O6wI-[7?c7ZBkJ+6Nk~Z!Z5GNCJ}.%op3,? ^nZb,X2Ws');
define('SECURE_AUTH_KEY',  '@C_Y9}A)?`2R4O%mM_IC|A?E%6~vRvZkt.^x>e/6Qv;?>2At{NQ=6.03>#Cq+Cwd');
define('LOGGED_IN_KEY',    'oGMD-kxV0`ITud<%zJIw^Ru&t;&B25k`yOE&w*elX2p|X4@}c@+wQ};(ep)(9vb*');
define('NONCE_KEY',        'ntLUfL~K1EE|fP*7bX#ng3P+2Ahn:=$2XpoHlVI9OG+8-T&AmNNdM6%NKyOD&JKY');
define('AUTH_SALT',        '3-7N/X5x2etA%|mR5+aIC{T#@~t-|QJ0XXB|x4|96H]`m;[qPe?j4lLzUT8+Q*7@');
define('SECURE_AUTH_SALT', 'Iu2A5s-/0*f3^WkTS5/2(G+,)43+h3*7RE.Q+ZG0qx ro.FSDJh@|.[T*kS[s+KV');
define('LOGGED_IN_SALT',   '%}gt8-mT(X02)l toTR;m+$Ku?+5j|T-A#T;:Iy%F&7.o1y|G~G+t![)N4j/-w.w');
define('NONCE_SALT',       'LGJ <x.*}`vqbyju4@n)Y;S;R|Q4PIm-#FsE+ +qP/&7@[oo<lw^5Z0,cpPDH52H');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = 'wp_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
