<?php
class Event
{
    function __construct(string $id, string $ownerId, int $categoryId, string $name)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->createdAt = date('Y-m-d H:i:s');
    }
    public string $id;
    public string $ownerId;
    public int $categoryId;
    public string $name;
    public ?string $description;
    public string $startAt;
    public string $location;
    public int $entryPrice;
    public ?string $imageUri;
    public string $createdAt;
}
