-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Jan 21. 13:23
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `zsebszakács`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `email`) VALUES
(3, 'GipszesJakab', '$2y$10$nfPnqGjJ.9Fl6vFgO7Y1hudZ.oUunkDMw8K5YISgq6fQoTE3ErQ1G', 'gipszesjakab123@gmail.com'),
(4, 'BaloghEndre', '$2y$10$zEHqtryuj2JXYYJ7ylmwMuE3Hk4j.2G3xlDhTablj9IJlNTlXvVtO', 'baloghendre15@gmail.com'),
(5, 'BaloghEndre', '$2y$10$zEHqtryuj2JXYYJ7ylmwMuE3Hk4j.2G3xlDhTablj9IJlNTlXvVtO', 'baloghendre15@gmail.com'),
(6, 'Subi Roland', '$2y$10$.OM5AoNo2FdJHD9dKn23uOaMf8VvdB4mqZtm.iR7sQomtMyKvR2om', 'subiroli@gmail.com'),
(7, 'Subi Roland', '$2y$10$.OM5AoNo2FdJHD9dKn23uOaMf8VvdB4mqZtm.iR7sQomtMyKvR2om', 'subiroli@gmail.com');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
