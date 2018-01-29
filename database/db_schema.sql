CREATE TABLE `Jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(1000) NOT NULL,
  `state` ENUM('queued', 'finished', 'failed', 'error') DEFAULT 'queued',
  `result` BLOB,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `QueuedJobs` (
  `jobId` int(11) NOT NULL,
  `tries` int(11) DEFAULT 0,
  `lastAttempt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jobId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

ALTER table `QueuedJobs` ADD INDEX queued_jobs_query_idx (`createdAt`, `lastAttempt`);