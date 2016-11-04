INSERT INTO `iko_modules` VALUES ('IkoBB','iko','Core Engine','1.0.0a',1),
('IkoBB','forum','Forum Engine','1.0.0a',1),
('IkoBB','cms','Template Engine','1.0.0a',1),
('IkoBB','user','User Engine','1.0.0a',1),
('IkoBB','language','Language Engine','1.0.0a',1);


INSERT INTO `iko_configs` VALUES ('site_name', 's:10:"Test Value";', 'The name of the site', 'iko'),
	('site_template', "i:1;", 'Insert the ID of the template which should be the default template.', 'iko'),
	('site_email', 's:13:"test@test.com";', 'The contact email of the forum. Also used for sending emails.', 'iko'),
	('site_maintenance', "i:0;", 'Indicates if the site is maintenance. 1 - Maintenance Mode on; 2 - Maintenance Mode off','iko');

INSERT INTO `iko_templates` (`template_name`, `template_author`, `template_version`, `template_directory`, `template_required_core_version`)
VALUES ('Default Template', 'IkoBB', '0.0.1', 'default', '1.0.0a');

INSERT INTO `iko_users` VALUES ('1','Administrator','admin_password','admin@ikobb.de','1','','','1','1','11021996','12.09.1998','1','');

INSERT INTO `iko_usergroups` VALUES ('1','Admin','<span class="color: red; ">%% group_name %%</span>'),
('2','Moderator','<span class="color: green; ">%% group_name %%</span>'),
('3','User','%% group_name %%');

INSERT INTO `iko_user_assignment` VALUES ('1','1');

/*
INSERT INTO `iko_group_assignment` VALUES ('1','2'),
('3','1'),
('1','4');*/

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


INSERT INTO `iko_translation` VALUES ('user_name','Benutzername','username'),
('password','Kennwort','password');