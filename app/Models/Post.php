<?php

declare(strict_types=1);

namespace App\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(table: 'wp_posts')]
class Post
{
    #[Column(type: 'primary', name: 'ID')]
    public int $id;

    #[Column(type: 'string', name: 'post_title')]
    public string $title;

    #[Column(type: 'text', name: 'post_content')]
    public string $content;

    #[Column(type: 'string', name: 'post_status')]
    public string $status;

    #[Column(type: 'string', name: 'post_type')]
    public string $type;
    
    #[Column(type: 'datetime', name: 'post_date')]
    public \DateTimeImmutable $date;
}
