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

 Date: 01/06/2020 16:23:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for access_token
-- ----------------------------
DROP TABLE IF EXISTS `access_token`;
CREATE TABLE `access_token`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_agent` int(11) NULL DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `api_client` bigint(20) NULL DEFAULT NULL,
  `user` bigint(20) NULL DEFAULT NULL,
  `revoked` tinyint(1) NULL DEFAULT 0,
  `created_at` datetime NULL DEFAULT NULL,
  `expires_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK5kmvrg6uuo55il7lx84mimu4f`(`api_client`) USING BTREE,
  INDEX `FKjll8aufysmo6yvf124vsqpd81`(`user`) USING BTREE,
  CONSTRAINT `access_token_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `access_token_ibfk_2` FOREIGN KEY (`api_client`) REFERENCES `api_client` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of access_token
-- ----------------------------
INSERT INTO `access_token` VALUES (1, 1, '7c0106e20ad1418c55dd9fe3f93f8cf10bc11a48fe53139e47a2493528501e0252b9f28a5c01b5a9ce47cc80c0183737af39d5d3086db2bcae10f6a9f9a69686', 1, 1, 0, '2020-06-01 16:03:46', '2021-06-01 16:06:46', '2020-06-01 16:03:46');

-- ----------------------------
-- Table structure for api_client
-- ----------------------------
DROP TABLE IF EXISTS `api_client`;
CREATE TABLE `api_client`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `api_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `api_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `by_pass` tinyint(1) NULL DEFAULT 0,
  `status` tinyint(1) NULL DEFAULT 1,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `updated_user` bigint(11) NULL DEFAULT NULL,
  `created_user` bigint(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

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
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NULL DEFAULT 1,
  `ip_address` char(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '1',
  `api_client` bigint(20) NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `FK5pu9gbj8rvr9gdx27uwua7ug9`(`api_client`) USING BTREE,
  CONSTRAINT `api_client_ip_ibfk_1` FOREIGN KEY (`api_client`) REFERENCES `api_client` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for edr_youtube_mapper
-- ----------------------------
DROP TABLE IF EXISTS `edr_youtube_mapper`;
CREATE TABLE `edr_youtube_mapper`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `active` tinyint(1) NULL DEFAULT NULL,
  `youtube_link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `metadata` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of edr_youtube_mapper
-- ----------------------------
INSERT INTO `edr_youtube_mapper` VALUES (1, 'งานพัฒนาหลักสูตรฯ : การจัดทำตารางเรียน ตารางสอน', 1, 'https://www.youtube.com/watch?v=OnyNRH6Ed1Q', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=OnyNRH6Ed1Q\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e08\\u0e31\\u0e14\\u0e17\\u0e33\\u0e15\\u0e32\\u0e23\\u0e32\\u0e07\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19 \\u0e15\\u0e32\\u0e23\\u0e32\\u0e07\\u0e2a\\u0e2d\\u0e19\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/OnyNRH6Ed1Q\\/maxresdefault.jpg\",\"description\":\"REV.05\"}');
INSERT INTO `edr_youtube_mapper` VALUES (2, 'งานพัฒนาหลักสูตรฯ : การจัดทำแผนการเรียน', 1, 'https://www.youtube.com/watch?v=P1d07M4QDuI', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=P1d07M4QDuI\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e08\\u0e31\\u0e14\\u0e17\\u0e33\\u0e41\\u0e1c\\u0e19\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/P1d07M4QDuI\\/hqdefault.jpg\",\"description\":\"REV.01\"}');
INSERT INTO `edr_youtube_mapper` VALUES (3, 'งานพัฒนาหลักสูตรฯ : การคัดลอกแผนการเรียน', 1, 'https://www.youtube.com/watch?v=8BsPWau1ojk', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=8BsPWau1ojk\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e04\\u0e31\\u0e14\\u0e25\\u0e2d\\u0e01\\u0e41\\u0e25\\u0e30\\u0e1b\\u0e23\\u0e31\\u0e1a\\u0e1b\\u0e23\\u0e38\\u0e07\\u0e41\\u0e1c\\u0e19\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19 REV.01\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/8BsPWau1ojk\\/maxresdefault.jpg\",\"description\":\"#EDR #\\u0e23\\u0e30\\u0e1a\\u0e1a #\\u0e41\\u0e1c\\u0e19\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19 #\\u0e1b\\u0e23\\u0e31\\u0e1a\\u0e1b\\u0e23\\u0e38\\u0e07\\u0e41\\u0e1c\\u0e19\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\"}');
INSERT INTO `edr_youtube_mapper` VALUES (4, 'งานพัฒนาหลักสูตรฯ : การกำหนดแผนการเรียนให้นักศึกษา', 1, 'https://www.youtube.com/watch?v=TdrY9c2SEk8', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=TdrY9c2SEk8\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e01\\u0e33\\u0e2b\\u0e19\\u0e14\\u0e41\\u0e1c\\u0e19\\u0e01\\u0e32\\u0e23\\u0e40\\u0e23\\u0e35\\u0e22\\u0e19\\u0e43\\u0e2b\\u0e49\\u0e19\\u0e31\\u0e01\\u0e28\\u0e36\\u0e01\\u0e29\\u0e32\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/TdrY9c2SEk8\\/hqdefault.jpg\",\"description\":\"REV.01\"}');
INSERT INTO `edr_youtube_mapper` VALUES (5, 'ครูผู้สอน : การสร้างหน่วยการสอนออนไลน์', 1, 'https://www.youtube.com/watch?v=gZDYZzdeoXU', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=gZDYZzdeoXU\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e2a\\u0e23\\u0e49\\u0e32\\u0e07\\u0e2b\\u0e19\\u0e48\\u0e27\\u0e22\\u0e01\\u0e32\\u0e23\\u0e2a\\u0e2d\\u0e19\\u0e2d\\u0e2d\\u0e19\\u0e44\\u0e25\\u0e19\\u0e4c\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/gZDYZzdeoXU\\/hqdefault.jpg\",\"description\":\"REV.02\"}');
INSERT INTO `edr_youtube_mapper` VALUES (7, 'ครูผู้สอน : การกำหนดแบบทดสอบให้กลุ่มที่สอน', 1, 'https://www.youtube.com/watch?v=nFojTdQPz14', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=nFojTdQPz14\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e01\\u0e33\\u0e2b\\u0e19\\u0e14\\u0e0a\\u0e38\\u0e14\\u0e02\\u0e49\\u0e2d\\u0e2a\\u0e2d\\u0e1a\\u0e43\\u0e2b\\u0e49\\u0e01\\u0e31\\u0e1a\\u0e01\\u0e25\\u0e38\\u0e48\\u0e21\\u0e17\\u0e35\\u0e48\\u0e2a\\u0e2d\\u0e19\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/nFojTdQPz14\\/hqdefault.jpg\",\"description\":\" \"}');
INSERT INTO `edr_youtube_mapper` VALUES (8, 'นักศึกษา : การเข้าทำแบบทดสอบออนไลน์', 1, 'https://www.youtube.com/watch?v=qv99HlbY8Oo', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=qv99HlbY8Oo\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e01\\u0e32\\u0e23\\u0e40\\u0e02\\u0e49\\u0e32\\u0e17\\u0e33\\u0e41\\u0e1a\\u0e1a\\u0e17\\u0e14\\u0e2a\\u0e2d\\u0e1a\\u0e2d\\u0e2d\\u0e19\\u0e44\\u0e25\\u0e19\\u0e4c\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/qv99HlbY8Oo\\/hqdefault.jpg\",\"description\":\" \"}');
INSERT INTO `edr_youtube_mapper` VALUES (9, 'ครูผู้สอน : การตรวจสอบคะแนนจากสอบออนไลน์', 1, 'https://www.youtube.com/watch?v=zd7ZOBPSkgs', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=zd7ZOBPSkgs\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e15\\u0e23\\u0e27\\u0e08\\u0e2a\\u0e2d\\u0e1a\\u0e04\\u0e30\\u0e41\\u0e19\\u0e19\\u0e08\\u0e32\\u0e01\\u0e2a\\u0e2d\\u0e1a\\u0e2d\\u0e2d\\u0e19\\u0e44\\u0e25\\u0e19\\u0e4c\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/zd7ZOBPSkgs\\/maxresdefault.jpg\",\"description\":\" \"}');
INSERT INTO `edr_youtube_mapper` VALUES (10, 'ครูผู้สอน : การสร้างแบบทดสอบออนไลน์', 1, 'https://www.youtube.com/watch?v=MjVlRvUDHxY', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=MjVlRvUDHxY\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e2a\\u0e23\\u0e49\\u0e32\\u0e07\\u0e41\\u0e1a\\u0e1a\\u0e17\\u0e14\\u0e2a\\u0e2d\\u0e1a\\u0e2d\\u0e2d\\u0e19\\u0e44\\u0e25\\u0e19\\u0e4c\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/MjVlRvUDHxY\\/hqdefault.jpg\",\"description\":\" \"}');
INSERT INTO `edr_youtube_mapper` VALUES (11, 'ครูผู้สอน : การตรวจสอบการเข้าศึกษาสื่อการสอนออนไลน์', 1, 'https://www.youtube.com/watch?v=_SFWQ9L3xis', '{\"url\":\"https:\\/\\/www.youtube.com\\/watch?v=_SFWQ9L3xis\",\"title\":\"EDR : \\u0e02\\u0e31\\u0e49\\u0e19\\u0e15\\u0e2d\\u0e19\\u0e01\\u0e32\\u0e23\\u0e15\\u0e23\\u0e27\\u0e08\\u0e2a\\u0e2d\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e40\\u0e02\\u0e49\\u0e32\\u0e28\\u0e36\\u0e01\\u0e29\\u0e32\\u0e2a\\u0e37\\u0e48\\u0e2d\\u0e01\\u0e32\\u0e23\\u0e2a\\u0e2d\\u0e19\\u0e2d\\u0e2d\\u0e19\\u0e44\\u0e25\\u0e19\\u0e4c\",\"image\":\"https:\\/\\/i.ytimg.com\\/vi\\/_SFWQ9L3xis\\/maxresdefault.jpg\",\"description\":\" \"}');
INSERT INTO `edr_youtube_mapper` VALUES (12, 'การจัดกลุ่มเรียนนักเรียน-นักศึกษา ', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (13, 'การการตรวจสอบ ปรับปรุงข้อมูลนักศึกษา (เฉพาะข้อมูลประวัติ นศ.)', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (14, 'การปรับสถานภาพนักศึกษา', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (15, 'การอัพโหลดรูปนักเรียน-นักศึกษา', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (16, 'การกำหนดปฏิทินประจำภาคเรียน (วันที่เริ่ม-สิ้นสุดภาคเรียน , วันที่เริ่ม-สิ้นสุดการเช็คชื่อฯ)', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (17, 'การกำหนดปฏิทินวันหยุดราชการและวันหยุดพิเศษ', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (18, 'การประชาสัมพันธ์ข่าว การส่งแจ้งเตือนไปยังแอพพลิเคชั่น', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (19, 'การกำหนดกิจกรรมกลางของสถานศึกษา', 0, NULL, NULL);
INSERT INTO `edr_youtube_mapper` VALUES (20, 'การกำหนดกลุ่มนักศึกษาที่ต้องเช็คชื่อกิจกรรมกลางวิทยาลัย', 0, NULL, NULL);

-- ----------------------------
-- Table structure for edr_youtube_mapper_detail
-- ----------------------------
DROP TABLE IF EXISTS `edr_youtube_mapper_detail`;
CREATE TABLE `edr_youtube_mapper_detail`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edr_youtube_mapper` int(11) NOT NULL,
  `edr_link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_youtube_mapper`(`edr_youtube_mapper`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 431 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of edr_youtube_mapper_detail
-- ----------------------------
INSERT INTO `edr_youtube_mapper_detail` VALUES (197, 2, '/edr/courseOfStudyList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (198, 2, '/edr/courseOfStudyAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (199, 2, '/edr/courseOfStudyView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (200, 2, '/edr/planOfStudyList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (201, 2, '/edr/planOfStudyDragDropAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (202, 2, '/edr/courseOfStudyEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (203, 2, '/edr/courseOfStudyTeachingSubSubject.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (204, 2, '/edr/courseOfStudyCreditDisplay.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (205, 2, '/edr/planOfStudyView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (206, 3, '/edr/courseOfStudyCopyPlanOfStudy.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (207, 3, '/edr/revisePlanList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (208, 3, '/edr/reviseCourseOfStudyAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (209, 3, '/edr/reviseCourseOfStudyView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (210, 3, '/edr/revisePlanOfStudy.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (220, 4, '/edr/courseOfStudyDefaultCourseOfStudy.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (256, 8, '/edr/studentSubjectExamUnitList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (257, 8, '/edr/studentSubjectExamUnitList.do?studentSubjectId=');
INSERT INTO `edr_youtube_mapper_detail` VALUES (258, 8, '/edr/studentSubjectExamViewAnswer.do?teacherSubjectTestId=');
INSERT INTO `edr_youtube_mapper_detail` VALUES (368, 11, '/edr/teacherSubjectContentAssignStudyGroup.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (369, 11, '/edr/teacherSubjectContentStudyGroupList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (370, 11, '/edr/teacherSubjectContentPreviewStudyGroup.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (371, 7, '/edr/teacherSubjectContentTestStudyGroupList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (372, 7, '/edr/teacherSubjectContentAssignStudyGroup.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (373, 7, '/edr/teacherSubjectContentStudyGroupList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (374, 7, '/edr/teacherSubjectContentTestAssignStudyGroup.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (375, 7, '/edr/teacherSubjectContentTestAssignExamUnit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (377, 9, '/edr/teacherSubjectTestSyncScoreGroupList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (378, 9, '/edr/teacherSubjectTestSyncScoreTestList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (379, 9, '/edr/teacherSubjectTestSyncScoreStudentList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (380, 9, '/edr/teacherSubjectTestSyncScoreStudent.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (381, 5, '/edr/teacherSubjectContentSubjectList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (382, 5, '/edr/teacherSubjectContentList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (383, 5, '/edr/teacherSubjectContentAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (384, 5, '/edr/teacherSubjectContentView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (385, 5, '/edr/teacherSubjectContentEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (386, 5, '/edr/teacherSubjectContentUrlAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (387, 5, '/edr/teacherSubjectContentUrlView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (388, 5, '/edr/teacherSubjectContentUrlEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (389, 5, '/edr/teacherSubjectContentStudyGroupList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (399, 10, '/edr/teacherSubjectQuestionSetSubjectList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (400, 10, '/edr/teacherSubjectQuestionSetList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (401, 10, '/edr/teacherSubjectQuestionSetView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (402, 10, '/edr/teacherSubjectQuestionSetEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (403, 10, '/edr/teacherSubjectQuestionView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (404, 10, '/edr/teacherSubjectQuestionEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (405, 10, '/edr/teacherSubjectQuestionChoiceAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (406, 10, '/edr/teacherSubjectQuestionChoiceView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (407, 10, '/edr/teacherSubjectQuestionAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (408, 10, '/edr/teacherSubjectQuestionChoiceEdit.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (423, 1, '/edr/educationTableList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (424, 1, '/edr/generalInformationTeacherTimeTableList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (425, 1, '/edr/generalInformationTeacherTimeTableData.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (426, 1, '/edr/educationTableDetailDirectAdd.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (427, 1, '/edr/educationTableDetailList.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (428, 1, '/edr/educationTableDetailView.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (429, 1, '/edr/educationTimeTable.do');
INSERT INTO `edr_youtube_mapper_detail` VALUES (430, 1, '/edr/educationTableDetailEdit.do');

-- ----------------------------
-- Table structure for permission
-- ----------------------------
DROP TABLE IF EXISTS `permission`;
CREATE TABLE `permission`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `crud_table` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

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
INSERT INTO `permission` VALUES (31, 'edr_youtube_mapper_list', 'ลิ้งค์ยูทูป(รายการ)', 'edr_youtube_mapper', 1);
INSERT INTO `permission` VALUES (32, 'edr_youtube_mapper_add', 'ลิ้งค์ยูทูป(เพิ่ม)', 'edr_youtube_mapper', 1);
INSERT INTO `permission` VALUES (33, 'edr_youtube_mapper_view', 'ลิ้งค์ยูทูป(ดู)', 'edr_youtube_mapper', 1);
INSERT INTO `permission` VALUES (34, 'edr_youtube_mapper_edit', 'ลิ้งค์ยูทูป(แก้ไข)', 'edr_youtube_mapper', 1);
INSERT INTO `permission` VALUES (35, 'edr_youtube_mapper_delete', 'ลิ้งค์ยูทูป(ลบ)', 'edr_youtube_mapper', 1);

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `status` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (1, 'Dev', 'Dev role', 1);
INSERT INTO `role` VALUES (2, 'Administrator', 'Admin role', 1);
INSERT INTO `role` VALUES (3, 'User', 'User role', 1);
INSERT INTO `role` VALUES (4, 'Implement', 'Implementer role', 1);
INSERT INTO `role` VALUES (5, 'Support', 'Support role', 1);
INSERT INTO `role` VALUES (7, 'Service', 'Service role', 1);
INSERT INTO `role` VALUES (10, 'Trainee', 'Trainee role', 1);

-- ----------------------------
-- Table structure for role_permission
-- ----------------------------
DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission`  (
  `permission` bigint(20) NOT NULL,
  `role` bigint(20) NOT NULL,
  PRIMARY KEY (`permission`, `role`) USING BTREE,
  INDEX `FKgi97nqcoshtqa28hiy11fc8ho`(`role`) USING BTREE,
  CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

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
INSERT INTO `role_permission` VALUES (31, 1);
INSERT INTO `role_permission` VALUES (32, 1);
INSERT INTO `role_permission` VALUES (33, 1);
INSERT INTO `role_permission` VALUES (34, 1);
INSERT INTO `role_permission` VALUES (35, 1);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(1) NULL DEFAULT 0,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `created_user` bigint(20) NULL DEFAULT NULL,
  `updated_user` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_created_user`(`created_user`) USING BTREE,
  INDEX `k_updated_user`(`updated_user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'admin', 'admin@bekaku.com', NULL, '667b1173c2b9890884bc6924ddfdd83d019c852ca312cbd8d933ba87fee85003cc6a4c0fe59450ddd12c7c1e0908883e847ab1babd499806219fdeb108ded7bf', '94bd93851c4437d96dd21a38e682973c8b6750422667e7fa2cc85d69b6a0406a5263a1e30d2387fb36f90625da184cd83722c011f80b7d9a2de3366b338e5e3b', 1, '2020-04-27 11:23:19', '2020-06-01 09:18:46', NULL, 1);

-- ----------------------------
-- Table structure for user_agent
-- ----------------------------
DROP TABLE IF EXISTS `user_agent`;
CREATE TABLE `user_agent`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user_login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `user_login_attempts`;
CREATE TABLE `user_login_attempts`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) NOT NULL,
  `time` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `ip_address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_date` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user_login_log
-- ----------------------------
DROP TABLE IF EXISTS `user_login_log`;
CREATE TABLE `user_login_log`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loged_in_date` datetime NULL DEFAULT NULL,
  `loged_ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `user` bigint(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `k_app_user`(`user`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user_role
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role`  (
  `role` bigint(20) NOT NULL,
  `user` bigint(20) NOT NULL,
  PRIMARY KEY (`role`, `user`) USING BTREE,
  INDEX `FKmnacayuqabmejp7e23rvitaol`(`user`) USING BTREE,
  CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of user_role
-- ----------------------------
INSERT INTO `user_role` VALUES (1, 1);

SET FOREIGN_KEY_CHECKS = 1;
