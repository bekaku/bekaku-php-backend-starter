/*
 Navicat Premium Data Transfer

 Source Server         : localhost-docker-mysql8
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : 127.0.0.1:3308
 Source Schema         : php_rest_api

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 08/03/2023 16:08:08
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for access_token
-- ----------------------------
DROP TABLE IF EXISTS `access_token`;
CREATE TABLE `access_token`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_agent` int NULL DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `api_client` bigint NULL DEFAULT NULL,
  `user` bigint NULL DEFAULT NULL,
  `revoked` tinyint(1) NULL DEFAULT 0,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `expires_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK5kmvrg6uuo55il7lx84mimu4f`(`api_client`) USING BTREE,
  INDEX `FKjll8aufysmo6yvf124vsqpd81`(`user`) USING BTREE,
  CONSTRAINT `access_token_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `access_token_ibfk_2` FOREIGN KEY (`api_client`) REFERENCES `api_client` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of access_token
-- ----------------------------
INSERT INTO `access_token` VALUES (3, 1, '9aac4e6d32eb6310bcaa82b62299e8666cba33e6534cf60a12465851e1078c89f6d2d0fd8dffd7151155f4fb636378625ead1c6bd8ad38c44f316fc3ab5f72af', 1, 1, 0, '2023-03-08 14:45:42', '2024-03-08 14:03:42', '2023-03-08 14:45:42');

-- ----------------------------
-- Table structure for api_client
-- ----------------------------
DROP TABLE IF EXISTS `api_client`;
CREATE TABLE `api_client`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `api_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `api_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `by_pass` tinyint(1) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 1,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `updated_user` bigint NULL DEFAULT NULL,
  `created_user` bigint NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of api_client
-- ----------------------------
INSERT INTO `api_client` VALUES (1, 'default', '4480b668766262a3eb1a51945ef5cb0e7faba9032eaecebce1d8227e3403ed564b7bea6ba620b34a47492c81cb5cf252bb32', 1, 1, '2020-04-28 22:07:45', '2020-04-28 22:07:45', 1, 1);
INSERT INTO `api_client` VALUES (2, 'edr', 'aa39d37846ae6e7222081ef415cd6fce30f4f378c46d7eb1bb9c2dd359b1a639c639f2eb1492d02a4965531f62b57d350f77', 1, 1, '2020-05-27 15:38:14', '2020-05-28 10:29:29', 1, 1);

-- ----------------------------
-- Table structure for api_client_ip
-- ----------------------------
DROP TABLE IF EXISTS `api_client_ip`;
CREATE TABLE `api_client_ip`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NULL DEFAULT 1,
  `ip_address` char(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT '1',
  `api_client` bigint NULL DEFAULT NULL,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK5pu9gbj8rvr9gdx27uwua7ug9`(`api_client`) USING BTREE,
  CONSTRAINT `api_client_ip_ibfk_1` FOREIGN KEY (`api_client`) REFERENCES `api_client` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of api_client_ip
-- ----------------------------

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `crud_table` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of permission
-- ----------------------------
INSERT INTO `permission` VALUES (1, 'role_list', 'กลุ่มผู้ใช้งาน(รายการ)', 'role', 1);
INSERT INTO `permission` VALUES (2, 'role_add', 'กลุ่มผู้ใช้งาน(เพิ่ม)', 'role', 1);
INSERT INTO `permission` VALUES (3, 'role_view', 'กลุ่มผู้ใช้งาน(ดู)', 'role', 1);
INSERT INTO `permission` VALUES (4, 'role_edit', 'กลุ่มผู้ใช้งาน(แก้ไข)', 'role', 1);
INSERT INTO `permission` VALUES (5, 'role_delete', 'กลุ่มผู้ใช้งาน(ลบ)', 'role', 1);
INSERT INTO `permission` VALUES (6, 'permission_list', 'สิทธิ์การใช้งาน(รายการ)', 'permission', 1);
INSERT INTO `permission` VALUES (7, 'permission_add', 'สิทธิ์การใช้งาน(เพิ่ม)', 'permission', 1);
INSERT INTO `permission` VALUES (8, 'permission_view', 'สิทธิ์การใช้งาน(ดู)', 'permission', 1);
INSERT INTO `permission` VALUES (9, 'permission_edit', 'สิทธิ์การใช้งาน(แก้ไข)', 'permission', 1);
INSERT INTO `permission` VALUES (10, 'permission_delete', 'สิทธิ์การใช้งาน(ลบ)', 'permission', 1);
INSERT INTO `permission` VALUES (11, 'api_client_list', 'Api client(รายการ)', 'api_client', 1);
INSERT INTO `permission` VALUES (12, 'api_client_add', 'Api client(เพิ่ม)', 'api_client', 1);
INSERT INTO `permission` VALUES (13, 'api_client_view', 'Api client(ดู)', 'api_client', 1);
INSERT INTO `permission` VALUES (14, 'api_client_edit', 'Api client(แก้ไข)', 'api_client', 1);
INSERT INTO `permission` VALUES (15, 'api_client_delete', 'Api client(ลบ)', 'api_client', 1);
INSERT INTO `permission` VALUES (16, 'api_client_ip_list', 'Api client ip(รายการ)', 'api_client_ip', 1);
INSERT INTO `permission` VALUES (17, 'api_client_ip_add', 'Api client ip(เพิ่ม)', 'api_client_ip', 1);
INSERT INTO `permission` VALUES (18, 'api_client_ip_view', 'Api client ip(ดู)', 'api_client_ip', 1);
INSERT INTO `permission` VALUES (19, 'api_client_ip_edit', 'Api client ip(แก้ไข)', 'api_client_ip', 1);
INSERT INTO `permission` VALUES (20, 'api_client_ip_delete', 'Api client ip(ลบ)', 'api_client_ip', 1);
INSERT INTO `permission` VALUES (21, 'access_token_list', 'Token(รายการ)', 'access_token', 1);
INSERT INTO `permission` VALUES (22, 'access_token_add', 'Token(เพิ่ม)', 'access_token', 1);
INSERT INTO `permission` VALUES (23, 'access_token_view', 'Token(ดู)', 'access_token', 1);
INSERT INTO `permission` VALUES (24, 'access_token_edit', 'Token(แก้ไข)', 'access_token', 1);
INSERT INTO `permission` VALUES (25, 'access_token_delete', 'Token(ลบ)', 'access_token', 1);
INSERT INTO `permission` VALUES (26, 'user_list', 'ผู้ใช้ระบบ(รายการ)', 'user', 1);
INSERT INTO `permission` VALUES (27, 'user_add', 'ผู้ใช้ระบบ(เพิ่ม)', 'user', 1);
INSERT INTO `permission` VALUES (28, 'user_view', 'ผู้ใช้ระบบ(ดู)', 'user', 1);
INSERT INTO `permission` VALUES (29, 'user_edit', 'ผู้ใช้ระบบ(แก้ไข)', 'user', 1);
INSERT INTO `permission` VALUES (30, 'user_delete', 'ผู้ใช้ระบบ(ลบ)', 'user', 1);

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `status` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (1, 'Dev', 'Dev role', 1);
INSERT INTO `role` VALUES (2, 'Administrator', 'Admin role', 1);
INSERT INTO `role` VALUES (3, 'User', 'User role', 1);
INSERT INTO `role` VALUES (4, 'Implement', 'Implementer role', 1);
INSERT INTO `role` VALUES (5, 'Support', 'Support role', 1);
INSERT INTO `role` VALUES (7, 'Service', 'Service role', 1);
INSERT INTO `role` VALUES (10, 'Trainee', 'นักศึกษาฝึกงาน', 1);

-- ----------------------------
-- Table structure for role_permission
-- ----------------------------
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission`  (
  `permission` bigint NOT NULL,
  `role` bigint NOT NULL,
  PRIMARY KEY (`permission`, `role`) USING BTREE,
  INDEX `FKgi97nqcoshtqa28hiy11fc8ho`(`role`) USING BTREE,
  CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of role_permission
-- ----------------------------
INSERT INTO `role_permission` VALUES (1, 1);
INSERT INTO `role_permission` VALUES (2, 1);
INSERT INTO `role_permission` VALUES (3, 1);
INSERT INTO `role_permission` VALUES (4, 1);
INSERT INTO `role_permission` VALUES (5, 1);
INSERT INTO `role_permission` VALUES (6, 1);
INSERT INTO `role_permission` VALUES (7, 1);
INSERT INTO `role_permission` VALUES (8, 1);
INSERT INTO `role_permission` VALUES (9, 1);
INSERT INTO `role_permission` VALUES (10, 1);
INSERT INTO `role_permission` VALUES (11, 1);
INSERT INTO `role_permission` VALUES (12, 1);
INSERT INTO `role_permission` VALUES (13, 1);
INSERT INTO `role_permission` VALUES (14, 1);
INSERT INTO `role_permission` VALUES (15, 1);
INSERT INTO `role_permission` VALUES (16, 1);
INSERT INTO `role_permission` VALUES (17, 1);
INSERT INTO `role_permission` VALUES (18, 1);
INSERT INTO `role_permission` VALUES (19, 1);
INSERT INTO `role_permission` VALUES (20, 1);
INSERT INTO `role_permission` VALUES (21, 1);
INSERT INTO `role_permission` VALUES (22, 1);
INSERT INTO `role_permission` VALUES (23, 1);
INSERT INTO `role_permission` VALUES (24, 1);
INSERT INTO `role_permission` VALUES (25, 1);
INSERT INTO `role_permission` VALUES (26, 1);
INSERT INTO `role_permission` VALUES (27, 1);
INSERT INTO `role_permission` VALUES (28, 1);
INSERT INTO `role_permission` VALUES (29, 1);
INSERT INTO `role_permission` VALUES (30, 1);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `salt` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` tinyint(1) NULL DEFAULT 0,
  `created_at` datetime(0) NULL DEFAULT NULL,
  `updated_at` datetime(0) NULL DEFAULT NULL,
  `created_user` bigint NULL DEFAULT NULL,
  `updated_user` bigint NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', 'admin@bekaku.com', NULL, '9354272ca64cccc93cbd2b226266c107b39aaed43e885da90a088527f8a562460fe1941e11c3b30fb69126c487b0f4acd8fbd458889d90c27188753e6d4faa05', '717c428dcdd5aad37fb8be8d830bb4b3abdf54f3186cf026de72c5cf7b069909d1bdcac9fadfc8042b5c6b47267fa9e0e917c315b81cd0d21e78601b9d0d8d2b', 1, '2020-04-27 11:23:19', '2020-06-01 09:18:46', NULL, 1);

-- ----------------------------
-- Table structure for user_agent
-- ----------------------------
DROP TABLE IF EXISTS `user_agent`;
CREATE TABLE `user_agent`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `agent` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user_agent
-- ----------------------------
INSERT INTO `user_agent` VALUES (1, 'PostmanRuntime/7.31.1');

-- ----------------------------
-- Table structure for user_login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `user_login_attempts`;
CREATE TABLE `user_login_attempts`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` bigint NOT NULL,
  `time` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ip_address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_date` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user_login_attempts
-- ----------------------------
INSERT INTO `user_login_attempts` VALUES (5, 1, '1678261466', '::1', '2023-03-08 14:44:26');

-- ----------------------------
-- Table structure for user_login_log
-- ----------------------------
DROP TABLE IF EXISTS `user_login_log`;
CREATE TABLE `user_login_log`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `loged_in_date` datetime(0) NULL DEFAULT NULL,
  `loged_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `user` bigint NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user_login_log
-- ----------------------------
INSERT INTO `user_login_log` VALUES (1, '2023-03-08 14:43:20', '::1', 1);
INSERT INTO `user_login_log` VALUES (2, '2023-03-08 14:45:42', '::1', 1);

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role`  (
  `role` bigint NOT NULL,
  `user` bigint NOT NULL,
  PRIMARY KEY (`role`, `user`) USING BTREE,
  INDEX `FKmnacayuqabmejp7e23rvitaol`(`user`) USING BTREE,
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES (1, 1);

SET FOREIGN_KEY_CHECKS = 1;
