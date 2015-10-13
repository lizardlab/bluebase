SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(6) unsigned NOT NULL,
  `username` varchar(50) NOT NULL,
  `hashed_pass` varchar(255) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `expires` date DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`), ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `users`
  MODIFY `userid` int(6) unsigned NOT NULL AUTO_INCREMENT;


CREATE TABLE IF NOT EXISTS `admins` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admins` (`username`, `password`) VALUES
('admin', '$2y$11$km3Uk0bJA78ZcjokqOTn5uWCLpzqsqYVwLl.5jJM3wup6CuYQVJey');

ALTER TABLE `admins`
  ADD PRIMARY KEY (`username`);

COMMIT;