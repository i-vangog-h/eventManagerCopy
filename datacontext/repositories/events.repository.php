<?php

require_once (__DIR__."/../data.context.php");
require_once (__DIR__."/../entities/event.php");

class EventsRepository {
    private $dataContext;

    function __construct()
    {
        $this->dataContext = new DataContext();
    }

    function addEvent(Event $event)
    {
        try
        {
            $qry = "INSERT INTO Events (id, ownerId, categoryId, name, description, startAt, location, entryPrice, createdAt, imageUri) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$event->id, $event->ownerId, $event->categoryId, $event->name, $event->description, $event->startAt, $event->location, $event->entryPrice, $event->createdAt, $event->imageUri];

            $this->dataContext->executeSQL($qry, $params);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getEventsCount($userId = null)
    {
        try
        {
            $qry = $userId != null
                ? "SELECT COUNT(*) as count FROM Events WHERE ownerId = ?"
                : "SELECT COUNT(*) as count FROM Events";

            $res = $this->dataContext->executeFromSQL($qry, $userId ? [$userId] : null);
            return intval($res[0]["count"]);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getAllEvents($limit=100, $offset=0)
    {
        try
        {
            $qry = "SELECT * FROM Events
                    ORDER BY createdAt
                    LIMIT :limit
                    OFFSET :offset ";

            $params = [
                ':limit' => $limit,
                ':offset' => $offset 
            ];

            $res = $this->dataContext->executeFromSQL($qry, $params, true);
            return $res;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function getEventsByUser($userId, $limit=100, $offset=0)
    {
        try
        {
            $qry = "SELECT * FROM Events
                    WHERE ownerId = :userId
                    ORDER BY createdAt
                    LIMIT :limit
                    OFFSET :offset ";

            $params = [
                ':userId' => $userId,
                ':limit' => $limit,
                ':offset' => $offset 
            ];

            $res = $this->dataContext->executeFromSQL($qry, $params, true);
            return $res;
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function deleteEventImages($eventId)
    {
        $eventImagesDir = __DIR__."/../../public/uploads/event.images/";
        $eventImages = [
            $eventId."_eventimg.png",
            $eventId."_eventimg.jpg",
            $eventId."_eventimg.jpeg"
        ];

        foreach($eventImages as $eventImage)
        {
            $imgPath = $eventImagesDir.$eventImage;
            if(file_exists($imgPath))
            {
                unlink($imgPath);
            }
        }
    }

    function deleteEvent($eventId)
    {
        try
        {
            $qry = "DELETE FROM Events WHERE id = ?";
            $this->dataContext->executeSQL($qry, [$eventId]);

            $this->deleteEventImages($eventId);

            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
}