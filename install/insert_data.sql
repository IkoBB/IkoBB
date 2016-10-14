INSERT INTO `iko_modules` VALUES ('IkoBB','iko','Core Engine','1.0.0a',1),
('IkoBB','Error','Error Engine','1.0.0a',1),
('IkoBB','email','Email Engine','1.0.0a',1),
('IkoBB','emote','Emote Engine','1.0.0a',1);


INSERT INTO `iko_configs` VALUES ('site_name', 's:10:"Test Value";', 'The name of the site', 'iko'),
	('site_template', "i:1;", 'Insert the ID of the template which should be the default template.', 'iko'),
	('site_email', 's:13:"test@test.com";', 'The contact email of the forum. Also used for sending emails.', 'iko'),
	('site_maintenance', "i:0;", 'Indicates if the site is maintenance. 1 - Maintenance Mode on; 2 - Maintenance Mode off','iko'),
	('error_test', "qee", 'Shows an error code','Error'),
	('error_email', 'p.w@ikobb.de', 'Enables ikoBB email adress','email'),
	('glitchy_emote', 'loading_emote', 'Loads an funny glitchy emote.','emote')
	;

INSERT INTO `iko_templates` (`template_name`, `template_author`, `template_version`, `template_directory`, `template_required_core_version`)
VALUES ('Default Template', 'IkoBB', '0.0.1', 'default', '1.0.0a'),
('Purple Unicorn Template', 'Flipper', '0.1.1', 'purple_unicorn', '1.0.0a'),
('Default Extreme', 'IkoBB', '0.2.1e', 'default_extreme', '11.33.10333b'),
('Default Sunny', 'Sunny', '0.0.1', 'sunny', '1.0.0a'),
('Default Clown', 'IkoBBclown', '0.0.1', 'default_clown', '1.0.0a');

INSERT INTO `iko_users` VALUES ('1','Pascal','abcde123123','pascal@ikobb.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1'),
('2','Peter','asdasdas','asd@ikobb.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1'),
('3','Susi','abcdfdfghghfe123123','pascal@asddd.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1'),
('4','Unicorn123','fghfghfgh','pascal@asdasdasdas.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1'),
('5','Helga','abcde12hjkjhkhj3123','asdasdasdasd@@kobb.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1'),
('6','Dieter','öäölähjkhjgfhfg!','pascal@asdaaaaaa.de','1','I´m over the top.','I´m the best. Everywhere and in everything.','1','1','11021996','12.09.1998','1');

INSERT INTO `iko_usergroups` VALUES ('1','Admin','blue'),
('2','Moderator','black'),
('3','User','green'),
('4','Noobs','red'),
('5','Unicorns','rainbow'),
('6','Co-Founder','big');

INSERT INTO `iko_user_assignment` VALUES ('1','1'),
('1','5'),
('1','4'),
('3','1'),
('2','3'),
('2','5'),
('4','5'),
('5','5'),
('6','5'),
('6','4'),
('4','3'),
('3','2'),
('1','1');


INSERT INTO `iko_group_assignment` VALUES ('1','2'),
('3','1'),
('1','4');

/*
INSERT INTO `iko_template_assignment` VALUES ('1','2'),
('3','1'),
('1','4');
*/

INSERT INTO `iko_permissions` VALUES ('controll_everything','iko','Can do what they want to do.'),
('delete_user','iko','Have the opportunity to delete an user');

INSERT INTO `iko_user_permissions` VALUES ('1','controll_everything'),
('1','delete_user');

INSERT INTO `iko_group_permissions` VALUES ('1','controll_everything'),
('1','delete_user');
