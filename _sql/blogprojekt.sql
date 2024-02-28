-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 28, 2024 at 09:54 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blogprojekt`
--
CREATE DATABASE IF NOT EXISTS `blogprojekt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blogprojekt`;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `blogID` int(11) NOT NULL,
  `blogHeadline` varchar(256) NOT NULL,
  `blogImagePath` varchar(256) DEFAULT NULL,
  `blogImageAlignment` varchar(10) NOT NULL,
  `blogContent` text NOT NULL,
  `blogDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `catID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`blogID`, `blogHeadline`, `blogImagePath`, `blogImageAlignment`, `blogContent`, `blogDate`, `catID`, `userID`) VALUES
(1, 'Ouija 101: A Beginner&apos;s Guide to Navigating the Spirit Realm', './uploaded_images/1427560305-qzmvx_1igkfj694s7wyda3nc0upbr2to5h_e8l-17091086537956.png', 'left', 'Are you ready to connect with spirits from beyond the grave? Here&apos;s everything you need to know to get started with Ouija boards.\r\n\r\nFirst things first, what exactly is a Ouija board? Essentially it&apos;s a flat board with letters, numbers, and symbols that you place a glass or small object on to move around and communicate with spirits. While many people view these boards as a game, they are actually a tool for communication with the spirit realm.\r\n\r\nTo start using the board, gather a group of friends and sit down with the board between you. Each person places a finger on the glass, and then the fun begins! The glass moves around the board, spelling out messages from the spirits. It may take some practice to get the hang of it, but once you do, the possibilities are endless!\r\n\r\nBut remember, just because you are communicating with spirits doesn&apos;t mean they are always friendly. Always approach Ouija boards with caution and respect. Never ask questions that you aren&apos;t prepared to answer and never anger any spirits.\r\n\r\nOne last tip, if you feel uneasy or uncomfortable at any point during the session, immediately end it. Remember, you are in control of the experience and the spirits are guests in your space.', '2024-02-28 08:24:13', 2, 3),
(2, 'Brewing the Perfect Potion: A Step-by-Step Guide', './uploaded_images/54211519ur-_ikx-qpzsc819lofn6y4wdv2ja3btg07_5meh17091090959584.png', 'right', 'First, you&apos;ll need to gather the necessary ingredients. These include rare mushrooms found in enchanted forests, the roots of a special tree that grows near the source of a mystical river, and the juice of a rare fruit found only in the depths of the jungle.\r\n\r\nWith the ingredients gathered, we turn our attention to brewing the potion. First, crush the mushrooms into a fine powder and mix them with the tree roots. Next, add the juice of the fruit and simmer the mixture over a low flame until it thickens. \r\n\r\nAfter the mixture has simmered, carefully strain out the solids and discard them. Pour the liquid into a beautifully decorated bottle or vial, and store it in a cool, dark place until you are ready to use it. When you drink the potion, its effects will be fully realized, providing you with a burst of strength and agility. Voila! The perfect potion.', '2024-02-28 08:31:35', 3, 4),
(3, 'The Ancient Art of Palm Reading', './uploaded_images/564237034-g-uj41smetbl6_2dw_7vqz3pxyf5ikh98o0rnca17091092094655.png', 'left', 'Palmistry, the ancient art of reading the lines and shapes of the hand to divine the future, has a rich and storied history. Originating in ancient India, palmistry was believed to hold the key to understanding the mysteries of the universe. As time passed, it spread throughout the world, finding its way to ancient Greece, China, and even medieval Europe.\r\n\r\nIn modern times, palmistry has evolved beyond a mere parlor trick, becoming a respected and valued form of divination. Today, practitioners study the intricate patterns of the hand, using their knowledge to uncover hidden insights into a person&apos;s personality, life path, and future destiny.\r\n\r\nEven today, palmistry remains a fascinating and mysterious practice, with new techniques and interpretations emerging all the time. From the major lines of the heart, head, and life line to the minor markings that reveal a person&apos;s emotional depth and complexity, every aspect of the hand holds a wealth of information waiting to be unlocked.', '2024-02-28 08:33:29', 1, 3),
(4, 'Divine Deceptions: Exploring Magic in Ancient Greek Mythology', NULL, 'left', 'In Greek mythology, Prometheus was known as the titan who defied the gods and gave humans fire. \r\n\r\nAccording to legend, Prometheus tricked Zeus by presenting him with two sacrificial offerings: one made entirely of bones wrapped in enticing fat, and the other consisting of the tastiest, juiciest meat hidden beneath a layer of unappealing skin. Zeus, blinded by his arrogance, chose the offering of bones and fat, leaving the delicious meat for humanity.\r\n\r\nPrometheus then took the fire from Mount Olympus and gifted it to humans, teaching them how to harness its power for cooking, light, and warmth. The humans were eternally grateful for this divine gift and celebrated Prometheus as a hero.', '2024-02-28 08:35:54', 4, 4),
(5, 'Unlocking the Mysteries of Tarot', './uploaded_images/32466748aphc97br_t1_nqgjeuy83v6x-4wszik2m0-l5ofd17091094870511.png', 'left', 'Let&apos;s talk about tarot. For those who don&apos;t know, tarot cards are a tool used for divination and self-reflection. The deck consists of 78 cards, each depicting a different archetype, symbol or situation. When interpreted correctly, these cards can reveal hidden truths, provide guidance and insight, and even predict future events.\r\n\r\nNow, to truly understand tarot, one must learn to interpret the symbols within each card. Take, for example, the Death card. Despite its ominous name, this card represents transformation and rebirth. It suggests that an old chapter is closing and a new one is about to begin.\r\n\r\nOn the other hand, the Lovers card signifies harmony and balance between opposing forces. It represents relationships, choices, and partnerships. When drawn in a reading, it suggests that a decision must be made with care and consideration.\r\n\r\nTo perform a tarot reading, one must first choose a spread. A simple three-card spread involves drawing the past, present, and future cards. More complex spreads, like the Celtic Cross, involve multiple positions and provide a more comprehensive view of the situation. Once the cards are drawn, one must use intuition and knowledge to interpret the meaning behind them.\r\n\r\nIn conclusion, tarot offers a powerful tool for unlocking the mysteries of life. Whether seeking answers to the unknown or searching for guidance in uncertain times, tarot provides a path forward. With practice and dedication, anyone can harness the power of tarot and discover the secrets hidden within its ancient wisdom.', '2024-02-28 08:38:07', 1, 3),
(6, 'Enchanted Strings: Unveiling the Magical Power of Celtic Music', './uploaded_images/2010854880rw1yepxciahujzftmd0l_o-q45vb-7n2ks9863g_17091099245701.png', 'right', 'In ancient times, the Celts believed that music was a powerful force, capable of influencing both the natural world and the actions of men.\r\n\r\nIt was commonplace for Celts to use music during important events such as battles, funerals, and religious ceremonies. Harpers were highly respected in society and were considered keepers of tradition and culture.\r\n\r\nThe harp was the most popular instrument among the Celts, but other instruments such as flutes and horns were also commonly played. The Celts believed that music had the power to evoke emotions and connect with the divine.\r\n\r\nIn Celtic mythology, there were several gods and goddesses associated with music. Lugh, the god of light, was also considered the god of music and craftsmanship. Another goddess named Morrigan was associated with war and fate, and her voice was said to have been so enchanting that she could lure soldiers to their deaths.\r\n\r\nIn addition to its spiritual significance, Celtic music was often used as a means of storytelling and preserving cultural traditions. Songs were passed down from generation to generation, and they served as a way to maintain a sense of identity and community.', '2024-02-28 08:45:24', 4, 4),
(7, 'Magical Honey Rose Potion: A Recipe for Love and Passion?', './uploaded_images/1236372803c6__is09axyv35u7r8bf1je-nz4w2km-tdhogplq17091101845253.png', 'left', 'This delicious concoction combines the sweetness of honey with the fragrant aroma of rose petals to create a potion that can ignite your senses and enhance your connection with the person you love. Here&apos;s how you can make it:  \r\n\r\nIngredients: \r\n\r\n- 1 cup of honey \r\n- 1 cup of dried rose petals \r\n- 1 cup of distilled water \r\n- 1 teaspoon of cinnamon powder \r\n- 1 teaspoon of vanilla extract \r\n- 1 teaspoon of almond oil  Instructions: \r\n\r\n1. Combine honey, rose petals, and cinnamon powder in a small saucepan. Heat over medium heat, stirring until the mixture becomes fragrant. Remove from heat and set aside. 2. In a separate pan, bring distilled water and vanilla extract to a boil. Reduce heat and simmer for 5 minutes. \r\n\r\n3. Add almond oil to the pan and mix well. \r\n\r\n4. Pour the hot liquid mixture over the honey-rose petal mixture, stirring thoroughly. \r\n\r\n5. Allow the mixture to cool before pouring it into a bottle. Once cooled, you can serve the Magical Honey Rose Potion to your loved one and watch the sparks fly.', '2024-02-28 08:49:44', 3, 3),
(8, 'Elemental Magic: Harnessing the Power of the Classical Elements', './uploaded_images/1736842199wr7lxsd-c93bu_gqtp5jvi4e8on1k_yh6-f0a2mz17091102817163.png', 'right', 'Elemental magic is a type of magic that draws its energy from the four classical elements of earth, air, fire, and water. Practitioners of elemental magic believe that they can manipulate these elements to achieve specific goals or outcomes.\r\n\r\nOf course, not all practitioners have access to all of the elements. Some may only be able to control a single element, while others might have mastery over several. It&apos;s said that true mastery over all four elements is incredibly rare, but those who achieve it are capable of feats that most can only dream of.\r\n\r\nLet&apos;s talk about each element individually. Earth magic is often associated with stability and grounding. Practitioners of earth magic are said to be able to control plants, rocks, and soil. Air magic is associated with change and movement. Those who possess air magic can control the winds and the weather. Fire magic is tied to passion and transformation. Fire mages are said to be able to control flames and heat. Water magic is associated with healing and emotions. Those who wield water magic can control water, ice, and steam.\r\n\r\nElemental magic is both beautiful and dangerous. It requires great skill and patience to master, but those who do are rewarded with incredible powers. If you&apos;re interested in learning elemental magic, remember that it&apos;s not something to be taken lightly. But with practice and dedication, you too can become a master of the elements.', '2024-02-28 08:51:21', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `catID` int(11) NOT NULL,
  `catLabel` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`catID`, `catLabel`) VALUES
(1, 'Divination Practices'),
(2, 'Spells and Rituals'),
(3, 'Magical Recipes'),
(4, 'Magic Folklore');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userFirstName` varchar(256) NOT NULL,
  `userLastName` varchar(256) NOT NULL,
  `userEmail` varchar(256) NOT NULL,
  `userCity` varchar(256) NOT NULL,
  `userPassword` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `userFirstName`, `userLastName`, `userEmail`, `userCity`, `userPassword`) VALUES
(1, 'Ingmar', 'Ehrig', 'a@b.c', 'Berlin', '$2y$10$gTFdJebOXcRjlNLnRfrS7er6J7ksfW/7Q5ZNMqiBnMkiud8ag00tC'),
(2, 'Faylina', 'the Coding Sorceress', 'faylina@codingsorceress.com', 'Coding Realm', '$2y$10$5btl7Z72YflNIv4qnQCwf.i9aWIDRq3/x.go4wUELnutN0rmZ05/.'),
(3, 'Lan', 'Yun', 'lan@yun.com', 'Moon Village', '$2y$10$SxvrHLPlR.YP3DF6DcZ0HujK9id6pWkboYvJin/6E/feDgjLpBa36'),
(4, 'Hiroshi', 'Kim', 'hiroshi@kim.com', 'Star Mountain', '$2y$10$ruFywAsTs59F2a/4FafBRuS0kg7JEq9EJI2hEEK51hViwvcC2BVR6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blogID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`catID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `blogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `catID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
