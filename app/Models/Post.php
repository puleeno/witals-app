<?php

declare(strict_types=1);

namespace App\Models;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

use App\Support\Traits\Translatable;

#[Entity(table: 'wp_posts')]
class Post
{
    use Translatable;

    protected array $translatable = ['title', 'content'];
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

    #[Column(type: 'string', name: 'post_name', nullable: true)]
    public ?string $slug = null;
    
    #[Column(type: 'datetime', name: 'post_date')]
    public \DateTimeImmutable $date;
}
