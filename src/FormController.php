<?php

declare(strict_types=1);

namespace App;

final class FormController
{
    public function __construct(
        private readonly Config $config,
        private readonly UserDirectory $users,
        private readonly Csrf $csrf,
        private readonly View $view,
    ) {
    }

    public function handle(?string $username, string $nonce): Response
    {
        if ($username === null || !$this->users->exists($username)) {
            return $this->page('No data', $this->view->render('error'), $nonce, 404);
        }

        if (!$this->config->active) {
            $content = $this->view->render('closed', ['orgName' => $this->config->orgName]);
            return $this->page('Submissions closed', $content, $nonce);
        }

        $content = $this->view->render('form', [
            'orgName' => $this->config->orgName,
            'username' => $username,
            'today' => date('Y-m-d'),
            'nonce' => $nonce,
        ]);

        return $this->page(
            $this->config->orgName . ' Photo Release',
            $content,
            $nonce,
            200,
            $this->csrf->token(),
        );
    }

    private function page(string $title, string $content, string $nonce, int $status = 200, ?string $csrf = null): Response
    {
        $body = $this->view->render('layout', [
            'title' => $title,
            'orgName' => $this->config->orgName,
            'content' => $content,
            'nonce' => $nonce,
            'csrfToken' => $csrf,
        ]);

        // The form carries a CSRF token, so it must never be cached.
        return new Response($status, $body, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
