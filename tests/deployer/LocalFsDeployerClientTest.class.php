<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class LocalFsDeployerClientTest extends LTestCase {
	
	const TEST_DIR = "tests";

	private function initEmptyServer() {
		$deployer_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/');
		$deployer_dir->delete(true);
		$deployer_dir->touch();

		$source_deployer_file = new LFile($_SERVER['FRAMEWORK_DIR'].'tools/deployer.php');

		$target_deployer_file = $deployer_dir->newFile('deployer.php');

		$source_deployer_file->copy($target_deployer_file);
	}

	private function initEmptyFakeProject() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

	}

	private function initAll() {
		$this->initEmptyFakeProject();
		$this->initEmptyServer();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);

		$deployer_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/');
		$deployer_dir->delete(true);
		$deployer_dir->touch();

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');
		$backup_save_dir->delete(true);
		$backup_save_dir->touch();
	}

	private function isSuccess($result) {
		if ($result===true) return true;
		else return false;
	}

	private function getErrorMessage($result) {
		if (is_array($result)) return $result['message'];
		else return '';
	}

	//ok
	function testAddRemoveListIgnore() {
		$this->initAll();

		$dc = new LDeployerClient();

		$ignore_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.ignore_list');

		if ($ignore_file->exists()) $ignore_file->delete();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$custom_ignore_list = $dc->print_ignore_list("default_key");

		$this->assertTrue(empty($custom_ignore_list),"L'ignore list non è vuota come dovrebbe essere!");

		$dc->add_to_ignore_list("default_key","qualcosa/");

		$custom_ignore_list = $dc->print_ignore_list("default_key");

		$this->assertTrue(count($custom_ignore_list)==1,"L'ignore list non è vuota come dovrebbe essere!");

		$this->assertTrue($custom_ignore_list[0],"qualcosa/","L'elemento nella ignore list non corrisponde!");

		$dc->rm_from_ignore_list("default_key","ahhh");

		$dc->add_to_ignore_list("default_key","qualcosa/");

		$custom_ignore_list = $dc->print_ignore_list("default_key");

		$this->assertTrue(count($custom_ignore_list)==1,"L'ignore list non è vuota come dovrebbe essere!");

		$this->assertTrue($custom_ignore_list[0],"qualcosa/","L'elemento nella ignore list non corrisponde!");

		$dc->add_to_ignore_list("default_key","qualcosa/");

		$custom_ignore_list = $dc->print_ignore_list("default_key");

		$this->assertTrue(count($custom_ignore_list)==1,"L'ignore list non è vuota come dovrebbe essere!");

		$this->assertTrue($custom_ignore_list[0],"qualcosa/","L'elemento nella ignore list non corrisponde!");

		$dc->rm_from_ignore_list("default_key","qualcosa/");

		$custom_ignore_list = $dc->print_ignore_list("default_key");

		$this->assertTrue(empty($custom_ignore_list),"L'ignore list non è vuota come dovrebbe essere!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

		if ($ignore_file->exists()) $ignore_file->delete();

    }
	
	//ok
	function testGetExecMode() {
		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->get_exec_mode('default_key');

		$this->assertFalse($r,"Il comando è andato a buon fine senza un file di execution mode!");

		$d = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/mode/');
		$d->touch();

		$f = $d->newFile('development.txt');
		$f->setContent("howla");

		$r = $dc->get_exec_mode('default_key');

		$this->assertEqual($r,"development","Il comando non è andato a buon fine e avrebbe dovuto!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok
	function testSetExecMode() {
		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->get_exec_mode('default_key');

		$this->assertFalse($r,"Il comando è andato a buon fine senza un file di execution mode!");

		$r = $dc->set_exec_mode('default_key','fd');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/mode/framework_development.txt');

		$this->assertTrue($f->exists(),"Il file del config mode non è dove dovrebbe essere!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertEqual($r,"framework_development","Il comando non è stato eseguito con successo!");

		$r = $dc->set_exec_mode('default_key','testing');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/mode/testing.txt');

		$this->assertTrue($f->exists(),"Il file del config mode non è dove dovrebbe essere!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertEqual($r,"testing","Il comando non è stato eseguito con successo!");

		$r = $dc->set_exec_mode('default_key','maintenance');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/mode/maintenance.txt');

		$this->assertTrue($f->exists(),"Il file del config mode non è dove dovrebbe essere!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertEqual($r,"maintenance","Il comando non è stato eseguito con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok
	function testAttachDetach() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}
	
	//ok
	function testReset() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$enemy_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/enemy.txt');

		$this->assertFalse($enemy_file->exists(),"Il file intruso esiste già!");

		$enemy_file->touch();

		$this->assertTrue($enemy_file->exists(),"Il file intruso non esiste ma dovrebbe!");

		$r = $dc->reset('default_key');

		$this->assertTrue($r,"La procedura di reset non funziona correttamente!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok
	function testTempClean() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$other_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/other/');

		$other_dir->touch();

		$temp_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/temp/');

		$temp_dir->touch();

		$enemy_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/temp/enemy.txt');

		$this->assertFalse($enemy_file->exists(),"Il file intruso esiste già!");

		$enemy_file->touch();

		$this->assertTrue($enemy_file->exists(),"Il file intruso non esiste ma dovrebbe!");

		$r = $dc->temp_clean('default_key');

		$this->assertTrue($r,"La procedura di reset non funziona correttamente!");

		$this->assertFalse($enemy_file->exists(),"Il file intruso non è stato ripulito!");

		$this->assertTrue($other_dir->exists(),"L'altra directory è stata cancellata senza motivo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok
	function testSetGetEnv() {
		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$dc->set_deployer_path_from_root('default_key','ABCD');

		$r = $dc->get_deployer_path_from_root('default_key');

		$this->assertEqual($r,"ABCD","Il percorso del deployer dalla root non è corretto!");

		$r = $dc->detach('default_key');

		$this->assertTrue($this->isSuccess($r),"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	
	//ok
	function testDeployerUpdate() {

		$this->initAll();

		$df = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($df->exists(),"Il deployer non è stato trovato al suo posto!");

		$time1 = $df->getLastModificationTime();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		sleep(1);

		$r = $dc->set_deployer_path_from_root('default_key','deployer.php');

		$r = $dc->deployer_update('default_key');

		$time2 = $df->getLastModificationTime();

		$this->assertTrue($time2>$time1,"Il deployer non è stato aggiornato con l'ultima versione!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	
	//ok
	function testProjectCheck() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','deployer.php');

		$this->assertTrue($r,"Il set del percorso dalla root non è andato a buon fine!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->project_check('default_key');

		$this->assertTrue($r,"Il check non è stato eseguito con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	
	
	//ok
	function testProjectUpdate() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','deployer.php');

		$this->assertTrue($r,"Il set del percorso del deployer non è valido");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->project_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$f1 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/project_file.txt');

		$this->assertTrue($f1->exists(),"Il file di progetto non è stato copiato con successo!");

		$f2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/project_dir/my_file.txt');

		$this->assertTrue($f2->exists(),"Il file di progetto nella sottodirectory non è stato copiato con successo!");

		$f3 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/project_dir/subdir/my_subdir_file.txt');

		$this->assertTrue($f3->exists(),"Il file di progetto nella sotto sotto directory non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	
	//ok
	function testFrameworkCheck() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->framework_check('default_key');

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	
	//ok
	function testFrameworkUpdate() {
		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->framework_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$f1 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/limalably_framework/SampleClass.class.php');

		$this->assertTrue($f1->exists(),"Il file del framework non è stato copiato con successo!");

		$f2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/limalably_framework/bin/sample_command.sh');

		$this->assertFalse($f2->exists(),"Il file nella sottodirectory del framework è stato copiato ma non doveva esserci!");

		$f3 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/limalably_framework/lib/subdir/SampleLib.class.php');

		$this->assertTrue($f3->exists(),"Il file nella sotto sotto directory del framework non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}

	//ok
	function testDisappear() {
		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$fd = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($fd->exists(),"Il deployer non è al suo posto!");

		$r = $dc->disappear('default_key');

		$this->assertTrue($r,"Il comando di disappear non è andato a buon fine!");

		$this->assertFalse($fd->exists(),"Il deployer non è stato cancellato!");
	}
	

	//ok
	function testAutoConfig() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->auto_config('default_key');

		$this->assertFalse($r,"La procedura di auto_config ha dato esito positivo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	//ok
	function testManualConfig() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$host_config = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/hostnames/my_host/config.json');

		$this->assertFalse($host_config->exists(),"Il file di configurazione esiste già nella destinazione e non dovrebbe!");

		$r = $dc->manual_config('default_key','my_host');

		$this->assertTrue($r,"La procedura di manual_config ha dato esito negativo!");

		$this->assertTrue($host_config->exists(),"Il file di configurazione non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	//ok
	function testDeployerVersion() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->deployer_version('default_key');

		$this->assertTrue($r!==false,"La procedura di version ha dato esito negativo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	//ok
	function testBackup() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');

		$backup_save_dir->touch();

		$backup_save_dir->delete(true);

		$backup_save_dir->touch();

		$r = $dc->backup('default_key','/',$backup_save_dir->getFullPath());

		$this->assertTrue($r,"La procedura di backup ha dato esito negativo!");

		$file_list = $backup_save_dir->listFiles();

		$this->assertTrue(count($file_list)==1,"The backup save dir does not contain any saved file");

		$this->assertTrue(LStringUtils::endsWith($file_list[0]->getFilename(),'zip'),"The backup saved file is not a zip file");

		$this->assertTrue($file_list[0]->getSize()>0,"The backup file is empty!");

		$r = $dc->detach('default_key');

		$this->disposeAll();
	}

	//ok
	function testFixPermissions() {

		$this->initAll();

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$perm_to_fix_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/perm_to_fix/');

		$perm_to_fix_dir->touch();

		$f1 = $perm_to_fix_dir->newFile("f1.txt");
		$f1->touch();

		$f2 = $perm_to_fix_dir->newFile("f2.txt");
		$f2->setContent("Hello!");

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$p_flags = '-rwxrw-rw-';

		$r = $dc->fix_permissions('default_key',$p_flags);

		$this->assertTrue($r,"La procedura di fix permissions ha dato esito negativo!");

		$this->assertEqual($f1->getPermissions(),$p_flags,"I flag impostati non corrispondono!");
		$this->assertEqual($f2->getPermissions(),$p_flags,"I flag impostati non corrispondono!");
		
		$r = $dc->detach('default_key');

		$this->disposeAll();
	}

	//ok
	function testHelp() {
		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->help();

		$this->assertTrue($r,"C'è stato un errore nella visualizzazione dell'help del deployer");
	}
	
	
}