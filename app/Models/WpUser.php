<?php

declare(strict_types=1);

namespace App\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'wp_users')]
class WpUser
{
    #[Column(type: 'primary', name: 'ID')]
    public int $id;

    #[Column(type: 'string', name: 'user_login')]
    public string $login;

    #[Column(type: 'string', name: 'user_pass')]
    public string $password;

    #[Column(type: 'string', name: 'user_email')]
    public string $email;

    #[Column(type: 'string', name: 'display_name')]
    public string $displayName;
}
