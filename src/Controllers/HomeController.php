<?php

namespace W0q\Request\Controllers;

use W0q\Request\Controllers\User\UserService;
use W0q\Request\Http\Request;

class HomeController
{
    public function index(
        Request $request,
        UserService $service,
    ): array {
        return [
            'users' => $service->all(),
        ];
    }
}