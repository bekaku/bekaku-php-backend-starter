/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50539
 Source Host           : localhost:3306
 Source Schema         : bekaku_php

 Target Server Type    : MySQL
 Target Server Version : 50539
 File Encoding         : 65001

 Date: 04/05/2020 10:17:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `act_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK_ADMIN_LOG_CREATEDUSER`(`created_user`) USING BTREE,
  INDEX `FK_ADMIN_LOG_UPDATEDUSER`(`updated_user`) USING BTREE,
  CONSTRAINT `FK_ADMIN_LOG_CREATEDUSER` FOREIGN KEY (`created_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_ADMIN_LOG_UPDATEDUSER` FOREIGN KEY (`updated_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for api_client
-- ----------------------------
DROP TABLE IF EXISTS `api_client`;
CREATE TABLE `api_client`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `api_token` char(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `bypass` tinyint(1) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 0,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE,
  INDEX `k_api_token`(`api_token`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of api_client
-- ----------------------------
INSERT INTO `api_client` VALUES (1, 'default', '743777217A25432A462D4A614E645266556A586E3272357538782F413F442847', 0, 1, 1, '2020-03-09 14:06:36', 1, '2020-03-09 14:06:44');

-- ----------------------------
-- Table structure for api_client_ip
-- ----------------------------
DROP TABLE IF EXISTS `api_client_ip`;
CREATE TABLE `api_client_ip`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_client` int(11) NULL DEFAULT NULL,
  `api_address` char(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE,
  INDEX `k_api_client`(`api_client`) USING BTREE,
  INDEX `k_api_address`(`api_address`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of api_client_ip
-- ----------------------------
INSERT INTO `api_client_ip` VALUES (1, 1, 'localhost', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');
INSERT INTO `api_client_ip` VALUES (2, 1, '127.0.0.1', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');
INSERT INTO `api_client_ip` VALUES (3, 1, '::1', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');

-- ----------------------------
-- Table structure for app_permission
-- ----------------------------
DROP TABLE IF EXISTS `app_permission`;
CREATE TABLE `app_permission`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `crud_table` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_permission
-- ----------------------------
INSERT INTO `app_permission` VALUES (1, 'login', 'เข้าสู่ระบบ', '', 1, 1, '2016-01-30 21:04:31', 1, '2018-05-10 16:45:10');
INSERT INTO `app_permission` VALUES (2, 'app_table_list', 'ตารางของโปรแกรม (รายการ)', 'app_table', 1, 1, '2016-01-31 23:39:45', 1, '2016-01-31 23:39:45');
INSERT INTO `app_permission` VALUES (3, 'app_table_add', 'ตารางของโปรแกรม (เพิ่มรายการใหม่)', 'app_table', 1, 1, '2016-01-31 23:39:45', 1, '2016-01-31 23:39:45');
INSERT INTO `app_permission` VALUES (4, 'app_table_edit', 'ตารางของโปรแกรม (แก้ไข)', 'app_table', 1, 1, '2016-01-31 23:39:45', 1, '2016-01-31 23:39:45');
INSERT INTO `app_permission` VALUES (5, 'app_table_delete', 'ตารางของโปรแกรม (ลบ)', 'app_table', 1, 1, '2016-01-31 23:39:45', 1, '2016-01-31 23:39:45');
INSERT INTO `app_permission` VALUES (6, 'app_table_view', 'ตารางของโปรแกรม (ดูข้อมูล)', 'app_table', 1, 1, '2016-01-31 23:39:45', 1, '2016-01-31 23:39:45');
INSERT INTO `app_permission` VALUES (7, 'app_permission_list', 'สิทธิ์การใช้งาน (รายการ)', 'app_permission', 1, 1, '2016-01-31 23:40:24', 1, '2016-01-31 23:40:24');
INSERT INTO `app_permission` VALUES (8, 'app_permission_add', 'สิทธิ์การใช้งาน (เพิ่มรายการใหม่)', 'app_permission', 1, 1, '2016-01-31 23:40:24', 1, '2016-01-31 23:40:24');
INSERT INTO `app_permission` VALUES (9, 'app_permission_edit', 'สิทธิ์การใช้งาน (แก้ไข)', 'app_permission', 1, 1, '2016-01-31 23:40:24', 1, '2016-01-31 23:40:24');
INSERT INTO `app_permission` VALUES (10, 'app_permission_delete', 'สิทธิ์การใช้งาน (ลบ)', 'app_permission', 1, 1, '2016-01-31 23:40:24', 1, '2016-01-31 23:40:24');
INSERT INTO `app_permission` VALUES (11, 'app_permission_view', 'สิทธิ์การใช้งาน (ดูข้อมูล)', 'app_permission', 1, 1, '2016-01-31 23:40:24', 1, '2016-01-31 23:40:24');
INSERT INTO `app_permission` VALUES (12, 'app_user_list', 'ผู้ใช้ระบบ (รายการ)', 'app_user', 1, 1, '2016-01-31 23:41:50', 1, '2016-01-31 23:41:50');
INSERT INTO `app_permission` VALUES (13, 'app_user_add', 'ผู้ใช้ระบบ (เพิ่มรายการใหม่)', 'app_user', 1, 1, '2016-01-31 23:41:50', 1, '2016-01-31 23:41:50');
INSERT INTO `app_permission` VALUES (14, 'app_user_edit', 'ผู้ใช้ระบบ (แก้ไข)', 'app_user', 1, 1, '2016-01-31 23:41:50', 1, '2016-01-31 23:41:50');
INSERT INTO `app_permission` VALUES (15, 'app_user_delete', 'ผู้ใช้ระบบ (ลบ)', 'app_user', 1, 1, '2016-01-31 23:41:50', 1, '2016-01-31 23:41:50');
INSERT INTO `app_permission` VALUES (16, 'app_user_view', 'ผู้ใช้ระบบ (ดูข้อมูล)', 'app_user', 1, 1, '2016-01-31 23:41:50', 1, '2016-01-31 23:41:50');
INSERT INTO `app_permission` VALUES (17, 'app_user_role_list', 'กลุ่มผู้ใช้ระบบ (รายการ)', 'app_user_role', 1, 1, '2016-01-31 23:43:27', 1, '2016-01-31 23:43:27');
INSERT INTO `app_permission` VALUES (18, 'app_user_role_add', 'กลุ่มผู้ใช้ระบบ (เพิ่มรายการใหม่)', 'app_user_role', 1, 1, '2016-01-31 23:43:27', 1, '2016-01-31 23:43:27');
INSERT INTO `app_permission` VALUES (19, 'app_user_role_edit', 'กลุ่มผู้ใช้ระบบ (แก้ไข)', 'app_user_role', 1, 1, '2016-01-31 23:43:27', 1, '2016-01-31 23:43:27');
INSERT INTO `app_permission` VALUES (20, 'app_user_role_delete', 'กลุ่มผู้ใช้ระบบ (ลบ)', 'app_user_role', 1, 1, '2016-01-31 23:43:27', 1, '2016-01-31 23:43:27');
INSERT INTO `app_permission` VALUES (21, 'app_user_role_view', 'กลุ่มผู้ใช้ระบบ (ดูข้อมูล)', 'app_user_role', 1, 1, '2016-01-31 23:43:27', 1, '2016-01-31 23:43:27');
INSERT INTO `app_permission` VALUES (22, 'configuration_edit', 'ตั้งค่าโปรแกรม (แก้ไข)', 'configuration', 1, 1, '2016-01-31 23:45:05', 1, '2016-01-31 23:45:05');
INSERT INTO `app_permission` VALUES (23, 'configuration_view', 'ตั้งค่าโปรแกรม (ดูข้อมูล)', 'configuration', 1, 1, '2016-01-31 23:45:05', 1, '2016-01-31 23:45:05');
INSERT INTO `app_permission` VALUES (24, 'app_geography_list', 'ภาค (รายการ)', 'app_geography', 1, 1, '2016-07-27 16:59:18', 1, '2016-07-27 16:59:18');
INSERT INTO `app_permission` VALUES (25, 'app_geography_add', 'ภาค (เพิ่มรายการใหม่)', 'app_geography', 1, 1, '2016-07-27 16:59:18', 1, '2016-07-27 16:59:18');
INSERT INTO `app_permission` VALUES (26, 'app_geography_edit', 'ภาค (แก้ไข)', 'app_geography', 1, 1, '2016-07-27 16:59:18', 1, '2016-07-27 16:59:18');
INSERT INTO `app_permission` VALUES (27, 'app_geography_delete', 'ภาค (ลบ)', 'app_geography', 1, 1, '2016-07-27 16:59:18', 1, '2016-07-27 16:59:18');
INSERT INTO `app_permission` VALUES (28, 'app_geography_view', 'ภาค (ดูข้อมูล)', 'app_geography', 1, 1, '2016-07-27 16:59:18', 1, '2016-07-27 16:59:18');
INSERT INTO `app_permission` VALUES (29, 'app_province_list', 'จังหวัด (รายการ)', 'app_province', 1, 1, '2016-07-27 17:00:29', 1, '2016-07-27 17:00:29');
INSERT INTO `app_permission` VALUES (30, 'app_province_add', 'จังหวัด (เพิ่มรายการใหม่)', 'app_province', 1, 1, '2016-07-27 17:00:29', 1, '2016-07-27 17:00:29');
INSERT INTO `app_permission` VALUES (31, 'app_province_edit', 'จังหวัด (แก้ไข)', 'app_province', 1, 1, '2016-07-27 17:00:29', 1, '2016-07-27 17:00:29');
INSERT INTO `app_permission` VALUES (32, 'app_province_delete', 'จังหวัด (ลบ)', 'app_province', 1, 1, '2016-07-27 17:00:29', 1, '2016-07-27 17:00:29');
INSERT INTO `app_permission` VALUES (33, 'app_province_view', 'จังหวัด (ดูข้อมูล)', 'app_province', 1, 1, '2016-07-27 17:00:29', 1, '2016-07-27 17:00:29');
INSERT INTO `app_permission` VALUES (34, 'app_amphur_list', 'อำเภอ (รายการ)', 'app_amphur', 1, 1, '2016-07-27 17:01:27', 1, '2016-07-27 17:01:27');
INSERT INTO `app_permission` VALUES (35, 'app_amphur_add', 'อำเภอ (เพิ่มรายการใหม่)', 'app_amphur', 1, 1, '2016-07-27 17:01:27', 1, '2016-07-27 17:01:27');
INSERT INTO `app_permission` VALUES (36, 'app_amphur_edit', 'อำเภอ (แก้ไข)', 'app_amphur', 1, 1, '2016-07-27 17:01:27', 1, '2016-07-27 17:01:27');
INSERT INTO `app_permission` VALUES (37, 'app_amphur_delete', 'อำเภอ (ลบ)', 'app_amphur', 1, 1, '2016-07-27 17:01:27', 1, '2016-07-27 17:01:27');
INSERT INTO `app_permission` VALUES (38, 'app_amphur_view', 'อำเภอ (ดูข้อมูล)', 'app_amphur', 1, 1, '2016-07-27 17:01:27', 1, '2016-07-27 17:01:27');
INSERT INTO `app_permission` VALUES (39, 'app_district_list', 'ตำบล (รายการ)', 'app_district', 1, 1, '2016-07-27 17:01:45', 1, '2016-07-27 17:01:45');
INSERT INTO `app_permission` VALUES (40, 'app_district_add', 'ตำบล (เพิ่มรายการใหม่)', 'app_district', 1, 1, '2016-07-27 17:01:45', 1, '2016-07-27 17:01:45');
INSERT INTO `app_permission` VALUES (41, 'app_district_edit', 'ตำบล (แก้ไข)', 'app_district', 1, 1, '2016-07-27 17:01:45', 1, '2016-07-27 17:01:45');
INSERT INTO `app_permission` VALUES (42, 'app_district_delete', 'ตำบล (ลบ)', 'app_district', 1, 1, '2016-07-27 17:01:45', 1, '2016-07-27 17:01:45');
INSERT INTO `app_permission` VALUES (43, 'app_district_view', 'ตำบล (ดูข้อมูล)', 'app_district', 1, 1, '2016-07-27 17:01:45', 1, '2016-07-27 17:01:45');
INSERT INTO `app_permission` VALUES (63, 'app_user_change_pass_session', 'เปลี่ยนรหัสผ่าน', 'app_user_change_pass_session', 1, 1, '2016-08-02 15:55:38', 1, '2016-08-02 15:55:38');
INSERT INTO `app_permission` VALUES (64, 'shipment_loading_manual', 'Loading Manual', 'shipment_loading_manual', 1, 1, '2018-08-12 09:10:07', 1, '2018-08-12 09:10:07');
INSERT INTO `app_permission` VALUES (65, 'shipment_unloading_manual', 'Unloading Manual', 'shipment_unloading_manual', 1, 1, '2018-08-12 09:10:38', 1, '2018-08-12 09:10:38');

-- ----------------------------
-- Table structure for app_permission_role
-- ----------------------------
DROP TABLE IF EXISTS `app_permission_role`;
CREATE TABLE `app_permission_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE,
  INDEX `k_role`(`role`) USING BTREE,
  INDEX `k_permission`(`permission`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 656 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_permission_role
-- ----------------------------
INSERT INTO `app_permission_role` VALUES (621, 1, 1, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (622, 1, 2, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (623, 1, 3, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (624, 1, 4, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (625, 1, 5, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (626, 1, 6, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (627, 1, 7, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (628, 1, 8, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (629, 1, 9, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (630, 1, 10, 1, '2018-08-12 18:33:44', 1, '2018-08-12 18:33:44');
INSERT INTO `app_permission_role` VALUES (631, 1, 11, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (632, 1, 12, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (633, 1, 13, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (634, 1, 14, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (635, 1, 15, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (636, 1, 16, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (637, 1, 17, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (638, 1, 18, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (639, 1, 19, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (640, 1, 20, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (641, 1, 21, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (642, 1, 22, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (643, 1, 23, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (644, 1, 63, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (645, 1, 64, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (646, 1, 65, 1, '2018-08-12 18:33:45', 1, '2018-08-12 18:33:45');
INSERT INTO `app_permission_role` VALUES (647, 2, 1, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (648, 2, 22, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (649, 2, 23, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (650, 2, 63, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (651, 2, 64, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (652, 2, 65, 1, '2018-08-13 09:46:33', 1, '2018-08-13 09:46:33');
INSERT INTO `app_permission_role` VALUES (653, 3, 1, 1, '2018-08-13 09:49:59', 1, '2018-08-13 09:49:59');
INSERT INTO `app_permission_role` VALUES (654, 3, 22, 1, '2018-08-13 09:49:59', 1, '2018-08-13 09:49:59');
INSERT INTO `app_permission_role` VALUES (655, 3, 23, 1, '2018-08-13 09:49:59', 1, '2018-08-13 09:49:59');

-- ----------------------------
-- Table structure for app_table
-- ----------------------------
DROP TABLE IF EXISTS `app_table`;
CREATE TABLE `app_table`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_table_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `vtheme` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `PK_ID`(`id`) USING BTREE,
  INDEX `FK_APPTABLE_CREATEDUSER`(`created_user`) USING BTREE,
  INDEX `FK_APPTABLE_UPDATEDUSER`(`updated_user`) USING BTREE,
  CONSTRAINT `app_table_ibfk_1` FOREIGN KEY (`created_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `app_table_ibfk_2` FOREIGN KEY (`updated_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for app_user
-- ----------------------------
DROP TABLE IF EXISTS `app_user`;
CREATE TABLE `app_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `login_password` char(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `salt` char(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `status` tinyint(1) NULL DEFAULT NULL,
  `img_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `PK_ID`(`id`) USING BTREE,
  INDEX `k_username`(`username`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE,
  INDEX `k_login_password`(`login_password`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_user
-- ----------------------------
INSERT INTO `app_user` VALUES (1, 'admin', 'admin@yourmail.com', '55be81a4f0b53f445d515474e85bdaf2ae186332ff7424964e73a842caa69db757df3d0a16ca9cc523fb7c1fd6b848b4647cd00302c31bef7105f7675f82600c', 'd54f6c95d3f21288dfc5be75033b6d6bc98cb1fb1f82db6b19f5dacf8c44807509e3d2017cd0b228289b1afb4f3c7f376a1b2c8de8f72f4b2ce1c7065a7310bf', 1, NULL, 1, '2020-03-09 11:06:01', 1, '2020-03-09 09:46:36');

-- ----------------------------
-- Table structure for app_user_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `app_user_access_tokens`;
CREATE TABLE `app_user_access_tokens`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `app_user` int(11) UNSIGNED NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `api_client` int(11) NULL DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `token`) USING BTREE,
  INDEX `app_user_access_tokens_user_id_index`(`app_user`) USING BTREE,
  INDEX `app_user_access_tokens_api_client`(`api_client`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_user_access_tokens
-- ----------------------------
INSERT INTO `app_user_access_tokens` VALUES (4, 'fe7d28371e931e2f1a07818e7f8d4849e91d4efbbf018014a8a2d01080a9318cbb8774a26ae3235c2d316a8e8334b03deb656bb3d724c462c6ca2ecae01c3fc0', 1, 'PostmanRuntime/7.24.1', 1, 0, '2021-05-04 09:05:08', '2020-05-04 09:28:08', '2020-05-04 09:28:08');

-- ----------------------------
-- Table structure for app_user_login
-- ----------------------------
DROP TABLE IF EXISTS `app_user_login`;
CREATE TABLE `app_user_login`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loged_in_date` datetime NULL DEFAULT NULL,
  `loged_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `app_user` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`app_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_user_login
-- ----------------------------
INSERT INTO `app_user_login` VALUES (1, '2020-03-23 07:59:33', '::1', 1);
INSERT INTO `app_user_login` VALUES (2, '2020-03-23 08:14:18', '::1', 1);
INSERT INTO `app_user_login` VALUES (3, '2020-03-23 08:14:38', '::1', 1);
INSERT INTO `app_user_login` VALUES (4, '2020-05-04 09:28:08', '::1', 1);

-- ----------------------------
-- Table structure for app_user_login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `app_user_login_attempts`;
CREATE TABLE `app_user_login_attempts`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_user` int(11) NOT NULL,
  `time` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ip_address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`app_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for app_user_role
-- ----------------------------
DROP TABLE IF EXISTS `app_user_role`;
CREATE TABLE `app_user_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` tinyint(1) NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `PK_ID`(`id`) USING BTREE,
  INDEX `FK_APPUSERROLE_CREATEDUSER`(`created_user`) USING BTREE,
  INDEX `FK_APPUSERROLE_UPDATEDUSER`(`updated_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_user_role
-- ----------------------------
INSERT INTO `app_user_role` VALUES (1, 'developer', 'for me only', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');
INSERT INTO `app_user_role` VALUES (2, 'admin', 'ผู้ดูแลระบบ', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');
INSERT INTO `app_user_role` VALUES (3, 'user', 'User', 1, 1, '2020-03-09 16:43:08', 1, '2020-03-09 16:43:08');

-- ----------------------------
-- Table structure for app_user_role_roles
-- ----------------------------
DROP TABLE IF EXISTS `app_user_role_roles`;
CREATE TABLE `app_user_role_roles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_user` int(11) NOT NULL,
  `app_user_role` int(11) NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `app_user`) USING BTREE,
  UNIQUE INDEX `PK_ID`(`id`) USING BTREE,
  INDEX `FKAPPUSERROLEROLES_APPSUSER`(`app_user`) USING BTREE,
  INDEX `FK_APPUSERROLEROLES_CREATEDUSER`(`created_user`) USING BTREE,
  INDEX `FK_APPUSERROLEROLES_UPDATEDUSER`(`updated_user`) USING BTREE,
  INDEX `FK_APPUSERROLEROLES_APPUSERROLE`(`app_user_role`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of app_user_role_roles
-- ----------------------------
INSERT INTO `app_user_role_roles` VALUES (5, 1, 1, 1, '2016-08-02 09:46:36', 1, '2016-08-02 09:46:36');
INSERT INTO `app_user_role_roles` VALUES (13, 2, 2, 1, '2018-07-12 08:20:29', 1, '2018-07-12 08:20:29');
INSERT INTO `app_user_role_roles` VALUES (27, 3, 2, 3, '2019-04-03 00:33:56', 3, '2019-04-03 00:33:56');
INSERT INTO `app_user_role_roles` VALUES (29, 6, 3, 6, '2019-04-03 17:05:30', 6, '2019-04-03 17:05:30');

-- ----------------------------
-- Table structure for configuration
-- ----------------------------
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `name_eng` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `web` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `phone_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `img_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fax` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `e_mail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `map_latitude` double(20, 6) NULL DEFAULT NULL,
  `map_longtitude` double(20, 6) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `PK_ID`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of configuration
-- ----------------------------
INSERT INTO `configuration` VALUES (1, 'begagu inc.', 'begagu inc.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for user_log
-- ----------------------------
DROP TABLE IF EXISTS `user_log`;
CREATE TABLE `user_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `act_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `img_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `created_user` int(11) NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  `updated_user` int(11) NULL DEFAULT NULL,
  `updated_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK_USER_LOG_CREATEDUSER`(`created_user`) USING BTREE,
  INDEX `FK_USER_LOG_UPDATEDUSER`(`updated_user`) USING BTREE,
  CONSTRAINT `FK_USER_LOG_CREATEDUSER` FOREIGN KEY (`created_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `FK_USER_LOG_UPDATEDUSER` FOREIGN KEY (`updated_user`) REFERENCES `app_user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
