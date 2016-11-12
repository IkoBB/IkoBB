INSERT INTO `iko_modules` VALUES ('IkoBB','iko','Core Engine','1.0.0a',1),
('IkoBB','forum','Forum Engine','1.0.0a',1),
('IkoBB','cms','Template Engine','1.0.0a',1),
('IkoBB','user','User Engine','1.0.0a',1),
('IkoBB','language','Language Engine','1.0.0a',1);


INSERT INTO `iko_configs` VALUES ('site_name', 's:10:"Test Value";', 'The name of the site', 'iko'),
	('site_template', "i:1;", 'Insert the ID of the template which should be the default template.', 'cms'),
	('site_email', 's:13:"test@test.com";', 'The contact email of the forum. Also used for sending emails.', 'iko'),
	('site_maintenance', "i:0;", 'Indicates if the site is maintenance. 1 - Maintenance Mode on; 2 - Maintenance Mode off','iko'),
	('date_format', 's9:"d-m-Y H:i";', 'Default date format', 'iko');;



INSERT INTO `iko_templates` (`template_name`, `template_author`, `template_version`, `template_directory`, `template_required_core_version`)
VALUES ('Default Template', 'IkoBB', '0.0.1', 'default', '1.0.0a');

INSERT INTO `iko_users` (`user_id`, `user_name`, `user_password`, `user_email`, `user_avatar_id`, `user_signature`, `user_about_user`, `user_location_id`, `user_gender`, `user_date_joined`, `user_birthday`, `user_timezone_id`, `user_last_login`, `user_language`, `user_template`)
VALUES
	(1, 'Administrator', '54d5ace062310f6f617d5f29d70ceb457441b1bbd3869334c9753ab68c8d0969', 'admin@ikobb.de', 1,
		'signature', 'about me', 1, 1, 11021996, '0000-00-00', 1, 1478377625, 'german', 1);


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

INSERT INTO `iko_permissions` VALUES ('*', 'iko', 'Can do what they want to do.'),
	('iko.admin.user.delete', 'iko', 'Have the opportunity to delete an user'),
	('iko.user.change.user_name', 'user', 'Change the Username'),
	('iko.user.change.user_email', 'user', 'Change the User Email address');

INSERT INTO `iko_user_permissions` VALUES ('1', '*'),
	('1', 'iko.admin.user.delete');

INSERT INTO `iko_group_permissions` VALUES ('1', '*'),
	('1', 'iko.admin.user.delete');


INSERT INTO `iko_translation` VALUES ('user_name','Benutzername','username'),
('password','Kennwort','password');