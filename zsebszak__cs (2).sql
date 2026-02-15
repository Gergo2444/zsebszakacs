-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Feb 11. 15:12
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
(37, 6, 1),
(21, 6, 5),
(39, 6, 8),
(40, 10, 1),
(29, 10, 2),
(13, 10, 3),
(38, 10, 8),
(28, 10, 9);

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
  `kategoria` enum('Reggeli','Ebéd','Vacsora','kedvencek') NOT NULL,
  `ido` int(11) NOT NULL,
  `kaloria` int(11) NOT NULL,
  `hozzavalok` text NOT NULL,
  `leiras` text NOT NULL,
  `kep` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `receptek`
--

INSERT INTO `receptek` (`id`, `cim`, `kategoria`, `ido`, `kaloria`, `hozzavalok`, `leiras`, `kep`, `user_id`) VALUES
(1, 'Rántotta sajttal', 'Reggeli', 10, 350, '3 db tojás\r\n5 dkg reszelt sajt\r\n1 ek olaj vagy vaj\r\n1 csipet só\r\n(opcionális) bors\r\n', '1. A tojásokat egy tálban felütjük és enyhén megsózzuk.\r\n2. Serpenyőben az olajat vagy vajat közepes lángon felmelegítjük.\r\n3. A tojásokat beleöntjük, folyamatosan kevergetjük.\r\n4. Amikor a tojás már majdnem kész, hozzáadjuk a reszelt sajtot.\r\n5. Addig keverjük, amíg a sajt ráolvad, majd azonnal tálaljuk.\r\n', 'rantotta.jpg', 0),
(2, 'Csirkés tészta', 'Ebéd', 30, 650, '20 dkg csirkemell\r\n15 dkg tészta\r\n1 dl tejszín\r\n1 gerezd fokhagyma\r\n1 ek olaj\r\nsó\r\nbors\r\n', '1. A tésztát sós vízben kifőzzük.\r\n2. A csirkemellet felkockázzuk.\r\n3. Serpenyőben olajat hevítünk és a csirkét megpirítjuk.\r\n4. Hozzáadjuk a fokhagymát és a tejszínt.\r\n5. Összeforgatjuk a tésztával és tálaljuk.\r\n', 'csirkes_teszta.jpg', 0),
(3, 'Tonhalas saláta', 'Vacsora', 15, 350, '1 konzerv tonhal (vízben)\r\n1/2 fej jégsaláta\r\n1 paradicsom\r\n1/2 kígyóuborka\r\n1 ek olívaolaj\r\nsó\r\nbors\r\ncitromlé\r\n', '1. A tonhalat lecsepegtetjük.\r\n2. A zöldségeket felaprítjuk.\r\n3. Egy tálban összekeverjük a tonhalat a zöldségekkel.\r\n4. Sózzuk, borsozzuk, meglocsoljuk olívaolajjal és citromlével.\r\n5. Azonnal tálaljuk.\r\n', 'tonhalas_salata.jpg', 0),
(4, 'Zabpehely gyümölcsökkel', 'Reggeli', 10, 400, '5 dkg zabpehely\r\n2 dl tej\r\n1 banán\r\n1 marék bogyós gyümölcs\r\n1 tk méz\r\n', '1. A zabpehely a tejjel egy lábasban felforraljuk.\r\n2. Alacsony lángon pár percig főzzük, amíg besűrűsödik.\r\n3. A banánt felkarikázzuk.\r\n4. A zabkását tálba öntjük, rátesszük a gyümölcsöket.\r\n5. Mézzel ízesítjük és azonnal fogyasztjuk.\r\n', 'zabkasa.jpg', 0),
(5, 'Bolognai spagetti', 'Ebéd', 45, 750, '20 dkg darált marhahús\r\n20 dkg spagetti\r\n1 fej vöröshagyma\r\n2 gerezd fokhagyma\r\n2 dl paradicsomszósz\r\n1 ek olaj\r\nsó\r\nbors\r\noregánó\r\n(bolognai spagetti)\r\n', '1. A tésztát sós vízben kifőzzük.\r\n2. A hagymát felaprítjuk, olajon megdinszteljük.\r\n3. Hozzáadjuk a darált húst és megpirítjuk.\r\n4. Belekeverjük a fokhagymát és a paradicsomszószt.\r\n5. Fűszerezzük, majd a tésztával összeforgatjuk.\r\n', 'bolognai_spagetti.jpg\r\n', 0),
(6, 'Tojásos–zöldséges tortilla', 'Vacsora', 15, 380, '2 db tojás\r\n1 db tortilla lap\r\n1/2 paprika\r\n1/2 paradicsom\r\n5 dkg reszelt sajt\r\n1 tk olaj\r\nsó\r\nbors\r\n', '1. A zöldségeket felaprítjuk.\r\n2. Serpenyőben kevés olajat hevítünk.\r\n3. A tojásokat felverjük, megsózzuk, megborsozzuk.\r\n4. A tojást a serpenyőbe öntjük, rászórjuk a zöldségeket.\r\n5. Rátesszük a tortilla lapot, majd megfordítjuk.\r\n6. Sajtot szórunk rá, félbehajtjuk és pár percig pirítjuk.\r\n', 'zoldseges-tojasos-tortilla.jpg', 0),
(7, 'Sonkás-sajtos melegszendvics', 'Vacsora', 15, 500, '2 szelet kenyér\r\n5 dkg sonka\r\n5 dkg reszelt sajt\r\nvaj\r\n(Sonkás-sajtos melegszendvics)', '1. A kenyereket megvajazzuk.\r\n2. Rátesszük a sonkát és a sajtot.\r\n3. Sütőben vagy szendvicssütőben megsütjük.\r\n4. Amikor a sajt megolvadt, kivesszük és tálaljuk.\r\n', 'sonkas_sajtos_melegszendvics.jpg', 0),
(8, 'Csirkepaprikás galuskával', 'Ebéd', 60, 850, '30 dkg csirkecomb vagy csirkemell\r\n1 fej vöröshagyma\r\n1 ek olaj\r\n1 ek pirospaprika\r\n2 dl tejföl\r\nsó\r\n(Csirkepaprikás galuskával)', '1. A hagymát felaprítjuk és olajon megdinszteljük.\r\n2. Lehúzzuk a tűzről, megszórjuk pirospaprikával.\r\n3. Hozzáadjuk a csirkét, sózzuk.\r\n4. Felöntjük kevés vízzel és puhára főzzük.\r\n5. Tejföllel behabarjuk és galuskával tálaljuk.\r\n', 'Csirkepaprikas_galuskaval.jpg', 0),
(9, 'Kakaós csiga', 'Reggeli', 25, 420, '1 csomag leveles tészta\r\n2 ek cukrozatlan kakaópor\r\n2 ek porcukor\r\n2 ek olvasztott vaj\r\n(kakaós csiga)\r\n', '1. A leveles tésztát kiterítjük.\r\n2. Megkenjük az olvasztott vajjal.\r\n3. A kakaóport összekeverjük a porcukorral.\r\n4. Egyenletesen rászórjuk a tésztára.\r\n5. Szorosan feltekerjük, majd felszeleteljük.\r\n6. 180°C-ra előmelegített sütőben 15–20 percig sütjük.\r\n7. Langyosan tálaljuk.\r\n', 'kakaos-csiga.jpg', 0),
(11, 'tojáskrém kenyérrel', 'Reggeli', 15, 460, '2 db tojás\r\n2 szelet teljes kiőrlésű kenyér\r\n1 ek majonéz\r\n1 tk mustár\r\nSó, bors\r\n(opcionális: lilahagyma, snidling)', '1. A tojásokat főzd keményre (kb. 10 perc).\r\n2. Hűtsd le, pucold meg, majd villával törd össze.\r\n3. Keverd hozzá a majonézt, mustárt, sót, borsot.\r\n4. Kend rá a kenyérre, szórd meg apróra vágott hagymával vagy snidlinggel.', 'tojaskremkenyerrel.jpg', 0),
(12, 'Amerikai palacsinta', 'Reggeli', 20, 190, '200 g liszt\r\n2 ek cukor\r\n1 csomag sütőpor (12 g)\r\n1 csipet só\r\n2 db tojás\r\n2,5 dl tej\r\n2 ek olaj vagy olvasztott vaj\r\n(amerikai palacsinta)', '1. A száraz hozzávalókat (liszt, cukor, sütőpor, só) keverd össze.\r\n2. Egy másik tálban keverd össze a tojást, tejet és az olajat/vajat.\r\n3. Öntsd össze a két keveréket, és keverd simára (ne keverd túl!).\r\n4. Serpenyőt enyhén olajozz ki, közepes lángon süsd.\r\n5. Amikor buborékos lesz a teteje (kb. 1–2 perc), fordítsd meg és süsd még 1 percig.', 'amerikai_palacsinta.jpg', 0),
(13, 'Lasagne', 'Ebéd', 75, 800, '500 g darált sertés vagy marhahús\r\n1 fej vöröshagyma\r\n2 gerezd fokhagyma\r\n400 g darabolt paradicsom (konzerv)\r\n2 ek olaj\r\nSó, bors, oregánó\r\nTeljes adag (egész tepsi): kb. 3200–3600 kcal\r\n1 adag (4 lazagne részre osztva): kb. 800–900 kcal', '1.A hagymát olajon pirítsd meg, add hozzá a darált húst, majd a paradicsomot és a fűszereket. Főzd 15–20 percig.\r\n2.A besamelhez olvaszd meg a vajat, keverd bele a lisztet, majd fokozatosan öntsd hozzá a tejet. Sűrítsd be.\r\n3. Egy tepsiben rétegezd: tészta → húsos ragu → besamel → sajt.\r\n4.Ismételd, a tetejére sajt kerüljön.\r\n5.180°C-on süsd 35–40 percig.\r\n(3-4 adag)', 'lasagne.jpg', 0),
(14, 'Chilis bab rizssel', 'Ebéd', 35, 700, '500 g darált marha- vagy sertéshús\r\n1 konzerv vörösbab (kb. 400 g)\r\n1 konzerv darabolt paradicsom (400 g)\r\n1 fej vöröshagyma\r\n2 gerezd fokhagyma\r\n1 db paprika\r\n2 ek ola\r\n1 tk őrölt pirospaprika\r\n1 tk chili por (ízlés szerint)\r\nSó, bors, kömény\r\n(Opcionális: kukorica, koriander, tejföl)\r\nAz egész étel kb. 2800–3000 kcal\r\n1 adag (4 részre osztva): kb. 700–750 kcal\r\n(Chilis bab rizssel)', '1. A hagymát és fokhagymát olajon pirítsd meg.\r\n2. Add hozzá a darált húst, és pirítsd fehéredésig.\r\n3. Szórd meg fűszerekkel, majd öntsd hozzá a paradicsomot és a leöblített babot.\r\n4. Főzd közepes lángon 20–25 percig, amíg besűrűsödik.', 'chilis_bab_rizzsel.jpg', 0),
(15, 'Házi pizza', 'Vacsora', 60, 500, '500 g finomliszt\r\n2,5 dl langyos víz\r\n1 csomag szárított élesztő (7 g)\r\n1 tk cukor\r\n1 tk só\r\n2 ek olívaolaj\r\n200 g paradicsomszósz\r\n300 g reszelt mozzarella\r\n150 g sonka vagy szalámi\r\nOregánó\r\nTeljes pizza (1 db): kb. 1800–2000 kcal\r\n1 adag (negyed pizza): 450–500 kcal\r\n(Házi pizza)', '1. Az élesztőt keverd el a langyos vízben a cukorral, hagyd 5 percig állni.\r\n2. Add hozzá a lisztet, sót, olajat, majd gyúrd sima tésztává.\r\n3. Letakarva keleszd 30–45 percig.\r\n4. Nyújtsd ki, kend meg szósszal, szórd meg sajttal és feltéttel.\r\n5. 220°C-ra előmelegített sütőben süsd 12–15 percig.', 'hazi_pizza.jpg', 0),
(16, 'Leveles tésztás virsli', 'Vacsora', 30, 280, '1 csomag leveles tészta (kb. 275–300 g)\r\n4 db virsli\r\n1 db tojás (kenéshez)\r\n(opcionális: szezámmag, sajt, mustár)\r\n(4 adag / 8 darab)\r\nTeljes adag (8 db): kb. 2200–2400 kcal\r\n1 darab: kb. 270–300 kcal', '1. A leveles tésztát tekerd ki és vágd csíkokra.\r\nA virsliket felezd el (így 8 darab lesz).\r\n2. Tekerd körbe a virsliket a tésztacsíkokkal.\r\n3. Kend meg felvert tojással, szórd meg szezámmaggal vagy sajttal.\r\n4. 200°C-ra előmelegített sütőben süsd 15–20 percig, amíg aranybarna lesz.', 'levesteszta_virsli.jpg', 0),
(24, 'bableves', 'Ebéd', 30, 300, 'bab', 'leves', '826716c53c8c88ba.jpg', 12);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(40) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `email`, `reset_token`) VALUES
(3, 'GipszesJakab', '$2y$10$nfPnqGjJ.9Fl6vFgO7Y1hudZ.oUunkDMw8K5YISgq6fQoTE3ErQ1G', 'gipszesjakab123@gmail.com', NULL),
(4, 'BaloghEndre', '$2y$10$zEHqtryuj2JXYYJ7ylmwMuE3Hk4j.2G3xlDhTablj9IJlNTlXvVtO', 'baloghendre15@gmail.com', NULL),
(6, 'Subi Roland', '$2y$10$.OM5AoNo2FdJHD9dKn23uOaMf8VvdB4mqZtm.iR7sQomtMyKvR2om', 'subiroli@gmail.com', NULL),
(10, 'Elemér Farkas', '$2y$10$nk/3gMevwBRIDoBDyJ73wO4XE.koNjlaxt7sPtXHl3YqLyuaFZkC.', 'elemerfarkas22@gmail.com', NULL),
(12, 'Gergo24', '$2y$10$iWinhZxa9cIFI.AKYQ5d0e6RGz0TAlZwhs44TwGs1nBmOK0SrE0ci', 'gergo.tornyai@gmail.com', 'd8b27209c765bcfe3de754bbb68b0058f69fde5195307a528aaebe520f177628'),
(13, 'szentesibence', '$2y$10$lWi.by7xWlkmHXSbMMR6JO3ry6lcO1/uKQtfqUs7hx5EBq9cH2mMG', 'szentesibence29@gmail.com', '41871798001f2d8912edf3453677f2937d8af051fcd7b6f2c76f1242d5cdc426');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT a táblához `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `receptek`
--
ALTER TABLE `receptek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
