--
-- Create the new table for MFA
--
CREATE TABLE IF NOT EXISTS "#__user_mfa" (
  "id" serial NOT NULL,
  "user_id" bigint NOT NULL,
  "title" varchar(255) DEFAULT '' NOT NULL,
  "method" varchar(100) NOT NULL,
  "default" smallint DEFAULT 0 NOT NULL,
  "options" text NOT NULL,
  "created_on" timestamp without time zone NOT NULL,
  "last_used" timestamp without time zone,
  PRIMARY KEY ("id")
);

CREATE INDEX "#__user_mfa_idx_user_id" ON "#__user_mfa" ("user_id") /** CAN FAIL **/;

COMMENT ON TABLE "#__user_mfa" IS 'Multi-factor Authentication settings';

--
-- Remove obsolete postinstallation message
--
DELETE FROM "#__postinstall_messages" WHERE "condition_file" = 'site://plugins/twofactorauth/totp/postinstall/actions.php';

--
-- Add new MFA plugins
--
INSERT INTO "#__extensions" ("package_id", "name", "type", "element", "folder", "client_id", "enabled", "access", "protected", "locked", "manifest_cache", "params", "custom_data", "ordering", "state") VALUES
(0, 'plg_multifactorauth_totp', 'plugin', 'totp', 'multifactorauth', 0, 0, 1, 0, 1, '', '', '', 1, 0),
(0, 'plg_multifactorauth_yubikey', 'plugin', 'yubikey', 'multifactorauth', 0, 0, 1, 0, 1, '', '', '', 2, 0),
(0, 'plg_multifactorauth_webauthn', 'plugin', 'webauthn', 'multifactorauth', 0, 0, 1, 0, 1, '', '', '', 3, 0),
(0, 'plg_multifactorauth_email', 'plugin', 'email', 'multifactorauth', 0, 0, 1, 0, 1, '', '', '', 4, 0),
(0, 'plg_multifactorauth_fixed', 'plugin', 'fixed', 'multifactorauth', 0, 0, 1, 0, 1, '', '', '', 5, 0);

--
-- Update MFA plugins' publish status
--
UPDATE "#__extensions" AS "a"
SET "enabled" = "b"."enabled"
FROM "#__extensions" AS "b"
WHERE "a"."element" = "b"."element"
	AND "a"."folder" = 'multifactorauth'
	AND "b"."folder" = 'twofactorauth';

--
-- Remove legacy TFA plugins
--
DELETE FROM "#__extensions"
WHERE "type" = 'plugin' AND "folder" = 'twofactorauth' AND "element" IN ('totp', 'yubikey');

--
-- Add post-installation message
--
INSERT INTO "#__postinstall_messages" ("extension_id", "title_key", "description_key", "action_key", "language_extension", "language_client_id", "type", "action_file", "action", "condition_file", "condition_method", "version_introduced", "enabled")
SELECT "extension_id", 'COM_USERS_POSTINSTALL_MULTIFACTORAUTH_TITLE', 'COM_USERS_POSTINSTALL_MULTIFACTORAUTH_BODY', 'COM_USERS_POSTINSTALL_MULTIFACTORAUTH_ACTION', 'com_users', 1, 'action', 'admin://components/com_users/postinstall/multifactorauth.php', 'com_users_postinstall_mfa_action', 'admin://components/com_users/postinstall/multifactorauth.php', 'com_users_postinstall_mfa_condition', '4.2.0', 1 FROM "#__extensions" WHERE "name" = 'files_joomla'
ON CONFLICT DO NOTHING;

--
-- Create a mail template for plg_multifactorauth_email
--
INSERT INTO "#__mail_templates" ("template_id", "extension", "language", "subject", "body", "htmlbody", "attachments", "params") VALUES
('plg_multifactorauth_email.mail', 'plg_multifactorauth_email', '', 'PLG_MULTIFACTORAUTH_EMAIL_EMAIL_SUBJECT', 'PLG_MULTIFACTORAUTH_EMAIL_EMAIL_BODY', '', '', '{"tags":["code","sitename","siteurl","username","email","fullname"]}')
ON CONFLICT DO NOTHING;
