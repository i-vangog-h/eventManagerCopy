CREATE TABLE `Events` (
  `id` varchar(50) NOT NULL,
  `ownerId` varchar(50) NOT NULL,
  `categoryId` int NOT NULL,
  `name` varchar(45) NOT NULL,
  `description` mediumtext,
  `startAt` datetime NOT NULL,
  `location` varchar(200) NOT NULL,
  `entryPrice` int NOT NULL,
  `createdAt` datetime NOT NULL,
  `imageUri` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_owner_fk_idx` (`ownerId`),
  KEY `event_category_fk_idx` (`categoryId`),
  CONSTRAINT `event_category_fk` FOREIGN KEY (`categoryId`) REFERENCES `Categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_owner_fk` FOREIGN KEY (`ownerId`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
