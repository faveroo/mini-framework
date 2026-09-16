<?php

namespace W0q\Request\Controllers\User;

class UserRepository
{
    public function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Gabriel'],
            ['id' => 2, 'name' => 'Peter'],
        ];
    }
}