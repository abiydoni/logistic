<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LanguageFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $lang    = $request->getGet('lang');

        if ($lang) {
            $lang = normalize_locale($lang);
            $session->set('lang', $lang);


            $userId = $session->get('user_id');
            if ($userId && $session->get('logged_in')) {
                (new UserModel())->update($userId, ['language' => $lang]);
            }
        }

        $locale = $session->get('lang');

        if (! $locale && $session->get('logged_in') && $session->get('user_id')) {
            $user = (new UserModel())->find($session->get('user_id'));
            if ($user) {
                $locale = normalize_locale($user['language'] ?? null);
                $session->set('lang', $locale);
            }
        }

        $locale = normalize_locale($locale);
        $session->set('lang', $locale);

        service('request')->setLocale($locale);

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
