-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: shop
-- ------------------------------------------------------
-- Server version	5.7.22-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'后台主页','fa-bar-chart','/',NULL,NULL,'2019-02-25 08:09:40'),(2,0,7,'Admin','fa-tasks','',NULL,NULL,'2019-02-25 08:11:32'),(3,2,8,'Users','fa-users','auth/users',NULL,NULL,'2019-02-25 08:11:32'),(4,2,9,'Roles','fa-user','auth/roles',NULL,NULL,'2019-02-25 08:11:32'),(5,2,10,'Permission','fa-ban','auth/permissions',NULL,NULL,'2019-02-25 08:11:32'),(6,2,11,'Menu','fa-bars','auth/menu',NULL,NULL,'2019-02-25 08:11:32'),(7,2,12,'Operation log','fa-history','auth/logs',NULL,NULL,'2019-02-25 08:11:33'),(8,0,2,'用户管理','fa-users','/users',NULL,'2019-02-25 07:26:42','2019-02-25 07:26:48'),(9,0,3,'商品管理','fa-cubes','/products',NULL,'2019-02-25 07:27:16','2019-02-25 07:27:19'),(10,0,4,'订单管理','fa-money','/orders',NULL,'2019-02-25 07:28:05','2019-02-25 07:28:09'),(11,0,5,'优惠券管理','fa-tags','/coupons',NULL,'2019-02-25 07:28:25','2019-02-25 07:28:55'),(12,0,6,'系统日志','fa-book','/logs',NULL,'2019-02-25 08:11:19','2019-02-25 08:11:32');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL),(6,'用户管理权限','users','','/users*','2019-02-25 07:32:41','2019-02-25 07:33:25'),(7,'商品管理权限','produtcs','','/products*','2019-02-25 07:33:16','2019-02-25 07:33:16'),(8,'优惠券管理权限','coupons','','/coupons*','2019-02-25 07:33:47','2019-02-25 07:33:47'),(9,'订单管理权限','orders','','/orders*','2019-02-25 07:34:07','2019-02-25 07:34:07');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(2,3,NULL,NULL),(2,4,NULL,NULL),(2,6,NULL,NULL),(3,2,NULL,NULL),(3,3,NULL,NULL),(3,4,NULL,NULL),(3,7,NULL,NULL),(4,2,NULL,NULL),(4,3,NULL,NULL),(4,4,NULL,NULL),(4,9,NULL,NULL),(5,2,NULL,NULL),(5,3,NULL,NULL),(5,4,NULL,NULL),(5,8,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2019-02-25 07:26:14','2019-02-25 07:26:14'),(2,'用户管理猿','users_operator','2019-02-25 07:35:06','2019-02-25 07:35:06'),(3,'商品管理猿','products_operator','2019-02-25 07:36:05','2019-02-25 07:36:05'),(4,'订单管理猿','orders_operator','2019-02-25 07:36:31','2019-02-25 07:36:31'),(5,'优惠券管理猿','coupons_operator','2019-02-25 07:36:52','2019-02-25 07:36:52');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$/IVH3Dk9QlCQLtmJYgfgoe59lYEI2lfWh61np.sxcwXWjF0TUyWbS','Administrator',NULL,'7kCw9dq5ZMBeYbtqcuvmOia06hD4rTZUqZ3cMCtG1vfaiR858lyrBk0AuPeZ','2019-02-25 07:26:14','2019-02-25 07:26:14'),(2,'operator_one','$2y$10$7hnTXSjEPEMCj1t/PgcSNOQjNiZ/NWF3DnyW6rseoOaGLSF1JcRoy','operator_one','images/IMG_2313.JPG','q6GgXZFiEaMRRG2M5OH1IGncJsin5cA3EfloV4IVTr97b3COUkWY1I20naFg','2019-02-25 08:05:45','2019-02-25 08:05:45');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-02-25 16:35:47
