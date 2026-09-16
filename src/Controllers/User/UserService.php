<?php

namespace W0q\Request\Controllers\User;

class UserService
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function all(): array
    {
        return $this->repository->all();
    }
}