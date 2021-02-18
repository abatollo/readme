<?php

use JetBrains\PhpStorm\Pure;

require_once('helpers.php');

define("MAX_POST_STRING_LENGTH", 300);
define("DAYS_IN_WEEK", 7);
define("MAX_WEEKS_DAYS", 5 * DAYS_IN_WEEK);

$page_title = "readme: популярное";

$is_auth = rand(0, 1);

$user_name = 'Александр Батолло';

// двумерный массив с постами
$posts_col = [
    [
        'heading' => 'Цитата',
        'type' => 'post-quote',
        'content' => 'Мы в жизни любим только раз, а после ищем лишь похожих',
        'username' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'heading' => 'Игра престолов',
        'type' => 'post-text',
        'content' => 'Не могу дождаться начала финального сезона своего любимого сериала!',
        'username' => 'Владик',
        'avatar' => 'userpic.jpg'
    ],
    [
        'heading' => 'Наконец, обработал фотки!',
        'type' => 'post-photo',
        'content' => 'rock-medium.jpg',
        'username' => 'Виктор',
        'avatar' => 'userpic-mark.jpg'
    ],
    [
        'heading' => 'Моя мечта',
        'type' => 'post-photo',
        'content' => 'coast-medium.jpg',
        'username' => 'Лариса',
        'avatar' => 'userpic-larisa-small.jpg'
    ],
    [
        'heading' => 'Лучшие курсы',
        'type' => 'post-link',
        'content' => 'www.htmlacademy.ru',
        'username' => 'Владик',
        'avatar' => 'userpic.jpg'
    ]
];

function get_human_time_diff(string $event_date): string
{
    $comparative_date = DateTime::createFromFormat('Y-m-d H:i:s', $event_date);
    $current_date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
    $diff = $comparative_date->diff($current_date);

    if ($diff->y !== 0) {
        $human_time_diff = $diff->y . ' ' . get_noun_plural_form($diff->y, 'год', 'года', 'лет');
    } elseif ($diff->m !== 0) {
        $human_time_diff = $diff->m . ' ' . get_noun_plural_form($diff->m, 'месяц', 'месяца', 'месяцев');
    } elseif ($diff->d >= DAYS_IN_WEEK && $diff->d < MAX_WEEKS_DAYS) {
        $human_time_diff = $diff->d / 7 . ' ' . get_noun_plural_form($diff->d / 7, 'неделя', 'недели', 'недель');
    } elseif ($diff->d > 0 && $diff->d < DAYS_IN_WEEK) {
        $human_time_diff = $diff->d . ' ' . get_noun_plural_form($diff->d, 'день', 'дня', 'дней');
    } elseif ($diff->h !== 0) {
        $human_time_diff = $diff->h . ' ' . get_noun_plural_form($diff->h, 'час', 'часа', 'часов');
    } elseif ($diff->i !== 0) {
        $human_time_diff = $diff->i . ' ' . get_noun_plural_form($diff->i, 'минута', 'минуты', 'минут');
    } elseif ($diff->s !== 0) {
        $human_time_diff = $diff->s . ' ' . get_noun_plural_form($diff->s, 'секунда', 'секунды', 'секунд');
    } else {
        $human_time_diff = 'Неизвестный интервал';
    }

    return $human_time_diff;
}

function get_formatted_date(string $raw_date): string
{
    return DateTime::createFromFormat('Y-m-d H:i:s', $raw_date)->format('Y-m-d H:i');
}

function check_content_length(string $post_content): string
{
    $is_excerpted = false;

    if (strlen($post_content) > MAX_POST_STRING_LENGTH) {
        $post_content = get_content_excerpt($post_content);
        $is_excerpted = true;
    }

    $post_content = '<p>' . esc($post_content) . '</p>';

    if ($is_excerpted) {
        $post_content .= '<a class="post-text__more-link" href="#">Читать далее</a>';
    }

    return $post_content;
}

function get_content_excerpt(string $post_content): string
{
    $exploded_post_string = explode(" ", $post_content);
    $string_length_counter = MAX_POST_STRING_LENGTH;
    for ($i = 0, $j = count($exploded_post_string); $i < $j ; $i++) {
        $string_length_counter -= strlen($exploded_post_string[$i]);
        if ($string_length_counter <= 0) {
            break;
        }
    }
    $post_content = rtrim(implode(" ", array_slice($exploded_post_string, 0, $i))) . '&hellip;';

    return $post_content;
}

#[Pure] function esc($str): string
{
    return htmlspecialchars($str);
}

// HTML-код главной страницы
$page_content = include_template('main.php', ['posts' => $posts_col]);

// окончательный HTML-код
$layout_content = include_template('layout.php', ['content' => $page_content, 'title' => $page_title, 'user_name' => $user_name, 'is_auth' => $is_auth]);

print($layout_content);

