-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 08, 2017 at 09:43 PM
-- Server version: 5.5.54-0ubuntu0.14.04.1
-- PHP Version: 5.6.23-1+deprecated+dontuse+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `postr`
--
CREATE DATABASE IF NOT EXISTS `postr` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `postr`;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE IF NOT EXISTS `account` (
  `accountId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account` varchar(60) DEFAULT NULL,
  `businessTypeId` int(10) unsigned DEFAULT '0',
  `categoryId` int(10) unsigned DEFAULT '0',
  `creationDate` date DEFAULT NULL,
  `endDate` date DEFAULT NULL,
  `apiKey` varchar(50) NOT NULL,
  `accountStatus` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Signup, 1 - User created, 2- Confirm Email send, 3 - Topic setting 4 - Social Setting, 5 - Signup Newsletter',
  `btCustomerId` varchar(100) DEFAULT '0',
  `btCardtoken` varchar(100) DEFAULT '0',
  `btCreditCardNo` varchar(50) DEFAULT NULL,
  `btExpirationDate` varchar(10) DEFAULT NULL,
  `btCardType` varchar(50) DEFAULT NULL,
  `btSubscriptionId` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `account_category`
--

CREATE TABLE IF NOT EXISTS `account_category` (
  `accountCategotyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned DEFAULT '0',
  `categoryId` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`accountCategotyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `account_frequency`
--

CREATE TABLE IF NOT EXISTS `account_frequency` (
  `accountFrequencyId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned DEFAULT '0',
  `category` varchar(50) DEFAULT 'Autopilot',
  `frequency` varchar(10) DEFAULT '1111111',
  `timezone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`accountFrequencyId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `account_param`
--

CREATE TABLE IF NOT EXISTS `account_param` (
  `id` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` bigint(15) unsigned DEFAULT '0',
  `discountCode` varchar(100) DEFAULT NULL,
  `sid` varchar(100) DEFAULT NULL,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE IF NOT EXISTS `admin_user` (
  `adminUserId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`adminUserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`adminUserId`, `name`, `email`, `password`) VALUES
(1, 'Howard Engelhart', 'howard.engelhart@gmail.com', '$2y$11$UEBTVF9UUkVORCFJTkdfQOL6bo2searFzhywjkIUe2bMBDL4AbNCe'),
(2, 'jglickman', 'jglickman@tribalventures.com', '$2y$11$UEBTVF9UUkVORCFJTkdfQOL6bo2searFzhywjkIUe2bMBDL4AbNCe'),
(3, 'Tony', 'mr.tonymonaco@gmail.com', '$2y$11$UEBTVF9UUkVORCFJTkdfQOL6bo2searFzhywjkIUe2bMBDL4AbNCe'),
(4, 'Dipak Patil', 'dipak14884@gmail.com', '$2y$11$UEBTVF9UUkVORCFJTkdfQOL6bo2searFzhywjkIUe2bMBDL4AbNCe');

-- --------------------------------------------------------

--
-- Table structure for table `article_notify_history`
--

CREATE TABLE IF NOT EXISTS `article_notify_history` (
  `articleNotifyHistoryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned DEFAULT '0',
  `notificationSettingsId` int(10) unsigned DEFAULT '0',
  `trendingArticleId` int(10) unsigned DEFAULT '0',
  `notifyType` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - Email, 2 - Messenger, 3 - Suggested',
  `creationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`articleNotifyHistoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `categoryId` int(10) NOT NULL AUTO_INCREMENT,
  `parentCategoryId` int(10) unsigned DEFAULT '0',
  `category` varchar(100) DEFAULT NULL,
  `categoryType` tinyint(1) unsigned DEFAULT NULL COMMENT '0 - User Type. 1 - Category\n',
  `image` text,
  `fromTime` tinyint(2) unsigned DEFAULT '3' COMMENT 'Time is in hours, Default is 3 hours\n',
  `language` varchar(50) DEFAULT 'en',
  `size` smallint(3) unsigned DEFAULT '150',
  `sortBy` varchar(60) DEFAULT 'fb_likes' COMMENT 'default, fb_likes, fb_shares, fb_comments, fb_total, twitter, linkedin, fb_tw_and_li, nw_score, nw_max_score, created_at.\n',
  `includeKeywords` text,
  `excludeKeywords` text,
  `includePublisher` text,
  `excludePublisher` text,
  `includeCountry` text,
  `excludeCountry` text,
  `includeTopic` text,
  `excludeTopic` text,
  `categoryStatus` tinyint(1) unsigned DEFAULT '0' COMMENT '0 - Active, 1 - Inactive, 2 - Deleted',
  PRIMARY KEY (`categoryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`categoryId`, `parentCategoryId`, `category`, `categoryType`, `image`, `fromTime`, `language`, `size`, `sortBy`, `includeKeywords`, `excludeKeywords`, `includePublisher`, `excludePublisher`, `includeCountry`, `excludeCountry`, `includeTopic`, `excludeTopic`, `categoryStatus`) VALUES
(1, 0, 'Interior', 0, '', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(2, 1, 'Kitchens', 1, 'uploads/category/category220170131161930000000qqSw.jpg', 72, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(3, 1, 'Bathrooms', 1, 'uploads/category/category320170131161946000000Xjem.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(4, 1, 'Home Decor', 1, 'uploads/category/category420170131162009000000rEe5.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(5, 1, 'Bedrooms', 1, 'uploads/category/category520170131162021000000fhsh.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(6, 1, 'Interior Design Master', 1, '', 24, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 1),
(7, 1, 'Furniture', 1, 'uploads/category/category720170131162033000000xTR4.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(8, 1, 'Fixtures', 1, 'uploads/category/category8201701311620440000001HQ9.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(9, 1, 'Living & Dining', 1, 'uploads/category/category920170131162055000000awQB.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0),
(10, 1, 'Patios & Decks', 1, 'uploads/category/category1020170131162106000000A8Oq.jpg', 3, 'en', 150, 'fb_total_engagement', 'home design,home remodel,bathroom remodel,kitchen remodel,home decoration,interior deign,interior designers,bedroom deign,bedroom makeover,kitchen makeover,home furnishing', '', '', '', 'US', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_history`
--

CREATE TABLE IF NOT EXISTS `email_history` (
  `emailHistoryId` bigint(15) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` bigint(10) unsigned DEFAULT '0',
  `creationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`emailHistoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_settings`
--

CREATE TABLE IF NOT EXISTS `notification_settings` (
  `notificationSettingsId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL,
  `receivers` text,
  `period` varchar(100) DEFAULT '09:00',
  `notifyType` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 - Email, 2 - Messenger, 3 - Suggested',
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`notificationSettingsId`),
  KEY `fk_email_notification_account1_idx` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `settingKey` varchar(100) DEFAULT NULL,
  `settingValue` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `social_post`
--

CREATE TABLE IF NOT EXISTS `social_post` (
  `socialPostId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL,
  `message` text,
  `link` text,
  `facebookPostId` varchar(100) DEFAULT '0',
  `twitterPostId` varchar(100) DEFAULT '0',
  `postStatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `validStatus` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `social` int(10) unsigned NOT NULL DEFAULT '0',
  `trendingArticleId` int(10) unsigned NOT NULL DEFAULT '0',
  `articleNotifyHistoryId` int(10) unsigned NOT NULL DEFAULT '0',
  `creationDate` datetime DEFAULT NULL,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`socialPostId`),
  KEY `fk_social_post_account1_idx` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `social_post_metric`
--

CREATE TABLE IF NOT EXISTS `social_post_metric` (
  `socialPostMetricId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL,
  `socialPostId` int(10) unsigned NOT NULL,
  `social` int(10) unsigned DEFAULT '0',
  `socialIncrease` int(10) unsigned NOT NULL DEFAULT '0',
  `fbSocial` int(10) unsigned DEFAULT '0',
  `fbLike` int(10) unsigned DEFAULT '0',
  `fbShare` int(10) unsigned DEFAULT '0',
  `fbComment` int(10) unsigned DEFAULT '0',
  `twLike` int(10) unsigned DEFAULT '0',
  `creationDate` date DEFAULT NULL,
  PRIMARY KEY (`socialPostMetricId`),
  KEY `fk_social_post_metric_account1_idx` (`accountId`),
  KEY `fk_social_post_metric_social_post1_idx` (`socialPostId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `social_profile`
--

CREATE TABLE IF NOT EXISTS `social_profile` (
  `socialProfileId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL,
  `profileType` varchar(45) DEFAULT 'Facebook' COMMENT 'Facebook, Twitter',
  `socialId` varchar(100) DEFAULT NULL,
  `accessToken` text,
  `oauthToken` text,
  `oauthTokenSecret` text,
  `name` varchar(255) DEFAULT NULL,
  `picture` text,
  `category` varchar(100) DEFAULT NULL,
  `creationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`socialProfileId`),
  KEY `fk_social_profile_account1_idx` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `transactionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned DEFAULT '0',
  `btTransactionId` varchar(100) DEFAULT NULL,
  `billingPeriod` varchar(50) DEFAULT NULL,
  `amount` double(10,2) unsigned DEFAULT '0.00',
  `transactionStatus` varchar(45) DEFAULT NULL,
  `creationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`transactionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `trending_article`
--

CREATE TABLE IF NOT EXISTS `trending_article` (
  `trendingArticleId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `postId` varchar(255) NOT NULL DEFAULT '0',
  `category` varchar(100) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `score` double(10,2) unsigned DEFAULT '0.00',
  `caption` text,
  `trendingArticleStatus` tinyint(1) unsigned DEFAULT '0',
  `approveStatus` tinyint(1) unsigned DEFAULT '1',
  `publicationDate` datetime DEFAULT NULL,
  `lastUpdate` datetime DEFAULT NULL,
  PRIMARY KEY (`trendingArticleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `trending_article_category`
--

CREATE TABLE IF NOT EXISTS `trending_article_category` (
  `trendingArticleCategoryId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trendingArticleId` int(10) unsigned DEFAULT '0',
  `categoryId` int(10) unsigned DEFAULT '0',
  `score` int(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`trendingArticleCategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `profile` varchar(255) DEFAULT '',
  `fbId` varchar(100) DEFAULT NULL,
  `senderId` varchar(100) NOT NULL DEFAULT '0',
  `twId` varchar(100) NOT NULL DEFAULT '0',
  `timezone` varchar(100) NOT NULL DEFAULT '',
  `userType` tinyint(1) unsigned DEFAULT '0' COMMENT '0 - User, 1 - Admin\n',
  `userStatus` tinyint(1) unsigned DEFAULT '0' COMMENT '0 - Active, 1 - Inactive',
  `uniqueKey` varchar(45) NOT NULL,
  `lastLoginDate` datetime DEFAULT NULL,
  `uniqueToken` varchar(50) DEFAULT '',
  `tokenValidDate` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`),
  KEY `fk_user_account_idx` (`accountId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_feedback`
--

CREATE TABLE IF NOT EXISTS `user_feedback` (
  `feedbackId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountId` int(10) unsigned DEFAULT '0',
  `userId` int(10) unsigned DEFAULT '0',
  `subject` text,
  `feedback` text,
  `note` text,
  `comment` text,
  `creationDate` datetime DEFAULT NULL,
  PRIMARY KEY (`feedbackId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD CONSTRAINT `fk_email_notification_account1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `social_post`
--
ALTER TABLE `social_post`
  ADD CONSTRAINT `fk_social_post_account1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `social_post_metric`
--
ALTER TABLE `social_post_metric`
  ADD CONSTRAINT `fk_social_post_metric_account1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_social_post_metric_social_post1` FOREIGN KEY (`socialPostId`) REFERENCES `social_post` (`socialPostId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `social_profile`
--
ALTER TABLE `social_profile`
  ADD CONSTRAINT `fk_social_profile_account1` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_account` FOREIGN KEY (`accountId`) REFERENCES `account` (`accountId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
