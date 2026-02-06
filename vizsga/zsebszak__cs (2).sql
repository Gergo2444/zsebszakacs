-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Feb 06. 12:25
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
-- Tábla szerkezet ehhez a táblához `kedvencek`
--

CREATE TABLE `kedvencek` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recept_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `kedvencek`
--

INSERT INTO `kedvencek` (`id`, `user_id`, `recept_id`) VALUES
(21, 6, 5),
(10, 10, 1),
(14, 10, 2),
(13, 10, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `receptek`
--

CREATE TABLE `receptek` (
  `id` int(11) NOT NULL,
  `cim` varchar(255) NOT NULL,
  `kategoria` enum('Reggeli','Ebed','Vacsora','kedvencek') NOT NULL,
  `ido` int(11) NOT NULL,
  `kaloria` int(11) NOT NULL,
  `hozzavalok` text NOT NULL,
  `leiras` text NOT NULL,
  `kep` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `receptek`
--

INSERT INTO `receptek` (`id`, `cim`, `kategoria`, `ido`, `kaloria`, `hozzavalok`, `leiras`, `kep`) VALUES
(1, 'Rántotta sajttal', 'Reggeli', 10, 350, '3 db tojás\r\n5 dkg reszelt sajt\r\n1 ek olaj vagy vaj\r\n1 csipet só\r\n(opcionális) bors\r\n', '1. A tojásokat egy tálban felütjük és enyhén megsózzuk.\r\n2. Serpenyőben az olajat vagy vajat közepes lángon felmelegítjük.\r\n3. A tojásokat beleöntjük, folyamatosan kevergetjük.\r\n4. Amikor a tojás már majdnem kész, hozzáadjuk a reszelt sajtot.\r\n5. Addig keverjük, amíg a sajt ráolvad, majd azonnal tálaljuk.\r\n', 'rantotta.jpg'),
(2, 'Csirkés tészta', 'Ebed', 30, 650, '20 dkg csirkemell\r\n15 dkg tészta\r\n1 dl tejszín\r\n1 gerezd fokhagyma\r\n1 ek olaj\r\nsó\r\nbors\r\n', '1. A tésztát sós vízben kifőzzük.\r\n2. A csirkemellet felkockázzuk.\r\n3. Serpenyőben olajat hevítünk és a csirkét megpirítjuk.\r\n4. Hozzáadjuk a fokhagymát és a tejszínt.\r\n5. Összeforgatjuk a tésztával és tálaljuk.\r\n', 'csirkes_teszta.jpg'),
(3, 'Tonhalas saláta', 'Vacsora', 15, 350, '1 konzerv tonhal (vízben)\r\n1/2 fej jégsaláta\r\n1 paradicsom\r\n1/2 kígyóuborka\r\n1 ek olívaolaj\r\nsó\r\nbors\r\ncitromlé\r\n', '1. A tonhalat lecsepegtetjük.\r\n2. A zöldségeket felaprítjuk.\r\n3. Egy tálban összekeverjük a tonhalat a zöldségekkel.\r\n4. Sózzuk, borsozzuk, meglocsoljuk olívaolajjal és citromlével.\r\n5. Azonnal tálaljuk.\r\n', 'tonhalas_salata.jpg'),
(4, 'Zabpehely gyümölcsökkel', 'Reggeli', 10, 400, '5 dkg zabpehely\r\n2 dl tej\r\n1 banán\r\n1 marék bogyós gyümölcs\r\n1 tk méz\r\n', '1. A zabpehely a tejjel egy lábasban felforraljuk.\r\n2. Alacsony lángon pár percig főzzük, amíg besűrűsödik.\r\n3. A banánt felkarikázzuk.\r\n4. A zabkását tálba öntjük, rátesszük a gyümölcsöket.\r\n5. Mézzel ízesítjük és azonnal fogyasztjuk.\r\n', 'zabkasa.jpg'),
(5, 'Bolognai spagetti', 'Ebed', 45, 750, '20 dkg darált marhahús\r\n20 dkg spagetti\r\n1 fej vöröshagyma\r\n2 gerezd fokhagyma\r\n2 dl paradicsomszósz\r\n1 ek olaj\r\nsó\r\nbors\r\noregánó\r\n', '1. A tésztát sós vízben kifőzzük.\r\n2. A hagymát felaprítjuk, olajon megdinszteljük.\r\n3. Hozzáadjuk a darált húst és megpirítjuk.\r\n4. Belekeverjük a fokhagymát és a paradicsomszószt.\r\n5. Fűszerezzük, majd a tésztával összeforgatjuk.\r\n', 'bolognai_spagetti.jpg\r\n'),
(6, 'Tojásos–zöldséges tortilla', 'Vacsora', 15, 380, '2 db tojás\r\n1 db tortilla lap\r\n1/2 paprika\r\n1/2 paradicsom\r\n5 dkg reszelt sajt\r\n1 tk olaj\r\nsó\r\nbors\r\n', '1. A zöldségeket felaprítjuk.\r\n2. Serpenyőben kevés olajat hevítünk.\r\n3. A tojásokat felverjük, megsózzuk, megborsozzuk.\r\n4. A tojást a serpenyőbe öntjük, rászórjuk a zöldségeket.\r\n5. Rátesszük a tortilla lapot, majd megfordítjuk.\r\n6. Sajtot szórunk rá, félbehajtjuk és pár percig pirítjuk.\r\n', 'zoldseges-tojasos-tortilla.jpg'),
(7, 'Sonkás-sajtos melegszendvics', 'Vacsora', 15, 500, '2 szelet kenyér\r\n5 dkg sonka\r\n5 dkg reszelt sajt\r\nvaj\r\n', '1. A kenyereket megvajazzuk.\r\n2. Rátesszük a sonkát és a sajtot.\r\n3. Sütőben vagy szendvicssütőben megsütjük.\r\n4. Amikor a sajt megolvadt, kivesszük és tálaljuk.\r\n', 'sonkas_sajtos_melegszendvics.jpg'),
(8, 'Csirkepaprikás galuskával', 'Ebed', 60, 850, '30 dkg csirkecomb vagy csirkemell\r\n1 fej vöröshagyma\r\n1 ek olaj\r\n1 ek pirospaprika\r\n2 dl tejföl\r\nsó\r\n', '1. A hagymát felaprítjuk és olajon megdinszteljük.\r\n2. Lehúzzuk a tűzről, megszórjuk pirospaprikával.\r\n3. Hozzáadjuk a csirkét, sózzuk.\r\n4. Felöntjük kevés vízzel és puhára főzzük.\r\n5. Tejföllel behabarjuk és galuskával tálaljuk.\r\n', 'Csirkepaprikas_galuskaval.jpg'),
(9, 'Kakaós csiga', 'Reggeli', 25, 420, '1 csomag leveles tészta\r\n2 ek cukrozatlan kakaópor\r\n2 ek porcukor\r\n2 ek olvasztott vaj\r\n', '1. A leveles tésztát kiterítjük.\r\n2. Megkenjük az olvasztott vajjal.\r\n3. A kakaóport összekeverjük a porcukorral.\r\n4. Egyenletesen rászórjuk a tésztára.\r\n5. Szorosan feltekerjük, majd felszeleteljük.\r\n6. 180°C-ra előmelegített sütőben 15–20 percig sütjük.\r\n7. Langyosan tálaljuk.\r\n', 'kakaos-csiga.jpg');

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
(6, 'Subi Roland', '$2y$10$.OM5AoNo2FdJHD9dKn23uOaMf8VvdB4mqZtm.iR7sQomtMyKvR2om', 'subiroli@gmail.com'),
(10, 'Elemér Farkas', '$2y$10$nk/3gMevwBRIDoBDyJ73wO4XE.koNjlaxt7sPtXHl3YqLyuaFZkC.', 'elemerfarkas22@gmail.com');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `kedvencek`
--
ALTER TABLE `kedvencek`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_recipe` (`user_id`,`recept_id`);

--
-- A tábla indexei `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- A tábla indexei `receptek`
--
ALTER TABLE `receptek`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `kedvencek`
--
ALTER TABLE `kedvencek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT a táblához `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `receptek`
--
ALTER TABLE `receptek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
