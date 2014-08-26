<?
$updater->CopyFiles("install/admin", "admin");
$updater->CopyFiles("install/components", "components");
$updater->CopyFiles("install/js", "js");
$updater->CopyFiles("install/themes", "themes");

//2. Add column to b_user for registration confirmation
if($updater->TableExists("b_user") || $updater->TableExists("B_USER"))
{
	if(!$DB->Query("SELECT CONFIRM_CODE from b_user WHERE 1=0", true))
	{
		$updater->Query(array(
			"MySql"  => "alter table b_user add CONFIRM_CODE varchar(8)",
			"MsSql"  => "alter table b_user add CONFIRM_CODE varchar(8)",
			"Oracle" => "alter table b_user add CONFIRM_CODE varchar2(8)",
		));
	}
//3. create table b_event_log
	if(!$DB->Query("SELECT ID from b_event_log WHERE 1=0", true))
	{
		$updater->Query(array(
			"MySql"  => "CREATE TABLE b_event_log
				(
					ID INT(18) NOT NULL auto_increment,
					TIMESTAMP_X TIMESTAMP not null,
					SEVERITY VARCHAR(50) not null,
					AUDIT_TYPE_ID VARCHAR(50) not null,
					MODULE_ID VARCHAR(50) not null,
					ITEM_ID VARCHAR(255) not null,
					REMOTE_ADDR VARCHAR(15),
					USER_AGENT TEXT,
					REQUEST_URI TEXT,
					SITE_ID CHAR(2),
					USER_ID INT(18),
					GUEST_ID INT(18),
					DESCRIPTION TEXT,
					PRIMARY KEY (ID),
					index ix_b_event_log_time(TIMESTAMP_X)
				)
			",
			"MsSql"  => "CREATE TABLE B_EVENT_LOG
				(
					ID INT NOT NULL IDENTITY (1,1),
					TIMESTAMP_X  DATETIME null,
					SEVERITY VARCHAR(50) not null,
					AUDIT_TYPE_ID VARCHAR(50) not null,
					MODULE_ID VARCHAR(50) not null,
					ITEM_ID VARCHAR(255) not null,
					REMOTE_ADDR VARCHAR(15),
					USER_AGENT VARCHAR(2000),
					REQUEST_URI VARCHAR(2000),
					SITE_ID CHAR(2),
					USER_ID INT,
					GUEST_ID INT,
					DESCRIPTION TEXT
				)
			",
			"Oracle" => "CREATE TABLE b_event_log
				(
					ID NUMBER(18) NOT NULL,
					TIMESTAMP_X DATE default sysdate not null,
					SEVERITY VARCHAR2(50) not null,
					AUDIT_TYPE_ID VARCHAR2(50) not null,
					MODULE_ID VARCHAR2(50) not null,
					ITEM_ID VARCHAR2(255) not null,
					REMOTE_ADDR VARCHAR2(15),
					USER_AGENT VARCHAR2(2000),
					REQUEST_URI VARCHAR2(2000),
					SITE_ID CHAR(2),
					USER_ID NUMBER(18),
					GUEST_ID NUMBER(18),
					DESCRIPTION CLOB,
					PRIMARY KEY (ID)
				)
			",
		));
		$updater->Query(array(
			"MsSql"  => "ALTER TABLE B_EVENT_LOG ADD CONSTRAINT PK_B_EVENT_LOG PRIMARY KEY (ID)",
		));
		$updater->Query(array(
			"MsSql"  => "CREATE INDEX IX_B_EVENT_LOG_TIME ON B_EVENT_LOG(TIMESTAMP_X)",
			"Oracle" => "CREATE INDEX ix_b_event_log_time ON b_event_log(TIMESTAMP_X)",
		));
		$updater->Query(array(
			"MsSql"  => "ALTER TABLE B_EVENT_LOG ADD CONSTRAINT DF_B_EVENT_LOG_TIMESTAMP_X DEFAULT GETDATE() FOR TIMESTAMP_X",
			"Oracle" => "CREATE SEQUENCE SQ_b_event_log",
		));
		$updater->Query(array(
			"MsSql"  => "create trigger B_EVENT_LOG_UPDATE on B_EVENT_LOG for update as
if (not update(TIMESTAMP_X))
begin
	UPDATE B_EVENT_LOG SET
		TIMESTAMP_X = GETDATE()
	FROM
		B_EVENT_LOG U,
		INSERTED I,
		DELETED D
	WHERE
		U.ID = I.ID
		and U.ID = D.ID
end

if @@error <>0
begin
	raiserror('Trigger B_EVENT_LOG_UPDATE Error', 16, 1)
end",
			"Oracle" => "CREATE OR REPLACE TRIGGER b_event_log_insert
BEFORE INSERT
ON b_event_log
FOR EACH ROW
BEGIN
	IF :NEW.ID IS NULL THEN
 		SELECT sq_b_event_log.NEXTVAL INTO :NEW.ID FROM dual;
	END IF;
END;",
		));
	}

	//4. add operation:
	$rs = $DB->Query("select * from b_operation where NAME='view_event_log' AND MODULE_ID='main' AND BINDING='module'");
	if(!$rs->Fetch())
	{
		$DB->Query("insert into b_operation (NAME,MODULE_ID,DESCRIPTION,BINDING) values('view_event_log','main',null,'module')");
		$DB->Query("insert into b_task_operation
			select t.ID,o.ID
			from b_task t
			,b_operation o
			where
			o.NAME = 'view_event_log' and o.MODULE_ID='main' and o.BINDING='module'
			and t.NAME = 'main_full_access' and t.LETTER='W' and t.MODULE_ID='main' and t.SYS='Y' and t.binding='module'
		");
	}

	//5. e-mail templates for registration confirmation
	include($_SERVER["DOCUMENT_ROOT"].$updater->curPath."/ru/index.php");
	include($_SERVER["DOCUMENT_ROOT"].$updater->curPath."/en/index.php");
}
?>