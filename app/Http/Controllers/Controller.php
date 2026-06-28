<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Get the current cart identifier (user_id for auth, session_id for guest).
     */
    protected function identifier(): array
    {
        if (auth()->check()) {
            return ['user_id' => auth()->id()];
        }
        return ['session_id' => session()->getId()];
    }
}
