<?php

namespace App\Models;

class Action extends Model
{
    use Collection;

    public $table = 'actions';
    public string $name;
    public int $id;

    public function __serialize(): array
    {
        parent::__serialize();

        return (array) $this;
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->id   = $data['id'];
        $this->name = $data['name'];
    }
}
