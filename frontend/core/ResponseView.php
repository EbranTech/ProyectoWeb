<?php
declare(strict_types=1);

namespace Core;

class ResponseView {
    public static function render(string $view, array $data = []): void {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }

    public static function layout(string $contentView, array $data = [], string $title = 'BiblioSys'): void {
        $titleData = ['title' => $title];
        include __DIR__ . '/../views/common/header.php';
        self::render($contentView, $data);
        include __DIR__ . '/../views/common/footer.php';
    }
}
