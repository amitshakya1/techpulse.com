<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public $title;
    public $description;
    public $keywords;
    public $author;

    public function __construct($title = null, $description = null, $keywords = null, $author = null)
    {
        $this->title = $title ?? config('app.name');
        $this->description = $description ?? '';
        $this->keywords = $keywords ?? '';
        $this->author = $author ?? config('app.author');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.admin.guest');
    }
}
